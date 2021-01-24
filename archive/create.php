
<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method not allowed', true, 405);
  exit();
}

require_once __DIR__ . '/../config/Database.class.php';
require_once __DIR__ . '/../models/Post.class.php';


$conn = (new Database())->get_connection();
$post = new Post($conn);

$req_body = json_decode(file_get_contents("php://input"));

if (!isset($req_body->text)) {
  http_response_code(400);
  echo json_encode(['error' => 'No post text given.']);
  exit();
}

try {
  $post->name = $req_body->name ?? '';
  $post->reply_to = $req_body->replyTo ?? '';
  $post->text = $req_body->text;

  $stmt = isset($req_body->replyTo) ? $post->reply() : $post->create();

  if ($stmt->rowCount()) {
    $post->id = $conn->lastInsertId();
    $stmt = $post->read_one();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
      $single_post = [
        'id' => $id,
        'name' => $name,
        'text' => $text,
        'postDate' => $post_date,
        'likes' => $likes,
        'replyTo' => $reply_to,
      ];
      http_response_code(200);
      echo json_encode(['data' => $single_post]);
    }
  } else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->errorInfo()[2]]);
  }
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['error' => $e->getMessage()]);
}



?>