
<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
  header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method not allowed', true, 405);
  exit();
}

require_once __DIR__ . '/../config/Database.class.php';
require_once __DIR__ . '/../models/Post.class.php';

$conn = (new Database())->get_connection();
$post = new Post($conn);

$req_body = json_decode(file_get_contents("php://input"));

if (!isset($req_body->id)) {
  http_response_code(400);
  echo json_encode(['message' => 'No post id given.']);
  exit();
}

$post->id = $req_body->id;
$stmt = $post->like();

if ($stmt->rowCount()) {
  echo json_encode(['data' => true]);
} else {
  echo json_encode(['data' => false]);
}

?>