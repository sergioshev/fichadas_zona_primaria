/* $Id: metadatos.sql,v 1.5 2013-08-20 23:58:08 sshevtsov Exp $
 *
 * Archivo de metadatos.
 */

INSERT INTO elevador.puesto_control (nombre_corto, nombre) VALUES 
  ('ENTRPRIN', 'Entrada principal'),
  ('CALADO', 'Calado'),
  ('PLAYA','Playa de camiones');


INSERT INTO consorcio.puesto_control (nombre_corto, nombre) VALUES 
  ('ZONAPRIM', 'Zona primaria');
