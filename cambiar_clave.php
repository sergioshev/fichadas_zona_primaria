<?php
  include("config.php");
  include("funciones.php");
  if (empty($_SESSION)) { session_start(); }
  if (!empty($_SESSION['logged'])) {
    $user = $_SESSION['user'];
    $clave = $_POST['new_passwd'];
    $rep_clave = $_POST['rep_new_passwd'];
    $_SESSION['pw_change_state'] = "";
    if (strcmp($clave, $rep_clave) != 0) {
      $_SESSION['pw_change_state'] = "fail";
    } else {
      $ret = cambiar_clave($user, $clave);
      if ($ret != true ) {
        $_SESSION['pw_change_state'] = "fail";
      }
    }
    if ($_SESSION['pw_change_state'] == "fail") {
      header("Location: cambio_clave.php");
    } else {
      header("Location: salir.php");
    }
  } else {
    header("Location: index.php");
  }
?>
