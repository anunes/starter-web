<?php

$avatarFile = $user->avatar ?? null;
$hasAvatar = !empty($avatarFile);
$avatarSrc = $hasAvatar ? '/avatars/' . rawurlencode($avatarFile) : '';
$avatarButtonLabel = $hasAvatar ? t('profile.change_avatar') : t('profile.add_avatar');
