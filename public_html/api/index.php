<?php
    // Include the Router class
    // @note: it's recommended to just use the composer autoloader when working with other packages too
    require __DIR__ . '/../../vendor/autoload.php';
    include_once '../../app/Discord.php';
    require_once '../../app/Router.php';


    
    session_start();

    // Create a Router
    $router = new \Bramus\Router\Router();

    Router::start($router);
    // Custom 404 Handler
    $router->set404(function () {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        echo '404, route not found!';
    });

    $router->get('/server/(\d+)/guildlog', function($id) {
        require_once('../../Database/database.php');
        header('Content-type: application/json');
        if (true) {
            $db = new Database();
            $guildLog = $db->GetGenericById($id, 'GuildLog');

            if($guildLog == "connection error") {
                SendGenericError('401', 'Failed');
                exit();
            }

            $result = array('status' => '200');
            $result['data'] = array_values($guildLog);
            echo json_encode($result);
            exit();
        } else {
            SendGenericError('403', 'Please provide a token.');
        }
    });

    



    // Thunderbirds are go!
    $router->run();

    function IsAuthorized($id) {

        if (isset($_SESSION['home_t'])) {
            try {

                $provider = Discord::GetProvider();
                $guilds = $provider->getGuildsDetails($_SESSION['home_t'])->toArray();

                $guildExist = false;

                if (count($guilds) >= 1) {
                    foreach ($guilds as $key) {
                        if (isset($key['id'])) {
                            if (isset($key['permissions'])) {
                                if ((($key['permissions'] & 0x20) != 0)) {
                                    if ($key['id'] == $id) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }

            } catch(Exception $e) {
                return false;
            }
            
        }
        return false;
    }

    function SendGenericError($id, $message) {
        echo json_encode(array('message' => $message, 'status' => $id));
        exit();
    }

    function array_flatten($array) { 
        if (!is_array($array)) { 
            return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
            $result = array_merge($result, array_flatten($value)); 
            } 
            else { 
            $result[$key] = $value; 
            } 
        } 
        return $result; 
    } 

    function GetGeneric($id, $table) {
        require_once('../../Database/database.php');
        include('../config.php');
        header('Content-type: application/json');
        $db = new Database();
        if (isset($_SESSION['home_t'])) {
            try {
                $guilds = $provider->getGuildsDetails($_SESSION['home_t'])->toArray();

                $guildExist = false;

                if (count($guilds) >= 1) {
                    foreach ($guilds as $key) {
                        if (isset($key['id'])) {
                            if (isset($key['permissions'])) {
                                if ((($key['permissions'] & 0x20) != 0)) {
                                    if ($key['id'] == $id) {
                                        $guildExist = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($guildExist) {
                    $datafeatch = $db->GetGenericById($id, $table);
                    if($datafeatch == "connection error") {
                        SendGenericError('401', 'Failed');
                        exit();
                    } else if ($datafeatch == 'no row') {
                        SendGenericError('202', 'no data');
                        exit();
                    }

                    $result = array('status' => '200');
                    $result['data'] = array_values($datafeatch);
                    echo json_encode($result);
                    exit();
                } else {
                    SendGenericError('402', 'Unauthorized');
                    exit();
                }

            } catch(Exception $e) {
                SendGenericError('401', 'Failed');
                exit();
            }
            
        } else {
            SendGenericError('403', 'Please provide a token.');
            exit();
        }
        // 402 => Unauthorized
        // 403 => Missing token
        // 404 => not found
        // 200 => success
        // 202 => success but not row
        // 401 => exception
    }
// EOF

