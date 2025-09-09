<?php
declare(strict_types=1);

require_once __DIR__.'/../database/connection.php';
require_once __DIR__.'/UserManager.php';
require_once __DIR__.'/SimpleJWT.php';

final class Auth {
    private PDO $db;
    private UserManager $users;

    public function __construct() {
        $this->db = DB::conn();
        $this->users = new UserManager();
    }

    /** @return array{ok:bool,error?:string} */
    public function login(string $email, string $password): array {
        $u = $this->users->findByEmail($email);
        if (!$u || $u['status'] !== 'ativo') return ['ok'=>false,'error'=>'Credenciais inválidas.'];
        if (!password_verify($password, $u['senha'])) return ['ok'=>false,'error'=>'Credenciais inválidas.'];

        session_regenerate_id(true);

        $_SESSION['uid'] = (int)$u['id'];
        $_SESSION['uemail'] = $u['email'];
        $_SESSION['uname'] = $u['nome'];

        // emitir JWT
        $now = time();
        $payload = [
            'iss' => JWT_ISS,
            'sub' => (int)$u['id'],
            'email' => $u['email'],
            'iat' => $now,
            'exp' => $now + JWT_EXP_SECONDS
        ];
        $jwt = SimpleJWT::encode($payload, JWT_SECRET);

        // salvar no banco para revogação simples
        $exp = date('Y-m-d H:i:s', $payload['exp']);
        $st = $this->db->prepare('INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?,?,?)');
        $st->execute([(int)$u['id'], $jwt, $exp]);

        $_SESSION['token'] = $jwt;
        setcookie('kittybetu_token', $jwt, [
            'expires' => 0, 'path' => '/', 'secure' => false, 'httponly' => true, 'samesite' => 'Lax'
        ]);

        return ['ok'=>true];
    }

    public function logout(): void {
        if (!empty($_SESSION['uid']) && !empty($_SESSION['token'])) {
            $st = $this->db->prepare('DELETE FROM user_tokens WHERE user_id=? AND token=?');
            $st->execute([(int)$_SESSION['uid'], (string)$_SESSION['token']]);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        setcookie('kittybetu_token','', time()-3600, '/');
        session_destroy();
    }

    public function check(): bool {
        if (empty($_SESSION['uid']) || empty($_SESSION['token'])) return false;
        $dec = SimpleJWT::decode($_SESSION['token'], JWT_SECRET);
        if (!$dec['valid']) return false;

        // conferir no banco (revogação/expiração)
        $st = $this->db->prepare('SELECT 1 FROM user_tokens WHERE user_id=? AND token=? AND expires_at > NOW()');
        $st->execute([(int)$_SESSION['uid'], (string)$_SESSION['token']]);
        return (bool)$st->fetchColumn();
    }

    public function user(): ?array {
        if (!$this->check()) return null;
        return $this->users->findById((int)$_SESSION['uid']);
    }
}
