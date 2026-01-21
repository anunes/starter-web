<?php

use app\core\Model;
use app\core\Session;

$settingsModel = new Model();
$settings = $settingsModel->settings();
$logoColumn = null;
foreach (['company_logo', 'app_logo', 'logo'] as $candidate) {
    if ($settingsModel->hasColumn('settings', $candidate)) {
        $logoColumn = $candidate;
        break;
    }
}
$logoFile = ($settings && $logoColumn) ? ($settings->{$logoColumn} ?? null) : null;
$brandLogoSrc = $logoFile ? '/logo/' . rawurlencode($logoFile) : logo_placeholder_src();

$authEnabled = auth_enabled();
$registrationEnabled = registration_enabled();
$loggedIn = Session::loggedIn();
$showGuestLinks = $authEnabled && !$loggedIn;
$showRegistrationLink = $showGuestLinks && $registrationEnabled;
$showUserMenu = $authEnabled && $loggedIn;
$userName = $_SESSION['name'] ?? '';
$avatarFile = $_SESSION['avatar'] ?? null;
$hasAvatar = !empty($avatarFile);

// Add cache-busting query parameter for avatar
if ($hasAvatar) {
    $avatarPath = APP_ROOT . '/storage/avatars/' . basename($avatarFile);
    $cacheVersion = is_file($avatarPath) ? filemtime($avatarPath) : time();
    $avatarSrc = '/avatars/' . rawurlencode($avatarFile) . '?v=' . $cacheVersion;
} else {
    $avatarSrc = '';
}
