<?php
declare(strict_types=1);

require_once __DIR__.'/../database/connection.php';

final class BetManager {
    private PDO $db;
    public function __construct(){ $this->db = DB::conn(); }

    /* ================= Eventos & Seleções (genérico) ================= */

    public function createEvent(int $creatorId, string $titulo, ?string $descricao, ?string $fecha_em): array {
        $titulo = trim($titulo);
        if ($titulo === '') return ['ok'=>false,'error'=>'Título é obrigatório.'];
        $st = $this->db->prepare('INSERT INTO apostas_eventos (creator_user_id, titulo, descricao, fecha_em) VALUES (?,?,?,?)');
        $st->execute([$creatorId, $titulo, $descricao, $fecha_em]);
        return ['ok'=>true, 'id'=>(int)$this->db->lastInsertId()];
    }

    public function addSelection(int $eventoId, string $rotulo, float $odd): array {
        $rotulo = trim($rotulo);
        if ($rotulo === '') return ['ok'=>false,'error'=>'Rótulo obrigatório.'];
        if ($odd < 1.01) return ['ok'=>false,'error'=>'Odd deve ser >= 1.01'];
        $st = $this->db->prepare('INSERT INTO apostas_selecoes (evento_id, rotulo, odd) VALUES (?,?,?)');
        $st->execute([$eventoId, $rotulo, $odd]);
        return ['ok'=>true, 'id'=>(int)$this->db->lastInsertId()];
    }

    /** cria partida (mandante vs visitante) com mercado e odds */
    public function createMatchEvent(
        int $creatorId, string $mandante, string $visitante,
        ?string $fecha_em, string $mercado,
        array $odds // ver formato abaixo
    ): array {
        $mandante = trim($mandante);
        $visitante = trim($visitante);
        if ($mandante === '' || $visitante === '') return ['ok'=>false,'error'=>'Times são obrigatórios.'];
        if (!in_array($mercado, ['1x2','dupla_chance','vencedor'], true)) return ['ok'=>false,'error'=>'Mercado inválido.'];

        $titulo = "{$mandante} vs {$visitante} — ".strtoupper(str_replace('_',' ',$mercado));
        $st = $this->db->prepare('INSERT INTO apostas_eventos (creator_user_id, titulo, descricao, fecha_em, mandante, visitante, mercado) VALUES (?,?,?,?,?,?,?)');
        $st->execute([$creatorId, $titulo, null, $fecha_em, $mandante, $visitante, $mercado]);
        $eid = (int)$this->db->lastInsertId();

        // criar seleções conforme mercado
        if ($mercado === '1x2') {
            // $odds = ['odd1'=>..,'oddx'=>..,'odd2'=>..]
            $this->addSelection($eid, $mandante, (float)$odds['odd1']);
            $this->addSelection($eid, 'Empate', (float)$odds['oddx']);
            $this->addSelection($eid, $visitante, (float)$odds['odd2']);
        } elseif ($mercado === 'dupla_chance') {
            // $odds = ['odd1x'=>..,'oddx2'=>..,'odd12'=>..]
            $this->addSelection($eid, '1X', (float)$odds['odd1x']);
            $this->addSelection($eid, 'X2', (float)$odds['oddx2']);
            $this->addSelection($eid, '12', (float)$odds['odd12']);
        } else { // vencedor
            // $odds = ['odd1'=>..,'odd2'=>..]
            $this->addSelection($eid, $mandante, (float)$odds['odd1']);
            $this->addSelection($eid, $visitante, (float)$odds['odd2']);
        }
        return ['ok'=>true,'id'=>$eid];
    }

    /** lista eventos abertos com seleções */
    public function listOpen(): array {
        $ev = $this->db->query("SELECT * FROM apostas_eventos WHERE status='aberto' AND (fecha_em IS NULL OR fecha_em > NOW()) ORDER BY created_at DESC")->fetchAll();
        $out = [];
        foreach ($ev as $e) {
            $st = $this->db->prepare('SELECT * FROM apostas_selecoes WHERE evento_id=? ORDER BY id');
            $st->execute([(int)$e['id']]);
            $e['selecoes'] = $st->fetchAll();
            $out[] = $e;
        }
        return $out;
    }

    public function getEvent(int $id): ?array {
        $st = $this->db->prepare('SELECT * FROM apostas_eventos WHERE id=?');
        $st->execute([$id]);
        $e = $st->fetch();
        if (!$e) return null;
        $st = $this->db->prepare('SELECT * FROM apostas_selecoes WHERE evento_id=? ORDER BY id');
        $st->execute([$id]);
        $e['selecoes'] = $st->fetchAll();
        return $e;
    }

    /* ================= Apostar / Cancelar ================= */

    public function placeBet(int $userId, int $selecaoId, float $valor): array {
        if ($valor <= 0) return ['ok'=>false,'error'=>'Valor precisa ser > 0'];

        $st = $this->db->prepare(
          "SELECT s.id as selecao_id, s.odd, e.id as evento_id, e.status, e.fecha_em
           FROM apostas_selecoes s JOIN apostas_eventos e ON e.id = s.evento_id
           WHERE s.id = ? LIMIT 1"
        );
        $st->execute([$selecaoId]);
        $row = $st->fetch();
        if (!$row) return ['ok'=>false,'error'=>'Seleção não encontrada.'];
        if ($row['status'] !== 'aberto') return ['ok'=>false,'error'=>'Evento fechado.'];
        if (!empty($row['fecha_em']) && strtotime((string)$row['fecha_em']) <= time()) return ['ok'=>false,'error'=>'Evento venceu.'];

        $odd = (float)$row['odd'];
        $retorno = round($valor * $odd, 2);

        $this->db->beginTransaction();
        try {
            $c = $this->db->prepare('SELECT id, saldo FROM contas WHERE user_id=? FOR UPDATE');
            $c->execute([$userId]);
            $conta = $c->fetch();
            if (!$conta) { $this->db->rollBack(); return ['ok'=>false,'error'=>'Conta não encontrada.']; }
            if ((float)$conta['saldo'] < $valor) { $this->db->rollBack(); return ['ok'=>false,'error'=>'Saldo insuficiente.']; }

            $novo = (float)$conta['saldo'] - $valor;
            $u = $this->db->prepare('UPDATE contas SET saldo=? WHERE id=?');
            $u->execute([$novo, (int)$conta['id']]);
            $l = $this->db->prepare('INSERT INTO lancamentos (conta_id, tipo, valor, descricao) VALUES (?, "debito", ?, ?)');
            $l->execute([(int)$conta['id'], $valor, 'Aposta criada']);

            $ap = $this->db->prepare('INSERT INTO apostas (user_id, evento_id, selecao_id, valor, odd, retorno_potencial) VALUES (?,?,?,?,?,?)');
            $ap->execute([$userId, (int)$row['evento_id'], $selecaoId, $valor, $odd, $retorno]);

            $this->db->commit();
            return ['ok'=>true];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return ['ok'=>false,'error'=>'Erro ao apostar.'];
        }
    }

    /** cancelar aposta (antes do fechamento) */
    public function cancelBet(int $userId, int $betId): array {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare(
              "SELECT a.*, e.status as ev_status, e.fecha_em, c.id as conta_id
               FROM apostas a
               JOIN apostas_eventos e ON e.id=a.evento_id
               JOIN contas c ON c.user_id=a.user_id
               WHERE a.id=? AND a.user_id=? FOR UPDATE"
            );
            $st->execute([$betId, $userId]);
            $b = $st->fetch();
            if (!$b) { $this->db->rollBack(); return ['ok'=>false,'error'=>'Aposta não encontrada.']; }
            if ($b['status'] !== 'aberta') { $this->db->rollBack(); return ['ok'=>false,'error'=>'Aposta não está aberta.']; }
            if ($b['ev_status'] !== 'aberto') { $this->db->rollBack(); return ['ok'=>false,'error'=>'Evento fechado.']; }
            if (!empty($b['fecha_em']) && strtotime((string)$b['fecha_em']) <= time()) { $this->db->rollBack(); return ['ok'=>false,'error'=>'Evento venceu.']; }

            // estorna o valor
            $updC = $this->db->prepare('UPDATE contas SET saldo = saldo + ? WHERE id=?');
            $updC->execute([(float)$b['valor'], (int)$b['conta_id']]);

            $insL = $this->db->prepare('INSERT INTO lancamentos (conta_id, tipo, valor, descricao) VALUES (?, "credito", ?, ?)');
            $insL->execute([(int)$b['conta_id'], (float)$b['valor'], 'Aposta cancelada #'.$betId]);

            $updA = $this->db->prepare("UPDATE apostas SET status='cancelada' WHERE id=?");
            $updA->execute([$betId]);

            $this->db->commit();
            return ['ok'=>true];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return ['ok'=>false,'error'=>'Falha ao cancelar.'];
        }
    }

    /* ================= Liquidação ================= */

    public function settleEvent(int $adminId, string $adminEmail, int $eventoId, int $winningSelecaoId): array {
        $isAdmin = (strcasecmp($adminEmail, 'admin@kittybetu.com')===0);
        if (!$isAdmin) return ['ok'=>false,'error'=>'Somente admin pode liquidar.'];

        $this->db->beginTransaction();
        try {
            $ev = $this->db->prepare("SELECT id,status FROM apostas_eventos WHERE id=? FOR UPDATE");
            $ev->execute([$eventoId]);
            $E = $ev->fetch();
            if (!$E) { $this->db->rollBack(); return ['ok'=>false,'error'=>'Evento não existe.']; }
            if ($E['status'] !== 'aberto') { $this->db->rollBack(); return ['ok'=>false,'error'=>'Evento não está aberto.']; }

            $win = $this->db->prepare("SELECT a.*, c.id as conta_id FROM apostas a 
              JOIN contas c ON c.user_id=a.user_id
              WHERE a.evento_id=? AND a.selecao_id=? AND a.status='aberta' FOR UPDATE");
            $win->execute([$eventoId, $winningSelecaoId]);
            $wins = $win->fetchAll();

            $los = $this->db->prepare("SELECT a.id FROM apostas a WHERE a.evento_id=? AND a.selecao_id<>? AND a.status='aberta'");
            $los->execute([$eventoId, $winningSelecaoId]);
            $losers = $los->fetchAll();

            foreach ($wins as $w) {
                $credit = (float)$w['retorno_potencial'];
                $updC = $this->db->prepare('UPDATE contas SET saldo = saldo + ? WHERE id=?');
                $updC->execute([$credit, (int)$w['conta_id']]);

                $insL = $this->db->prepare('INSERT INTO lancamentos (conta_id, tipo, valor, descricao) VALUES (?, "credito", ?, ?)');
                $insL->execute([(int)$w['conta_id'], $credit, 'Aposta ganha #'.$w['id']]);

                $updA = $this->db->prepare("UPDATE apostas SET status='ganha' WHERE id=?");
                $updA->execute([(int)$w['id']]);
            }

            if (!empty($losers)) {
                $ids = array_map(fn($r)=>(int)$r['id'], $losers);
                $in  = implode(',', array_fill(0, count($ids), '?'));
                $upd = $this->db->prepare("UPDATE apostas SET status='perdida' WHERE id IN ($in)");
                $upd->execute($ids);
            }

            $fe = $this->db->prepare("UPDATE apostas_eventos SET status='liquidado' WHERE id=?");
            $fe->execute([$eventoId]);

            $this->db->commit();
            return ['ok'=>true];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return ['ok'=>false,'error'=>'Falha ao liquidar.'];
        }
    }

    /* ================= Consultas ================= */

    public function listUserBets(int $userId): array {
        $st = $this->db->prepare("SELECT a.*, e.titulo, e.status as evento_status, e.fecha_em, s.rotulo 
          FROM apostas a 
          JOIN apostas_eventos e ON e.id=a.evento_id
          JOIN apostas_selecoes s ON s.id=a.selecao_id
          WHERE a.user_id=? ORDER BY a.created_at DESC");
        $st->execute([$userId]);
        return $st->fetchAll();
    }
}
