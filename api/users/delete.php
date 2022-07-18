<?php

// APAGA REGISTRO DO BANCO
// DELETE
// HEADER: Content-Type:application/json | Token: 62cb71d991c343.73925452
// BODY: none
// OBS.: Apaga somente o registro cuja id corresponda a token fornecida no header.

header("Access-Control-Allow-Origin: http://localhost/auth/api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();
  
$user = new user($db);

$Headers = getallheaders();
$user->token = isset($Headers['Token']) ? $Headers['Token'] : 0;

$user->id = $user->match_token();

if ($user->token > 0 && $user->id > 0){
    if($user->delete()){
        http_response_code(200);
        echo json_encode(array("message" => "user was deleted."));
    }
    
    else{
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete user."));
    }
}
else{
    http_response_code(401);
    echo json_encode(array("message" => "Fail, wrong or missing authorization token."));
}
?>