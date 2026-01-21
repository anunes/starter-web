<?php

use app\core\Session;
use app\core\Model;

// Variables for navbar
$isLoggedIn = Session::loggedIn();
$showGuestLinks = !$isLoggedIn && auth_enabled();
$showUserMenu = $isLoggedIn;

// Brand logo
$brandLogoSrc = logo_placeholder_src();
$logoDir = APP_ROOT . '/storage/logo';
if (is_dir($logoDir)) {
    $logoFiles = glob($logoDir . '/logo_*.webp');
    if (!empty($logoFiles)) {
        $brandLogoPath = $logoFiles[0];
        $brandLogoSrc = '/logo/' . basename($brandLogoPath) . '?v=' . filemtime($brandLogoPath);
    }
}

// Registration link (only relevant when showing guest links)
$showRegistrationLink = registration_enabled();

// User menu variables (only relevant when user is logged in)
$userName = '';
$hasAvatar = false;
$avatarSrc = '';
$isAdmin = false;

if ($isLoggedIn) {
    $userId = $_SESSION['id'] ?? null;
    $userName = $_SESSION['name'] ?? '';
    $avatarFile = $_SESSION['avatar'] ?? null;
    $isAdmin = Session::isAdmin();

    // Check for user avatar from session
    if (!empty($avatarFile)) {
        $avatarPath = APP_ROOT . '/storage/avatars/' . basename($avatarFile);
        if (is_file($avatarPath)) {
            $hasAvatar = true;
            $cacheVersion = filemtime($avatarPath) . '-' . time();
            $avatarSrc = '/avatars/' . rawurlencode($avatarFile) . '?v=' . $cacheVersion;
        }
    }
}
