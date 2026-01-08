<?php

namespace app\controllers;

final class JavaScriptController
{
    public function serve($filename): void
    {
        $safeName = basename((string) $filename);
        if (!preg_match('/^[A-Za-z0-9._-]+\\.js$/', $safeName)) {
            http_response_code(404);
            return;
        }

        $path = APP_ROOT . '/core/javascript/' . $safeName;
        if (!is_file($path)) {
            http_response_code(404);
            return;
        }

        header('Content-Type: application/javascript; charset=UTF-8');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=604800');
        readfile($path);
    }
}
