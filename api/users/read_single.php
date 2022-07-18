<?php

// RETORNA USUÁRIO CUJA ID ESTÁ VINCULADA AO TOKEN FORNECIDO
// GET
// HEADER: Content-Type:application/json | Token: 62cb71d991c343.73925452
// BODY: none
// OBS.: Retorna somente usuário vinculado ao token fornecido

header("Access-Control-Allow-Origin: http://localhost/auth/api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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

$id_single = $user->match_token();

$user->readSingle();
  
if($user->username!=null && ($id_single > 0)){
    $user_array = array(
        "id" =>  $id_single,
        "username" => $user->username,
        "email" => $user->email
    );

    http_response_code(200);
    echo json_encode($user_array);
}
  
else{
    http_response_code(401);
    echo json_encode(array("message" => "Fail, invalid user or token."));
}
?>