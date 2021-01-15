<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class Server
{
    public function getServer($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];

        $channels = $db->select('Channels', '*', ['ServerID' => $id]);
        $settings = $db->select('Settings', '*', ['ServerID' => $id]);
        $roles = $db->select('Roles', '*', ['ServerID' => $id]);

        $result['channel'] = $channels;
        array_push($result['channel'], array("Name" => "Disabled", "id" => "0")); // Hard Coding??????
        $result['role'] = $roles;
        $result['setting'] = $settings;

        Router::ExitWithSuccess($result);
    }

    public function setSetting($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];
        $post = Router::GetPostData();

        $data = $post['data'];
        //$validateData = Router::ValidateData($post['data'], 'Roles', $id, 'ID');

        if (isset($data['Log_Channel']) && isset($data['Twitch_Channel']) && isset($data['CultureInfo'])) {
            
            $result = $this->insertOrUpdate('Settings', [
                "ServerID" => $id,
                "Log_Channel" => $data['Log_Channel'],
                "Twitch_Channel" => $data['Twitch_Channel'],
                "CultureInfo" =>$data['CultureInfo']
            ], [ "ServerID" => $id ], $db);

            if (!$result->rowCount() >= 1) {
                Router::ExitWithError(417, 'Expectation Failed');
            }

            Router::ExitWithSuccess(null);
        }

        Router::ExitWithError(417, 'Expectation Failed');
    }

    public function insertOrUpdate($table,$data,$where, $db){
        if($db->has($table,$where)){
            return $db->update($table,$data,$where);
        } else {
            return $db->insert($table,$data);
        }
    }
}