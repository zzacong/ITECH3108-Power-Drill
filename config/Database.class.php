
<?php

class Database {

  const DB_DSN = 'mysql:dbname=itech3108_30360914_a2;host=localhost;charset=utf8mb4';
  const DB_USER = 'grapevine';
  const DB_PASSWD = 'password';
  private $conn = null;

  public function __construct() {
    try {
      $this->conn = new PDO(self::DB_DSN, self::DB_USER, self::DB_PASSWD);
    } catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
    }
  }

  public function getConnection() {
    return $this->conn;
  }
}

?>