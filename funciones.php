<?php

/* $Id: funciones.php,v 1.12 2015/05/18 22:40:40 sshevtsov Exp $
 *
 * Archivo de funciones para gestionar las fichadas en zona primaria.
 */

require_once('config.php');

function print_error($msj)
//{{{
{
  print('<p class="error">Error: ' . $msj);
}
//}}}

function open_db()
//{{{
{
  if (empty($_SESSION)) { session_start(); }
  try {
    $db = pg_connect($_SESSION['db']);
    if ($db===false) {
      print_error("ERROR: no se ha podido conectar a la base de datos");
    }
  } catch (Exception $e) {
    print_error($e->getMessage);
    return FALSE;
  }
  return $db;
}
//}}}

function vista_fichada_sin_salida()
{
  $db = open_db();
  $data = '';
  if ($db !== false) {
    $conds = "order by puesto_control_entrada,fecha_entrada";
    $sql = "SELECT * from fichada_sin_salida $conds;";
    $pg_e = pg_exec($db,$sql);
    if ( $pg_e !== false) {
      $data = '';
      $i = 0;
      while ($row = pg_fetch_assoc($pg_e)) {
        $css_class = ($i++ % 2 == 0) ? 'even_row' : 'odd_row';
        $data .= "<tr class='$css_class'><td>{$row['uid']}</td>";
        $data .= "<td>{$row['fecha_entrada']}</td>";
        $data .= "<td>{$row['nombre']}</td>";
        $data .= "<td>{$row['apellido']}</td>";
        $data .= "<td>{$row['dni']}</td>";
        $data .= "<td>{$row['nombre_puesto_entrada']}</td></tr>";
      }
    } else {
      print_error("ERROR: obteniendo datos de la vista fichada_sin_salida");
    }
  }
  pg_close($db);
  return $data;
}


function vista_personal_autorizado()
{
  $db = open_db();
  $data = '';
  if ($db !== false) {
    $conds = "order by apellido";
    $sql = "SELECT * from personas_autorizadas $conds;";
    $pg_e = pg_exec($db,$sql);
    if ( $pg_e !== false) {
      $data = '';
      $i = 0;
      while ($row = pg_fetch_assoc($pg_e)) {
        $css_class = ($i++ % 2 == 0) ? 'even_row' : 'odd_row';
        $data .= "<tr id='vpa_$i' class='$css_class' onclick='click_authorized_stuf(this.id)'>";
        $data .= "<td>{$row['nombre']}</td>";
        $data .= "<td>{$row['apellido']}</td>";
        $data .= "<td>{$row['dni']}</td>";
      }
    } else {
      print_error("ERROR: obteniendo datos de la tabla ");
    }
  }
  pg_close($db);
  return $data;
}



function vista_fichada_con_salida($dias = 10)
{
  $db = open_db();
  $data = '';
  if ($db !== false) {
    $conds = "where fecha_entrada > current_timestamp-'$dias day'::interval order by fecha_entrada";
    $sql = "SELECT * from fichada_con_salida $conds;";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $data = '';
      $i = 0;
      while ($row = pg_fetch_assoc($pg_e)) {
        $css_class = ($i++ % 2 == 0) ? 'even_row' : 'odd_row';
        $data .= "<tr class='$css_class'><td>{$row['uid']}</td>";
        $data .= "<td>{$row['fecha_entrada']}</td>";
        $data .= "<td>{$row['fecha_salida']}</td>";
        $data .= "<td>{$row['nombre']}</td>";
        $data .= "<td>{$row['apellido']}</td>";
        $data .= "<td>{$row['dni']}</td>";
        $data .= "<td>{$row['nombre_puesto_entrada']}</td>";
        $data .= "<td>{$row['nombre_puesto_salida']}</td></tr>";
      }
      $data .= "</table>";
    } else {
      print_error("ERROR: obteniendo datos de la vista fichada_con_salida");
    }
  }
  pg_close($db);
  return $data;
}

function vista_fichada_entre_fechas($fdesde, $fhasta)
{
  $db = open_db();
  $data = '';
  if ($db !== false) {
    $conds = "where fecha_entrada between '$fdesde 00:00:00'  and '$fhasta 23:59:59'";
    $sql = "SELECT * from fichada_ext $conds ;";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $data = '';
      $i = 0;
      while ($row = pg_fetch_assoc($pg_e)) {
        $css_class = ($i++ % 2 == 0) ? 'even_row' : 'odd_row';
        $data .= "<tr class='$css_class'><td>{$row['uid']}</td>";
        $data .= "<td>{$row['fecha_entrada']}</td>";
        $data .= "<td>{$row['fecha_salida']}</td>";
        $data .= "<td>{$row['nombre']}</td>";
        $data .= "<td>{$row['apellido']}</td>";
        $data .= "<td>{$row['dni']}</td>";
        $data .= "<td>{$row['nombre_puesto_entrada']}</td>";
        $data .= "<td>{$row['nombre_puesto_salida']}</td></tr>";
      }
    } else {
      print_error("ERROR: obteniendo datos de la vista fichada_con_salida $sql");
    }
  }
  pg_close($db);
  return $data;
}

function existe_persona_autorizada($dni)
{
  if (empty($dni)) {
    return false;
  }
  $db = open_db();
  $res = false;
  if ($db !== false) {
    $conds = "where dni=$dni";
    $sql = "SELECT * from personas_autorizadas $conds;";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $row = pg_fetch_assoc($pg_e);
      if ($row['dni'] == $dni) {
        $res = true;
      }
    }
  }
  pg_close($db);
  return $res;
}

function insert_persona_autorizada($dni, $nombre, $apellido)
{
  $db = open_db();
  $res = false;
  if ($db !== false) {
    $sql = "insert into personas_autorizadas (nombre, apellido, dni) values ('$nombre', '$apellido', $dni);";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $res = true;
    }
  }
  pg_close($db);
  return $res;
}

function update_persona_autorizada($dni, $old_dni, $nombre, $apellido)
{
  $db = open_db();
  $res = false;
  if (empty($old_dni)) {
    $old_dni = $dni;
  }
  if ($db !== false) {
    $sql = "update personas_autorizadas set dni=$dni, nombre='$nombre', apellido='$apellido' where dni=$old_dni;";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $res = true;
    }
  }
  pg_close($db);
  return $res;
}

function alta_persona_autorizada($dni, $old_dni, $nombre, $apellido)
{
  if (existe_persona_autorizada($dni) ||
      (isset($old_dni) && existe_persona_autorizada($old_dni))) {
    $res = update_persona_autorizada($dni, $old_dni, $nombre, $apellido);
  } else {
    $res = insert_persona_autorizada($dni, $nombre, $apellido);
  }
  return $res;
}

function eliminar_persona_autorizada($dni)
{
  $db = open_db();
  $res = false;
  if ($db !== false) {
    $sql = "delete from personas_autorizadas where dni=$dni;";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $res = true;
    }
  }
  pg_close($db);
  return $res;
}

function cambiar_clave($usuario, $clave)
{
  $db = open_db();
  $clave = preg_replace("/'/", "''", $clave);
  $res = false;
  if ($db !== false) {
    $sql = "alter role $usuario password '$clave';";
    $pg_e = pg_exec($db, $sql);
    if ( $pg_e !== false) {
      $res = true;
    }
  }
  pg_close($db);
  return $res;
}

?>
