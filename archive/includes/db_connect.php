
<?php

try {
  $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWD);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

?>