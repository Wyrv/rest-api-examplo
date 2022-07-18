<?php

// LOGIN DO USUARIO
// POST
// HEADER: NONE
// BODY: JSON {email, secret}
// OBS.: 

header("Access-Control-Allow-Origin: http://localhost/auth/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once 'api/config/database.php';
include_once 'api/objects/user.php';
 
$database = new Database();
$db = $database->getConnection();
 
$user = new User($db);
 
$data = json_decode(file_get_contents("php://input")); // OBJETO JSON

if(!empty($data->email) && !empty($data->secret)){

    $user->email = filter_var($data->email, FILTER_VALIDATE_EMAIL);
    $user->secret = filter_var($data->secret, FILTER_SANITIZE_STRING);

    $check_email = $user->checkEmail(); // VERIFICA SE O EMAIL EXISTE DENTRE OS USUÁRIOS CADASTRADOS

    if($check_email){
        $login = $user->match_credentials(); // A AUTENTICAÇÃO NESTE EXEMPLO NÃO É SEGURA, É APENAS UMA CHAVE ALFANUMERICA SALVA NO BANCO.
        if($login > 0){
            $return_array = array(
                "token" => $user->token,
                "id" =>  $user->id,
                "email" => $user->email,
                "username" => $user->username
            );
            http_response_code(200);
            echo json_encode($return_array);
        }
        else{
            http_response_code(401);
            echo json_encode(array("message" => "Fail, invalid credentials.")); // USUÁRIO E SENHA SEM MATCH
        }
    }
    else{
        http_response_code(401);
        echo json_encode(array("message" => "Fail, invalid email.")); // EMAIL NÃO CADASTRADO NA BASE
    }
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "Fail, credentials missing.")); // CREDENCIAIS DE AUTENTICAÇÃO NÃO ENVIADAS
}

?>