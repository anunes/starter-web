<?php

namespace app\core;

use Exception;

class Session
{
    public static function setSession($user): void
    {
        $_SESSION['id'] = $user->id;
        $_SESSION['name'] = $user->name;
        $_SESSION['email'] = $user->email;
        $_SESSION['role'] = $user->role;
        $_SESSION['avatar'] = $user->avatar ?? null;
    }

    public static function flash(): void
    {
        if (isset($_SESSION['flash'])) {
            echo '<div class="flash-message alert mb-4 alert-' . $_SESSION['flash']['type'] . ' alert-dismissible fade show" role="alert">
              ' . $_SESSION['flash']['message'] . '
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';

            unset($_SESSION['flash']);
        }
    }

    public static function setflash($message, $type): void
    {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }


    public static function loggedIn(): bool
    {
        if (!isset($_SESSION['id'])) {
            return false;
        }
        return true;
    }

    public static function isAdmin(): bool
    {
        if (self::loggedIn() && $_SESSION['role'] == '1') {
            return true;
        }
        return false;
    }

    public static function csrf(): void
    {
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(50));
        }
        echo '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
    }

    public static function checkCsrf(): bool
    {
        if (!isset($_SESSION['csrf']) || !isset($_POST['csrf'])) {
            return false;
        }
        if ($_SESSION['csrf'] != $_POST['csrf']) {
            return false;
        }
        return true;
    }
}
