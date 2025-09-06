<?php
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
function setSecurityHeaders() {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Content-Security-Policy: default-src \'self\';');
}
// Rate limit login (simple, by IP)
function rateLimitLogin($ip) {
    if (empty($_SESSION)) session_start();
    if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = [];
    $now = time();
    $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($t) use ($now) {
        return $t > $now - 900;
    });
    if (count($_SESSION['login_attempts']) >= 5) return false;
    $_SESSION['login_attempts'][] = $now;
    return true;
}
