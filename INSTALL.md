# Starter Web Installation

## Quick setup
1. Point your web server document root to `public`.
2. Visit `/install.php` in the browser.
3. Fill in the app, database, and mail settings.
4. Submit the form to create the database, tables (from `schema.sql`), and the `.env` file.
5. Remove or rename `public/install.php` after install.

## Database notes
- MySQL: use a user that can create databases, or pre-create the database and reuse the name.
- SQLite: use a file path like `app/storage/database.sqlite` (the installer creates the file).

## Making a website
- Update the main pages in `app/views/main/home.view.php`, `app/views/main/about.view.php`, and `app/views/main/contact.view.php`.
- Adjust navigation links in `app/views/layouts/navbar.view.php`.
- Edit layout structure in `app/views/layouts/header.view.php` and `app/views/layouts/footer.view.php`.
- Replace the brand logo by uploading a new logo in the admin settings.
- Add translations in `app/lang/en.php` and `app/lang/pt.php`.

## Security
- Keep `.env` out of version control.
- Delete `public/install.php` after setup.
