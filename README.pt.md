# Starter Web

Starter Web é um iniciador PHP leve para pequenos sites, com autenticação opcional, painel de administração e interface baseada em Bootstrap.

## Recursos
- Roteador simples com estrutura controller + view.
- Sistema de autenticação com registro, login e redefinição de senha.
- Painel de administração para gerenciamento de usuários e configurações de aplicativo.
- Upload de marcas (logo + ícones de favorito gerados automaticamente).
- Perfis de usuário com upload de avatar.
- Alternador de idioma (EN/PT) e alternância de tema claro/escuro.
- Criação de tabelas de banco de dados personalizadas diretamente do painel de administração.
- Desativação de conta do usuário com reativação do administrador.

## Requisitos
- PHP 7.4 ou superior com as seguintes extensões:
  - PDO (MySQL ou SQLite)
  - `intl` - para suporte de internacionalização
  - `gd` - para processamento de imagens (uploads de avatar e logo)
  - `mbstring` - para manipulação de strings multibyte
- Um servidor web (Apache, Nginx ou servidor embutido do PHP)
- Composer (para gerenciamento de dependências)
- MySQL 5.7+ ou SQLite 3.x
- Permissões de escrita para o diretório `app/storage` e seus subdiretórios

## Instalação

### Passo 1: Obter o Código
Clone ou baixe este repositório em sua máquina local ou servidor:
```bash
git clone <repository-url> starter-web
cd starter-web
```

Ou baixe e extraia o arquivo ZIP.

### Passo 2: Instalar Dependências
Instale os pacotes PHP necessários usando o Composer:
```bash
composer install
```

Isso instalará:
- `vlucas/phpdotenv` - para gerenciamento de variáveis de ambiente
- `phpmailer/phpmailer` - para funcionalidade de email

Se você não tem o Composer instalado, baixe em [getcomposer.org](https://getcomposer.org/).

### Passo 3: Definir Permissões de Diretório
Certifique-se de que os diretórios de armazenamento são graváveis pelo seu servidor web:
```bash
chmod -R 775 app/storage
chmod -R 775 app/storage/avatars
chmod -R 775 app/storage/branding
chmod -R 775 app/storage/favicon
chmod -R 775 app/storage/logo
```

### Passo 4: Configurar Seu Servidor Web

#### Opção A: Usando o Servidor Embutido do PHP (Apenas Desenvolvimento)
Para desenvolvimento local, você pode usar o servidor embutido do PHP:
```bash
php -S localhost:8000 -t public
```

Então abra seu navegador em `http://localhost:8000`

#### Opção B: Configuração do Apache
Aponte a raiz do documento do seu host virtual para o diretório `public`.

Crie uma configuração de host virtual (por exemplo, `/etc/apache2/sites-available/starter-web.conf`):
```apache
<VirtualHost *:80>
    ServerName starter-web.local
    DocumentRoot /path/to/starter-web/public

    <Directory /path/to/starter-web/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/starter-web-error.log
    CustomLog ${APACHE_LOG_DIR}/starter-web-access.log combined
</VirtualHost>
```

Habilite o site e reinicie o Apache:
```bash
sudo a2ensite starter-web
sudo systemctl restart apache2
```

Certifique-se de que `mod_rewrite` está habilitado:
```bash
sudo a2enmod rewrite
```

#### Opção C: Configuração do Nginx
Adicione isto ao seu bloco de servidor Nginx:
```nginx
server {
    listen 80;
    server_name starter-web.local;
    root /path/to/starter-web/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Reinicie o Nginx:
```bash
sudo systemctl restart nginx
```

### Passo 5: Criar um Arquivo de Ambiente Mínimo
Antes de executar o instalador, crie um arquivo `.env` básico para evitar erros de inicialização:
```bash
cp env_example.text .env
```

O instalador sobrescreverá este arquivo com sua configuração real, mas isso evita o erro "Unable to read environment file" ao acessar a aplicação pela primeira vez.

### Passo 6: Executar o Instalador Web
1. Abra seu navegador e navegue até `http://seu-dominio.com/install.php` (ou `http://localhost:8000/install.php` se usar o servidor embutido).

2. Preencha o formulário de instalação com as seguintes informações:

   **Configurações de Aplicativo:**
   - Nome da Aplicação: O nome do seu site
   - URL da Aplicação: URL completa (por exemplo, `http://localhost:8000` ou `https://seusite.com`)
   - Aplicação Em: Texto do rodapé (por exemplo, "Minha Empresa")
   - Ano de Início: O ano em que seu projeto começou

   **Configurações de Banco de Dados:**

   Para MySQL:
   - Tipo de Banco de Dados: `mysql`
   - Host do Banco de Dados: `127.0.0.1` (ou endereço do seu servidor MySQL)
   - Porta do Banco de Dados: `3306` (padrão)
   - Nome do Banco de Dados: Seu nome de banco de dados (será criado se não existir)
   - Usuário do Banco de Dados: Seu nome de usuário MySQL
   - Senha do Banco de Dados: Sua senha MySQL
   - Charset do Banco de Dados: `utf8mb4` (recomendado)

   Para SQLite:
   - Tipo de Banco de Dados: `sqlite`
   - Nome do Banco de Dados: `app/storage/database.sqlite` (caminho relativo da raiz do projeto)
   - Deixe os outros campos de banco de dados em branco

   **Configurações de Email (para funcionalidade de redefinição de senha):**
   - Host SMTP: Seu servidor de correio (por exemplo, `smtp.gmail.com`)
   - Porta SMTP: Geralmente `587` para TLS ou `465` para SSL
   - Usuário SMTP: Seu endereço de email
   - Senha SMTP: Sua senha de email ou senha específica do aplicativo
   - Email De: Endereço de email mostrado como remetente

3. Clique em "Instalar" para:
   - Criar o banco de dados (se usar MySQL e ele não existir)
   - Executar `schema.sql` para criar tabelas (`users` e `settings`)
   - Gerar um arquivo `.env` com sua configuração
   - Criar uma chave segura aleatória `APP_SECRET_KEY`

4. Após a instalação bem-sucedida, você será redirecionado para a página de login.

### Passo 7: Remover o Instalador
**Importante:** Por segurança, delete ou renomeie o instalador após a configuração:
```bash
rm public/install.php
# ou
mv public/install.php public/install.php.bak
```

### Passo 8: Acessar a Aplicação
Visite a URL da sua aplicação. Agora você pode registrar uma nova conta ou usar a conta de administrador padrão (veja abaixo).

## Instalação Manual (Alternativa ao Instalador Web)

Se você preferir configurar o ambiente manualmente sem usar o instalador web:

### 1. Criar o Arquivo de Ambiente
Copie o arquivo de ambiente de exemplo:
```bash
cp env_example.text .env
```

### 2. Editar o Arquivo .env
Abra `.env` em seu editor de texto e preencha todos os valores:
```env
APP_NAME=Meu Starter Web
APP_URL=http://localhost:8000
APP_AT=Minha Empresa
YEAR_START=2025

# Para MySQL
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS=sua_senha
DB_NAME=starter_web
DB_TYPE=mysql
DB_CHAR=utf8mb4
DB_PORT=3306

# Para SQLite
# DB_NAME=app/storage/database.sqlite
# DB_TYPE=sqlite

# Gere uma chave secreta aleatória (32+ caracteres)
APP_SECRET_KEY=sua_chave_secreta_aleatoria_aqui_min_32_chars

# Configuração de email
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua_senha_de_aplicativo
MAIL_PORT=587
MAIL_FROM=noreply@seusite.com
```

**Gerando uma Chave Secreta:**
Você pode gerar uma chave aleatória segura usando:
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

### 3. Criar o Banco de Dados
Para MySQL, crie o banco de dados manualmente:
```bash
mysql -u root -p
```
```sql
CREATE DATABASE starter_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Para SQLite, crie um arquivo de banco de dados vazio:
```bash
touch app/storage/database.sqlite
chmod 664 app/storage/database.sqlite
```

### 4. Importar o Esquema do Banco de Dados
Para MySQL:
```bash
mysql -u root -p starter_web < schema.sql
```

Para SQLite:
```bash
sqlite3 app/storage/database.sqlite < schema.sql
```

### 5. Criar Usuário Administrador Padrão (Opcional)
Se você deseja criar a conta de administrador padrão manualmente:
```sql
INSERT INTO users (name, email, password, role, active)
VALUES ('Admin', 'admin@admin.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);
```
Isso cria uma conta de administrador com:
- Email: `admin@admin.net`
- Senha: `admin`

### 6. Verificar Instalação
Visite a URL da sua aplicação e tente fazer login. Se tudo estiver configurado corretamente, você deverá ver a página de login.

## Conta de Administrador Padrão
Após a instalação, uma conta de administrador padrão é pré-configurada:
- **Email:** admin@admin.net
- **Senha:** admin

Para configurar sua própria conta de administrador:
1. Registre uma nova conta de usuário.
2. Faça login usando a conta de administrador padrão (admin@admin.net / admin).
3. Vá para o painel de administração e edite sua conta de usuário recém-criada para promovê-la a administrador.
4. Delete a conta admin@admin.net do painel de administração.

## Notas do Banco de Dados
- MySQL: use um usuário que possa criar bancos de dados, ou crie previamente o banco de dados e reutilize o nome.
- SQLite: use um caminho de arquivo como `app/storage/database.sqlite` (o instalador cria o arquivo).

## Ambiente
- O instalador escreve um arquivo `.env` na raiz do projeto.
- Para configurar manualmente, copie `env_example.text` para `.env` e preencha os valores.
- **Importante:** Nunca faça commit de `.env` para controle de versão. Ele já está em `.gitignore`.

## Verificando Sua Instalação do PHP

Antes de instalar, verifique se todas as extensões PHP necessárias estão habilitadas:

```bash
php -m | grep -E 'pdo|intl|gd|mbstring'
```

Você deve ver todas as quatro extensões listadas. Se alguma estiver faltando:

**No Ubuntu/Debian:**
```bash
sudo apt-get install php-pdo php-mysql php-intl php-gd php-mbstring
sudo systemctl restart apache2  # ou php-fpm
```

**No macOS (Homebrew):**
```bash
brew install php
# A maioria das extensões vem pré-instalada com o PHP Homebrew
```

**No Windows (XAMPP/WAMP):**
Edite `php.ini` e descomente estas linhas:
```ini
extension=pdo_mysql
extension=intl
extension=gd
extension=mbstring
```

Verifique sua versão do PHP:
```bash
php -v  # Deve ser 7.4 ou superior
```

## Criando um Site
- Atualize as páginas principais em `app/views/main/home.view.php`, `app/views/main/about.view.php` e `app/views/main/contact.view.php`.
- Ajuste os links de navegação em `app/views/layouts/navbar.view.php`.
- Edite a estrutura de layout em `app/views/layouts/header.view.php` e `app/views/layouts/footer.view.php`.
- Substitua o logo de marca fazendo upload de um novo logo nas configurações de administração.
- Adicione traduções em `app/lang/en.php` e `app/lang/pt.php`.

## Recursos do Painel de Administração

### Gerenciamento de Usuários
- **Usuários Ativos**: Visualize e edite todas as contas de usuário ativas
- **Usuários Inativos**: Visualize e gerencie contas desativadas
- **Desativação de Usuário**: Ao deletar um usuário, escolha entre:
  - **Marcar como Inativo**: A conta do usuário permanece no banco de dados, mas não pode fazer login
  - **Deletar Permanentemente**: Remova completamente o usuário do banco de dados (irreversível)
- **Reativação Automática**: Usuários promovidos para função de administrador são automaticamente reativados

### Gerenciamento de Banco de Dados
- **Criar Tabelas Personalizadas**: Construa tabelas de banco de dados diretamente no painel de administração sem conhecimento de SQL
  - Suporta 10+ tipos de coluna (VARCHAR, TEXT, INT, DECIMAL, BOOLEAN, DATETIME, etc.)
  - Gerenciamento dinâmico de colunas (adicionar/remover colunas)
  - Define colunas nulas e valores padrão
  - Criação automática de chave primária com auto-increment
  - Validação evita nomes de tabela duplicados e inválidos

### Gerenciamento de Configurações
- **Configurações de App**: Configure cores de ícone e ative/desative recursos
- **Marca**: Upload de logo da empresa (auto-otimizado para WebP com ícones de favorito gerados)
- **Autenticação**: Alterne o sistema de login/registro do usuário ativado ou desativado

## Adicionando Novas Páginas
1. Crie um arquivo de visualização em `app/views` (para páginas simples, use `app/views/main`).
2. Adicione uma ação de controlador que chame `view()`.
3. Registre uma rota em `app/routes.php`.
4. (Opcional) Adicione um link de navbar e strings de tradução.

Exemplo: adicionar uma página pública `/services`.
1. Crie `app/views/main/services.view.php`.
2. Adicione uma ação a `app/controllers/MainController.php`:
```
public function services(): void
{
    view('main/services');
}
```
3. Registre a rota em `app/routes.php`:
```
$router->get('/services', [MainController::class, 'services'])->name('services');
```
4. Adicione um link de nav em `app/views/layouts/navbar.view.php` e strings em `app/lang/en.php` e `app/lang/pt.php`.

Para páginas que requerem login, coloque a rota dentro do grupo `auth` em `app/routes.php`.

## Gerenciamento de Conta de Usuário

### Desativando Usuários
Os administradores podem desativar contas de usuários do painel de administração sem deletá-las:

1. Vá para **Administração** → **Usuários Ativos**
2. Clique no botão editar próximo ao usuário
3. Clique no botão **Deletar este usuário**
4. Escolha sua ação:
   - **Marcar como Inativo**: O usuário permanece no banco de dados, mas não pode fazer login (pode ser reativado)
   - **Deletar Permanentemente**: O usuário é completamente removido do banco de dados (não pode ser desfeito)

### Comportamento de Login de Usuário Inativo
Quando um usuário inativo tenta fazer login, ele recebe a mensagem:
> "Sua conta foi desativada. Entre em contato com um administrador para reativar sua conta."

### Reativando Usuários
Para reativar um usuário inativo:

1. Vá para **Administração** → **Usuários Inativos**
2. Clique no botão editar próximo ao usuário
3. Marque a caixa de seleção **Reativar usuário**
4. Clique em **Atualizar**

Usuários promovidos para função de administrador são automaticamente reativados.

## Criando Tabelas Personalizadas de Banco de Dados

Os usuários podem criar tabelas de banco de dados personalizadas diretamente no painel de administração:

1. Vá para **Administração** → **Tabelas de Banco de Dados**
2. Clique em **Criar Tabela Personalizada**
3. Digite um nome de tabela (apenas letras minúsculas, números e sublinhados)
4. Adicione colunas com as seguintes opções:
   - **Nome da Coluna**: Obrigatório, validação de nomenclatura
   - **Tipo**: Escolha entre 10+ tipos de dados (VARCHAR, INT, DECIMAL, TEXT, DATETIME, etc.)
   - **Anulável**: Opcional - desmarque para tornar a coluna obrigatória
   - **Valor Padrão**: Opcional - defina um valor padrão para a coluna
5. Clique em **Criar Tabela**

O sistema automaticamente:
- Cria uma chave primária com auto-increment
- Valida todos os nomes e tipos
- Evita criação de tabela duplicada
- Mostra mensagens de erro se a criação falhar

## Segurança
- Mantenha `.env` fora do controle de versão.
- Delete `public/install.php` após a configuração.

## Solução de Problemas

### Erro de Impossibilidade de Ler Arquivo de Ambiente
Se você vir este erro:
```
Fatal error: Uncaught Dotenv\Exception\InvalidPathException: Unable to read any of the environment file(s) at [/path/to/.env]
```

**Solução:** O arquivo `.env` não existe. Crie-o antes de acessar a aplicação:
```bash
cp env_example.text .env
```

Em seguida, acesse o instalador em `/install.php` para configurá-lo adequadamente, ou edite manualmente o arquivo `.env` com suas configurações.

### Rotas Retornam Erros 404
Se rotas como `/about` ou `/login` retornam erros 404:

**Apache:** Certifique-se de que `mod_rewrite` está habilitado e `.htaccess` está funcionando:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Verifique que seu host virtual permite `.htaccess` com `AllowOverride All`.

**Nginx:** Verifique se sua diretiva `try_files` está correta:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**Servidor PHP Embutido:** Isso deve funcionar automaticamente, nenhuma configuração necessária.

### Falha na Página de Instalação ao Criar Banco de Dados

**Problemas com MySQL:**
- Verifique as credenciais do banco de dados no formulário do instalador
- Certifique-se de que o usuário MySQL tem privilégios `CREATE DATABASE`
- Tente criar o banco de dados manualmente primeiro:
  ```bash
  mysql -u root -p -e "CREATE DATABASE starter_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  ```
- Verifique se MySQL está em execução: `sudo systemctl status mysql`

**Problemas com SQLite:**
- Certifique-se de que o caminho é gravável (por exemplo, `app/storage/database.sqlite`)
- O diretório pai deve ser gravável pelo servidor web
- Verifique permissões: `chmod 775 app/storage`

### Erros de Upload de Logo/Avatar
- Verifique se a extensão `gd` está habilitada: `php -m | grep gd`
- Verifique o suporte para WebP: `php -r "echo (function_exists('imagewebp') ? 'WebP suportado' : 'WebP não suportado') . PHP_EOL;"`
- Certifique-se de que os diretórios de armazenamento são graváveis:
  ```bash
  chmod -R 775 app/storage/avatars
  chmod -R 775 app/storage/logo
  chmod -R 775 app/storage/branding
  ```
- Verifique os limites de tamanho de upload em `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

### Erros de Permissão Negada
Se você vir "Permission denied" ao salvar arquivos:
```bash
# Defina a propriedade apropriada (substitua www-data pelo seu usuário de servidor web)
sudo chown -R www-data:www-data app/storage

# Defina as permissões apropriadas
chmod -R 775 app/storage
```

Para encontrar seu usuário de servidor web:
```bash
# Apache
ps aux | grep apache2 | head -1

# Nginx
ps aux | grep nginx | head -1
```

### Página em Branco ou Erro Interno do Servidor 500
- Habilite a exibição de erros em desenvolvimento. Adicione a `public/index.php` (temporariamente):
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Verifique os logs de erro do PHP:
  ```bash
  # Ubuntu/Debian
  tail -f /var/log/apache2/error.log

  # macOS (Homebrew)
  tail -f /usr/local/var/log/php-fpm.log

  # Ou verifique a localização do log de erros do PHP
  php -i | grep error_log
  ```
- Verifique se todas as extensões necessárias estão habilitadas: `php -m`
- Verifique que o arquivo `.env` existe e tem valores corretos

### Email Não Está Sendo Enviado (Redefinição de Senha)
- Verifique as configurações de SMTP em `.env`
- Para Gmail, use uma [Senha de Aplicativo](https://support.google.com/accounts/answer/185833)
- Verifique se o firewall permite conexões de saída na porta 587 ou 465
- Teste com um serviço SMTP mais simples como Mailtrap para desenvolvimento
- Verifique os logs de erro do PHP para erros específicos do PHPMailer

### Falha na Conexão do Banco de Dados
- Verifique as credenciais do banco de dados em `.env`
- Para MySQL:
  ```bash
  mysql -h 127.0.0.1 -u seu_usuario -p seu_banco_de_dados
  ```
- Para SQLite, certifique-se de que o arquivo existe e é legível:
  ```bash
  ls -la app/storage/database.sqlite
  ```
- Verifique se `DB_TYPE` corresponde ao seu banco de dados (`mysql` ou `sqlite`)

### Falha na Instalação do Composer
- Atualize o Composer: `composer self-update`
- Limpe cache: `composer clear-cache`
- Tente com saída detalhada: `composer install -vvv`
- Verifique se você tem permissões de escrita no diretório do projeto

### Problemas de Sessão / Não Conseguir Manter-se Logado
- Verifique se o diretório de sessão é gravável
- Verifique se `APP_SECRET_KEY` está definido em `.env`
- Para produção, certifique-se de que os cookies funcionam sobre HTTPS (definido na configuração de sessão)
- Limpe os cookies do navegador e tente novamente

### Usuários Inativos Ainda Podem Fazer Login
Se um usuário inativo ainda conseguir fazer login:
- Verifique se a coluna `active` existe na tabela `users`
- Verifique se o campo `active` do usuário está definido como `0` no banco de dados:
  ```sql
  SELECT id, email, active FROM users WHERE email = 'usuario@exemplo.com';
  ```
- Se a coluna não existir, o sistema de autenticação permitirá que todos os usuários façam login, independentemente do status

### Falha na Criação de Tabela Personalizada
Se você vir o erro "Failed to create table":
- **Nome de tabela inválido**: Use apenas letras minúsculas, números e sublinhados. Nomes de tabela não podem começar com um número.
- **Nome de coluna inválido**: As mesmas regras de nomenclatura se aplicam aos nomes de colunas
- **Tabela já existe**: O nome da tabela já é usado no banco de dados
- **Permissões de banco de dados**: Certifique-se de que o usuário do banco de dados tem privilégios `CREATE TABLE`
- **Sintaxe do tipo de coluna**: Alguns tipos de coluna podem precisar de ajuste dependendo do seu banco de dados (por exemplo, `DECIMAL(10,2)` para MySQL)

### Não Consegue Acessar Painel de Administração
- Verifique se você está logado com uma conta de administrador (`role = 'admin'`)
- Verifique se o sistema de autenticação está habilitado nas configurações de administração
- Certifique-se de que o campo `active` do usuário está definido como `1` no banco de dados
