<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class SelfRole
{

    public function getSelfRole($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];

        $data = $db->select('SelfRole', '*', ['ServerID' => $id]);
        $role = $db->select('Roles', '*', ['ServerID' => $id]);

        $result['selfrole'] = $data;
        $result['roles'] = $role;

        Router::ExitWithSuccess($result);
    }

    public function setSelfRole($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];
        $post = Router::GetPostData();
        if ($post['data'] == null) {
            $db->delete('SelfRole', ['AND' => ['ServerID' => $id]]);
            Router::ExitWithSuccess(null);
        }
        $validateData = Router::ValidateData($post['data'], 'Roles', $id, 'ID');

        if (count($validateData) != count($post['data'])) {
            Router::ExitWithError(409, 'Conflict');
        }


        if ($post['data_april'] == true || $post['data_april'] == 'true') {
            $db->delete('SelfRole', ['AND' => ['ServerID' => $id]]);
        }
        
        if (count($validateData) >= 1) {

            $toInsert = array();

            foreach ($validateData as $key => $value) {
                array_push($toInsert, [ 'Role' => $value, 'ServerID' => $id, 'ID' => $key ]);
            }

            $result = $db->insert('SelfRole', $toInsert);

            if ($result->rowCount() != count($validateData)) {
                Router::ExitWithError(206, 'Updated partial content');
            }
            Router::ExitWithSuccess(null);
        }

        Router::ExitWithError(417, 'Expectation Failed');
    }
}

