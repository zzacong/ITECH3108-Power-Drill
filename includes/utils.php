
<?php

function url_maker() {
  $base_url = '/api/posts/';
  return $base_url;
}

function post_url($id) {
  return url_maker() . $id;
}

function like_url($id) {
  return url_maker() . $id . '/like';
}

function post_maker($post) {
  extract($post);
  return [
    'id' => $id,
    'name' => $name,
    'text' => $text,
    'likes' => $likes,
    'postDate' => $post_date,
    'replyTo' => $reply_to,
    'links' => [
      'post' => post_url($id),
      'like' => like_url($id),
    ],
  ];
}
function camel_to_snake($str) {
  return strtolower(preg_replace('/([A-Z])/', '_$1', $str));
}

function fail_response($res) {
  http_response_code(404);
  echo json_encode($res);
  exit();
}

function success_response($res) {
  http_response_code(200);
  echo json_encode($res);
  exit();
}

?>