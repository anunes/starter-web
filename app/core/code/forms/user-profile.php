<?php

$avatarFile = $user->avatar ?? null;
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

// Set button label based on whether user has an avatar
$avatarButtonLabel = $hasAvatar ? t('profile.change_avatar') : t('profile.add_avatar');
