<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);
$envPath = $rootPath . '/.env';
$schemaPath = $rootPath . '/schema.sql';
$envExists = file_exists($envPath);
$errors = [];
$success = false;
$dbMessage = '';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function is_absolute_path(string $path): bool
{
    if ($path === '') {
        return false;
    }
    if ($path[0] === '/' || $path[0] === '\\') {
        return true;
    }
    return preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
}

function normalize_path(string $path, string $rootPath): string
{
    if ($path === '') {
        return $path;
    }
    if (is_absolute_path($path)) {
        return $path;
    }
    return rtrim($rootPath, '/') . '/' . ltrim($path, '/');
}

function format_env_value(string $value): string
{
    if ($value === '') {
        return '';
    }
    $needsQuotes = preg_match('/[\s#"=]/', $value) === 1;
    if (!$needsQuotes) {
        return $value;
    }
    $escaped = str_replace(["\\", '"'], ["\\\\", '\\"'], $value);
    return '"' . $escaped . '"';
}

function split_sql_statements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $inSingle = false;
    $inDouble = false;
    $inBacktick = false;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $next = $i + 1 < $length ? $sql[$i + 1] : '';

        if (!$inSingle && !$inDouble && !$inBacktick) {
            if ($char === '-' && $next === '-') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                continue;
            }
            if ($char === '#') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                continue;
            }
            if ($char === '/' && $next === '*') {
                $i += 2;
                while ($i < $length - 1 && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                    $i++;
                }
                $i++;
                continue;
            }
        }

        if ($char === "'" && !$inDouble && !$inBacktick) {
            $inSingle = !$inSingle;
        } elseif ($char === '"' && !$inSingle && !$inBacktick) {
            $inDouble = !$inDouble;
        } elseif ($char === '`' && !$inSingle && !$inDouble) {
            $inBacktick = !$inBacktick;
        }

        if ($char === ';' && !$inSingle && !$inDouble && !$inBacktick) {
            $trimmed = trim($buffer);
            if ($trimmed !== '') {
                $statements[] = $trimmed;
            }
            $buffer = '';
            continue;
        }

        $buffer .= $char;
    }

    $trimmed = trim($buffer);
    if ($trimmed !== '') {
        $statements[] = $trimmed;
    }

    return $statements;
}

function normalize_schema_statement_for_sqlite(string $statement): string
{
    $statement = preg_replace('/\\s+CHARACTER SET\\s+\\w+/i', '', $statement);
    $statement = preg_replace('/\\s+COLLATE\\s+\\w+/i', '', $statement);
    $statement = preg_replace('/\\bunsigned\\b/i', '', $statement);
    $statement = preg_replace('/\\btinyint\\(1\\)\\b/i', 'INTEGER', $statement);
    $statement = preg_replace('/\\bbigint\\b/i', 'INTEGER', $statement);
    $statement = preg_replace('/\\bint\\b/i', 'INTEGER', $statement);
    $statement = preg_replace('/`id`\\s+INTEGER\\s+NOT\\s+NULL\\s+AUTO_INCREMENT/i', '`id` INTEGER PRIMARY KEY AUTOINCREMENT', $statement);
    $statement = preg_replace('/`id`\\s+INTEGER\\s+AUTO_INCREMENT/i', '`id` INTEGER PRIMARY KEY AUTOINCREMENT', $statement);
    $statement = preg_replace('/\\s+AUTO_INCREMENT\\b/i', '', $statement);
    $statement = preg_replace('/\\s*PRIMARY KEY\\s*\\(`id`\\)\\s*,?/i', '', $statement);
    $statement = preg_replace('/UNIQUE KEY\\s+`[^`]+`\\s*\\(([^)]+)\\)/i', 'UNIQUE ($1)', $statement);
    $statement = preg_replace('/\\s+ON UPDATE CURRENT_TIMESTAMP/i', '', $statement);
    $statement = preg_replace('/\\)\\s*ENGINE\\s*=.*$/i', ')', $statement);
    $statement = preg_replace('/,\\s*,/', ',', $statement);
    $statement = preg_replace('/,\\s*\\)/', ')', $statement);

    return $statement;
}

function load_schema_statements(string $schemaPath, string $dbType): array
{
    if (!is_file($schemaPath)) {
        throw new RuntimeException('schema.sql not found.');
    }
    $schema = trim((string) file_get_contents($schemaPath));
    if ($schema === '') {
        throw new RuntimeException('schema.sql is empty.');
    }
    $statements = split_sql_statements($schema);
    if (!$statements) {
        throw new RuntimeException('schema.sql did not contain any statements.');
    }
    if ($dbType === 'sqlite') {
        foreach ($statements as $index => $statement) {
            $statements[$index] = normalize_schema_statement_for_sqlite($statement);
        }
    }
    return $statements;
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$defaultUrl = $scheme . '://' . $host;

$defaults = [
    'app_name' => 'Starter Web',
    'app_url' => $defaultUrl,
    'app_at' => '@yourdomain',
    'year_start' => date('Y'),
    'db_type' => 'mysql',
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'starter-web',
    'db_user' => 'root',
    'db_pass' => '',
    'db_char' => 'utf8',
    'sqlite_path' => 'app/storage/database.sqlite',
    'mail_host' => '',
    'mail_username' => '',
    'mail_password' => '',
    'mail_port' => '587',
    'mail_from' => '',
];

$values = $defaults;
$overwriteEnv = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $values = [
        'app_name' => trim((string)($_POST['app_name'] ?? '')),
        'app_url' => trim((string)($_POST['app_url'] ?? '')),
        'app_at' => trim((string)($_POST['app_at'] ?? '')),
        'year_start' => trim((string)($_POST['year_start'] ?? '')),
        'db_type' => trim((string)($_POST['db_type'] ?? 'mysql')),
        'db_host' => trim((string)($_POST['db_host'] ?? '')),
        'db_port' => trim((string)($_POST['db_port'] ?? '')),
        'db_name' => trim((string)($_POST['db_name'] ?? '')),
        'db_user' => trim((string)($_POST['db_user'] ?? '')),
        'db_pass' => (string)($_POST['db_pass'] ?? ''),
        'db_char' => trim((string)($_POST['db_char'] ?? '')),
        'sqlite_path' => trim((string)($_POST['sqlite_path'] ?? '')),
        'mail_host' => trim((string)($_POST['mail_host'] ?? '')),
        'mail_username' => trim((string)($_POST['mail_username'] ?? '')),
        'mail_password' => (string)($_POST['mail_password'] ?? ''),
        'mail_port' => trim((string)($_POST['mail_port'] ?? '')),
        'mail_from' => trim((string)($_POST['mail_from'] ?? '')),
    ];

    $values['db_type'] = strtolower($values['db_type']);
    $overwriteEnv = isset($_POST['overwrite_env']);

    if ($values['app_name'] === '') {
        $errors[] = 'App name is required.';
    }
    if ($values['app_url'] === '') {
        $errors[] = 'App URL is required.';
    }
    if ($values['app_at'] === '') {
        $errors[] = 'APP_AT is required.';
    }
    if ($values['year_start'] === '' || !ctype_digit($values['year_start'])) {
        $errors[] = 'YEAR_START must be a year number.';
    }

    if (!in_array($values['db_type'], ['mysql', 'sqlite'], true)) {
        $errors[] = 'Database type must be mysql or sqlite.';
    }

    if ($values['db_type'] === 'mysql') {
        if ($values['db_host'] === '') {
            $errors[] = 'DB host is required for MySQL.';
        }
        if ($values['db_name'] === '') {
            $errors[] = 'DB name is required for MySQL.';
        }
        if ($values['db_user'] === '') {
            $errors[] = 'DB user is required for MySQL.';
        }
        if ($values['db_char'] === '') {
            $errors[] = 'DB charset is required for MySQL.';
        }
        if ($values['db_port'] !== '' && !ctype_digit($values['db_port'])) {
            $errors[] = 'DB port must be numeric.';
        }
        if ($values['db_name'] !== '' && preg_match('/^[A-Za-z0-9_-]+$/', $values['db_name']) !== 1) {
            $errors[] = 'DB name can only include letters, numbers, underscores, and dashes.';
        }
    }

    if ($values['db_type'] === 'sqlite') {
        if ($values['sqlite_path'] === '') {
            $errors[] = 'SQLite path is required.';
        }
    }

    if ($values['mail_host'] === '') {
        $errors[] = 'Mail host is required.';
    }
    if ($values['mail_username'] === '') {
        $errors[] = 'Mail username is required.';
    }
    if ($values['mail_password'] === '') {
        $errors[] = 'Mail password is required.';
    }
    if ($values['mail_port'] === '' || !ctype_digit($values['mail_port'])) {
        $errors[] = 'Mail port must be numeric.';
    }
    if ($values['mail_from'] === '') {
        $errors[] = 'Mail from is required.';
    }

    if ($envExists && !$overwriteEnv) {
        $errors[] = 'An .env file already exists. Check overwrite to replace it.';
    }

    if (!$errors) {
        try {
            if ($values['db_type'] === 'mysql') {
                $portPart = $values['db_port'] !== '' ? 'port=' . $values['db_port'] . ';' : '';
                $dsn = 'mysql:host=' . $values['db_host'] . ';' . $portPart . 'charset=' . $values['db_char'];
                $pdo = new PDO($dsn, $values['db_user'], $values['db_pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $dbName = $values['db_name'];
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$values['db_char']}");
                $pdo->exec("USE `{$dbName}`");
                $dbMessage = 'MySQL database and tables are ready.';
            }

            if ($values['db_type'] === 'sqlite') {
                $sqlitePath = normalize_path($values['sqlite_path'], $rootPath);
                $directory = dirname($sqlitePath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $pdo = new PDO('sqlite:' . $sqlitePath);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $values['db_name'] = $sqlitePath;
                $values['db_host'] = '';
                $values['db_user'] = '';
                $values['db_pass'] = '';
                $values['db_port'] = '';
                $values['db_char'] = 'utf8';
                $dbMessage = 'SQLite database and tables are ready.';
            }

            $schemaStatements = load_schema_statements($schemaPath, $values['db_type']);
            foreach ($schemaStatements as $statement) {
                $pdo->exec($statement);
            }

            $adminEmail = 'admin@admin.net';
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $checkStmt->execute([$adminEmail]);
            if ((int) $checkStmt->fetchColumn() === 0) {
                $hash = password_hash('admin', PASSWORD_DEFAULT);
                $insertStmt = $pdo->prepare('INSERT INTO users (name, email, password, role, active) VALUES (?, ?, ?, ?, ?)');
                $insertStmt->execute(['admin', $adminEmail, $hash, 'admin', 1]);
                $dbMessage = trim($dbMessage . ' Default admin user created.');
            } else {
                $dbMessage = trim($dbMessage . ' Admin user already exists.');
            }
        } catch (Throwable $error) {
            $errors[] = 'Database error: ' . $error->getMessage();
        }
    }

    if (!$errors) {
        try {
            $secretKey = bin2hex(random_bytes(32));
            $envLines = [
                'APP_NAME=' . format_env_value($values['app_name']),
                'APP_URL=' . format_env_value($values['app_url']),
                'APP_AT=' . format_env_value($values['app_at']),
                'YEAR_START=' . format_env_value($values['year_start']),
                '',
                'DB_HOST=' . format_env_value($values['db_host']),
                'DB_USER=' . format_env_value($values['db_user']),
                'DB_PASS=' . format_env_value($values['db_pass']),
                'DB_NAME=' . format_env_value($values['db_name']),
                'DB_TYPE=' . format_env_value($values['db_type']),
                'DB_CHAR=' . format_env_value($values['db_char']),
                'DB_PORT=' . format_env_value($values['db_port']),
                '',
                'APP_SECRET_KEY=' . format_env_value($secretKey),
                '',
                'MAIL_HOST=' . format_env_value($values['mail_host']),
                'MAIL_USERNAME=' . format_env_value($values['mail_username']),
                'MAIL_PASSWORD=' . format_env_value($values['mail_password']),
                'MAIL_PORT=' . format_env_value($values['mail_port']),
                'MAIL_FROM=' . format_env_value($values['mail_from']),
                '',
            ];

            $envContent = implode(PHP_EOL, $envLines) . PHP_EOL;
            file_put_contents($envPath, $envContent);
            $success = true;
        } catch (Throwable $error) {
            $errors[] = 'Could not write .env: ' . $error->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Starter Web Installer</title>
    <style>
        :root {
            color-scheme: light;
        }

        body {
            margin: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            background: #f5f6f8;
            color: #1f2933;
        }

        .container {
            max-width: 880px;
            margin: 40px auto;
            background: #fff;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(18, 38, 63, 0.08);
        }

        h1 {
            margin-top: 0;
            font-size: 28px;
        }

        h2 {
            margin-top: 28px;
            font-size: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d5d7db;
            border-radius: 8px;
            font-size: 14px;
        }

        .help {
            font-size: 12px;
            color: #5b6b7c;
            margin-top: 6px;
        }

        .alert {
            background: #fff4e5;
            border: 1px solid #f0c36d;
            color: #5b3b00;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
        }

        .success {
            background: #e8f7ef;
            border: 1px solid #7ad4a0;
            color: #1f4f32;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        button {
            background: #1c3faa;
            color: #fff;
            border: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>
    <main class="container">
        <h1>Starter Web Installer</h1>
        <p>Fill in the settings to generate your .env file and prepare the database.</p>

        <?php if ($success): ?>
            <div class="alert success">
                <strong>Install complete.</strong> <?= e($dbMessage) ?> The .env file is ready.
            </div>
            <p>Next steps:</p>
            <ul>
                <li>Remove or rename <strong>public/install.php</strong> for safety.</li>
                <li>Import your database schema and seed data.</li>
                <li>Visit the site homepage to continue.</li>
            </ul>
        <?php else: ?>
            <?php if ($errors): ?>
                <div class="alert">
                    <strong>Please fix the following:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <h2>App Settings</h2>
                <div class="grid">
                    <div>
                        <label for="app_name">App name</label>
                        <input id="app_name" name="app_name" type="text" value="<?= e($values['app_name']) ?>" required>
                    </div>
                    <div>
                        <label for="app_url">App URL</label>
                        <input id="app_url" name="app_url" type="text" value="<?= e($values['app_url']) ?>" required>
                    </div>
                    <div>
                        <label for="app_at">APP_AT</label>
                        <input id="app_at" name="app_at" type="text" value="<?= e($values['app_at']) ?>" required>
                        <div class="help">Shown in the footer.</div>
                    </div>
                    <div>
                        <label for="year_start">Year start</label>
                        <input id="year_start" name="year_start" type="text" value="<?= e($values['year_start']) ?>" required>
                    </div>
                </div>

                <h2>Database Settings</h2>
                <div class="grid">
                    <div>
                        <label for="db_type">Database type</label>
                        <select id="db_type" name="db_type">
                            <option value="mysql" <?= $values['db_type'] === 'mysql' ? 'selected' : '' ?>>MySQL</option>
                            <option value="sqlite" <?= $values['db_type'] === 'sqlite' ? 'selected' : '' ?>>SQLite</option>
                        </select>
                    </div>
                    <div>
                        <label for="db_name">DB name</label>
                        <input id="db_name" name="db_name" type="text" value="<?= e($values['db_name']) ?>">
                        <div class="help">For SQLite this is replaced by the file path below.</div>
                    </div>
                    <div>
                        <label for="db_host">DB host</label>
                        <input id="db_host" name="db_host" type="text" value="<?= e($values['db_host']) ?>">
                    </div>
                    <div>
                        <label for="db_port">DB port</label>
                        <input id="db_port" name="db_port" type="text" value="<?= e($values['db_port']) ?>">
                    </div>
                    <div>
                        <label for="db_user">DB user</label>
                        <input id="db_user" name="db_user" type="text" value="<?= e($values['db_user']) ?>">
                    </div>
                    <div>
                        <label for="db_pass">DB password</label>
                        <input id="db_pass" name="db_pass" type="password" value="<?= e($values['db_pass']) ?>">
                    </div>
                    <div>
                        <label for="db_char">DB charset</label>
                        <input id="db_char" name="db_char" type="text" value="<?= e($values['db_char']) ?>">
                    </div>
                    <div>
                        <label for="sqlite_path">SQLite file path</label>
                        <input id="sqlite_path" name="sqlite_path" type="text" value="<?= e($values['sqlite_path']) ?>">
                        <div class="help">Use a path like app/storage/database.sqlite</div>
                    </div>
                </div>

                <h2>Mail Settings</h2>
                <div class="grid">
                    <div>
                        <label for="mail_host">Mail host</label>
                        <input id="mail_host" name="mail_host" type="text" value="<?= e($values['mail_host']) ?>">
                    </div>
                    <div>
                        <label for="mail_username">Mail username</label>
                        <input id="mail_username" name="mail_username" type="text" value="<?= e($values['mail_username']) ?>">
                    </div>
                    <div>
                        <label for="mail_password">Mail password</label>
                        <input id="mail_password" name="mail_password" type="password" value="<?= e($values['mail_password']) ?>">
                    </div>
                    <div>
                        <label for="mail_port">Mail port</label>
                        <input id="mail_port" name="mail_port" type="text" value="<?= e($values['mail_port']) ?>">
                    </div>
                    <div>
                        <label for="mail_from">Mail from</label>
                        <input id="mail_from" name="mail_from" type="text" value="<?= e($values['mail_from']) ?>">
                    </div>
                </div>

                <?php if ($envExists): ?>
                    <p class="help">An existing .env was found. Check overwrite to replace it.</p>
                <?php endif; ?>
                <div class="actions">
                    <button type="submit">Create .env and database</button>
                    <label class="checkbox">
                        <input type="checkbox" name="overwrite_env" <?= $overwriteEnv ? 'checked' : '' ?>>
                        Overwrite existing .env
                    </label>
                </div>
            </form>
        <?php endif; ?>
    </main>
</body>

</html>
