<?php

use Medoo\Medoo;

class Router
{
    public static $database;

    public static $middleware = [
        \middleware\headers\VerifyHeaders::class,
        \middleware\token\VerifyToken::class,
        \middleware\CsrfToken\VerifyCsrfToken::class,
        \middleware\PostSecurity\ValidatePOST::class
    ];

    public static function start($router): void
    {

        #require_once '/../routes/web.php';
        $GLOBALS["database"] = new Medoo([
            'database_type' => getenv('database_type'),
            'database_name' => getenv('database_name'),
            'server' => getenv('server'),
            'username' => getenv('username'),
            'password' => getenv('password'),
            'charset' => getenv('charset'),
            'port' => getenv('port')
        ]);

        

        $router->before('GET|POST', '/.*', function() {
            self::LoadControllers();
            self::LoadMiddleware();
            // Initialize
        });

        include_once '../../routes/api.php';
        
    }

    public static function LoadControllers() {
        foreach (glob('../../app/controllers/*.php') as $filename) {
            include_once $filename;
        }
    }

    private static function LoadMiddleware() {
        foreach (glob('../../app/middleware/*.php') as $filename) {
            include_once $filename;
        }
        foreach (self::$middleware as $class) {
            new $class();
        }
    }

    /**
     * Get POST data
     */
    public static function GetPostData() {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Exit with error code
     */
    public static function ExitWithError($code, $message) {
        echo json_encode(array('status' => $code, 'message' => $message, 'data' => null));
        exit();
    }

    /**
     * Exit with success
     */
    public static function ExitWithSuccess($data) {
        echo json_encode(array('status' => 200, 'message' => 'success', 'data' => $data));
        exit();
    }

    /**
     * Verify if POST data are valid
     * @param $data Data to validate
     * @param $target Target Database Table
     * @param $id Idientier
     * @param $field Table Field
     */
    public static function ValidateData($data, $target , $id, $field) {
        $dbdatas = $GLOBALS["database"]->select($target, '*', ['ServerID' => $id]);
        if (!isset($data)) {
            return array();
        }
        $validateData = array();
        foreach ($dbdatas as $dbdata) {
            foreach ($data as $key => $value) {
                if (ctype_digit($key)) {
                    if ($dbdata[$field] == $key) {
                        $validateData[$dbdata[$field]] = $dbdata['Name'];
                    }
                }
            }
        }
        return $validateData;
    } 
}