<?php

    require __DIR__ . '/../../vendor/autoload.php';
    require_once '../../app/Discord.php';

    session_start();

    $provider = Discord::GetProvider();


    if (isset($_SESSION['home_t']) && isset($_SESSION['home_csrf'])) {
        if (!Discord::hasExpired($_SESSION['home_t'])) {
            if (Discord::SessionExpired($provider)) {
                session_unset();
                header('Location: ./login.php');
            };
        }
        exit('You are already logged in');

    } else if (!isset($_GET['code'])) {

        // Step 1. Get authorization code
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authUrl);

    // Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

        unset($_SESSION['oauth2state']);
        exit('Invalid state');

    } else {

        // Step 2. Get an access token using the provided authorization code
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        $_SESSION['home_t'] = $token;
        $_SESSION['home_csrf'] = bin2hex(openssl_random_pseudo_bytes(16));

        header('Location: /dashboard');
    }