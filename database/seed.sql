USE kittybetu;

INSERT INTO users (name, email, cpf, phone, birth_date, password_hash, role)
VALUES
('Admin Teste', 'admin@kittybetu.com', '123.456.789-09', '(11) 91234-5678', '1990-01-01', '$2y$10$eImGQ8r1bG6QJxQ6QJxQ6uQJxQ6QJxQ6QJxQ6QJxQ6QJxQ6QJxQ6', 'admin'),
('Usuário Teste', 'user@kittybetu.com', '987.654.321-00', '(21) 99876-5432', '1995-05-15', '$2y$10$eImGQ8r1bG6QJxQ6QJxQ6uQJxQ6QJxQ6QJxQ6QJxQ6QJxQ6QJxQ6', 'user');
-- As senhas acima são hashes de exemplo, troque por hashes reais ao popular.
