<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Lisbon');
setlocale(LC_ALL, ['pt_PT.utf8', 'pt_PT@euro', 'pt_UTF8', 'pt_PT', 'portuguese']);
ini_set('intl.default_locale', 'pt_PT');

/* start session */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../vendor/autoload.php';

//get dotenv values
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

//require config file
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/functions.php';

//Auth middleware
$auth = function () {
    if (!auth_enabled()) {
        if (\app\core\Session::isAdmin()) {
            return;
        }
        header('Location: /');
        exit;
    }
    if (!\app\core\Session::loggedIn()) {
        header('Location: /login');
        exit;
    }
};

$admin = function () {
    if (!\app\core\Session::loggedIn() || !\app\core\Session::isAdmin()) {
        header('Location: /');
        exit;
    }
};
