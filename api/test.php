<?php
// $param = $_GET['sort'];

// $sort_params = explode(',', $param);

// foreach ($sort_params as $i => $param) {
//   $arr = explode(':', $param);
//   $sort_params[$i] = [$arr[0], $arr[1] ?? ''];
// }

// echo json_encode($sort_params);

// require_once 'config.php';

// try {
//   $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWD);
// } catch (PDOException $e) {
//   http_response_code(500);
//   header('Content-Type: application/json');
//   $res = ['error' => 'Connection failed: ' . $e->getMessage()];
//   echo json_encode($res, JSON_PRETTY_PRINT);
// }

// echo $_SERVER['QUERY_STRING'];

// $res = in_array('descs', ['asc', 'desc']);

$res = strtolower(preg_replace('/([A-Z])/', '_$1', $_GET['sort']));

echo json_encode($res);
