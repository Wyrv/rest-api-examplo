<?php

// RETORNA ARRAY COM USUÁRIOS CADASTRADOS
// GET
// HEADER: Content-Type:application/json | Token: 62cb71d991c343.73925452
// BODY: none
// OBS.: Requer um token válido de qualquer um dos usuários cadastrados no sistema (Não mostra campo de senha)

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

if($user->match_token() > 0){

    $stmt = $user->read();
    $num = $stmt->rowCount();
    
    if($num > 0){
    
        $users_array=array();
        $users_array["records"]=array();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);
    
            $user_item=array(
                "id" => $id,
                "username" => $username,
                "email" => $email
            );
    
            array_push($users_array["records"], $user_item);
        }
    
        http_response_code(200);
        echo json_encode($users_array);
    }
    else{
        http_response_code(404);
        echo json_encode(array("message" => "Fail, no users found." ));
    }
}
else{
    http_response_code(401);
    echo json_encode(array("message" => "Fail, invalid token."));
}