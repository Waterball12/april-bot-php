<?php

namespace Api\Controllers;

use Router;
use Discord;
use Medoo\Medoo;

class AutoRole
{
    public function getAutoRole($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];

        $data = $db->select('AutoRole', '*', ['ServerID' => $id]);
        $role = $db->select('Roles', '*', ['ServerID' => $id]);

        $result['autorole'] = $data;
        $result['roles'] = $role;

        Router::ExitWithSuccess($result);
    }

    public function setAutoRole($id) {
        if (!Discord::IsServerAuthorized($id)) {
            Router::ExitWithError(401, 'Unauthorized');
        };
        $db = $GLOBALS["database"];
        $post = Router::GetPostData();
        $validateData = Router::ValidateData($post['data'], 'Roles', $id, 'ID');
        
        if ($post['data'] == null) {
            $db->delete('AutoRole', ['AND' => ['ServerID' => $id]]);
            Router::ExitWithSuccess(null);
        }

        if (count($validateData) != count($post['data'])) {
            Router::ExitWithError(409, 'Conflict');
        }


        if ($post['data_april'] == true || $post['data_april'] == 'true') {
            $db->delete('AutoRole', ['AND' => ['ServerID' => $id]]);
        }
        
        if (count($validateData) >= 1) {

            $toInsert = array();

            foreach ($validateData as $key => $value) {
                array_push($toInsert, [ 'Role' => $value, 'ServerID' => $id, 'ID' => $key ]);
            }

            $result = $db->insert('AutoRole', $toInsert);

            if ($result->rowCount() != count($validateData)) {
                Router::ExitWithError(206, 'Updated partial content');
            }
            Router::ExitWithSuccess(null);
        }

        Router::ExitWithError(417, 'Expectation Failed');
    }
}

