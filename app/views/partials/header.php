<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kittybetu</title>
    <link rel="stylesheet" href="/kittybetu/assets/css/main.css">
    <link rel="stylesheet" href="/kittybetu/assets/css/animations.css">
</head>
<body>
<header class="header">
    <nav>
        <a href="/kittybetu/public/dashboard" class="logo">kittybetu</a>
        <ul>
            <li><a href="/kittybetu/public/users">Usuários</a></li>
            <li><form method="POST" action="/kittybetu/public/logout" style="display:inline;"><input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>"><button type="submit" class="btn">Sair</button></form></li>
        </ul>
    </nav>
</header>
<main class="main">
