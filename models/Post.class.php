
<?php

class Post {

  private $conn;
  private $table = "PowerDrillPost";

  public $id;
  public $name;
  public $text;
  public $likes;
  public $post_date;
  public $reply_to;

  function __construct($db) {
    $this->conn = $db;
  }

  function select($args) {
  }

  // public function read_all() {
  //   $query = "
  //     SELECT
  //       *
  //     FROM
  //       $this->table
  //     ";

  //   return query_execute($this->conn, $query);
  // }

  // public function read_top_level($sort_post_date = null, $sort_likes = null) {
  //   $query = "
  //     SELECT
  //       *
  //     FROM
  //       $this->table
  //     WHERE
  //       reply_to IS NULL
  //     ";

  //   if ($sort_post_date && $sort_likes) {
  //     $query = $query . " ORDER BY post_date $sort_post_date, likes $sort_likes";
  //   } elseif ($sort_post_date) {
  //     $query = $query . " ORDER BY post_date $sort_post_date";
  //   } elseif ($sort_likes) {
  //     $query = $query . " ORDER BY likes $sort_likes";
  //   }


  //   return query_execute($this->conn, $query);
  // }

  // public function read() {
  //   if (!$this->id) throw new Exception('Post id is empty');

  //   $query = "
  //     SELECT
  //       *
  //     FROM
  //       $this->table
  //     WHERE
  //       id = :id OR reply_to = :id
  //     ORDER BY
  //       post_date ASC 
  //       ";

  //   return query_execute($this->conn, $query, [':id' => $this->id]);
  // }

  // public function read_one() {
  //   if (!$this->id) throw new Exception('Post id is empty');

  //   $query = "
  //     SELECT
  //       *
  //     FROM
  //       $this->table
  //     WHERE
  //       id = :id
  //       ";

  //   return query_execute($this->conn, $query, [':id' => $this->id]);
  // }

  // public function create() {
  //   $this->name = html(strip_tags($this->name));
  //   $this->text = html(strip_tags($this->text));

  //   if (!$this->name) $this->name = 'anonymous';
  //   if (!$this->text) throw new Exception('Post text is empty');

  //   $query = "
  //     INSERT INTO $this->table
  //       (name, text)
  //     VALUES
  //       (:name, :text)
  //   ";

  //   $bindings = [
  //     ':name' => $this->name,
  //     ':text' => $this->text,
  //   ];
  //   return query_execute($this->conn, $query, $bindings);
  // }

  // public function reply() {
  //   $this->name = html(strip_tags($this->name));
  //   $this->text = html(strip_tags($this->text));
  //   $this->reply_to = html(strip_tags($this->reply_to));

  //   if (!$this->name) $this->name = 'anonymous';
  //   if (!$this->text) throw new Exception('Post text is empty');
  //   if (!$this->reply_to) throw new Exception('Post reply_to is empty');

  //   $query = "
  //     INSERT INTO $this->table
  //       (name, text, reply_to)
  //     VALUES
  //       (:name, :text, :reply_to)
  //   ";

  //   $bindings = [
  //     ':name' => $this->name,
  //     ':text' => $this->text,
  //     ':reply_to' => $this->reply_to,
  //   ];
  //   return query_execute($this->conn, $query, $bindings);
  // }

  // public function like() {
  //   $query = "
  //     UPDATE $this->table
  //     SET
  //       likes = likes + 1
  //     WHERE id = :id
  //   ";
  //   return query_execute($this->conn, $query, [':id' => $this->id]);
  // }

  // public function unlike() {
  //   $query = "
  //     SELECT likes FROM $this->table WHERE id = :id
  //   ";
  //   $stmt = query_execute($this->conn, $query, [':id' => $this->id]);
  //   $res = $stmt->fetch(PDO::FETCH_ASSOC);

  //   if ($res['likes'] > 0) {
  //     $query = "
  //       UPDATE $this->table
  //       SET
  //         likes = likes - 1
  //       WHERE id = :id
  //     ";
  //     return query_execute($this->conn, $query, [':id' => $this->id]);
  //   } else {
  //     return false;
  //   }
  // }
}

?>