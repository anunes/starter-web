<?php

namespace app\controllers;

class LanguageController
{
    public function switch($locale): void
    {
        set_lang((string) $locale);

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $parts = parse_url($referer);
        $path = $parts['path'] ?? '/';
        $query = $parts['query'] ?? '';
        $redirect = $path . ($query !== '' ? '?' . $query : '');

        header('Location: ' . $redirect);
        exit;
    }
}
