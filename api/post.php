
<?php

require_once '../includes/utils.php';

spl_autoload_register(function ($className) {
  require "../models/$className.class.php";
});

header('Content-Type: application/json; charset=utf-8');

$router = new Router();


// ! GET /posts/:id
// ! GET one post with associated replies
$router->get('#^/(\d+)/?$#', function ($params) {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());
  $id = $params[1];

  try {
    $post_mapper
      ->selectAll()
      ->where('id', $id);

    extract_query_params($_GET, $post_mapper);

    $stmt = $post_mapper->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $post = (new Post($row))->setMapper($post_mapper);
      $single_post = $post->__serialize();
      $single_post['replies'] = $post->getReplies();
      success_ok($single_post);
    }
    // No post with this ID
    not_found(null);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! GET /posts/:id/replies
// ! GET an array of replies of one post
$router->get('#^/(\d+)/replies/?$#', function ($params) {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());
  $id = $params[1];

  try {
    $stmt = $post_mapper
      ->select('id')
      ->where('id', $id)
      ->execute();

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      $post_mapper
        ->selectAll()
        ->where('reply_to', $id);

      extract_query_params($_GET, $post_mapper);
      sortable($_GET, $post_mapper);

      $stmt = $post_mapper->execute();

      $posts = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $post = new Post($row);
        $single_post = $post->__serialize();
        $posts[] = $single_post;
      }

      $post = (new Post())->setId($id);
      $res = [
        'data' => $posts,
        'links' => [
          'self' => $post->makeRepliesUri(),
          'create' => $post->makePostUri(),
          'parent' => $post->makePostUri(),
        ]
      ];
      success_ok($res);
    } else {
      // The post has no replies
      not_found(null);
    }
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! GET /posts
// ! GET array of posts with associated replies
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
      $post = new Post($row);
      $posts[] = $post->__serialize();
    }

    $res = [
      'data' => $posts,
      'links' => [
        'self' => Post::BASE_URI,
        'create' => Post::BASE_URI
      ]
    ];

    success_ok($res);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! POST /posts/:id
// ! Create a reply to a post
$router->post('#^/(\d+)/?$#', function ($params) {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());

  $req_body = json_decode(file_get_contents("php://input"), true);
  $post = [
    'text' => $req_body['text'] ?? null,
    'name' => $req_body['name'] ?? '',
    'reply_to' => $params[1],
  ];

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
      $post = new Post($row);
      success_created($post->__serialize());
    }
    not_found([], true);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! POST /posts
// ! Create a post
$router->post('#^/$#', function () {
  $db = new Database();
  $post_mapper = new PostMapper($db->getConnection());

  $req_body = json_decode(file_get_contents("php://input"), true);
  $post = [
    'text' => $req_body['text'] ?? null,
    'name' => $req_body['name'] ?? '',
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
      $post = new Post($row);
      success_created($post->__serialize());
    }
    not_found([], true);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! POST /posts/:id/like
// ! Like a post
$router->post('#^/(\d+)/like/?$#', function ($params) {
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
      $post = new Post($row);
      success_ok($post->__serialize());
    }
    not_found(null);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! POST /posts/:id/unlike
// ! Unlike a post
$router->post('#^/(\d+)/unlike/?$#', function ($params) {
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
      $post = new Post($row);
      success_ok($post->__serialize());
    }
    not_found(null);
  } catch (Exception $e) {
    handle_error($e);
  }
});


// ! Try routing
$router->route($_SERVER['PATH_INFO']);

// ! No matched route found, return a 404 Not Found respond
not_found(null);


?>