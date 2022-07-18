<?php

// CRIAR NOVO USUARIO
// POST
// HEADER: NONE
// BODY: JSON {username, email, secret}
// OBS.: 

header("Access-Control-Allow-Origin: http://localhost/auth/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/user.php';
  
$database = new Database();
$db = $database->getConnection();
  
$user = new user($db);
  
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->email) && !empty($data->secret)){

    $user->username = filter_var($data->username, FILTER_SANITIZE_STRING);
    $user->email =filter_var($data->email, FILTER_VALIDATE_EMAIL);
    $user->secret = filter_var($data->secret, FILTER_SANITIZE_STRING);

    $check_email = $user->checkEmail();

    if(!$check_email){
        if($user->create()){
            http_response_code(201);
            echo json_encode(array("message" => "Success, user created."));
        }
        else{
            http_response_code(503);
            echo json_encode(array("message" => "Fail, user was not created."));
        }
    }
    else{
        http_response_code(422);
        echo json_encode(array("message" => "Fail, email in use."));
    }
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "Fail, incomplete dataset."));
}

?>