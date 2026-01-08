<?php

$hasLogo = !empty($logoSrc) && strpos($logoSrc, '/logo/') === 0;
$logoButtonLabel = $hasLogo ? t('admin.change_logo') : t('admin.add_logo');
