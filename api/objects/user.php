<?php

//OBJETO USER

class user{

    private $conn;
    private $table_name = "users";
  
    public $token;
    public $id;
    public $username;
    public $email;
    public $secret;
  
    public function __construct($db){
        $this->conn = $db;
    }

    // ===============================================================================================================
    // CHECA SE O EMAIL DO USUARIO EXISTE NA BANCO DE DADOS
    // RETORNO:
    //  TRUE - EMAIL EXISTE
    //  FALSE - EMAIL NAO EXISTE NA BASE
    function checkEmail(){

        $query = "SELECT
                    email
                FROM
                    " . $this->table_name . "
                WHERE
                    email = ?
                LIMIT
                    0,1";
    
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
    
        $num = $stmt->rowCount();
        
        if($num > 0){
            return true; //EMAIL EM USO.
        }
        else{
            return false; //EMAIL LIVRE
        }
    }

    // ===============================================================================================================
    // INSERE NOVO USUARIO NO BANCO
    // RETORNO: 
    //  TRUE: INSERCAO EFETUADA COM SUCESSO
    //  FALSE: FALHA AO INSERIR
    function create(){

        // GERA TOKEN DE ACESSO SIMPLES (NÃO SEGURO)
        $token = uniqid("",true);
        $query = "INSERT INTO
                    " . $this->table_name . "
                    (username, email, secret, token)
                VALUES
                    (:username, :email, :secret, '$token')";

        $stmt = $this->conn->prepare($query);

        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->secret=htmlspecialchars(strip_tags($this->secret));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":secret", $this->secret);

        if($stmt->execute()){
            return true; //CADASTRO EFETUADO
        }
        return false; //CADASTRO NAO EFETUADO
    }
    // ===============================================================================================================
    // LER DO BANCO UM UNICO USUARIO
    // RETORNO: 
    //  PREENCHE DADOS PARA FORMAR OBJETO JSON PELO SCRIPT QUE CHAMA ESSE METODO
    function readSingle(){
    
        $query = "SELECT
                    id, username, email
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";
    
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
    
        $num = $stmt->rowCount();
        if ($num > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
        }
    }
    
    // ===============================================================================================================
    // LISTA DE USUARIOS
    // RETORNO
    //  RETORNA STATEMENT PARA MANIPULACAO E CRIACAO DE ARRAY COM OS USUARIOS
    function read(){

        $query = "SELECT 
                    id, username, email 
                FROM 
                    " . $this->table_name . " 
                ORDER BY 
                    id DESC";
        
        $stmt = $this->conn->prepare($query);
    
        $stmt->execute();
    
        return $stmt;
    }

    // ===============================================================================================================
    // ATUALIZAR USUARIO CADASTRADO
    // RETORNO: 
    //  TRUE: ATUALIZACAO REALIZADA
    //  FALSE: ATUALIZACAO FALHOU
    function update(){
    
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    secret = :secret
                WHERE
                    id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->secret=htmlspecialchars(strip_tags($this->secret));
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':secret', $this->secret);
        $stmt->bindParam(':id', $this->id);
    
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // ===============================================================================================================
    // APAGAR USUARIO CADASTRADO NO BANCO
    // RETORNO:
    //  TRUE: APAGADO COM SUCESSO
    //  FALSE: FALHA EM APAGAR O CONTATO
    function delete(){
    
        $query = "DELETE FROM 
                    " . $this->table_name . " 
                    WHERE 
                        id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
    
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // ===============================================================================================================
    // CHECAR CREDENCIAIS DE LOGIN
    // RETORNO:
    //  TRUE: LOGIN VALIDO (PREENCHE PARAMETROS PARA FORMAR OBJETO COM O TOKEN)
    //  FALSE: FALHA NO LOGIN
    function match_credentials(){
    
        $query = "SELECT 
                    token, id, email, username
                FROM 
                    " . $this->table_name . "
                WHERE 
                    email = :u
                AND 
                    secret = :p
                LIMIT 0,1";
    
        $stmt = $this->conn->prepare( $query );
    
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->secret=htmlspecialchars(strip_tags($this->secret));
    
        $stmt->bindParam(':u', $this->email);
        $stmt->bindParam(':p', $this->secret);
    
        $stmt->execute();
    
        $num = $stmt->rowCount();
    
        if($num>0){
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $this->token = $row['token'];
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->username = $row['username'];

            // Retorna True (Credenciais validas).
            return true;
        }
        // Fetorna Falso (Credenciais Invalidas)
        return false;
    }

    // ===============================================================================================================
    // VALIDACAO DO TOKEN
    // RETORNO: 
    //  RETORNA A ID DO REGISTRO PARA O CASO DO TOKEN VALIDO
    //  FALSE: TOKEN INVALIDO
    function match_token(){
    
        $query = "SELECT 
                    id, token
                FROM 
                    " . $this->table_name . "
                WHERE 
                    token = ?
                LIMIT 0,1";
    
        $stmt = $this->conn->prepare( $query );
        $this->token=htmlspecialchars(strip_tags($this->token));
        $stmt->bindParam(1, $this->token);
        $stmt->execute();
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->token = $row['token'];
            $this->id = $row['id'];
            return $this->id; // Retorna id do registro (sempre > 0).
        }
        return false; // Fetorna Falso (Token invalido).
    }

    // ===============================================================================================================
    // VALIDACAO DO ID + EMAIL
    // RETORNO: 
    //  TRUE: MATCH ID + EMAIL
    //  FALSE: NO MATCH ID + EMAIL
    function match_id_email(){
    
        $query = "SELECT 
                    id, email
                FROM 
                    " . $this->table_name . "
                WHERE 
                    email = ?
                AND
                    id = ?
                LIMIT 0,1";
    
        $stmt = $this->conn->prepare( $query );
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->email);
        $stmt->bindParam(2, $this->id);

        $stmt->execute();
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->email = $row['email'];
            $this->id = $row['id'];
            return true; // MATCH
        }
        return false; // NO MATCH
    }
}
?>