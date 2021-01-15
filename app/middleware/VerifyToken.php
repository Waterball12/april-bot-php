<?php

namespace middleware\token;

use Router;
use Discord;

class VerifyToken
{
    public function __construct() {
        if (isset($_SESSION['home_t'])) {
            $provider = Discord::GetProvider();
            if (!Discord::SessionExpired($provider)) {
                return;
            }
        }

        Router::ExitWithError(401, "Unauthorized");
    }
}