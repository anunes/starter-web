<?php

namespace app\controllers;

class AssetController
{
    public function logo($filename): void
    {
        $safeName = basename($filename);
        if (!preg_match('/^[A-Za-z0-9_-]+\\.webp$/', $safeName)) {
            http_response_code(404);
            return;
        }

        $path = APP_ROOT . '/storage/logo/' . $safeName;
        if (!is_file($path)) {
            http_response_code(404);
            return;
        }

        header('Content-Type: image/webp');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=604800');
        readfile($path);
    }

    public function favicon($filename): void
    {
        $safeName = basename($filename);
        if (!preg_match('/^[A-Za-z0-9_.-]+$/', $safeName)) {
            http_response_code(404);
            return;
        }

        $path = APP_ROOT . '/storage/favicon/' . $safeName;
        if (!is_file($path)) {
            http_response_code(404);
            return;
        }

        $extension = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'png' => 'image/png',
            'ico' => 'image/x-icon',
            'webmanifest' => 'application/manifest+json',
            default => 'application/octet-stream',
        };

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=604800');
        readfile($path);
    }
}
