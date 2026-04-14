<?php

namespace app\controller;

use app\middlewares\AuthMiddleware;
use app\middlewares\BaseMiddleware;
use app\Router;
use app\models\Users;
use app\OAuth;
use app\Mailer;
use app\Database;

class PagesController
{
    /** @var BaseMiddleware[] */
    protected array $middlewares = [];
    public string $action = '';

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['home', 'contact']));
    }

    public function login(Router $router): void
    {
        $errors   = [];
        $userData = [
            'email'    => '',
            'password' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email']    ?? '';
            $password = $_POST['password'] ?? '';

            $userData['email'] = $email;

            $result = Users::attempt($email, $password);
            $errors = $result['errors'];

            if ($result['success']) {
                $router->login($result['user']);
                header('Location: /home');
                exit;
            }
        }

        $userData['password'] = '';

        $router->renderView('pages/login', [
            'userData' => $userData,
            'errors'   => $errors,
            'title'    => 'Sign in to your account',
            'file'     => 'login',
        ]);
    }

    public function register(Router $router): void
    {
        $errors   = [];
        $userData = [
            'email'            => '',
            'password'         => '',
            'confirm_password' => '',
            'Firstname'        => '',
            'Lastname'         => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userData['email']            = $_POST['email']            ?? '';
            $userData['Firstname']        = $_POST['Firstname']        ?? '';
            $userData['Lastname']         = $_POST['Lastname']         ?? '';
            $userData['password']         = $_POST['password']         ?? '';
            $userData['confirm_password'] = $_POST['confirm_password'] ?? '';

            $user = new Users();
            $user->load($userData);
            $errors = $user->save();

            $userData['password']         = '';
            $userData['confirm_password'] = '';

            if (empty($errors)) {
                $router->session->setFlash('success', 'Thanks for registering');

                $sent = Mailer::send(
                    $userData['email'],
                    'Welcome to Luxé!',
                    '<p>Hi ' . htmlspecialchars($userData['Firstname'], ENT_QUOTES, 'UTF-8') . ', welcome aboard!</p>'
                );

                if (!$sent) {
                    error_log('Welcome email failed for: ' . $userData['email']);
                }

                header('Location: /');
                exit;
            }
        }

        $router->renderView('pages/register', [
            'userData'      => $userData,
            'errors'        => $errors,
            'title'         => 'Create your account',
            'file'          => 'register',
            'googleAuthUrl' => OAuth::getGoogleAuthUrl($router->session),
        ]);
    }

    public function home(Router $router): void
    {
        $router->renderView('pages/home', [
            'title' => 'Luxé — Ecommerce',
            'file'  => 'home',
        ]);
    }

    public function logout(Router $router): void
    {
        // FIX: verify CSRF token before logging out to prevent CSRF-logout attacks

        $token = $_POST['csrf_token'] ?? '';
        if (!$router->session->validateCsrfToken($token)) {
            http_response_code(403);
            $router->renderView('pages/403', [
                'title' => '403 Forbidden',
                'file'  => '403',
            ]);
            return;
        }

        $router->logout();
        header('Location: /');
        exit;
    }

    public function contact(Router $router): void
    {
        $errors   = [];
        $userData = [
            'email'              => '',
            'message'            => '',
            'Firstname'          => '',
            'Lastname'           => '',
            'phonenumber'        => '',
            'phonenumber-prefix' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userData['email']              = $_POST['email']              ?? '';
            $userData['message']            = $_POST['message']            ?? '';
            $userData['Firstname']          = $_POST['Firstname']          ?? '';
            $userData['Lastname']           = $_POST['Lastname']           ?? '';
            $userData['phonenumber']        = $_POST['phonenumber']        ?? '';
            $userData['phonenumber-prefix'] = $_POST['phonenumber-prefix'] ?? '';

            $user = new Users();
            $user->load($userData);
            $errors = $user->saveContact();
            error_log('CONTACT ERRORS: ' . print_r($errors, true));

            if (empty($errors)) {
                $router->session->setFlash('success', 'Thanks for contacting us');

                $sent = Mailer::send(
                    $userData['email'],
                    'We received your message',
                    '<p>Hi ' . htmlspecialchars($userData['Firstname'], ENT_QUOTES, 'UTF-8') . ', thanks for reaching out!</p>'
                );

                if (!$sent) {
                    error_log('Contact confirmation email failed for: ' . $userData['email']);
                }

                $phone = !empty($userData['phonenumber'])
                    ? htmlspecialchars($userData['phonenumber-prefix'], ENT_QUOTES, 'UTF-8') . ' '
                    . htmlspecialchars($userData['phonenumber'], ENT_QUOTES, 'UTF-8')
                    : 'Not provided';

                $adminSent = Mailer::send(
                    $_ENV['MAIL_ADMIN'],
                    'New contact form submission',
                    '
                    <p><strong>Name:</strong> '
                        . htmlspecialchars($userData['Firstname'], ENT_QUOTES, 'UTF-8') . ' '
                        . htmlspecialchars($userData['Lastname'],  ENT_QUOTES, 'UTF-8') . '</p>
                    <p><strong>Email:</strong> '
                        . htmlspecialchars($userData['email'],   ENT_QUOTES, 'UTF-8') . '</p>
                    <p><strong>Phone:</strong> '   . $phone . '</p>
                    <p><strong>Message:</strong> '
                        . nl2br(htmlspecialchars($userData['message'], ENT_QUOTES, 'UTF-8')) . '</p>
                    '
                );

                if (!$adminSent) {
                    error_log('Admin contact notification email failed.');
                }

                header('Location: /contact');
                exit;
            }
        }

        $router->renderView('pages/contact', [
            'userData'  => $userData,
            'errors'    => $errors,
            'title'     => 'Contact',
            'file'      => 'Contact',
            'csrfToken' => $router->session->getCsrfToken(), // ADD THIS
        ]);
    }

    public function googleRedirect(Router $router): void
    {
        header('Location: ' . OAuth::getGoogleAuthUrl($router->session));
        exit;
    }

    public function registerMiddleware(BaseMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function googleCallback(Router $router): void
    {
        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';

        if (!OAuth::validateState($state, $router->session)) {
            $router->session->setFlash('error', 'Invalid state. Please try again.');
            header('Location: /');
            exit;
        }

        if (empty($code)) {
            $router->session->setFlash('error', 'Google login failed. Please try again.');
            header('Location: /');
            exit;
        }

        $tokenData = OAuth::exchangeCodeForToken($code);

        if (empty($tokenData['access_token'])) {
            $router->session->setFlash('error', 'Could not get token from Google. Please try again.');
            header('Location: /');
            exit;
        }

        $googleUser = OAuth::getGoogleUser($tokenData['access_token']);

        if (empty($googleUser['sub'])) {
            $router->session->setFlash('error', 'Could not fetch Google profile. Please try again.');
            header('Location: /');
            exit;
        }

        $user = Users::findOrCreateGoogleUser([
            'google_id' => $googleUser['sub'],
            'email'     => $googleUser['email']       ?? '',
            'Firstname' => $googleUser['given_name']  ?? '',
            'Lastname'  => $googleUser['family_name'] ?? '',
        ]);

        if (!$user) {
            $router->session->setFlash('error', 'Could not log in with Google. Please try again.');
            header('Location: /');
            exit;
        }

        $router->login($user);
        header('Location: /home');
        exit;
    }

    public function forgotPassword(Router $router): void
    {
        $errors  = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $errors['email'][] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'][] = 'Email is not valid.';
            }

            if (empty($errors)) {
                if (Database::$db->emailExists($email)) {
                    $token     = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    Database::$db->insertPasswordReset($email, $token, $expiresAt);

                    $row       = Database::$db->getUserByEmail($email);
                    $name      = $row['Firstname'] . ' ' . $row['Lastname'];
                    $resetLink = rtrim($_ENV['APP_URL'], '/') . '/reset-password?token=' . $token;

                    try {
                        $mailer = new Mailer();
                        $mailer->sendPasswordReset($email, $name, $resetLink);
                    } catch (\Exception $e) {
                        error_log('Password reset mail failed: ' . $e->getMessage());
                    }
                }

                $success = true;
            }
        }

        $router->renderView('pages/forgot-password', [
            'errors'  => $errors,
            'success' => $success,
            'title'   => 'Forgot Password',
            'file'    => 'forgot-password',
        ]);
    }

    public function resetPassword(Router $router): void
    {
        $errors = [];
        $token  = trim($_GET['token'] ?? '');

        if (empty($token)) {
            header('Location: /forgot-password');
            exit;
        }

        $reset = Database::$db->getPasswordReset($token);

        if (!$reset || strtotime($reset['expires_at']) < time()) {
            if ($reset) {
                Database::$db->deletePasswordResetByToken($token);
            }
            $router->renderView('pages/reset-password', [
                'errors'     => ['token' => ['This reset link is invalid or has expired.']],
                'tokenValid' => false,
                'token'      => '',
                'title'      => 'Reset Password',
                'file'       => 'reset-password',
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password        = $_POST['password']         ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($password)) {
                $errors['password'][] = 'Password is required.';
            } elseif (strlen($password) < 8 || strlen($password) > 24) {
                $errors['password'][] = 'Password must be between 8 and 24 characters.';
            }

            if ($password !== $confirmPassword) {
                $errors['confirm_password'][] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                Database::$db->updateUserPassword($reset['email'], $hashed);
                Database::$db->deletePasswordResetByToken($token);
                $router->session->setFlash('success', 'Your password has been reset. You can now log in.');
                header('Location: /');
                exit;
            }
        }

        $router->renderView('pages/reset-password', [
            'errors'     => $errors,
            'tokenValid' => true,
            'token'      => $token,
            'title'      => 'Reset Password',
            'file'       => 'reset-password',
        ]);
    }
}
