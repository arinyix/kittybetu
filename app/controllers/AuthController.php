<?php
require_once __DIR__ . '/../models/User.php';
class AuthController {
    private $dbConfig, $jwtConfig;
    public function __construct($dbConfig, $jwtConfig) {
        $this->dbConfig = $dbConfig;
        $this->jwtConfig = $jwtConfig;
    }
    public function showLogin($errors = []) {
        require __DIR__ . '/../views/auth/login.php';
    }
    public function showRegister($errors = []) {
        require __DIR__ . '/../views/auth/register.php';
    }
    public function login($data) {
        $user = User::findByEmail($this->dbConfig, $data['email'] ?? '');
        if (!$user || !password_verify($data['password'] ?? '', $user['password_hash'])) {
            $this->showLogin(['Credenciais inválidas.']);
            return;
        }
        if ($user['deleted_at']) {
            $this->showLogin(['Usuário excluído.']);
            return;
        }
        // Rate limit: implementado em helpers/security.php
        // JWT
        $token = $this->generateJWT($user['id']);
        setcookie('access_token', $token, [
            'expires' => time() + $this->jwtConfig['JWT_EXP'],
            'path' => '/kittybetu/public',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => ($_SERVER['APP_ENV'] ?? 'local') === 'production',
        ]);
        header('Location: /kittybetu/public/dashboard');
        exit;
    }
    public function logout() {
        setcookie('access_token', '', time() - 3600, '/kittybetu/public');
        header('Location: /kittybetu/public/login');
        exit;
    }
    public function register($data) {
        $errors = validation_validateRegister($data, $this->dbConfig);
        if ($errors) {
            $this->showRegister($errors);
            return;
        }
        try {
            $userId = User::create($this->dbConfig, $data);
            if ($userId) {
                header('Location: /kittybetu/public/login');
                exit;
            }
            $this->showRegister(['Erro ao cadastrar usuário.']);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'cpf') !== false) {
                $this->showRegister(['CPF já cadastrado.']);
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                $this->showRegister(['Email já cadastrado.']);
            } else {
                $this->showRegister(['Erro ao cadastrar usuário.']);
            }
        }
    }
    private function generateJWT($userId) {
        $now = time();
        $payload = [
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + $this->jwtConfig['JWT_EXP'],
            'iss' => $this->jwtConfig['JWT_ISS'],
        ];
        return \Firebase\JWT\JWT::encode($payload, $this->jwtConfig['JWT_SECRET'], 'HS256');
    }
}
