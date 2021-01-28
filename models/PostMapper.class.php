
<?php

class PostMapper {

  private $conn = null;
  private $table = "PowerDrillPost";

  private const FIELDS = ['id', 'name', 'text', 'likes', 'post_date', 'reply_to'];
  private $query = "";
  private $bindings = array();
  private $stmt;
  private $andWhere = false;
  private $andOrder = false;


  function __construct(PDO $conn) {
    $this->conn = $conn;
  }


  private function valid_key($key) {
    return in_array($key, self::FIELDS);
  }


  private function reset() {
    $this->query = '';
    $this->bindings = array();
    $this->andWhere = false;
    $this->andOrder = false;
  }


  function execute(): PDOStatement {
    $this->stmt = $this->conn->prepare($this->query);
    if ($this->bindings) {
      foreach ($this->bindings as $i => $value) {
        $this->stmt->bindValue($i + 1, $value);
      }
    }
    $this->stmt->execute();
    return $this->stmt;
  }


  function selectAll(): self {
    $this->reset();
    $this->query = "
        SELECT * FROM $this->table
      ";
    return $this;
  }


  function select(...$args): self {
    $this->reset();
    if (!array_diff($args, self::FIELDS)) {
      $fields = implode(', ', $args);
      $this->query = "
        SELECT $fields
        FROM $this->table
      ";
      return $this;
    } else {
      throw new Exception("Invalid field in SELECT clause.", 1);
    }
  }


  function where($key, $value): self {
    if ($this->valid_key($key)) {
      $this->bindings[] = $value;
      $this->query .= $this->andWhere ? ' AND ' : ' WHERE ';
      $this->query .= "($key = ?)";
      $this->andWhere = true;
      return $this;
    } else {
      throw new Exception("Invalid key in WHERE clause.", 1);
    }
  }


  function whereNull($key): self {
    if ($this->valid_key($key)) {
      $this->query .= $this->andWhere ? ' AND ' : ' WHERE ';
      $this->query .= "($key IS NULL)";
      $this->andWhere = true;
      return $this;
    } else {
      throw new Exception("Invalid key in WHERE clause.", 1);
    }
  }


  function orderBy($key, $order): self {
    if ($this->valid_key($key) && in_array(strtolower($order), ['asc', 'desc'])) {
      $this->query .= $this->andOrder ? ' , ' : ' ORDER BY ';
      $this->query .= "$key $order";
      $this->andOrder = true;
      return $this;
    } else {
      throw new Exception("Invalid key in ORDER BY clause.", 1);
    }
  }


  function insert($args): self {
    $this->reset();
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
      throw new Exception("Invalid field in INSERT clause.", 1);
    }
  }


  public function update($args): self {
    $this->reset();
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
        throw new Exception("Invalid key in UPDATE clause.", 1);
      }
    }
  }
}

?>