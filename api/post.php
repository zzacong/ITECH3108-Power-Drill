
<?php

require_once '../includes/utils.php';

spl_autoload_register(function ($className) {
  require "../models/$className.class.php";
});

header('Content-Type: application/json; charset=utf-8');

$router = new Router();


// ! GET /posts/:id
$router->get('#^/(\d+)/?$#', function ($params) {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());
  $id = $params[1];

  try {
    $post_mapper
      ->selectAll()
      ->where('id', $id);

    extract_query_params($_GET, $post_mapper, true);

    $stmt = $post_mapper->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $post = new Post($row, $post_mapper);
      success_ok($post->__serialize());
    }
    // No post with this ID
    not_found(null);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! GET /posts/
$router->get('#^/$#', function () {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());

  try {
    $post_mapper->selectAll();

    extract_query_params($_GET, $post_mapper);
    sortable($_GET, $post_mapper);

    $stmt = $post_mapper->execute();

    $posts = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $post = new Post($row, $post_mapper);
      $posts[] = $post->__serialize();
    }

    if ($posts)
      success_ok($posts);
    else
      not_found([]); // The database has no posts

  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! POST /posts/
$router->post('#^/$#', function () {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());

  $req_body = json_decode(file_get_contents("php://input"), true);
  $post = [
    'text' => $req_body['text'] ?? null,
    'name' => $req_body['name'] ?? 'anonymous' ? $req_body['name'] : 'anonymous',
  ];

  if (isset($req_body['replyTo']))
    $post['reply_to'] = $req_body['replyTo'];

  try {
    if (!$post['text'])
      throw new Exception('No text is given');

    $stmt = $post_mapper
      ->insert($post)
      ->execute();

    $lastId = $db->getConnection()->lastInsertId();
    $stmt = $post_mapper
      ->selectAll()
      ->where('id', $lastId)
      ->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $post = new Post($row, $post_mapper);
      success_ok($post->__serialize());
    }
    not_found([], true);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! PUT /posts/:id/like
$router->put('#^/(\d+)/like/?$#', function ($params) {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());

  $id = $params[1];

  try {
    $stmt = $post_mapper
      ->select('likes')
      ->where('id', $id)
      ->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $likes = $row['likes'];

      $post_mapper
        ->update(['likes' => ++$likes])
        ->where('id', $id)
        ->execute();

      $stmt = $post_mapper
        ->selectAll()
        ->where('id', $id)
        ->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $post = new Post($row, $post_mapper);
      success_ok($post->__serialize());
    }
    not_found(null);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! PUT /posts/:id/unlike
$router->put('#^/(\d+)/unlike/?$#', function ($params) {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());

  $id = $params[1];

  try {
    $stmt = $post_mapper
      ->select('likes')
      ->where('id', $id)
      ->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $likes = $row['likes'];
      if ($likes > 0)
        $post_mapper
          ->update(['likes' => --$likes])
          ->where('id', $id)
          ->execute();

      $stmt = $post_mapper
        ->selectAll()
        ->where('id', $id)
        ->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $post = new Post($row, $post_mapper);
      success_ok($post->__serialize());
    }
    not_found(null);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! Try routing
$router->route($_SERVER['PATH_INFO']);
// No route matches, route is invalid
not_found([], true);


?>