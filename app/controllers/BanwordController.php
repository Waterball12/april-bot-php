<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class BanWord
{

    public function getBanWord($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];

        $data = $db->select('BanWords', '*', ['ServerID' => $id]);

        $result['banword'] = $data;

        Router::ExitWithSuccess($result);
    }

    public function setBanWord($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data['data_april'] == true || $data['data_april'] == 'true') {
            $db->delete('BanWords', ['AND' => ['ServerID' => $id]]);
        }
        
        $affected = 0;

        if ($data['data'] != null) {

            $toInsert = array();

            foreach ($data['data'] as $key => $value) {
                $str = preg_replace('/[^A-Za-z]/', '', $value);
                array_push($toInsert, [ 'Word' => $str, 'ServerID' => $id ]);
            }

            $result = $db->insert('BanWords', $toInsert);

            if ($result->rowCount() != count($data['data'])) {
                Router::ExitWithError(206, 'Updated partial content');
            }
        }

        Router::ExitWithSuccess(null);
    }
}

