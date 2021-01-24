
<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
  header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method not allowed', true, 405);
  exit();
}

require_once __DIR__ . '/../config/Database.class.php';
require_once __DIR__ . '/../models/Post.class.php';

$conn = (new Database())->getConnection();
$post = new Post($conn);

$req_body = json_decode(file_get_contents("php://input"));

if (!isset($req_body->id)) {
  http_response_code(400);
  echo json_encode(['error' => 'No post id given.']);
  exit();
}

$post->id = $req_body->id;
if ($post->unlike()) {
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
    exit();
  }
}

http_response_code(200);
echo json_encode(['data' => false]);

?>