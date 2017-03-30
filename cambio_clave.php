<?php
  require_once('funciones.php');
  if (empty($_SESSION)) { session_start(); }
  if (!empty($_SESSION['logged'])) {
    $content = file_get_contents('cambiar_clave.html');
    if (isset($_SESSION['pw_change_state']) && strcmp($_SESSION['pw_change_state'], 'fail') == 0) {
      $content = preg_replace('/#@@estado_cambio_clave@@#/', print_error("Fallo al cambiar la clave!"), $content);
    } else {
      $content = preg_replace('/#@@estado_cambio_clave@@#/', '', $content);
    }
    echo $content;
  }
?>

