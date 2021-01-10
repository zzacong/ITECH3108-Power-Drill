
<?php

class Database {

  private const DB_DSN = 'mysql:dbname=itech3108_30360914_a2;host=localhost;charset=utf8mb4';
  private const DB_USER = 'grapevine';
  private const DB_PASSWD = 'password';
  private $conn = null;

  public function __construct() {
    try {
      $this->conn = new PDO(self::DB_DSN, self::DB_USER, self::DB_PASSWD);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
      exit();
    }
  }

  public function get_connection() {
    return $this->conn;
  }
}

?>