<?php
function validation_validateRegister($data, $dbConfig) {
    $errors = [];
    if (empty($data['name'])) $errors[] = 'Nome obrigatório.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if (User::findByEmail($dbConfig, $data['email'])) $errors[] = 'Email já cadastrado.';
    if (!cpf_isValid($data['cpf'])) $errors[] = 'CPF inválido.';
    if (!phone_isValid($data['phone'])) $errors[] = 'Telefone inválido.';
    $senhaErro = validation_validatePassword($data['password']);
    if ($senhaErro) $errors[] = $senhaErro;
    return $errors;
}
function validation_validateEdit($data, $dbConfig, $id) {
    $errors = [];
    if (empty($data['name'])) $errors[] = 'Nome obrigatório.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    $user = User::findByEmail($dbConfig, $data['email']);
    if ($user && $user['id'] != $id) $errors[] = 'Email já cadastrado.';
    if (!cpf_isValid($data['cpf'])) $errors[] = 'CPF inválido.';
    if (!phone_isValid($data['phone'])) $errors[] = 'Telefone inválido.';
    return $errors;
}
function validation_validatePassword($password) {
    if (strlen($password) < 8) return 'Senha deve ter ao menos 8 caracteres.';
    if (!preg_match('/[A-Za-z]/', $password)) return 'Senha deve conter letra.';
    if (!preg_match('/\d/', $password)) return 'Senha deve conter dígito.';
    return '';
}
