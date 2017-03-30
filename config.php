<?php

/* $Id: config.php,v 1.7 2015/05/16 14:45:43 root Exp $
 *
 * Archivo de configuración.
 */

// Archivo de base de datos
#define("DBHOST","localhost");
define("DBHOST","eloso.terminalquequen.com.ar");
define("DBPORT","5434");
define("DBNAME","fichada");
define("DBUSER","#@@DBUSER@@#");
define("DBPASS","#@@DBPASS@@#");

define("DBCONN_STR","host='".DBHOST."' dbname='".DBNAME."' user='".DBUSER."' password='".DBPASS."' port=".DBPORT);

?>
