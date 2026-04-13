<?php

namespace app;

// FIX: OAuth now depends on the Session class for state storage instead of
// writing directly to $_SESSION. This keeps session access consistent across
// the whole application and removes the manual session_start() call that could
// conflict with the Session class.
//
// Usage: pass the Session instance when calling getGoogleAuthUrl() and validateState():
//   OAuth::getGoogleAuthUrl($router->session)
//   OAuth::validateState($state, $router->session)

class OAuth
{
    private static string $authUrl     = 'https://accounts.google.com/o/oauth2/v2/auth';
    private static string $tokenUrl    = 'https://oauth2.googleapis.com/token';
    private static string $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';

    // FIX: accept a Session instance so state is stored through the app's
    // single session abstraction rather than raw $_SESSION.

    public static function getGoogleAuthUrl(Session $session): string
    {
        $params = http_build_query([
            'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
            'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'],
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => self::generateState($session),
        ]);

        return self::$authUrl . '?' . $params;
    }

    public static function exchangeCodeForToken(string $code): array
    {
        $ch = curl_init(self::$tokenUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $_ENV['GOOGLE_CLIENT_ID'],
                'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
                'redirect_uri'  => $_ENV['GOOGLE_REDIRECT_URI'],
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            error_log('OAuth curl error: ' . curl_error($ch));
            curl_close($ch);
            return [];
        }

        curl_close($ch);
        return json_decode($response, true) ?? [];
    }

    public static function getGoogleUser(string $accessToken): array
    {
        $ch = curl_init(self::$userInfoUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            error_log('OAuth curl error: ' . curl_error($ch));
            curl_close($ch);
            return [];
        }

        curl_close($ch);
        return json_decode($response, true) ?? [];
    }

    // FIX: state is stored via the Session class, no manual session_start() needed.
    private static function generateState(Session $session): string
    {
        $state = bin2hex(random_bytes(16));
        $session->set('oauth_state', $state);
        return $state;
    }

    // FIX: accept a Session instance; state is read and removed through Session.

    public static function validateState(string $state, Session $session): bool
    {
        $stored = $session->get('oauth_state');
        $session->remove('oauth_state');
        return !empty($stored) && hash_equals($stored, $state);
    }
}
