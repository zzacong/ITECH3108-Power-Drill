
<?php

class Database {

  private const DB_DSN = 'mysql:dbname=itech3108_30360914_a2;host=localhost;charset=utf8mb4';
  private const DB_USER = 'powerdrill';
  private const DB_PASSWD = 'password';
  private $conn = null;


  public function __construct() {
    try {
      $this->conn = new PDO(self::DB_DSN, self::DB_USER, self::DB_PASSWD);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      http_response_code(500);
      header('Content-Type: application/json; charset=utf-8');
      $res = ['error' => 'Connection failed: ' . $e->getMessage()];
      echo json_encode($res, JSON_PRETTY_PRINT);
      exit();
    }
  }


  function getConnection(): PDO {
    return $this->conn;
  }
}

?>