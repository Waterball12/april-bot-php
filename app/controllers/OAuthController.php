<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class OAuth
{
    public function getOAuth() {
        try {
            $token = $_SESSION['home_t'];
            $provider = Discord::GetProvider();

            $user = $provider->getResourceOwner($token)->toArray();
            $guild = $provider->getGuildsDetails($token);
            $jsonGuild = array();
            $permittedGuids = array();

            foreach ($guild->toArray() as $row) {
                if ((($row['permissions'] & 0x20 || $row['owner']) === true)) {
                    array_push($jsonGuild, $row);
                    array_push($permittedGuids, $row['id']);
                }
            }

            $db = $GLOBALS["database"];

            $perm = $db->select('Settings', "*", ['ServerID' => $permittedGuids]);

            $result['perm'] = $perm;
            $result['guild'] = $jsonGuild;
            $result['user'] = $user;
            $result['authorized'] = true;
            $result['token'] = $_SESSION['home_csrf'];

            Router::ExitWithSuccess($result);
        } catch(Exception $e) {
            Router::ExitWithError(417, "Expectation Failed");
        }
    }
}

