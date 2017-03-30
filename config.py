
FICHERO_CFG = {
  0 : { 
    'host'   : '192.168.1.37',
    'zona'   : 'ZONAPRIM',
    'schema' : 'consorcio'
  },
  1 : { 
    'host'   : '192.168.1.36',
    'zona'   : 'ENTRPRIN',
    'schema' : 'elevador'
  },
  2 : { 
    'host'   : '192.168.1.35',
    'zona'   : 'CALADO',
    'schema' : 'elevador'
  },
  3 : { 
    'host'   : '192.168.1.39',
    'zona'   : 'PLAYA',
    'schema' : 'elevador'
  }

}

DB_CFG = {
  'host' : 'eloso.terminalquequen.com.ar',
  'port' : '5434',
  'user' : 'ufichada',
  'pass' : '1f1ch4d4',
  'db'   : 'fichada'
}

# enumerar los ficheros para la descarga
# poner los indices del hash FICHERO_CFG
DESCARGA = [0, 1, 2, 3]
#DESCARGA = [3]

PRO_ROOT = "/usr/share/CGPQ"
F_PURGADO_PREFIX = PRO_ROOT+"/"+"purgados"

