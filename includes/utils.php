
<?php

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

function extract_query_params(Post $post_mapper, $and = false) {
  $valid_query_params = ['id', 'name', 'likes', 'replyTo', 'sort'];

  if (array_diff(array_keys($_GET), $valid_query_params))
    throw new Exception("Invalid parameter.");

  $index = 0;
  foreach ($_GET as $key => $condition) {
    $key = camel_to_snake($key);
    if ($key === 'sort') continue;
    $and = $and || $index !== 0;
    if (strcasecmp($condition, 'null') === 0)
      $post_mapper->whereNull($key, $and);
    else
      $post_mapper->where($key, $condition, $and);
    $index++;
  }
}

function sortable(Post $post_mapper) {
  if (isset($_GET['sort'])) {
    $params = explode(',', $_GET['sort']);
    foreach ($params as $i => $value) {
      $arr = explode(':', $value);
      $key = camel_to_snake($arr[0]);
      // Default sorting to asc if not specified
      $order = $arr[1] ?? 'asc';
      $post_mapper->orderBy($key, $order, $i !== 0);
    }
  }
}

?>