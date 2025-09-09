<?php
declare(strict_types=1);

final class SimpleJWT {
    /** @return string base64url */
    private static function b64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    /** @return string */
    public static function encode(array $payload, string $secret): string {
        $header = ['alg'=>'HS256','typ'=>'JWT'];
        $h = self::b64url(json_encode($header, JSON_UNESCAPED_SLASHES));
        $p = self::b64url(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $sig = hash_hmac('sha256', $h.'.'.$p, $secret, true);
        $s = self::b64url($sig);
        return $h.'.'.$p.'.'.$s;
    }
    /** @return array{valid:bool,payload?:array} */
    public static function decode(string $jwt, string $secret): array {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return ['valid'=>false];
        [$h,$p,$s] = $parts;
        $calc = self::b64url(hash_hmac('sha256', $h.'.'.$p, $secret, true));
        if (!hash_equals($calc, $s)) return ['valid'=>false];
        $payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
        if (!is_array($payload)) return ['valid'=>false];
        if (isset($payload['exp']) && time() >= (int)$payload['exp']) return ['valid'=>false];
        return ['valid'=>true,'payload'=>$payload];
    }
}
