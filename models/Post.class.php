
<?php

require_once __DIR__ . '/../utils/utils.php';

class Post {

  private $conn;
  private $table = "PowerDrillPost";

  public $id;
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
        $this->table
      WHERE
        reply_to IS NULL
      ORDER BY
        post_date DESC";

    return query_execute($this->conn, $query);
  }

  public function read_one() {
    $query = "
      SELECT
        *
      FROM
        $this->table
      WHERE
        id = :id OR reply_to = :id
      ORDER BY
        post_date DESC
    ";

    return query_execute($this->conn, $query, [':id' => $this->id]);
  }

  public function create() {
    $query = "
      INSERT INTO $this->table
        (name, text)
      VALUES
        (:name, :text)
    ";

    $this->name = html(strip_tags($this->name));
    $this->text = html(strip_tags($this->text));

    $bindings = [':name' => $this->name, ':text' => $this->text];
    return query_execute($this->conn, $query, $bindings);
  }

  public function like() {
    $query = "
      UPDATE $this->table
      SET
        likes = likes + 1
      WHERE id = :id
    ";

    $stmt = query_execute($this->conn, $query, [':id' => $this->id]);
    return $stmt->rowCount() ? true : false;
  }

  public function unlike() {
    $query = "
      SELECT likes FROM $this->table WHERE id = :id
    ";
    $stmt = query_execute($this->conn, $query, [':id' => $this->id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res['likes'] > 0) {

      $query = "
        UPDATE $this->table
        SET
          likes = likes - 1
        WHERE id = :id
      ";
      $stmt = query_execute($this->conn, $query, [':id' => $this->id]);
      return $stmt->rowCount() ? true : false;
    } else {
      return false;
    }
  }
}

?>