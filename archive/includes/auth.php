
<?php

function login($username) {
  $_SESSION['username'] = $username;
}

function logout() {
  unset($_SESSION['username']);
}

function authenticated() {
  return isset($_SESSION['username']);
}

function current_user() {
  return $_SESSION['username'];
}

function auth_redirect() {
  if (!authenticated()) {
    header('Location: login.php');
    exit();
  }
}

?>