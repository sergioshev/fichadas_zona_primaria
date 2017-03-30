<?php
  require_once('funciones.php');
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  $content = '';
  if (! array_key_exists('logged',$_SESSION)) {
    $_SESSION['logged'] = null;
    $_SESSION['db_last_error'] = null;
  }
  #var_dump($_SESSION);
  if (empty($_SESSION['logged'])) {
    $content = file_get_contents('login.html');
    if (! empty($_SESSION['db_last_error'])) {
      $content = preg_replace('/#@@estado_login@@#/', 
                   print_error("Error:".$_SESSION['db_last_error']),
                   $content);
    } else {
      $content = preg_replace('/#@@estado_login@@#/', '', $content);
    }
  } else {
    $user = $_SESSION['user'];
    $content = file_get_contents('index.html');
    $fichadas_con_salida = vista_fichada_con_salida();
    $fichadas_sin_salida = vista_fichada_sin_salida();
    $personal_autorizado = vista_personal_autorizado();
    $mensaje_operacion = '';
    if (isset($_SESSION['operation_msg'])) {
      $mensaje_operacion = $_SESSION['operation_msg'];
      unset($_SESSION['operation_msg']);
    }
    $content = preg_replace('/#@@fichada_con_salida@@#/', $fichadas_con_salida, $content);
    $content = preg_replace('/#@@fichada_sin_salida@@#/', $fichadas_sin_salida, $content);
    $content = preg_replace('/#@@conectado_como@@#/', $user, $content);
    $content = preg_replace('/#@@personal_autorizado@@#/', $personal_autorizado, $content);
    $content = preg_replace('/#@@mensaje_operacion@@#/', $mensaje_operacion, $content);
  }
  echo $content;
?>

