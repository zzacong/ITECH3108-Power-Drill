
<?php

function html($str) {
  return htmlentities($str);
}

function query_execute($db, $query, $args = null) {
  $stmt = $db->prepare($query);
  if ($args) {
    foreach ($args as $param => $value) {
      $stmt->bindValue($param, $value);
    }
  }
  $stmt->execute();
  // print_r($stmt->errorInfo());
  return $stmt;
}

?>