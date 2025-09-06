# kittybetu

Gestão de usuários para plataforma de apostas com autenticação JWT, tema neon lilás + preto, máscaras de CPF/telefone, rodando em XAMPP (Apache + PHP 8+ + MySQL).

## Instalação e Execução

1. **Pré-requisitos:**
	 - XAMPP (PHP 8+, MySQL 8+)
	 - Composer

2. **Configuração:**
	 - Copie a pasta `kittybetu` para `htdocs` do XAMPP: `/opt/lampp/htdocs/kittybetu`
	 - Acesse o painel do MySQL (phpMyAdmin ou CLI)
	 - Importe o banco:
		 - Execute `database/schema.sql`
		 - Execute `database/seed.sql`
	 - Configure variáveis em `config/app.php`, `config/database.php`, `config/jwt.php` conforme necessário.

3. **Dependências:**
	 - No terminal, dentro da pasta do projeto:
		 ```bash
		 composer install
		 ```

4. **Execução:**
	 - Inicie o Apache/MySQL pelo XAMPP
	 - Acesse: [http://localhost/kittybetu/public](http://localhost/kittybetu/public)

## Usuários de Teste

- **Admin:**
	- Email: `adm@email.com`
	- Senha: `adm12345$` (hash no seed.sql)
- **Usuário:**
	- Email: `user@kittybetu.com`
	- Senha: `senha123` (hash no seed.sql)

## Estrutura de Pastas

```
kittybetu/
	public/
		.htaccess
		index.php
	app/
		controllers/
			AuthController.php
			UserController.php
		models/
			User.php
		middlewares/
			AuthMiddleware.php
			CsrfMiddleware.php
		views/
			auth/
				login.php
				register.php
			user/
				list.php
				show.php
				edit.php
				change_password.php
			dashboard/
				index.php
			partials/
				header.php
				footer.php
				alerts.php
		helpers/
			validation.php
			security.php
			cpf.php
			phone.php
			csrf.php
	config/
		app.php
		database.php
		jwt.php
	assets/
		css/
			main.css
			animations.css
		js/
			masks.js
			form-validation.js
			alerts.js
	database/
		schema.sql
		seed.sql
	composer.json
	README.md
```

## Segurança
- Cookies de autenticação são **HttpOnly**, `SameSite=Lax` e `Secure` em produção.
- JWT expira em 60 minutos.
- Todas requisições POST exigem **CSRF token**.
- Rate limit de login: 5 tentativas/15min por IP.
- Saída sempre sanitizada/escapada.
- Senhas com `password_hash`/`password_verify`.
- Prepared statements (PDO) em todas queries.

## Observações
- Não utilize frameworks PHP/CSS/JS.
- Máscaras e validações de CPF/telefone no front/back.
- Tema neon lilás + preto, responsivo, animações visíveis.
- Para dúvidas, consulte os arquivos de configuração e helpers.

---

> Siga os critérios de aceitação do projeto para garantir funcionamento e segurança.
# kittybetu