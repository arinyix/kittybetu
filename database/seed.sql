USE kittybetu_db;

-- Descobre um criador (admin se existir, senão o primeiro usuário)
SET @creator := (SELECT id FROM usuarios WHERE email='admin@kittybetu.com' LIMIT 1);
SET @creator := IFNULL(@creator, (SELECT id FROM usuarios ORDER BY id LIMIT 1));

-- Dá um gás no saldo para todos testarem
UPDATE contas SET saldo = saldo + 200;

-- ========== Evento 1: 1X2 ==========
INSERT INTO apostas_eventos (creator_user_id, titulo, descricao, fecha_em, status)
VALUES (@creator, 'SEED: Flamengo x Vasco — 1X2',
        'Jogo de demonstração (mercado 1X2).',
        DATE_ADD(NOW(), INTERVAL 2 DAY), 'aberto');
SET @ev1 := LAST_INSERT_ID();

INSERT INTO apostas_selecoes (evento_id, rotulo, odd) VALUES
(@ev1, '1', 1.80),   -- vitória Flamengo
(@ev1, 'X', 3.40),   -- empate
(@ev1, '2', 4.00);   -- vitória Vasco

-- ========== Evento 2: Dupla Chance ==========
INSERT INTO apostas_eventos (creator_user_id, titulo, descricao, fecha_em, status)
VALUES (@creator, 'SEED: Flamengo x Vasco — Dupla Chance',
        'Jogo de demonstração (mercado Dupla Chance).',
        DATE_ADD(NOW(), INTERVAL 2 DAY), 'aberto');
SET @ev2 := LAST_INSERT_ID();

INSERT INTO apostas_selecoes (evento_id, rotulo, odd) VALUES
(@ev2, '1X', 1.30),
(@ev2, 'X2', 1.90),
(@ev2, '12', 1.25);

-- ========== Evento 3: 1X2 (Outro jogo) ==========
INSERT INTO apostas_eventos (creator_user_id, titulo, descricao, fecha_em, status)
VALUES (@creator, 'SEED: Palmeiras x Santos — 1X2',
        'Segundo jogo de demonstração.',
        DATE_ADD(NOW(), INTERVAL 3 DAY), 'aberto');
SET @ev3 := LAST_INSERT_ID();

INSERT INTO apostas_selecoes (evento_id, rotulo, odd) VALUES
(@ev3, '1', 1.95),
(@ev3, 'X', 3.20),
(@ev3, '2', 3.80);

-- Visualização rápida (opcional)
-- SELECT e.id, e.titulo, s.rotulo, s.odd FROM apostas_eventos e JOIN apostas_selecoes s ON s.evento_id=e.id ORDER BY e.id, s.id;
