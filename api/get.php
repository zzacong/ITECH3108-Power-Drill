
<?php

// spl_autoload_register(function ($className) {
//   echo "<p>Loading class: $className</p>";
//   require $className . '.class.php';
// });

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../config/Database.class.php';
require_once __DIR__ . '/../models/Post.class.php';


$db = (new Database())->getConnection();
$post = new Post($db);

$stmt = $post->read();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
// $rows = [];
if ($rows) {
  $res = ['data' => []];
  foreach ($rows as $row) {
    $post = [
      'name' => $row['name'],
      'text' => $row['text'],
      'likes' => $row['likes'],
      'postDate' => $row['post_date'],
      'replyTo' => $row['reply_to']
    ];

    $res['data'][] = $post;
  }
  http_response_code(200);
  echo json_encode($res);
} else {
  http_response_code(404);
  echo json_encode(['message' => 'No posts found.']);
}

?>