
<?php

require_once __DIR__ . '/../utils/utils.php';

class Post {

  private $conn;
  private $table_name = "PowerDrillPost";

  public $name;
  public $text;
  public $likes;
  public $post_date;
  public $reply_to;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function read() {
    $query = "
      SELECT
        *
      FROM
        $this->table_name p
      ORDER BY
        p.post_date DESC";

    return query_execute($this->conn, $query);
  }
}

?>