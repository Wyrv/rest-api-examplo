<?php

// ATUALIZA REGISTROS NO BANCO
// PUT
// HEADER: Content-Type:application/json | Token: 62cb71d991c343.73925452
// BODY: JSON {username, email, secret}
// OBS.: Atualiza somente o registro cuja id corresponda a token fornecida no header.

header("Access-Control-Allow-Origin: http://localhost/auth/api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
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

$data = json_decode(file_get_contents("php://input"));

if ($user->token > 0){
    if(!empty($data->username) && !empty($data->email) && !empty($data->secret)){

        $user->id = $user->match_token();
        $user->email = filter_var($data->email, FILTER_VALIDATE_EMAIL);
        $user->username = filter_var($data->username, FILTER_SANITIZE_STRING);
        $user->secret = filter_var($data->secret, FILTER_SANITIZE_STRING);
    
        $check_email = $user->checkEmail();
        
        if(!$check_email){
            if($user->update()){
                http_response_code(200);
                echo json_encode(array("message" => "User was updated."));
            }
            
            else{
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update user."));
            }
        }
        else{
            http_response_code(422);
            echo json_encode(array("message" => "Fail, email already in use."));
        }
    }
    else{
        http_response_code(400);
        echo json_encode(array("message" => "Fail, incomplete dataset."));
    }
}
else{
    http_response_code(401);
    echo json_encode(array("message" => "Fail, missing authorization token."));
}


?>