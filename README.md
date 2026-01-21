# Starter Web

Starter Web is a lightweight PHP starter for small sites, with optional auth, admin settings, and a Bootstrap-based UI.

## Features
- Simple router with controller + view structure.
- Auth system with registration, login, and password reset.
- Admin panel for user management and app settings.
- Branding uploads (logo + generated favicons).
- User profiles with avatar upload.
- Language switcher (EN/PT) and a light/dark theme toggle.

## Requirements
- PHP 7.4 or higher with the following extensions:
  - PDO (MySQL or SQLite)
  - `intl` - for internationalization support
  - `gd` - for image processing (avatar and logo uploads)
  - `mbstring` - for multibyte string handling
- A web server (Apache, Nginx, or PHP's built-in server)
- Composer (for dependency management)
- MySQL 5.7+ or SQLite 3.x
- Write permissions for the `app/storage` directory and its subdirectories

## Installation

### Step 1: Get the Code
Clone or download this repository to your local machine or server:
```bash
git clone <repository-url> starter-web
cd starter-web
```

Or download and extract the ZIP file.

### Step 2: Install Dependencies
Install the required PHP packages using Composer:
```bash
composer install
```

This will install:
- `vlucas/phpdotenv` - for environment variable management
- `phpmailer/phpmailer` - for email functionality

If you don't have Composer installed, download it from [getcomposer.org](https://getcomposer.org/).

### Step 3: Set Directory Permissions
Ensure the storage directories are writable by your web server:
```bash
chmod -R 775 app/storage
chmod -R 775 app/storage/avatars
chmod -R 775 app/storage/branding
chmod -R 775 app/storage/favicon
chmod -R 775 app/storage/logo
```

For SQLite users, also ensure the parent directory is writable:
```bash
chmod 775 app/storage
```

### Step 4: Configure Your Web Server

#### Option A: Using PHP's Built-in Server (Development Only)
For local development, you can use PHP's built-in server:
```bash
php -S localhost:8000 -t public
```

Then open your browser to `http://localhost:8000`

#### Option B: Apache Configuration
Point your virtual host's document root to the `public` directory.

Create a virtual host configuration (e.g., `/etc/apache2/sites-available/starter-web.conf`):
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

Enable the site and restart Apache:
```bash
sudo a2ensite starter-web
sudo systemctl restart apache2
```

Make sure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
```

#### Option C: Nginx Configuration
Add this to your Nginx server block:
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

Restart Nginx:
```bash
sudo systemctl restart nginx
```

### Step 5: Create a Minimal Environment File
Before running the installer, create a basic `.env` file to prevent bootstrap errors:
```bash
cp env_example.text .env
```

The installer will overwrite this file with your actual configuration, but this prevents the "Unable to read environment file" error when first accessing the application.

### Step 6: Run the Web Installer
1. Open your browser and navigate to `http://your-domain.com/install.php` (or `http://localhost:8000/install.php` if using the built-in server).

2. Fill in the installation form with the following information:

   **Application Settings:**
   - Application Name: Your site name
   - Application URL: Full URL (e.g., `http://localhost:8000` or `https://yoursite.com`)
   - Application At: Footer text (e.g., "My Company")
   - Year Start: The year your project started

   **Database Settings:**

   For MySQL:
   - Database Type: `mysql`
   - Database Host: `127.0.0.1` (or your MySQL server address)
   - Database Port: `3306` (default)
   - Database Name: Your database name (will be created if it doesn't exist)
   - Database User: Your MySQL username
   - Database Password: Your MySQL password
   - Database Charset: `utf8mb4` (recommended)

   For SQLite:
   - Database Type: `sqlite`
   - Database Name: `app/storage/database.sqlite` (relative path from project root)
   - Leave other database fields empty

   **Email Settings (for password reset functionality):**
   - SMTP Host: Your mail server (e.g., `smtp.gmail.com`)
   - SMTP Port: Usually `587` for TLS or `465` for SSL
   - SMTP Username: Your email address
   - SMTP Password: Your email password or app-specific password
   - From Email: Email address shown as sender

3. Click "Install" to:
   - Create the database (if using MySQL and it doesn't exist)
   - Run the `schema.sql` to create tables (`users` and `settings`)
   - Generate a `.env` file with your configuration
   - Create a secure random `APP_SECRET_KEY`

4. After successful installation, you'll be redirected to the login page.

### Step 7: Remove the Installer
**Important:** For security, delete or rename the installer after setup:
```bash
rm public/install.php
# or
mv public/install.php public/install.php.bak
```

### Step 8: Access the Application
Visit your application URL. You can now register a new account or use the default admin account (see below).

## Manual Installation (Alternative to Web Installer)

If you prefer to set up the environment manually without using the web installer:

### 1. Create the Environment File
Copy the example environment file:
```bash
cp env_example.text .env
```

### 2. Edit the .env File
Open `.env` in your text editor and fill in all the values:
```env
APP_NAME=My Starter Web
APP_URL=http://localhost:8000
APP_AT=My Company
YEAR_START=2025

# For MySQL
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS=your_password
DB_NAME=starter_web
DB_TYPE=mysql
DB_CHAR=utf8mb4
DB_PORT=3306

# For SQLite
# DB_NAME=app/storage/database.sqlite
# DB_TYPE=sqlite

# Generate a random secret key (32+ characters)
APP_SECRET_KEY=your_random_secret_key_here_min_32_chars

# Email configuration
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_PORT=587
MAIL_FROM=noreply@yoursite.com
```

**Generating a Secret Key:**
You can generate a secure random key using:
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

### 3. Create the Database
For MySQL, create the database manually:
```bash
mysql -u root -p
```
```sql
CREATE DATABASE starter_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

For SQLite, create an empty database file:
```bash
touch app/storage/database.sqlite
chmod 664 app/storage/database.sqlite
```

### 4. Import the Database Schema
For MySQL:
```bash
mysql -u root -p starter_web < schema.sql
```

For SQLite:
```bash
sqlite3 app/storage/database.sqlite < schema.sql
```

### 5. Create Default Admin User (Optional)
If you want to create the default admin account manually:
```sql
INSERT INTO users (name, email, password, role, active)
VALUES ('Admin', 'admin@admin.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);
```
This creates an admin account with:
- Email: `admin@admin.net`
- Password: `admin`

### 6. Verify Installation
Visit your application URL and try to log in. If everything is configured correctly, you should see the login page.

## Default Admin Account
After installation, a default admin account is pre-configured:
- **Email:** admin@admin.net
- **Password:** admin

To set up your own admin account:
1. Register a new user account.
2. Log in using the default admin account (admin@admin.net / admin).
3. Go to the admin panel and edit your newly created user account to promote them to admin.
4. Delete the admin@admin.net account from the admin panel.

## Database notes
- MySQL: use a user that can create databases, or pre-create the database and reuse the name.
- SQLite: use a file path like `app/storage/database.sqlite` (the installer creates the file).

## Environment
- The installer writes a `.env` file at the project root.
- To set it up manually, copy `env_example.text` to `.env` and fill in the values.
- **Important:** Never commit `.env` to version control. It's already in `.gitignore`.

## Verifying Your PHP Installation

Before installing, verify that all required PHP extensions are enabled:

```bash
php -m | grep -E 'pdo|intl|gd|mbstring'
```

You should see all four extensions listed. If any are missing:

**On Ubuntu/Debian:**
```bash
sudo apt-get install php-pdo php-mysql php-intl php-gd php-mbstring
sudo systemctl restart apache2  # or php-fpm
```

**On macOS (Homebrew):**
```bash
brew install php
# Most extensions come pre-installed with Homebrew PHP
```

**On Windows (XAMPP/WAMP):**
Edit `php.ini` and uncomment these lines:
```ini
extension=pdo_mysql
extension=intl
extension=gd
extension=mbstring
```

Check your PHP version:
```bash
php -v  # Should be 7.4 or higher
```

## Making a website
- Update the main pages in `app/views/main/home.view.php`, `app/views/main/about.view.php`, and `app/views/main/contact.view.php`.
- Adjust navigation links in `app/views/layouts/navbar.view.php`.
- Edit layout structure in `app/views/layouts/header.view.php` and `app/views/layouts/footer.view.php`.
- Replace the brand logo by uploading a new logo in the admin settings.
- Add translations in `app/lang/en.php` and `app/lang/pt.php`.

## Adding new pages
1. Create a view file under `app/views` (for simple pages, use `app/views/main`).
2. Add a controller action that calls `view()`.
3. Register a route in `app/routes.php`.
4. (Optional) Add a navbar link and translation strings.

Example: add a public `/services` page.
1. Create `app/views/main/services.view.php`.
2. Add an action to `app/controllers/MainController.php`:
```
public function services(): void
{
    view('main/services');
}
```
3. Register the route in `app/routes.php`:
```
$router->get('/services', [MainController::class, 'services'])->name('services');
```
4. Add a nav link in `app/views/layouts/navbar.view.php` and strings in `app/lang/en.php` and `app/lang/pt.php`.

For pages that require login, place the route inside the `auth` group in `app/routes.php`.

## Security
- Keep `.env` out of version control.
- Delete `public/install.php` after setup.

## Troubleshooting

### Unable to Read Environment File Error
If you see this error:
```
Fatal error: Uncaught Dotenv\Exception\InvalidPathException: Unable to read any of the environment file(s) at [/path/to/.env]
```

**Solution:** The `.env` file doesn't exist. Create it before accessing the application:
```bash
cp env_example.text .env
```

Then access the installer at `/install.php` to configure it properly, or manually edit the `.env` file with your settings.

### Routes Return 404 Errors
If routes like `/about` or `/login` return 404 errors:

**Apache:** Ensure `mod_rewrite` is enabled and `.htaccess` is working:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Check that your virtual host allows `.htaccess` with `AllowOverride All`.

**Nginx:** Verify your `try_files` directive is correct:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**Built-in PHP Server:** This should work automatically, no configuration needed.

### Installation Page Fails to Create Database

**MySQL Issues:**
- Verify database credentials in the installer form
- Ensure the MySQL user has `CREATE DATABASE` privileges
- Try creating the database manually first:
  ```bash
  mysql -u root -p -e "CREATE DATABASE starter_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  ```
- Check MySQL is running: `sudo systemctl status mysql`

**SQLite Issues:**
- Ensure the path is writable (e.g., `app/storage/database.sqlite`)
- The parent directory must be writable by the web server
- Check permissions: `chmod 775 app/storage`

### Logo/Avatar Upload Errors
- Verify the `gd` extension is enabled: `php -m | grep gd`
- Check WebP support: `php -r "echo (function_exists('imagewebp') ? 'WebP supported' : 'WebP not supported') . PHP_EOL;"`
- Ensure storage directories are writable:
  ```bash
  chmod -R 775 app/storage/avatars
  chmod -R 775 app/storage/logo
  chmod -R 775 app/storage/branding
  ```
- Check upload size limits in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

### Permission Denied Errors
If you see "Permission denied" when saving files:
```bash
# Set proper ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data app/storage

# Set proper permissions
chmod -R 775 app/storage
```

To find your web server user:
```bash
# Apache
ps aux | grep apache2 | head -1

# Nginx
ps aux | grep nginx | head -1
```

### Blank Page or 500 Internal Server Error
- Enable error display in development. Add to `public/index.php` (temporarily):
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Check PHP error logs:
  ```bash
  # Ubuntu/Debian
  tail -f /var/log/apache2/error.log

  # macOS (Homebrew)
  tail -f /usr/local/var/log/php-fpm.log

  # Or check PHP's error log location
  php -i | grep error_log
  ```
- Verify all required extensions are enabled: `php -m`
- Check `.env` file exists and has correct values

### Email Not Sending (Password Reset)
- Verify SMTP settings in `.env`
- For Gmail, use an [App Password](https://support.google.com/accounts/answer/185833)
- Check firewall allows outbound connections on port 587 or 465
- Test with a simpler SMTP service like Mailtrap for development
- Check PHP error logs for specific PHPMailer errors

### Database Connection Fails
- Verify `.env` database credentials
- For MySQL:
  ```bash
  mysql -h 127.0.0.1 -u your_user -p your_database
  ```
- For SQLite, ensure the file exists and is readable:
  ```bash
  ls -la app/storage/database.sqlite
  ```
- Check `DB_TYPE` matches your database (`mysql` or `sqlite`)

### Composer Install Fails
- Update Composer: `composer self-update`
- Clear cache: `composer clear-cache`
- Try with verbose output: `composer install -vvv`
- Check you have write permissions in the project directory

### Session Issues / Can't Stay Logged In
- Check session directory is writable
- Verify `APP_SECRET_KEY` is set in `.env`
- For production, ensure cookies work over HTTPS (set in session config)
- Clear browser cookies and try again
