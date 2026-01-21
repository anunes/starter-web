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
// Use placeholder if logo file doesn't exist in storage
if ($logoFile) {
    $logoPath = APP_ROOT . '/storage/logo/' . basename($logoFile);
    $brandLogoSrc = is_file($logoPath) ? '/logo/' . rawurlencode($logoFile) : logo_placeholder_src();
} else {
    $brandLogoSrc = logo_placeholder_src();
}

$authEnabled = auth_enabled();
$registrationEnabled = registration_enabled();
$loggedIn = Session::loggedIn();
$showGuestLinks = $authEnabled && !$loggedIn;
$showRegistrationLink = $showGuestLinks && $registrationEnabled;
$showUserMenu = $authEnabled && $loggedIn;
$userName = $_SESSION['name'] ?? '';
$avatarFile = $_SESSION['avatar'] ?? null;
$hasAvatar = false;
$avatarSrc = '';

// Add cache-busting query parameter for avatar
if (!empty($avatarFile)) {
    $avatarPath = APP_ROOT . '/storage/avatars/' . basename($avatarFile);
    if (is_file($avatarPath)) {
        $hasAvatar = true;
        $cacheVersion = filemtime($avatarPath);
        $avatarSrc = '/avatars/' . rawurlencode($avatarFile) . '?v=' . $cacheVersion;
    }
}
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
