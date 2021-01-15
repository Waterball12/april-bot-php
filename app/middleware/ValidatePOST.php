<?php

namespace middleware\PostSecurity;

use Router;

class ValidatePOST
{
    public function __construct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            if (isset($_POST)) {
                try {
                    $data = Router::GetPostData();

                    if (isset($data['token']) && isset($data['is_ajax'])
                        && isset($data['data_april'])
                        && isset($_SESSION['home_csrf'])
                    ) {
                        if ($data['token'] == getenv('token')) {
                            return;
                        }
                    }
                } catch (Exception $e) {
                    Router::ExitWithError(417, 'Expectation Failed');
                }
            }
            Router::ExitWithError(417, 'Expectation Failed');
        }
    }
}