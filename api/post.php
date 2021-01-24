
<?php

require_once '../includes/db_connect.php';
require_once '../includes/utils.php';

spl_autoload_register(function ($className) {
  require "../models/$className.class.php";
});

header('Content-Type: application/json; charset=utf-8');

// echo $_SERVER['REQUEST_URI'] . PHP_EOL;
// echo $_SERVER['PATH_INFO'] . PHP_EOL;
// echo $_SERVER['QUERY_STRING'] . PHP_EOL;

$router = new Router();


// ! GET /posts/toplevel
$router->get('/^\/toplevel$/', function () {
  $db = new Database();

  $query = "
  SELECT * FROM PowerDrillPost WHERE reply_to IS NULL
  ";
  $query = $query . sortable();

  $stmt = $db->query_execute($query);
  $posts = array();

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $single_post = post_maker($row);
    $posts[] = $single_post;
  }

  if ($posts) {
    success_response($posts);
  } else {
    // No top-level posts found
    fail_response([]);
  }
});


// ! GET /
$router->get('/^\/$/', function () {
  $db = new Database();

  $query = "
    SELECT * FROM PowerDrillPost
  ";
  $query = $query . sortable();

  $stmt = $db->query_execute($query);

  $posts = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $single_post = post_maker($row);
    $posts[] = $single_post;
  }

  if ($posts) {
    success_response($posts);
  } else {
    // The database has no posts
    fail_response([]);
  }
});

// Try router
$router->route($_SERVER['PATH_INFO']);
// No route matches, route is invalid
fail_response([]);

function sortable() {
  if (isset($_GET['sort'])) {
    $params = explode(',', $_GET['sort']);
    $sort_keys = ['likes', 'post_date'];
    $sort_values = ['asc', 'desc', null];
    foreach ($params as $param) {
      $arr = explode(':', $param);

      $sort_by = camel_to_snake($arr[0]);
      $order_by = strtolower($arr[1] ?? null);

      if (in_array($sort_by, $sort_keys) && in_array($order_by, $sort_values)) {
        $sorts[$sort_by] = $order_by ?? 'asc';
      } else {
        fail_response([]);
      }
    }
  }

  if (isset($sorts)) {
    $query = "ORDER BY ";
    foreach ($sorts as $field => $order) {
      $query = $query . "$field $order, ";
    }
    return rtrim($query, ', ');
  }
}

?>