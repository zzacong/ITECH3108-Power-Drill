
<?php

function query_execute($db, $query, $args = null) {
  $stmt = $db->prepare($query);
  if ($args) {
    foreach ($args as $param => $value) {
      // $type = gettype($value) === 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR;
      $stmt->bindValue($param, $value);
    }
  }
  $stmt->execute();
  return $stmt;
}

?>