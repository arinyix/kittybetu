<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class AuthMiddleware {
    private $jwtConfig;
    public function __construct($jwtConfig) {
        $this->jwtConfig = $jwtConfig;
    }
    public function check() {
        if (empty($_COOKIE['access_token'])) return false;
        try {
            $decoded = JWT::decode($_COOKIE['access_token'], new Key($this->jwtConfig['JWT_SECRET'], 'HS256'));
            if ($decoded->exp < time()) return false;
            if ($decoded->iss !== $this->jwtConfig['JWT_ISS']) return false;
            return $decoded->sub;
        } catch (Exception $e) {
            return false;
        }
    }
}
