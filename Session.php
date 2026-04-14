<?php

namespace app;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_secure', '1');
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.gc_maxlifetime', '3600');
            session_save_path('/tmp');
            session_start();
        }

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        unset($flashMessage);

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value'  => $message,
        ];
    }

    public function getFlash(string $key): string|false
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function __destruct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken(string $token): bool
    {
        $stored = $_SESSION['csrf_token'] ?? '';
        return !empty($stored) && hash_equals($stored, $token);
    }
}








// php
// <?php

// namespace app;

// class Session
// {
//     protected const FLASH_KEY = 'flash_messages';

//     public function __construct()
//     {
//         if (session_status() === PHP_SESSION_NONE) {
//             ini_set('session.cookie_secure', '1');
//             ini_set('session.cookie_samesite', 'Lax');
//             ini_set('session.cookie_httponly', '1');
//             ini_set('session.gc_maxlifetime', '3600');
//             session_save_path('/tmp');
//             session_start();
//         }

//         $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
//         foreach ($flashMessages as $key => &$flashMessage) {
//             $flashMessage['remove'] = true;
//         }
//         unset($flashMessage);

//         $_SESSION[self::FLASH_KEY] = $flashMessages;
//     }

//     public function setFlash(string $key, string $message): void
//     {
//         $_SESSION[self::FLASH_KEY][$key] = [
//             'remove' => false,
//             'value'  => $message,
//         ];
//     }

//     public function getFlash(string $key): string|false
//     {
//         return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
//     }

//     public function __destruct()
//     {
//         if (session_status() !== PHP_SESSION_ACTIVE) {
//             return;
//         }

//         $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
//         foreach ($flashMessages as $key => $flashMessage) {
//             if ($flashMessage['remove']) {
//                 unset($flashMessages[$key]);
//             }
//         }
//         $_SESSION[self::FLASH_KEY] = $flashMessages;
//     }

//     public function set(string $key, mixed $value): void
//     {
//         $_SESSION[$key] = $value;
//     }

//     public function get(string $key): mixed
//     {
//         return $_SESSION[$key] ?? false;
//     }

//     public function remove(string $key): void
//     {
//         unset($_SESSION[$key]);
//     }

//     public function destroy(): void
//     {
//         $_SESSION = [];

//         if (ini_get('session.use_cookies')) {
//             $params = session_get_cookie_params();
//             setcookie(
//                 session_name(),
//                 '',
//                 time() - 42000,
//                 $params['path'],
//                 $params['domain'],
//                 $params['secure'],
//                 $params['httponly']
//             );
//         }

//         session_destroy();
//     }

//     public function getCsrfToken(): string
//     {
//         if (empty($_SESSION['csrf_token'])) {
//             $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
//         }
//         return $_SESSION['csrf_token'];
//     }

//     public function validateCsrfToken(string $token): bool
//     {
//         $stored = $_SESSION['csrf_token'] ?? '';
//         return !empty($stored) && hash_equals($stored, $token);
//     }
// }