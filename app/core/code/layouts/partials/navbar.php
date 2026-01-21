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
    $isAdmin = Session::isAdmin();

    // Check for user avatar
    if ($userId) {
        $avatarDir = APP_ROOT . '/storage/avatars';
        $avatarFiles = glob($avatarDir . '/' . $userId . '_*.webp');
        if (!empty($avatarFiles)) {
            $avatarPath = $avatarFiles[0];
            $hasAvatar = true;
            $avatarSrc = '/avatars/' . basename($avatarPath) . '?v=' . filemtime($avatarPath);
        }
    }
}
