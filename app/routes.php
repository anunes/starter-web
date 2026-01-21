<?php


use app\core\Router;
use app\controllers\AdminController;
use app\controllers\ProfileController;
use app\controllers\PasswordController;
use app\controllers\AuthController;
use app\controllers\AssetController;
use app\controllers\JavaScriptController;
use app\controllers\LanguageController;
use app\controllers\MainController;
use app\core\Session as SE;

$router = new Router();

$router->get('/avatars/{filename}', [ProfileController::class, 'avatar'])->name('avatar');
$router->get('/logo/{filename}', [AssetController::class, 'logo'])->name('logo');
$router->get('/favicon/{filename}', [AssetController::class, 'favicon'])->name('favicon');
$router->get('/core/javascript/{filename}', [JavaScriptController::class, 'serve'])->name('javascript');
$router->get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang');

if (!auth_enabled()) {
    $router->get('/', [MainController::class, 'home'])->name('home');
} elseif (!SE::loggedIn()) {
    $router->get('/', [MainController::class, 'guest'])->name('guest');
}
$router->get('/about', [MainController::class, 'about'])->name('about');
$router->get('/contact', [MainController::class, 'contact'])->name('contact');

/*   AUTH ROUTES   */
//Register routes
$router->get('/register', [AuthController::class, 'register'])->name('register');
$router->post('/register', [AuthController::class, 'register']);
//Login routes
$router->get('/login', [AuthController::class, 'login'])->name('login');
$router->post('/login', [AuthController::class, 'login']);
//Logout route
$router->get('/logout', [AuthController::class, 'logout'])->name('logout');

//Forgot password routes
$router->get('/forgot', [PasswordController::class, 'forgot'])->name('forgot');
$router->post('/forgot', [PasswordController::class, 'forgot']);
//Reset password routes
$router->get('/reset', [PasswordController::class, 'reset'])->name('reset');
$router->post('/reset', [PasswordController::class, 'reset']);

//Authenticated routes
$router->group(['middleware' => 'auth'], function ($router) {
    $router->get('/', [MainController::class, 'home'])->name('home');
    $router->get('/home', [MainController::class, 'home']);
    //User routes
    $router->get('/profile', [ProfileController::class, 'show'])->name('profile');
    $router->post('/profile', [ProfileController::class, 'update']);
    $router->get('/profile/delete', [ProfileController::class, 'destroy']);

    //Password routes
    $router->get('/password', [PasswordController::class, 'password'])->name('password');
    $router->post('/password', [PasswordController::class, 'update']);

    //ADMIN routes
    $router->group(['middleware' => 'admin'], function ($router) {
        $router->get('/admin', [AdminController::class, 'index'])->name('admin');
        $router->post('/admin/settings', [AdminController::class, 'updateSettings']);
        $router->get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('edit');
        $router->post('/admin/update/{id}', [AdminController::class, 'update']);
        $router->get('/admin/delete/{id}', [AdminController::class, 'softdelete']);
        $router->get('/admin/table/create', [AdminController::class, 'createTable'])->name('create-table');
        $router->post('/admin/table/create', [AdminController::class, 'storeTable']);
    });
});






$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
