<?php

namespace middleware\CsrfToken;

use Router;

class VerifyCsrfToken
{
    public function __construct() {

        if (isset($_SESSION['home_csrf'])) {
            $headers = apache_request_headers();

            if (isset($headers['HOME_TOKEN'])) {

                if ($headers['HOME_TOKEN'] == getenv('homeToken')) {
                    return;
                }
            }
        }

        Router::ExitWithError(428, 'Unauthorized');
    }
}