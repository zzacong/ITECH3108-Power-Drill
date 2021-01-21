
<?php

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method not allowed', true, 405);
  exit();
}

require_once __DIR__ . '/../config/Database.class.php';
require_once __DIR__ . '/../models/Post.class.php';


$conn = (new Database())->get_connection();
$post = new Post($conn);

$sorting = ['ASC', 'DESC', 'asc', 'desc'];

$sort_likes = isset($_GET['likes']) && in_array($_GET['likes'], $sorting) ? $_GET['likes'] : null;
$sort_post_date = isset($_GET['post_date']) && in_array($_GET['post_date'], $sorting) ? $_GET['post_date'] : null;

$stmt = $post->read_top_level($sort_post_date, $sort_likes);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
  http_response_code(404);
  echo json_encode(['error' => 'No posts found.']);
  exit();
}

$posts = ['data' => []];
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

?>