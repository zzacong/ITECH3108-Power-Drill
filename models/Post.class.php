
<?php

class Post {

  private $conn;
  private $table = "PowerDrillPost";

  private const FIELDS = ['id', 'name', 'text', 'likes', 'post_date', 'reply_to'];
  private $query = "";
  private $bindings = array();
  private $stmt;


  function __construct(PDO $db) {
    $this->conn = $db;
  }


  function valid_key($key) {
    return in_array($key, self::FIELDS);
  }


  function execute() {
    $this->stmt = $this->conn->prepare($this->query);
    echo json_encode($this->query) . PHP_EOL;
    if ($this->bindings) {
      echo json_encode(implode(', ', $this->bindings)) . PHP_EOL;
      foreach ($this->bindings as $i => $value) {
        $this->stmt->bindValue($i + 1, $value);
      }
    }
    $this->stmt->execute();
    // echo json_encode($this->stmt->errorInfo()) . PHP_EOL;
    return $this->stmt;
  }


  function selectAll() {
    $this->query = "
        SELECT * FROM $this->table
      ";
    return $this;
  }


  function select(...$args) {
    if (!array_diff($args, self::FIELDS)) {
      $fields = implode(', ', $args);
      $this->query = "
        SELECT $fields
        FROM $this->table
      ";
      return $this;
    } else {
      throw new Exception("Error Processing Request");
    }
  }


  function where($key, $value, $and = false) {
    if ($this->valid_key($key)) {
      $this->query .= $and ? " AND " : " WHERE ";
      $this->bindings[] = $value;
      $this->query .= "
          ($key = ?)
        ";
      return $this;
    } else {
      throw new Exception("Error Processing Request");
    }
  }

  function whereNull($key, $and = false) {
    if ($this->valid_key($key)) {
      $this->query .= $and ? " AND " : " WHERE ";
      $this->query .= "
        ($key IS NULL)
      ";
      return $this;
    } else {
      throw new Exception("Error Processing Request");
    }
  }

  function orderBy($key, $order, $and = false) {
    if ($this->valid_key($key) && in_array(strtolower($order), ['asc', 'desc'])) {
      $this->query .= $and ? ", " : " ORDER BY ";
      $this->query .= "
        $key $order
      ";
      return $this;
    } else {
      throw new Exception("Error Processing Request");
    }
  }


  function insert($args) {
    $keys = array_keys($args);
    $values = array_values($args);
    if (!array_diff($keys, self::FIELDS)) {
      $this->bindings = $values;
      $keys = implode(', ', $keys);
      $values = str_repeat('?, ', count($args));
      $values = rtrim($values, ', ');
    } else {
      throw new Exception("Error Processing Request");
    }
    $this->query = "
      INSERT INTO $this->table
        ($keys)
      VALUES
        ($values)
    ";

    return $this;
  }


  function makePost($post) {
    extract($post);
    $single_post  = [
      'id' => $id,
      'name' => $name,
      'text' => $text,
      'likes' => $likes,
      'postDate' => $post_date,
      'replyTo' => $reply_to,
      'links' => [
        'post' => $this->makePostUrl($id),
        'like' => $this->makeLikeUrl($id),
      ],
      'replies' => [],
    ];

    // Get a list of replies for the post
    $post_mapper = new Post($this->conn);
    $stmt = $post_mapper->selectAll()->where('reply_to', $id)->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $single_reply = $this->makeReply($row);
      $single_post['replies'][] = $single_reply;
    }

    return $single_post;
  }

  function makeReply($post) {
    extract($post);
    return [
      'id' => $id,
      'name' => $name,
      'text' => $text,
      'likes' => $likes,
      'postDate' => $post_date,
      'replyTo' => $reply_to,
      'links' => [
        'post' => $this->makePostUrl($id),
        'like' => $this->makeLikeUrl($id),
      ],
    ];
  }

  function makeUrl() {
    $base_url = '/api/posts/';
    return $base_url;
  }

  function makePostUrl($id) {
    return $this->makeUrl() . $id;
  }

  function makeLikeUrl($id) {
    return $this->makeUrl() . $id . '/like';
  }



  // function whereIn($key, $args) {
  //   if ($this->valid_key($key)) {
  //     $range = '';
  //     foreach ($args as $arg) {
  //       $range .= '?, ';
  //       $this->bindings[] = $arg;
  //     }
  //     $this->query .= "
  //       WHERE $key IN ($range)
  //     ";
  //     return $this;
  //   } else {
  //     throw new Exception("Error Processing Request");
  //   }
  // }





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