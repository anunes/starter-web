<?php

namespace app\controllers;

use app\core\Model;
use app\core\Session as SE;

final class AdminController
{
    public function index(): void
    {
        $admin = new Model();
        $activeUsers = $admin->activeUsers();
        $inactiveUsers = $admin->inactiveUsers();
        $settings = $admin->settings();
        $logoColumn = $this->resolveLogoColumn($admin);
        $logoFile = ($settings && $logoColumn) ? ($settings->{$logoColumn} ?? null) : null;
        $logoSrc = $logoFile ? '/logo/' . rawurlencode($logoFile) : logo_placeholder_src();
        $authColumn = $this->resolveAuthToggleColumn($admin);
        $authEnabled = true;
        if ($authColumn && $settings && isset($settings->{$authColumn})) {
            $authValue = strtolower(trim((string) $settings->{$authColumn}));
            $authEnabled = !in_array($authValue, ['0', 'false', 'off', 'no'], true);
        }
        $registrationColumn = $this->resolveRegistrationToggleColumn($admin);
        $registrationEnabled = $authEnabled;
        if ($registrationColumn && $settings && isset($settings->{$registrationColumn})) {
            $registrationValue = strtolower(trim((string) $settings->{$registrationColumn}));
            $registrationEnabled = !in_array($registrationValue, ['0', 'false', 'off', 'no'], true);
        }
        view('admin/admin', [
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'settings' => $settings,
            'logoSrc' => $logoSrc,
            'logoAvailable' => $logoColumn !== null,
            'authEnabled' => $authEnabled,
            'authAvailable' => $authColumn !== null,
            'registrationEnabled' => $registrationEnabled,
            'registrationAvailable' => $registrationColumn !== null
        ]);
    }

    public function edit($id): void
    {
        $admin = new Model();
        $user = $admin->find('users', $id);
        view('admin/edit', ['user' => $user]);
    }

    public function update($id): void
    {
        $update = new Model();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!SE::checkCsrf()) {
                SE::setflash(t('flash.invalid_csrf'), 'danger');
            }
            $data = [
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
                'role' => sanitize($_POST['role'])
            ];
            if ($update->hasColumn('users', 'active')) {
                // If user is being promoted to admin, automatically reactivate them
                if ($data['role'] === 'admin') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = isset($_POST['active']) ? 1 : 0;
                }
            }
            $update->update('users', $data, $id);
            SE::setflash(t('flash.user_updated'), 'success');
            redirect('/admin');
        }
    }

    public function softdelete($id): void
    {
        if (!SE::checkCsrf()) {
            SE::setflash(t('flash.invalid_csrf'), 'danger');
        }

        $delete = new Model();

        // Check if we should permanently delete or mark as inactive
        $isPermanent = isset($_GET['permanent']) && $_GET['permanent'] === '1';

        if ($isPermanent) {
            // Permanently delete from database
            $delete->delete('users', $id);
            SE::setflash(t('flash.user_deleted_permanent'), 'success');
        } else {
            // Mark as inactive (soft delete)
            if ($delete->hasColumn('users', 'active')) {
                $delete->softDelete('users', $id);
                SE::setflash(t('flash.user_marked_inactive'), 'success');
            } else {
                SE::setflash(t('flash.active_unavailable'), 'warning');
            }
        }
        redirect('/admin');
    }

    public function updateSettings(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!SE::checkCsrf()) {
                SE::setflash(t('flash.invalid_csrf'), 'danger');
            }

            $model = new Model();
            $updates = [];

            if (isset($_POST['auth_enabled_present'])) {
                $authColumn = $this->resolveAuthToggleColumn($model);
                if (!$authColumn) {
                    SE::setflash(t('flash.auth_toggle_missing'), 'danger');
                    redirect('/admin');
                }
                $updates[$authColumn] = isset($_POST['auth_enabled']) ? 1 : 0;
            }

            if (isset($_POST['registration_enabled_present'])) {
                $registrationColumn = $this->resolveRegistrationToggleColumn($model);
                if (!$registrationColumn) {
                    SE::setflash(t('flash.registration_toggle_missing'), 'danger');
                    redirect('/admin');
                }
                $updates[$registrationColumn] = isset($_POST['registration_enabled']) ? 1 : 0;
            }

            $color = trim(sanitize($_POST['app_icons_color'] ?? ''));
            if ($color === '') {
                $color = trim(sanitize($_POST['app_icons_color_picker'] ?? ''));
            }
            if ($color !== '') {
                if (
                    !preg_match('/^[#a-zA-Z0-9\\s(),.%]+$/', $color) ||
                    strlen($color) > 32 ||
                    (strpos($color, '#') === 0 && !preg_match('/^#([A-Fa-f0-9]{3,4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $color))
                ) {
                    SE::setflash(t('flash.icons_color_invalid'), 'danger');
                    redirect('/admin');
                }

                if (!$model->hasColumn('settings', 'app_icons_color')) {
                    SE::setflash(t('flash.icons_color_missing'), 'danger');
                    redirect('/admin');
                }

                $updates['app_icons_color'] = $color;
            }

            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $logoColumn = $this->resolveLogoColumn($model);
                if (!$logoColumn) {
                    SE::setflash(t('flash.logo_field_missing'), 'danger');
                    redirect('/admin');
                }

                $settings = $model->settings();
                $currentLogo = $settings && isset($settings->{$logoColumn}) ? $settings->{$logoColumn} : null;
                $result = $this->handleLogoUpload($_FILES['company_logo'], $currentLogo);
                if (isset($result['error'])) {
                    SE::setflash(t($result['error']), 'danger');
                    redirect('/admin');
                }

                $updates[$logoColumn] = $result['filename'];
                $this->generateFavicons($result['source_path']);
            }

            if (!empty($updates)) {
                $model->updateSettings($updates);
                SE::setflash(t('flash.settings_updated'), 'success');
            } else {
                SE::setflash(t('flash.settings_not_updated'), 'info');
            }
            redirect('/admin');
        }
    }

    private function resolveLogoColumn(Model $model): ?string
    {
        foreach (['company_logo', 'app_logo', 'logo'] as $candidate) {
            if ($model->hasColumn('settings', $candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    private function resolveAuthToggleColumn(Model $model): ?string
    {
        foreach (['auth_toggle'] as $candidate) {
            if ($model->hasColumn('settings', $candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    private function resolveRegistrationToggleColumn(Model $model): ?string
    {
        foreach (['registration_toggle', 'register_toggle', 'registration_enabled', 'register_enabled'] as $candidate) {
            if ($model->hasColumn('settings', $candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    private function handleLogoUpload(array $upload, ?string $currentLogo): array
    {
        if ($upload['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'flash.logo_upload_failed'];
        }

        if ($upload['size'] > 5 * 1024 * 1024) {
            return ['error' => 'flash.logo_too_large'];
        }

        if (!is_uploaded_file($upload['tmp_name'])) {
            return ['error' => 'flash.logo_upload_invalid'];
        }

        $imageInfo = getimagesize($upload['tmp_name']);
        if ($imageInfo === false) {
            return ['error' => 'flash.logo_not_image'];
        }

        if (!function_exists('imagewebp')) {
            return ['error' => 'flash.webp_not_supported'];
        }

        $mime = $imageInfo['mime'];
        $source = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($upload['tmp_name']),
            'image/png' => imagecreatefrompng($upload['tmp_name']),
            'image/gif' => imagecreatefromgif($upload['tmp_name']),
            'image/webp' => imagecreatefromwebp($upload['tmp_name']),
            default => null,
        };

        if (!$source) {
            return ['error' => 'flash.logo_unsupported'];
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $maxSize = 512;
        $scale = min($maxSize / $width, $maxSize / $height, 1);
        $newWidth = (int) round($width * $scale);
        $newHeight = (int) round($height * $scale);

        $output = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
        imagefilledrectangle($output, 0, 0, $newWidth, $newHeight, $transparent);
        imagecopyresampled($output, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $storageDir = APP_ROOT . '/storage/logo';
        if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true) && !is_dir($storageDir)) {
            imagedestroy($source);
            imagedestroy($output);
            return ['error' => 'flash.logo_dir_failed'];
        }

        $filename = 'logo_' . bin2hex(random_bytes(8)) . '.webp';
        $path = $storageDir . '/' . $filename;
        $saved = imagewebp($output, $path, 82);

        imagedestroy($source);
        imagedestroy($output);

        if (!$saved) {
            return ['error' => 'flash.logo_save_failed'];
        }

        if (!empty($currentLogo)) {
            $oldPath = $storageDir . '/' . basename($currentLogo);
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        return ['filename' => $filename, 'source_path' => $path];
    }

    private function generateFavicons(string $sourcePath): void
    {
        if (!is_file($sourcePath)) {
            return;
        }

        $source = imagecreatefromwebp($sourcePath);
        if (!$source) {
            return;
        }

        $sizes = [
            16 => 'favicon-16x16.png',
            32 => 'favicon-32x32.png',
            180 => 'apple-touch-icon.png',
            192 => 'android-chrome-192x192.png',
            512 => 'android-chrome-512x512.png',
        ];

        $faviconDir = APP_ROOT . '/storage/favicon';
        if (!is_dir($faviconDir) && !mkdir($faviconDir, 0755, true) && !is_dir($faviconDir)) {
            imagedestroy($source);
            return;
        }

        foreach (['favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png', 'android-chrome-192x192.png', 'android-chrome-512x512.png', 'site.webmanifest'] as $file) {
            $path = $faviconDir . '/' . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }

        $legacyDir = APP_ROOT . '/../public/assets/img/favicon';
        foreach (['favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png', 'android-chrome-192x192.png', 'android-chrome-512x512.png', 'site.webmanifest'] as $file) {
            $path = $legacyDir . '/' . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }

        $icoSources = [];
        foreach ($sizes as $size => $filename) {
            $png = $this->renderSquarePng($source, $size);
            if ($png === null) {
                continue;
            }
            file_put_contents($faviconDir . '/' . $filename, $png);
            if (in_array($size, [16, 32], true)) {
                $icoSources[] = ['size' => $size, 'data' => $png];
            }
        }

        $icoPath = $faviconDir . '/favicon.ico';
        $this->writeIco($icoSources, $icoPath);

        $manifest = [
            'name' => APP_NAME,
            'short_name' => APP_NAME,
            'icons' => [
                [
                    'src' => '/favicon/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/favicon/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
            'theme_color' => '#ffffff',
            'background_color' => '#ffffff',
            'display' => 'standalone',
        ];
        file_put_contents($faviconDir . '/site.webmanifest', json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        imagedestroy($source);
    }

    private function renderSquarePng($source, int $size): ?string
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min($size / $width, $size / $height);
        $newWidth = (int) round($width * $scale);
        $newHeight = (int) round($height * $scale);

        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $transparent);

        $dstX = (int) floor(($size - $newWidth) / 2);
        $dstY = (int) floor(($size - $newHeight) / 2);
        imagecopyresampled($canvas, $source, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagepng($canvas);
        $pngData = ob_get_clean();

        imagedestroy($canvas);

        return $pngData === false ? null : $pngData;
    }

    private function writeIco(array $icons, string $path): void
    {
        if (empty($icons)) {
            return;
        }

        $count = count($icons);
        $header = pack('vvv', 0, 1, $count);
        $entries = '';
        $data = '';
        $offset = 6 + ($count * 16);

        foreach ($icons as $icon) {
            $size = $icon['size'];
            $pngData = $icon['data'];
            $width = $size === 256 ? 0 : $size;
            $height = $size === 256 ? 0 : $size;
            $bytes = strlen($pngData);

            $entries .= pack('CCCCvvVV', $width, $height, 0, 0, 1, 32, $bytes, $offset);
            $data .= $pngData;
            $offset += $bytes;
        }

        file_put_contents($path, $header . $entries . $data);
    }

    public function createTable(): void
    {
        view('admin/create-table');
    }

    public function storeTable(): void
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('/admin');
        }

        if (!SE::checkCsrf()) {
            SE::setflash(t('flash.invalid_csrf'), 'danger');
            redirect('/admin/table/create');
        }

        $tableName = sanitize($_POST['table_name'] ?? '');
        $columns = $_POST['columns'] ?? [];

        // Validate table name
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $tableName)) {
            SE::setflash(t('admin.invalid_table_name'), 'danger');
            redirect('/admin/table/create');
        }

        // Validate columns
        if (empty($columns)) {
            SE::setflash(t('admin.table_columns') . ' ' . t('flash.fill_all_fields'), 'danger');
            redirect('/admin/table/create');
        }

        $model = new Model();

        // Check if table already exists
        try {
            $tables = $model->getDb()->rows("SHOW TABLES");
            foreach ($tables as $table) {
                $tableKey = array_key_first((array)$table);
                if ($table->{$tableKey} === $tableName) {
                    SE::setflash(t('admin.table_already_exists'), 'warning');
                    redirect('/admin/table/create');
                }
            }
        } catch (\Exception $e) {
            // For SQLite, use different query
            try {
                $tables = $model->getDb()->rows("SELECT name FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
                if (!empty($tables)) {
                    SE::setflash(t('admin.table_already_exists'), 'warning');
                    redirect('/admin/table/create');
                }
            } catch (\Exception $e2) {
                // Continue anyway
            }
        }

        // Build CREATE TABLE statement
        $columnDefs = [];
        $colIndex = 0;
        foreach ($columns as $column) {
            if (!isset($column['name']) || !isset($column['type'])) {
                continue;
            }

            $colName = sanitize($column['name']);
            $colType = sanitize($column['type']);

            // Validate column name
            if (!preg_match('/^[a-z_][a-z0-9_]*$/', $colName)) {
                SE::setflash(t('admin.invalid_column_name'), 'danger');
                redirect('/admin/table/create');
            }

            $def = "`$colName` $colType";

            // Handle nullable
            if (!isset($column['nullable']) || $column['nullable'] !== 'on') {
                $def .= " NOT NULL";
            }

            // Handle default value
            if (isset($column['default']) && !empty($column['default'])) {
                $defaultValue = sanitize($column['default']);
                // Escape for SQL
                if (in_array(strtoupper($colType), ['INT', 'BIGINT', 'DECIMAL(10,2)', 'BOOLEAN'])) {
                    $def .= " DEFAULT $defaultValue";
                } else {
                    $def .= " DEFAULT '$defaultValue'";
                }
            } elseif (isset($column['nullable']) && $column['nullable'] === 'on') {
                $def .= " DEFAULT NULL";
            }

            if ($colIndex === 0) {
                $def .= " PRIMARY KEY AUTO_INCREMENT";
            }

            $columnDefs[] = $def;
            $colIndex++;
        }

        if (empty($columnDefs)) {
            SE::setflash(t('admin.table_columns') . ' ' . t('flash.fill_all_fields'), 'danger');
            redirect('/admin/table/create');
        }

        $sqlColumns = implode(', ', $columnDefs);
        $sql = "CREATE TABLE `$tableName` ($sqlColumns)";

        try {
            $model->getDb()->raw($sql);
            SE::setflash(t('admin.table_created'), 'success');
        } catch (\Exception $e) {
            SE::setflash(t('admin.table_creation_failed') . ': ' . $e->getMessage(), 'danger');
        }

        redirect('/admin');
    }
}
