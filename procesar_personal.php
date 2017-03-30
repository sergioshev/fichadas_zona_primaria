<?php
  require_once('funciones.php');

  function generate_form($from_date, $to_date)
  {
    $content = file_get_contents('procesar_fechas.htmlt');
    $tabla = vista_fichada_entre_fechas(implode('-',$from_date), 
                                       implode('-',$to_date));
    $content = preg_replace('/#@@fichada_entre_fechas@@#/', $tabla, $content); 
    return $content;
  }

 
  function check_dni($data){
    if (preg_match( "/^\d{8,}/", $data) == 1 ) 
      return true;
    return false;
  }


  function escape_data($data){
    return str_replace( "'", "''", $data);
  }

  $dni = $_POST['dni'];
  if (empty($_SESSION)) { session_start(); }
  if (! check_dni($dni)) {
    $_SESSION['operation_msg'] = 'DNI invalido';
  } else {
    $name = escape_data($_POST['name']);
    $surname = escape_data($_POST['surname']);
    $old_dni = null;
    if (isset($_POST['old_dni'])) {
      $old_dni = $_POST['old_dni'];
    }
    $res = false;
    if (isset($_POST['send_person_data'])) {
      $res = alta_persona_autorizada($dni, $old_dni, $name, $surname);
    } else {
      if (isset($_POST['delete_person_data'])) {
        $res = eliminar_persona_autorizada($dni);
      }
    }
    if ($res == true) {
      $_SESSION['operation_msg'] = 'Operacion realizada correctamente';
    } else {
      $_SESSION['operation_msg'] = 'Fallo al realizar la operacion';
    }
  }
  header("Location: index.php");
?>
