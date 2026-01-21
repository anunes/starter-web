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
- PHP with PDO (MySQL or SQLite) and the `intl` and `gd` extensions enabled.
- A web server pointing its document root at `public`.
- Composer (optional if `vendor/` is already present).

## Quick setup
1. Point your web server document root to `public`.
2. (Optional) Install dependencies: `composer install`.
3. Visit `/install.php` in the browser.
4. Fill in the app, database, and mail settings.
5. Submit the form to create the database, tables (from `schema.sql`), and the `.env` file.
6. Remove or rename `public/install.php` after install.

You can also run locally with PHP's built-in server:
```
php -S localhost:8000 -t public
```

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
- `404` on routes like `/about`: ensure your web server routes all requests to `public/index.php` (the built-in PHP server works out of the box).
- Install page fails to create the database: double-check `.env` values and DB credentials; for SQLite, use a writable file path.
- Logo/avatar upload errors: enable the `gd` extension with WebP support.
- `Permission denied` when saving files: make sure `app/storage` and its subfolders are writable by the web server.
- Unexpected blank page or errors: check PHP error logs and confirm required extensions (`pdo`, `intl`, `gd`) are enabled.
