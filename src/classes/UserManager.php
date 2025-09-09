<?php
declare(strict_types=1);

require_once __DIR__.'/../database/connection.php';

final class UserManager {
    private PDO $db;
    public function __construct() { $this->db = DB::conn(); }

    private function sanitizeCpf(?string $cpf): ?string {
        if (!$cpf) return null;
        $d = preg_replace('/\D+/', '', $cpf);
        if (strlen($d) !== 11) return null;
        return substr($d,0,3).'.'.substr($d,3,3).'.'.substr($d,6,3).'-'.substr($d,9,2);
    }
    private function sanitizePhone(?string $tel): ?string {
        if (!$tel) return null;
        $d = preg_replace('/\D+/', '', $tel);
        if (strlen($d) < 10) return null;
        if (strlen($d) === 10) return '('.substr($d,0,2).') '.substr($d,2,4).'-'.substr($d,6,4);
        return '('.substr($d,0,2).') '.substr($d,2,5).'-'.substr($d,7,4);
    }

    /** @return array{ok:bool,errors?:array} */
    private function validateCreate(array $d): array {
        $errors = [];
        if (empty($d['nome'])) $errors['nome'] = 'Nome é obrigatório.';
        if (empty($d['email']) || !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $errors['email']='Email inválido.';
        if (empty($d['senha']) || strlen($d['senha']) < 6) $errors['senha']='Senha deve ter 6+ caracteres.';
        if (!empty($d['cpf'])) {
            $cpf = $this->sanitizeCpf($d['cpf']);
            if (!$cpf) $errors['cpf']='CPF inválido.';
        }
        if (!empty($d['telefone'])) {
            $tel = $this->sanitizePhone($d['telefone']);
            if (!$tel) $errors['telefone']='Telefone inválido.';
        }
        return ['ok'=>empty($errors), 'errors'=>$errors];
    }

    public function create(array $d): array {
        $v = $this->validateCreate($d);
        if (!$v['ok']) return ['ok'=>false,'errors'=>$v['errors']];
        $cpf = $this->sanitizeCpf($d['cpf'] ?? null);
        $tel = $this->sanitizePhone($d['telefone'] ?? null);
        $dn  = !empty($d['data_nascimento']) ? $d['data_nascimento'] : null;

        // checar unicidade manual para feedback melhor
        $st = $this->db->prepare('SELECT email, cpf FROM usuarios WHERE email = ? OR (cpf IS NOT NULL AND cpf = ?)');
        $st->execute([$d['email'], $cpf]);
        if ($row = $st->fetch()) {
            if ($row['email'] === $d['email']) return ['ok'=>false,'errors'=>['email'=>'Email já cadastrado.']];
            if ($cpf && $row['cpf'] === $cpf)  return ['ok'=>false,'errors'=>['cpf'=>'CPF já cadastrado.']];
        }

        $hash = password_hash($d['senha'], PASSWORD_DEFAULT);
        $this->db->beginTransaction();
        try {
            $ins = $this->db->prepare('INSERT INTO usuarios (nome,email,senha,telefone,data_nascimento,cpf,status) VALUES (?,?,?,?,?,?, "ativo")');
            $ins->execute([$d['nome'],$d['email'],$hash,$tel,$dn,$cpf]);
            $uid = (int)$this->db->lastInsertId();

            $acc = $this->db->prepare('INSERT INTO contas (user_id, saldo) VALUES (?, 0)');
            $acc->execute([$uid]);

            $this->db->commit();
            return ['ok'=>true,'id'=>$uid];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return ['ok'=>false,'errors'=>['db'=>'Erro ao criar usuário.']];
        }
    }

    public function findByEmail(string $email): ?array {
        $st = $this->db->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $u = $st->fetch();
        return $u ?: null;
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $u = $st->fetch();
        return $u ?: null;
    }

    /** @return array<int,array> */
    public function listAll(): array {
        $st = $this->db->query('SELECT id, nome, email, status, created_at FROM usuarios ORDER BY created_at DESC');
        return $st->fetchAll();
    }

    public function updateProfile(int $id, array $d): bool {
        $tel = $this->sanitizePhone($d['telefone'] ?? null);
        $dn  = !empty($d['data_nascimento']) ? $d['data_nascimento'] : null;
        $st = $this->db->prepare('UPDATE usuarios SET nome=?, telefone=?, data_nascimento=? WHERE id=?');
        return $st->execute([$d['nome'], $tel, $dn, $id]);
    }

    public function changePassword(int $id, string $old, string $new): array {
        $u = $this->findById($id);
        if (!$u) return ['ok'=>false,'error'=>'Usuário não encontrado.'];
        if (!password_verify($old, $u['senha'])) return ['ok'=>false,'error'=>'Senha atual incorreta.'];
        if (strlen($new) < 6) return ['ok'=>false,'error'=>'Nova senha deve ter 6+ caracteres.'];
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $st = $this->db->prepare('UPDATE usuarios SET senha=? WHERE id=?');
        $st->execute([$hash, $id]);
        return ['ok'=>true];
    }

    public function deleteUser(int $requesterId, int $id, string $requesterEmail): array {
        // Regra simples de demo: admin pode apagar qualquer um; usuários podem apagar a si próprios
        $isAdmin = (strcasecmp($requesterEmail, 'admin@kittybetu.com') === 0);
        if (!$isAdmin && $requesterId !== $id) return ['ok'=>false,'error'=>'Sem permissão para excluir este usuário.'];
        $st = $this->db->prepare('DELETE FROM usuarios WHERE id=?');
        $st->execute([$id]);
        return ['ok'=>true];
    }
}
