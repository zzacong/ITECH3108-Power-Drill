
<?php

class Handler {

  private $regex;
  private $func;
  private $method = null;


  public function __construct($regex, $func, $method) {
    $this->regex = $regex;
    $this->func = $func;
    $this->method = $method;
  }


  public function handle($url) {
    if ($this->method === $_SERVER['REQUEST_METHOD'] && preg_match($this->regex, $url, $params)) {
      ($this->func)($params);
      return TRUE;
    }
    return FALSE;
  }
}

?>