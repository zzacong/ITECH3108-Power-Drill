
<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
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

if (isset($req_body->text)) {
  $post->name = $req_body->name;
  $post->text = $req_body->text;

  $stmt = $post->create();

  if ($stmt->rowCount()) {
    http_response_code(201);
    echo json_encode(['message' => 'Post created.']);
  } else {
    http_response_code(500);
    echo json_encode(['message' => 'Create post failed.']);
  }
} else {
  http_response_code(400);
  echo json_encode(['message' => 'Insufficent body.']);
}

?>