<?php
// header("Content-Type: application/json; charset=utf-8");
// header("Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: X-Requested-With");
// header("Access-Control-Max-Age: 86400");
// header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type, Authorization');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

$rootPath = $_SERVER["DOCUMENT_ROOT"];
$apiPath = $rootPath . "/new_api";

require_once($apiPath .'/configs/Connection.php');
require_once($apiPath . '/middleware/middleware.php');

//Models
require_once($apiPath .'/model/try.model.php');
require_once($apiPath .'/model/Global.model.php');
require_once($apiPath.'/model/Auth.model.php');
require_once($apiPath.'/model/User.model.php');

$db = new Connection();
$pdo = $db->connect();

//Model Instantiates
$rm = new ResponseMethods();

$try = new Example($pdo, $rm);
$auth = new Auth($pdo, $rm);

$middlewares = new Middleware($auth);

$user = new User($pdo, $rm, $middlewares);

$req = [];
if (isset($_REQUEST['request']))
    $req = explode('/', rtrim($_REQUEST['request'], '/'));
else $req = array("errorcatcher");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if($req[0]=='try') {echo json_encode($try->hello());return;} // <--Example
        
        // For Protected Routes
        if($req[0] == 'user'){
            if(empty($req[1])) {echo json_encode($user->getAll()); return ;}
            return;
        }

        $rm->notFound();
        break;
    case 'POST':
        $data_input = json_decode(file_get_contents("php://input"));
        if($req[0] == 'insert'){echo json_encode($try->insert($data_input)); return;} //<--Example
        if($req[0] == 'login') {echo json_encode($auth->login($data_input)); return;}
        if($req[0] == 'register') {echo json_encode($auth->register($data_input)); return;}

        //This handles non-existing routes
        $rm->notFound();
        break;

    default:
        $rm->notFound();
        break;
}
