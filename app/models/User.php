<?php
class User {
    public static function getPDO($dbConfig) {
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['db']};charset={$dbConfig['charset']}";
        return new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    public static function findByEmail($dbConfig, $email) {
        $pdo = self::getPDO($dbConfig);
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function find($dbConfig, $id) {
        $pdo = self::getPDO($dbConfig);
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($dbConfig, $data) {
        $pdo = self::getPDO($dbConfig);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, cpf, phone, birth_date, password_hash) VALUES (?, ?, ?, ?, ?, ?)');
        $ok = $stmt->execute([
            $data['name'],
            $data['email'],
            $data['cpf'],
            $data['phone'],
            $data['birth_date'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        return $ok ? $pdo->lastInsertId() : false;
    }
    public static function update($dbConfig, $id, $data) {
        $pdo = self::getPDO($dbConfig);
        $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, cpf=?, phone=?, birth_date=? WHERE id=? AND deleted_at IS NULL');
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['cpf'],
            $data['phone'],
            $data['birth_date'],
            $id
        ]);
    }
    public static function changePassword($dbConfig, $id, $password) {
        $pdo = self::getPDO($dbConfig);
        $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE id=? AND deleted_at IS NULL');
        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id
        ]);
    }
    public static function softDelete($dbConfig, $id) {
        $pdo = self::getPDO($dbConfig);
        $stmt = $pdo->prepare('UPDATE users SET deleted_at=NOW() WHERE id=?');
        return $stmt->execute([$id]);
    }
    public static function paginate($dbConfig, $page = 1, $q = '') {
        $pdo = self::getPDO($dbConfig);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sql = 'SELECT * FROM users WHERE deleted_at IS NULL';
        $params = [];
        if ($q) {
            $sql .= ' AND (name LIKE ? OR email LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $sql .= ' ORDER BY id DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
