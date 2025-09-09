-- Criação do banco e tabelas do kittybetU
CREATE DATABASE IF NOT EXISTS kittybetu_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
USE kittybetu_db;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  telefone VARCHAR(20),
  data_nascimento DATE,
  cpf VARCHAR(14) UNIQUE, -- formato ###.###.###-##
  status ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tokens de sessão/JWT
CREATE TABLE IF NOT EXISTS user_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  token VARCHAR(512) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX user_token_idx (user_id, token(64)),
  CONSTRAINT fk_tokens_user FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Conta (única) por usuário
CREATE TABLE IF NOT EXISTS contas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  saldo DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_contas_user FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Lançamentos (didático – não há transações reais)
CREATE TABLE IF NOT EXISTS lancamentos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  conta_id INT UNSIGNED NOT NULL,
  tipo ENUM('credito','debito') NOT NULL,
  valor DECIMAL(12,2) NOT NULL,
  descricao VARCHAR(255),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX conta_created_idx (conta_id, created_at),
  CONSTRAINT fk_lanc_conta FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- (Opcional) Descomente para criar rapidamente um admin e definir a conta vazia.
-- ATENÇÃO: a senha precisa ser um hash bcrypt válido.
-- INSERT INTO usuarios (nome, email, senha, status) VALUES
-- ('Administrador', 'admin@kittybetu.com', '$2y$10$USE_UM_HASH_BCRYPT_REAL_AQUI..............', 'ativo');
-- INSERT INTO contas (user_id, saldo) VALUES (LAST_INSERT_ID(), 0);
