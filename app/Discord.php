<?php

use League\OAuth2\Client\Token\AccessToken;

class Discord 
{
    public static $provider;

    /**
     * Get a new Discord Provider
     */
    public static function GetProvider() {
        $provider = new \Wohali\OAuth2\Client\Provider\Discord([
            'clientId' => getenv('clientId'),
            'clientSecret' => getenv('clientSecret'),
            'redirectUri' => getenv('redirectUri')
        ]);

        return $provider;
    }

    /**
     * Check if token is expired
     */
    public static function hasExpired(AccessToken $token) {
        return $token->hasExpired();
    }

    /**
     * Get new token from expired one
     */
    public static function GetTokenFromExpired(AccessToken $token) {
        return $provider->getAccessToken('refresh_token', [
            'refresh_token' => $token->getRefreshToken()
        ]);
    }

    /**
     * Check if server is authorized
     */
    public static function IsServerAuthorized($id) {
        try {
            if (empty($provider)) {
                $provider = self::GetProvider();
            }

            $token = $_SESSION['home_t'];

            $guild = $provider->getGuildsDetails($token)->toArray();

            if (!isset($guild)) { return false;  }

            if (count($guild) >= 1) {
                foreach ($guild as $key) {
                    if (isset($key['id'])) {
                        if (isset($key['permissions'])) {
                            if ((($key['permissions'] & 0x20) != 0)) {
                                if ($key['id'] == $id) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }

            return false;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if session is expired
     */
    public static function SessionExpired($provider) {
        try {
            $user = $provider->getResourceOwner($_SESSION['home_t'])->toArray();
            return false;
        } catch (Exception $e) {
            return true;
        }
    }
}