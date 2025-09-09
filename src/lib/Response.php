<?php
declare(strict_types=1);

final class Response {
    public static function redirect(string $to): never {
        header('Location: '.$to, true, 302);
        exit;
    }

    public static function flash(string $type, string $msg): void {
        $_SESSION['_flash'][] = ['type'=>$type,'msg'=>$msg];
    }

    /** @return array<int, array{type:string,msg:string}> */
    public static function consumeFlash(): array {
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $f;
    }
}
