<?php
function phone_isValid($phone) {
    $phone = preg_replace('/\D/', '', $phone);
    return (strlen($phone) >= 10 && strlen($phone) <= 11);
}
function phone_normalize($phone) {
    $phone = preg_replace('/\D/', '', $phone);
    return $phone;
}
