<?php

$appIconsColor = $settings->app_icons_color ?? '#0d6efd';
$appIconsColor = trim($appIconsColor);
$pickerColor = '#0d6efd';
if (preg_match('/^#([A-Fa-f0-9]{6})$/', $appIconsColor)) {
    $pickerColor = $appIconsColor;
}
