
<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method not allowed', true, 405);
  exit();
}

require_once __DIR__ . '/../config/Database.class.php';
require_once __DIR__ . '/../models/Post.class.php';


$conn = (new Database())->get_connection();
$post = new Post($conn);

$stmt = $post->read();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
  $posts = [];
  foreach ($rows as $row) {
    extract($row);
    $single_post = [
      'id' => $id,
      'name' => $name,
      'text' => $text,
      'likes' => $likes,
      'postDate' => $post_date,
      'replyTo' => $reply_to,
    ];

    $posts['data'][] = $single_post;
  }
  http_response_code(200);
  echo json_encode($posts);
} else {
  http_response_code(404);
  echo json_encode(['message' => 'No posts found.']);
}

?>