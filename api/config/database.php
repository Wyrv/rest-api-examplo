<?php
class Database{
  
    // CREDENCIAIS DE CONEXÃO COM O BANCO
    private $host = "IP_HOST";
    private $db_name = "DB_NAME";
    private $username = "USER";
    private $password = "12345";
    public $conn;
  
    // GET CONEXÃO c/ DATABASE 
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>