<?php
declare(strict_types=1);

final class CSRF {
    private const KEY = '_csrf_tokens';

    public static function token(string $form): string {
        if (!isset($_SESSION[self::KEY])) $_SESSION[self::KEY] = [];
        $t = bin2hex(random_bytes(32));
        $_SESSION[self::KEY][$form] = $t;
        return $t;
    }

    public static function check(string $form, ?string $token): bool {
        $ok = isset($_SESSION[self::KEY][$form]) && hash_equals($_SESSION[self::KEY][$form], (string)$token);
        // uso único
        unset($_SESSION[self::KEY][$form]);
        return $ok;
    }

    public static function input(string $form): string {
        $t = self::token($form);
        return '<input type="hidden" name="csrf_token" value="'.e($t).'">';
    }
}
