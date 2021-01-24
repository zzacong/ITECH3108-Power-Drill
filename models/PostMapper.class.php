
<?php

class PostMapper {

  private $conn = null;
  private $table = "PowerDrillPost";

  private const FIELDS = ['id', 'name', 'text', 'likes', 'post_date', 'reply_to'];
  private $query = "";
  private $bindings = array();
  private $stmt;


  function __construct(PDO $conn) {
    $this->conn = $conn;
  }


  function valid_key($key) {
    return in_array($key, self::FIELDS);
  }


  function execute(): PDOStatement {
    $this->stmt = $this->conn->prepare($this->query);
    echo json_encode($this->query) . PHP_EOL;
    if ($this->bindings) {
      echo json_encode(implode(', ', $this->bindings)) . PHP_EOL;
      foreach ($this->bindings as $i => $value) {
        $this->stmt->bindValue($i + 1, $value);
      }
    }
    $this->stmt->execute();
    return $this->stmt;
  }


  function selectAll(): PostMapper {
    $this->bindings = array();
    $this->query = "
        SELECT * FROM $this->table
      ";
    return $this;
  }


  function select(...$args): PostMapper {
    $this->bindings = array();
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


  function where($key, $value, $and = false): PostMapper {
    if ($this->valid_key($key)) {
      $this->bindings[] = $value;
      $this->query .= $and ? 'AND' : 'WHERE';
      $this->query .= "
          ($key = ?)
        ";
      return $this;
    } else {
      throw new Exception("Error processing where.");
    }
  }


  function whereNull($key, $and = false): PostMapper {
    if ($this->valid_key($key)) {
      $this->query .= $and ? " AND " : " WHERE ";
      $this->query .= "
        ($key IS NULL)
      ";
      return $this;
    } else {
      throw new Exception("Error processing whereNull.");
    }
  }


  function orderBy($key, $order, $and = false): PostMapper {
    if ($this->valid_key($key) && in_array(strtolower($order), ['asc', 'desc'])) {
      $this->query .= $and ? ", " : " ORDER BY ";
      $this->query .= "
        $key $order
      ";
      return $this;
    } else {
      throw new Exception("Error ordering rows.");
    }
  }


  function insert($args): PostMapper {
    $this->bindings = array();
    $keys = array_keys($args);
    $values = array_values($args);

    if (!array_diff($keys, self::FIELDS)) {
      $this->bindings = $values;
      $keys = implode(', ', $keys);
      $values = str_repeat('?, ', count($args));
      $values = rtrim($values, ', ');

      $this->query = "
        INSERT INTO $this->table
          ($keys)
        VALUES
          ($values)
      ";
      return $this;
    } else {
      throw new Exception("Error inserting row.");
    }
  }


  public function update($args): PostMapper {
    $this->bindings = array();
    $this->query = "
      UPDATE $this->table
      SET
    ";

    foreach ($args as $key => $value) {
      if ($this->valid_key($key)) {
        $this->bindings[] = $value;
        $this->query .= "$key = ?, ";
        $this->query = rtrim($this->query, ', ');
        return $this;
      } else {
        throw new Exception("Error updating row.");
      }
    }
  }
}

?>