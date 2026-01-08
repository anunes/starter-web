<?php

function view($view, $data = []): void
{
    $GLOBALS['_view_data'] = $data;
    echo view_partial('layouts/header', $data);
    echo view_partial($view, $data);
    echo view_partial('layouts/footer', $data);
    unset($GLOBALS['_view_data']);
}

function redirect($path): void
{
    header("Location: {$path}");
    exit();
}

function goback(): void
{
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}

function sanitize($data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

// function dd($value): void
// {
//     echo '
//   <pre>';
//     var_dump($value);
//     echo "</pre>";
//     die();
// }

function date_pt($dateTime): bool|string
{
    if ($dateTime === null) {
        return false;
    }

    $dateTimeObj = new DateTime($dateTime, new DateTimeZone("Atlantic/Azores"));
    return IntlDateFormatter::formatObject($dateTimeObj, "dd-MMMM-y", "pt");
}

function isEmail($email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function match_pass($password, $repass): bool
{
    if (!$password == $repass) {
        return false;
    }
    return true;
}

/* function validate_password($password): bool
{
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 6) {
        return false;
    }
    return true;
} */

function csrf(): void
{
    echo app\core\Session::csrf();
}

function view_partial(string $partial, array $data = []): string
{
    $shared = $GLOBALS['_view_data'] ?? [];
    $payload = array_merge($shared, $data);

    ob_start();
    extract($payload);

    $codePath = APP_ROOT . '/core/code/' . $partial . '.php';
    if (is_file($codePath)) {
        require $codePath;
    }

    $viewPath = APP_ROOT . '/views/' . $partial . '.view.php';
    if (is_file($viewPath)) {
        require $viewPath;
    }

    return ob_get_clean();
}

function logo_placeholder_src(?string $label = null, int $size = 96): string
{
    $label = $label ?? APP_NAME;
    $label = preg_replace('/[^A-Za-z0-9\\s]/', '', (string) $label);
    $label = trim($label);
    if ($label === '') {
        $label = 'Logo';
    }

    $parts = preg_split('/\\s+/', $label);
    if (count($parts) >= 2) {
        $label = substr($parts[0], 0, 1) . substr($parts[1], 0, 1);
    } else {
        $label = substr($label, 0, 4);
    }

    $label = strtoupper($label);
    $fontSize = max(12, (int) round($size * 0.32));
    $radius = max(6, (int) round($size * 0.18));
    $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" role="img" aria-label="Logo placeholder">'
        . '<rect width="100%" height="100%" rx="' . $radius . '" fill="#e9ecef"/>'
        . '<text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="' . $fontSize . '" font-weight="600" fill="#6c757d">'
        . $safeLabel
        . '</text>'
        . '</svg>';

    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
}

function auth_enabled(): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    try {
        $model = new \app\core\Model();
    } catch (\Throwable $error) {
        $cached = true;
        return $cached;
    }

    $column = null;
    foreach (['auth_toggle'] as $candidate) {
        if ($model->hasColumn('settings', $candidate)) {
            $column = $candidate;
            break;
        }
    }

    if (!$column) {
        $cached = true;
        return $cached;
    }

    $settings = $model->settings();
    if (!$settings || !isset($settings->{$column})) {
        $cached = true;
        return $cached;
    }

    $value = $settings->{$column};
    if (is_bool($value)) {
        $cached = $value;
        return $cached;
    }

    $normalized = strtolower(trim((string) $value));
    $cached = !in_array($normalized, ['0', 'false', 'off', 'no'], true);
    return $cached;
}

function registration_enabled(): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    if (!auth_enabled()) {
        $cached = false;
        return $cached;
    }

    try {
        $model = new \app\core\Model();
    } catch (\Throwable $error) {
        $cached = true;
        return $cached;
    }

    $column = null;
    foreach (['registration_toggle', 'register_toggle', 'registration_enabled', 'register_enabled'] as $candidate) {
        if ($model->hasColumn('settings', $candidate)) {
            $column = $candidate;
            break;
        }
    }

    if (!$column) {
        $cached = true;
        return $cached;
    }

    $settings = $model->settings();
    if (!$settings || !isset($settings->{$column})) {
        $cached = true;
        return $cached;
    }

    $value = $settings->{$column};
    if (is_bool($value)) {
        $cached = $value;
        return $cached;
    }

    $normalized = strtolower(trim((string) $value));
    $cached = !in_array($normalized, ['0', 'false', 'off', 'no'], true);
    return $cached;
}

function available_languages(): array
{
    return ['en', 'pt'];
}

function normalize_lang(string $locale): string
{
    $locale = strtolower(substr(trim($locale), 0, 2));
    if (!in_array($locale, available_languages(), true)) {
        return 'en';
    }
    return $locale;
}

function set_lang(string $locale): void
{
    $locale = normalize_lang($locale);
    $_SESSION['lang'] = $locale;
    setcookie('lang', $locale, time() + 60 * 60 * 24 * 365, '/');
}

function lang(): string
{
    static $current = null;
    if ($current !== null) {
        return $current;
    }
    $value = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';
    $current = normalize_lang($value);
    return $current;
}

function translations(): array
{
    static $cache = [];
    $locale = lang();
    if (!isset($cache[$locale])) {
        $path = APP_ROOT . '/lang/' . $locale . '.php';
        $cache[$locale] = is_file($path) ? require $path : [];
    }
    return $cache[$locale];
}

function t(string $key, array $replacements = []): string
{
    $value = translations();
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            $value = null;
            break;
        }
        $value = $value[$segment];
    }

    if (!is_string($value)) {
        $value = $key;
    }

    foreach ($replacements as $placeholder => $replacement) {
        $value = str_replace(':' . $placeholder, $replacement, $value);
    }

    return $value;
}
