<?php
  include("config.php");
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  if (empty($_SESSION['logged'])) {
    $user = $_POST['user'];
    $passwd = $_POST['passwd'];
    $conn_str = DBCONN_STR;
    $conn_str = preg_replace("/#@@DBUSER@@#/", $user, $conn_str);
    $conn_str = preg_replace("/#@@DBPASS@@#/", $passwd, $conn_str);
    try {
      $db = pg_connect($conn_str);
      if (! $db) {
        $_SESSION['db'] = false;
        $_SESSION['db_last_error'] = 'fail';
        $_SESSION['control'] = 'no se pudo conectar';
        $_SESSION['logged'] = false;
      } else {
        $_SESSION['db'] = $conn_str;
        $_SESSION['db_last_error'] = null;
        $_SESSION['logged'] = true;
        $_SESSION['user'] = $user;
      }
    } catch (Exception $e) {
      $_SESSION['db'] = false;
      $_SESSION['db_last_error'] = $e->getMessage;
      $_SESSION['logged'] = false;
    } 
  }
  header("Location: index.php");
?>
