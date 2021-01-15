<?php

namespace middleware\headers;

use Router;

class VerifyHeaders
{
    public function __construct() {
        header('Content-type: application/json'); // Declare Json response

        // Check if request was made by Ajax
        // if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || !$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        //     Router::ExitWithError(428, 'Unauthorized');
        // }

        // Check if request was made by current Server/Domain
        if ((isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))) {
            if (strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) != strtolower($_SERVER['HTTP_HOST'])) {
                Router::ExitWithError(428, 'Unable to process');
            }
        }
    }
}