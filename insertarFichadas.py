from  zkemapi import zkem
import pprint
from config import FICHERO_CFG
from config import DB_CFG
from config import DESCARGA
from config import PRO_ROOT
from config import F_PURGADO_PREFIX
import datetime
import getopt
import pgdb
from exceptions import StandardError
import sys

fichero=zkem()

if len(DESCARGA) < 1:
  print "No se han definido ficheros para la descarga"
  print "Revisar la configuracion del conjunto DESCARGA"
  sys.exit(0)

print "Conectandose a la base de datos"
try:
  cx = pgdb.connect( host = DB_CFG['host']+':'+DB_CFG['port'], user=DB_CFG['user'], password=DB_CFG['pass'], database=DB_CFG['db'] )
except StandardError as e:
  print "Error: %s" % (e.message)
  sys.exit(1)

# me fijo si esta el parametro para purgar los ficheros
purgar = False;
parametros, extra_args = getopt.getopt(sys.argv[1:],'p')
for (parametro,valor) in parametros:
  if (parametro == "-p"):
    purgar=True

for id_fichero in DESCARGA:
  print "Descargando zona: " + FICHERO_CFG[id_fichero]['zona']
  status_cx = fichero.connect(host = FICHERO_CFG[id_fichero]['host'], timeout=15, debug = True)
  if status_cx:
    fichero.get_attendance_log()
    log = fichero.unpack_attendance_log()
    #pprint.pprint(log)
    print "Descargados: "+str(len(log))+" registros."
    if len(log)>0:
      print "Preparandose para el bulk insert de las siguientes fichadas"
      tuplas = []
      for fichada in log:
        nfichada = fichada[:2]
        nfichada.append(FICHERO_CFG[id_fichero]['zona'])
        tuplas.append(nfichada)
      pprint.pprint(tuplas)
      cur = cx.cursor()
      cur.execute('set search_path=%s' % FICHERO_CFG[id_fichero]['schema'])
      cx.commit()
      print "Insertando las fichadas"
      for tupla in tuplas:
        try:
          #cur.executemany('insert into fichada (uid, tfichada, puesto_control) values (%d,%s,%s);', tuplas)
          # uid, tfichada, puesto_control
          cur.execute('select insertar_fichada(%s,%s,%s);', tupla)
          cx.commit()
        except StandardError as e:
          print "Error: %s" % (e.message)
          cx.rollback()
          insert_db_error=True
    if purgar:
      print "Se purgara el fichador "+FICHERO_CFG[id_fichero]['zona']
      fecha_str = datetime.datetime.today().strftime("%Y-%m-%dT%H-%M-%S")
      file_name = F_PURGADO_PREFIX + "_" + FICHERO_CFG[id_fichero]['zona'] + "_" + fecha_str+".csv"
      try:
        file_handler = open(file_name, "w")
        for tupla in tuplas:
          file_handler.write('%s,%s\r\n' % tuple(tupla[:2]))
        file_handler.close()
        fichero.clear_attendance_log()
      except IOError as e:
        print "Error al abrir %s : %s" % (file_name, e.message)
    fichero.disconnect()
  else:
    print "No se pudo hacer la conexion al fichero"
print "Desconectandose de la base de datos"
cx.close()

