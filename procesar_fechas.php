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

 
  function analyse_date($date){
    $result = false;
    $val_expr = "/^([0-9]{4,4})-([0-9]{2,2})-([0-9]{2,2})$/";
    
    $res = preg_match($val_expr, $date, $matches);
    if ($res === false || $res = 0 || empty($matches)) return $result;
    $result = array();
    $result['year'] = $matches[1];
    $result['month'] = $matches[2];
    $result['day'] = $matches[3];
    return $result;
  }

  session_start();
  $from_date = $_GET['fdesde'];
  $to_date = $_GET['fhasta'];
 
  $parsed_from_date = analyse_date($from_date);
  $parsed_to_date = analyse_date($to_date);
  if ($parsed_from_date == false || $parsed_to_date == false) {
    print_error("Alguna de las fechas esta mal ingresada. Verifique el formato sea correcto.");
  } else {
    $content = generate_form($parsed_from_date, $parsed_to_date);
    echo $content;
  }
?>
