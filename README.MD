# kittybetU (PHP + MySQL + HTML/CSS/JS)

Projeto **acadêmico** de gestão de usuários com foco em **segurança**, **organização** e **performance**, rodando em **XAMPP (Apache + PHP 8.x + MySQL)** no Linux Mint.

## Funcionalidades
- Login, registro, dashboard, perfil, alterar senha, listar/ver/excluir usuários
- **Autenticação** com `password_hash` e **JWT HS256** (cookie HttpOnly + verificação no banco)
- **CSRF** em todos os formulários POST
- **XSS** mitigado (`htmlspecialchars`)
- **SQLi** blindado com **PDO + prepared statements**
- **Headers de segurança** (CSP, XFO, XCTO, Referrer-Policy) via `.htaccess`
- Tema **rosa neon + preto**, responsivo (grid/flex), **máscaras de CPF/telefone**

## Instalação (Linux Mint, XAMPP)
1. Inicie serviços:
   ```bash
   sudo /opt/lampp/lampp start
Copie o projeto para o htdocs:

bash
Copiar código
sudo mkdir -p /opt/lampp/htdocs/kittybetu
sudo chown -R "$USER":"$USER" /opt/lampp/htdocs/kittybetu
Importe o schema:

phpMyAdmin → Import database/schema.sql, ou:

bash
Copiar código
mysql -u root < /opt/lampp/htdocs/kittybetu/database/schema.sql
Configure src/config/config.php:

JWT_SECRET: substitua por um segredo robusto.

APP_URL: http://localhost/kittybetu/public

Acesse: http://localhost/kittybetu/public

Admin opcional
Você pode registrar pelo formulário ou criar admin depois executando um INSERT com hash bcrypt real (edite database/schema.sql e descomente as linhas de seed após inserir um hash válido).

Segurança — Checklist
 session.use_strict_mode=1, cookie HttpOnly, SameSite=Lax

 session_regenerate_id(true) no login

 CSRF por formulário (token one-time)

 XSS: e() (htmlspecialchars) em todas as saídas

 SQLi: sempre PDO->prepare()->execute([...])

 CSP restritiva, X-Frame-Options=DENY, X-Content-Type-Options=nosniff, Referrer-Policy

 JWT com expiração, revogação simples via user_tokens

Convenções
Código sem frameworks; somente PHP nativo.

Camadas: public/ (interfaces) + src/ (lógica).

Validações client-side (HTML5 + JS) e server-side (PHP).

yaml
Copiar código

---

# 4) (Opcional) composer.json — **somente** se você decidir usar libs depois
> **Não é necessário na Versão A.** Incluo vazio para referência.

## `composer.json`
```json
{
  "name": "kittybetu/kittybetu",
  "description": "Demo academico PHP nativo (JWT simples) - Versao A sem deps",
  "type": "project",
  "require": {},
  "autoload": {
    "psr-4": {
      "": "src/"
    }
  }
}
5) Dicas finais (visionárias & diretas)
Segredo do JWT: troque agora por um valor longo, aleatório (>= 64 chars).

Sem inline scripts: mantive CSP estrita (scripts só locais). Evite HTML inline de JS.

Permissões do htdocs: se der EACCES, ajuste posse e permissões como acima.

Admin & exclusão: por padrão, só o admin@kittybetu.com pode excluir terceiros; usuário comum só exclui a si mesmo — ética mínima para demo.

Escalar: se quiser logs/auditoria, crie uma tabela audit_log e registre login/logout, updates e deletes.