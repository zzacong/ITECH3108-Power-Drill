
<?php

class Router {
  private $handlers = array();

  function get($regex, $func) {
    $this->handlers[] = new Handler($regex, $func, 'GET');
  }

  function post($regex, $func) {
    $this->handlers[] = new Handler($regex, $func, 'POST');
  }

  function put($regex, $func) {
    $this->handlers[] = new Handler($regex, $func, 'PUT');
  }

  function delete($regex, $func) {
    $this->handlers[] = new Handler($regex, $func, 'DELETE');
  }

  function route($url) {
    // $params = null;
    foreach ($this->handlers as $handler) {
      if ($handler->handle($url)) {
        exit();
      }
    }
  }
}

?>