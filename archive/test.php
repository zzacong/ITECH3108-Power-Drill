<?php

header('Content-Type: application/json');
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

// $res = strtolower(preg_replace('/([A-Z])/', '_$1', $_GET['sort']));

// echo json_encode($res);

// echo json_encode(array_diff(['a', 'b', 'e'], ['a', 'b', 'c', 'd']));
// print_r(array_diff(['a', 'b', 'e'], ['a', 'b', 'c', 'd']));

spl_autoload_register(function ($className) {
  require "../models/$className.class.php";
});

$db = new Database();
$post = new Post($db->getConnection());

// $stmt = $post
//   ->selectAll()
//   ->whereIn('id', ['2', '3'])
//   ->execute();

$stmt = $post
  ->selectAll()
  ->whereNull('reply_to')
  ->where('id', 1, true)
  ->execute();

// $stmt = $post
//   ->selectAll()
//   ->whereNull('reply_to')
//   ->orderBy('likes', 'asc')
//   ->execute();

// $stmt = $post
//   ->selectAll()
//   ->whereNull('reply_to')
//   ->orderBy('likes', 'asc')
//   ->andOrderBy('post_date', 'desc')
//   ->execute();

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
// echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
