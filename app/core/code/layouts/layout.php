<?php

use app\core\Model;
use app\core\Session;

// Variables from header.php
$settingsModel = new Model();
$settings = $settingsModel->settings();

$appIconsColor = $settings->app_icons_color ?? '#0d6efd';
$appIconsColor = trim($appIconsColor);
if (
    $appIconsColor === '' ||
    !preg_match('/^[#a-zA-Z0-9\\s(),.%]+$/', $appIconsColor) ||
    strlen($appIconsColor) > 32 ||
    (strpos($appIconsColor, '#') === 0 && !preg_match('/^#([A-Fa-f0-9]{3,4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $appIconsColor))
) {
    $appIconsColor = '#0d6efd';
}

$faviconVersion = 0;
$faviconPath = APP_ROOT . '/storage/favicon/favicon.ico';
if (is_file($faviconPath)) {
    $faviconVersion = (int) filemtime($faviconPath);
}

ob_start();
Session::flash();
$flashContent = ob_get_clean();
$flashHtml = $flashContent !== '' ? "<div class='mt-3 mb-5'>{$flashContent}</div>" : '';

// Variables from footer.php
$currentLang = lang();
