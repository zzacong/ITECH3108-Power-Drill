
<?php

function camel_to_snake($str) {
  return strtolower(preg_replace('/([A-Z])/', '_$1', $str));
}

function success_ok($res) {
  http_response_code(200);
  echo json_encode($res);
  exit();
}

function not_found($res, $force_object = false) {
  http_response_code(404);
  if ($force_object)
    echo json_encode($res, JSON_FORCE_OBJECT);
  else
    echo json_encode($res);
  exit();
}

function bad_request($res) {
  http_response_code(400);
  echo json_encode($res);
  exit();
}

function server_error($res) {
  http_response_code(500);
  echo json_encode($res);
  exit();
}

function handle_error(Exception $e) {
  $msg = [
    'error' => $e->getMessage(),
    'code' => $e->getCode(),
    'trace' => $e->getTraceAsString()
  ];

  if ($e->getCode() === '42000')
    // SQL Syntax Error
    server_error($msg);
  else
    // 400 Bad Request
    bad_request($msg);
}

function extract_query_params($get, PostMapper $post_mapper) {
  $valid_query_params = ['id', 'name', 'likes', 'replyTo', 'sort'];

  if (array_diff(array_keys($get), $valid_query_params))
    throw new Exception("Invalid parameter.");

  foreach ($get as $key => $condition) {
    $key = camel_to_snake($key);
    if ($key === 'sort') continue;
    if (strcasecmp($condition, 'null') === 0)
      $post_mapper->whereNull($key);
    else
      $post_mapper->where($key, $condition);
  }
}

function sortable($get, PostMapper $post_mapper) {
  if (isset($get['sort'])) {
    $params = explode(',', $get['sort']);
    foreach ($params as $value) {
      $arr = explode(':', $value);
      $key = camel_to_snake($arr[0]);
      // Default sorting to asc if not specified
      $order = $arr[1] ?? 'asc';
      $post_mapper->orderBy($key, $order);
    }
  }
}

?>