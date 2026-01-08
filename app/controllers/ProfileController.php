<?php

namespace app\controllers;

use app\core\Model;
use app\core\Session as SE;


class ProfileController
{
    public function show()
    {
        if (!auth_enabled()) {
            SE::setflash(t('flash.user_system_disabled'), 'warning');
            redirect('/');
        }
        $model = new Model();
        $user = $model->find('users', $_SESSION['id']);
        view('user/profile', ['user' => $user]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!auth_enabled()) {
                SE::setflash(t('flash.user_system_disabled'), 'warning');
                redirect('/');
            }

            if (!SE::checkCsrf()) {
                SE::setflash(t('flash.invalid_csrf'), 'danger');
            }

            $model = new Model();
            $user = $model->find('users', $_SESSION['id']);
            $data = [
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
            ];

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $result = $this->handleAvatarUpload($_FILES['avatar'], $user?->avatar ?? null);
                if (isset($result['error'])) {
                    SE::setflash(t($result['error']), 'danger');
                    redirect('/profile');
                }
                $data['avatar'] = $result['filename'];
            }

            $model->update('users', $data, $_SESSION['id']);
            $updatedUser = $model->find('users', $_SESSION['id']);
            if ($updatedUser) {
                SE::setSession($updatedUser);
            }
            redirect('/profile');
        }
    }

    public function destroy($id)
    {
        if (!auth_enabled()) {
            SE::setflash(t('flash.user_system_disabled'), 'warning');
            redirect('/');
        }
        $model = new Model();
        $model->delete('users', $id);
        redirect('/');
    }

    public function avatar($filename)
    {
        $safeName = basename($filename);
        if (!preg_match('/^[A-Za-z0-9_-]+\\.webp$/', $safeName)) {
            http_response_code(404);
            return;
        }

        $path = APP_ROOT . '/storage/avatars/' . $safeName;
        if (!is_file($path)) {
            http_response_code(404);
            return;
        }

        header('Content-Type: image/webp');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=604800');
        readfile($path);
    }

    private function handleAvatarUpload(array $upload, ?string $currentAvatar): array
    {
        if ($upload['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'flash.avatar_upload_failed'];
        }

        if ($upload['size'] > 5 * 1024 * 1024) {
            return ['error' => 'flash.avatar_too_large'];
        }

        if (!is_uploaded_file($upload['tmp_name'])) {
            return ['error' => 'flash.avatar_upload_invalid'];
        }

        $imageInfo = getimagesize($upload['tmp_name']);
        if ($imageInfo === false) {
            return ['error' => 'flash.avatar_not_image'];
        }

        $mime = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if (!function_exists('imagewebp')) {
            return ['error' => 'flash.webp_not_supported'];
        }

        $source = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($upload['tmp_name']),
            'image/png' => imagecreatefrompng($upload['tmp_name']),
            'image/gif' => imagecreatefromgif($upload['tmp_name']),
            'image/webp' => imagecreatefromwebp($upload['tmp_name']),
            default => null,
        };

        if (!$source) {
            return ['error' => 'flash.avatar_unsupported'];
        }

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

        $storageDir = APP_ROOT . '/storage/avatars';
        if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true) && !is_dir($storageDir)) {
            imagedestroy($source);
            imagedestroy($output);
            return ['error' => 'flash.avatar_dir_failed'];
        }

        $filename = 'avatar_' . $_SESSION['id'] . '_' . bin2hex(random_bytes(8)) . '.webp';
        $path = $storageDir . '/' . $filename;

        $saved = imagewebp($output, $path, 82);

        imagedestroy($source);
        imagedestroy($output);

        if (!$saved) {
            return ['error' => 'flash.avatar_save_failed'];
        }

        if (!empty($currentAvatar)) {
            $oldPath = $storageDir . '/' . basename($currentAvatar);
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        return ['filename' => $filename];
    }
}
