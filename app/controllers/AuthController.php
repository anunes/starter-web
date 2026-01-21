<?php

namespace app\controllers;

use app\models\User;;

use app\core\Session as SE;

class AuthController extends User
{
    public function register()
    {
        if (!auth_enabled()) {
            SE::setflash(t('flash.user_system_disabled'), 'warning');
            redirect('/');
        }
        if (!registration_enabled()) {
            SE::setflash(t('flash.registration_disabled'), 'warning');
            redirect('/login');
        }
        if (SE::loggedIn()) {
            redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data   
            if (!SE::checkCsrf()) {
                SE::setflash(t('flash.invalid_csrf'), 'danger');
            }

            $name = sanitize($_POST['name']);
            $email = sanitize($_POST['email']);
            $password = sanitize($_POST['password']);
            $confirm_password = sanitize($_POST['confirm_password']);

            // Basic validation
            if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = t('flash.fill_all_fields');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = t('flash.invalid_email');
            }

            if ($password !== $confirm_password) {
                $error = t('flash.passwords_no_match');
            }
            //end validation

            if (!isset($error)) {
                $userModel = new User();

                // Check if user already exists
                $existingUser = $userModel->getUserByEmail($email);
                if ($existingUser) {
                    SE::setflash(t('flash.user_already_exists'), 'info');
                    redirect('/login');
                    exit();
                }

                $userId = $userModel->createUser($name, $email, $password);

                if ($userId) {
                    $user = $userModel->getUserByEmail($email);
                    if ($user) {
                        SE::setSession($user);
                        redirect('/');
                        exit();
                    }
                    $error = t('flash.registration_login_failed');
                } else {
                    $error = t('flash.registration_failed');
                }
            }

            if (isset($error)) {
                SE::setflash($error, 'danger');
            }
        }
        view('auth/register');
    }

    public function login()
    {
        if (!auth_enabled()) {
            SE::setflash(t('flash.user_system_disabled'), 'warning');
            redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (!SE::checkCsrf()) {
                SE::setflash(t('flash.invalid_csrf'), 'danger');
            }

            $email = sanitize($_POST['email']);
            $password = sanitize($_POST['password']);

            // Basic validation
            if (empty($email) || empty($password)) {
                $error = t('flash.fill_all_fields');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = t('flash.invalid_email');
            }

            if (!isset($error)) {
                $userModel = new User();
                $user = $userModel->getUserByEmail($email);

                if ($user) {
                    if (password_verify($password, $user->password)) {
                        SE::setSession($user);
                        redirect('/');
                        exit();
                    } else {
                        $error = t('flash.invalid_login');
                    }
                } else {
                    $error = t('flash.user_not_found');
                }
            }

            if (isset($error)) {
                SE::setflash($error, 'danger');
            }
        }
        view('auth/login');
    }

    public function logout()
    {
        session_destroy();
        redirect('/');
    }
}
