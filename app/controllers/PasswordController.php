<?php

namespace app\controllers;

use app\core\Model;
use app\models\User;
use app\core\Session as SE;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PasswordController
{

    public function password()
    {
        if (!auth_enabled()) {
            SE::setflash(t('flash.user_system_disabled'), 'warning');
            redirect('/');
        }
        view('user/password');
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
            $old_password = sanitize($_POST['old_password']);
            $new_password = sanitize($_POST['password']);
            $confirm_password = sanitize($_POST['confirm_password']);

            if ($new_password !== $confirm_password) {
                SE::setflash(t('flash.passwords_no_match'), 'danger');
                redirect('/password');
            }

            $model = new Model();
            $user = $model->find('users', $_SESSION['id']);

            if (!password_verify($old_password, $user->password)) {
                SE::setflash(t('flash.current_password_incorrect'), 'danger');
                redirect('/password');
            } else {
                $password = password_hash($new_password, PASSWORD_DEFAULT);
                $model->update('users', ['password' => $password], $_SESSION['id']);
                SE::setflash(t('flash.password_updated'), 'success');
                session_destroy();
                redirect('/login');
            }
        }
    }

    public function forgot()
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

            if (empty($email)) {
                SE::setflash(t('flash.email_required'), 'danger');
                redirect('/forgot');
            }

            $u = new User();
            $user = $u->getUserByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $u->updatePasswordResetToken($user->id, $token);
                $encryptionMethod = "AES-128-CBC";
                $secretKey = $_ENV['APP_SECRET_KEY'];
                $iv = substr(hash('sha256', $secretKey), 0, 16); // Use first 16 bytes of hash as IV
                $encryptedUserId = openssl_encrypt($user->id, $encryptionMethod, $secretKey, 0, $iv);

                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = $_ENV['MAIL_HOST'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['MAIL_USERNAME'];
                    $mail->Password = $_ENV['MAIL_PASSWORD'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = $_ENV['MAIL_PORT'];

                    //Recipients
                    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['APP_NAME']);
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset';
                    $resetUrl = $_ENV['APP_URL'] . '/reset?' . http_build_query(['token' => $token, 'id' => $encryptedUserId]);
                    $mail->Body = "Click the link below to reset your password <br> <a href='" . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . "'>Reset Password</a>";

                    $mail->send();
                    SE::setflash(t('flash.reset_link_sent'), 'success');
                    redirect('/');
                } catch (Exception $e) {
                    SE::setflash(t('flash.mailer_error', ['error' => $mail->ErrorInfo]), 'danger');
                    redirect('/forgot');
                }
            }
        }
        view('user/forgot');
    }

    public function reset()
    {
        if (!auth_enabled()) {
            SE::setflash(t('flash.user_system_disabled'), 'warning');
            redirect('/');
        }
        $token = sanitize($_POST['token'] ?? $_GET['token'] ?? '');
        $id = sanitize($_POST['id'] ?? $_GET['id'] ?? '');
        if ($token === '' || $id === '') {
            SE::setflash(t('flash.invalid_reset_link'), 'danger');
            redirect('/forgot');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!SE::checkCsrf()) {
                SE::setflash(t('flash.invalid_csrf'), 'danger');
            }

            $password = sanitize($_POST['password']);
            $confirm_password = sanitize($_POST['confirm_password']);

            if ($password !== $confirm_password) {
                SE::setflash(t('flash.passwords_no_match'), 'danger');
                redirect('/reset?token=' . $token . '&id=' . $id);
            }

            $u = new User();
            $user = $u->find('users', openssl_decrypt($id, "AES-128-CBC", $_ENV['APP_SECRET_KEY'], 0, substr(hash('sha256', $_ENV['APP_SECRET_KEY']), 0, 16)));

            if ($user) {
                if ($user->reset_token === $token) {
                    $new_password = password_hash($password, PASSWORD_DEFAULT);
                    $u->update('users', ['password' => $new_password, 'reset_token' => null], $user->id);
                    SE::setflash(t('flash.reset_success'), 'success');
                    redirect('/login');
                } else {
                    SE::setflash(t('flash.invalid_token'), 'danger');
                    redirect('/reset?token=' . $token . '&id=' . $id);
                }
            } else {
                SE::setflash(t('flash.user_not_found'), 'danger');
                redirect('/reset?token=' . $token . '&id=' . $id);
            }
        }
        view('user/reset', ['token' => $token, 'id' => $id]);
    }
}
