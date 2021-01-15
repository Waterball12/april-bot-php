<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class Streamers
{

    public function getStreamers($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];

        $data = $db->select('Streamers', '*', ['ServerID' => $id]);

        $result['streamers'] = $data;

        Router::ExitWithSuccess($result);
    }

    public function delStreamers($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };

        $db = $GLOBALS["database"];

        $data = json_decode(file_get_contents('php://input'), true);

        if ($data['data'] != null) {

            $validateData = Router::ValidateData($data['data'], "Streamers", $id, "ID");

            if (count($validateData) != count($data['data'])) {
                Router::ExitWithError(409, 'Conflict');
            }

            if (count($validateData) >= 1) {

                $result = null;

                foreach ($data['data'] as $key => $value) {
                    $result = $db->delete('Streamers', [
                        "AND" => [
                            "ID" => $key,
                            "ServerID" => $id
                        ]
                    ]);
                    break;
                }

                if ($result->rowCount() != count($validateData)) {
                    Router::ExitWithError(206, 'Updated partial content');
                }
                Router::ExitWithSuccess(null);
            }

        }

        Router::ExitWithError(417, 'Expectation Failed');
    }

    public function setStreamers($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data['data'] != null) {

            $toRequest = array();

            foreach ($data['data'] as $key => $value) {
                if (ctype_digit($key)) {
                    array_push($toRequest, $key);
                }
            }

            if (count($toRequest) >= 1) {
                $streamers = $this->GetStreamer($this->GetIds($toRequest));

                if ($streamers == null) {
                    Router::ExitWithError(417, 'Expectation Failed');
                }

                $toSave = array();

                foreach ($streamers->data as $value) {
                    array_push($toSave, [ 'ServerID' => $id, 'ID' => $value->id, 'Name' => $value->display_name ]);
                }

                $result = $db->insert('Streamers', $toSave);

                if ($result->rowCount() != count($data['data'])) {
                    Router::ExitWithError(206, 'Updated partial content');
                }
                Router::ExitWithSuccess($streamers->data);
            }
        }

        Router::ExitWithError(417, 'Expectation Failed');
    }

    private function GetStreamer($streamers) {
        try {

            $url = "https://api.twitch.tv/helix/users?$streamers";
            $curl = curl_init("$url");

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Client-ID: ' + getenv("TwitchClientId")
            ));

            $response = curl_exec($curl);
            if ($response) {
                $json = json_decode($response);
                if (isset($json->data)) {
                    return $json;
                }
            }
            return null;
        } catch(Exception $e) {
            return null;
        }
    }

    private function GetIds($ids) {
        $temp = '';
        foreach ($ids as $id) {
            $temp.= 'id=' . $id . '&';
        }
        return $temp;
    }
}