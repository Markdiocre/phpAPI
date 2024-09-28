<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With,  Origin, Content-Type,");
header("Access-Control-Max-Age: 86400");
// ini_set('display_errors',0);
date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

$rootPath = $_SERVER["DOCUMENT_ROOT"];
$apiPath = $rootPath . "/new_api";

require_once($apiPath .'/configs/Connection.php');

//Models
require_once($apiPath .'/model/try.model.php');
require_once($apiPath .'/model/Global.model.php');

$db = new Connection();
$pdo = $db->connect();

//Model Instantiates
$global = new GlobalMethods();
$try = new Example($pdo, $global);

$req = [];
if (isset($_REQUEST['request']))
    $req = explode('/', rtrim($_REQUEST['request'], '/'));
else $req = array("errorcatcher");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($req[0] == 'try') {echo json_encode($try->hello());return;}
        if($req[0]=='getAllItem'){echo json_encode($try->getAll()); return;}
        break;
    case 'POST':
        $data_input = json_decode(file_get_contents("php://input"));
        if($req[0] == 'insert'){echo json_encode($try->insert($data_input)); return;}
        break;

    default:
        echo "albert";
        http_response_code(403);
        break;
}
