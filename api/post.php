
<?php

require_once '../includes/utils.php';

spl_autoload_register(function ($className) {
  require "../models/$className.class.php";
});

header('Content-Type: application/json; charset=utf-8');

// echo $_SERVER['REQUEST_URI'] . PHP_EOL;
// echo json_encode($_SERVER['PATH_INFO']) . PHP_EOL;
// echo $_SERVER['QUERY_STRING'] . PHP_EOL;

$router = new Router();


// ! GET /posts/:id
$router->get('#^/(\d+)$#', function ($params) {
  $db = new Database();
  $post_mapper = new Post($db->getConnection());
  $id = $params[1];
  try {
    $post_mapper->selectAll()->where('id', $id);
    extract_query_params($post_mapper, true);
    $stmt = $post_mapper->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $single_post = $post_mapper->makePost($row);

      success_response($single_post);
    } else {
      // The database has no posts
      fail_response([]);
    }
  } catch (Exception $e) {
    fail_response(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
  }
});


// ! GET /posts/toplevel/
$router->get('#^/toplevel/?$#', function () {
  $db = new Database();
  $post_mapper = new Post($db->getConnection());

  try {
    $post_mapper->selectAll()->whereNull('reply_to');
    extract_query_params($post_mapper, true);
    sortable($post_mapper);
    $stmt = $post_mapper->execute();

    $posts = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $single_post = $post_mapper->makePost($row);
      $posts[] = $single_post;
    }

    if ($posts) {
      success_response($posts);
    } else {
      // The database has no posts
      fail_response([]);
    }
  } catch (Exception $e) {
    fail_response(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
  }
});


// ! GET /posts/
$router->get('#^/$#', function () {
  $db = new Database();
  $post_mapper = new Post($db->getConnection());

  try {
    $post_mapper->selectAll();
    extract_query_params($post_mapper);
    sortable($post_mapper);
    $stmt = $post_mapper->execute();

    $posts = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $single_post = $post_mapper->makePost($row);
      $posts[] = $single_post;
    }

    if ($posts) {
      success_response($posts);
    } else {
      // The database has no posts
      fail_response([]);
    }
  } catch (Exception $e) {
    fail_response(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
  }
});

// ! POST /posts/
$router->post('#^/$#', function () {
  $db = new Database();
  $post_mapper = new Post($db->getConnection());

  $req_body = json_decode(file_get_contents("php://input"), true);
  echo json_encode($req_body) . PHP_EOL;

  $post = [
    'text' => $req_body['text'] ?? null,
    'name' => $req_body['name'] ?? 'anonymous' ? $req_body['name'] : 'anonymous',
  ];

  if (isset($req_body['replyTo']))
    $post['reply_to'] = $req_body['replyTo'];

  try {
    if (!$post['text']) throw new Exception('No text is given');
    $post_mapper->insert($post);
    $stmt = $post_mapper->execute();

    $lastId = $db->getConnection()->lastInsertId();
    $post_mapper = new Post($db->getConnection());
    $stmt = $post_mapper->selectAll()->where('id', $lastId)->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $single_post = $post_mapper->makePost($row);
      success_response($single_post);
    } else {
      fail_response([]);
    }
  } catch (Exception $e) {
    fail_response(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
  }
});


// ! Try routing
$router->route($_SERVER['PATH_INFO']);
// No route matches, route is invalid
fail_response([]);


// ! Functions


?>