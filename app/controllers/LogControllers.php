<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class Log
{
    public function getLog($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];

        $data = $db->select('GuildLog', '*', ['ServerID' => $id, "LIMIT" => 300]);

        $result['log'] = $data;

        Router::ExitWithSuccess($result);
    }
}

