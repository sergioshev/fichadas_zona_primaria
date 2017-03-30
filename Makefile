# $Id: Makefile,v 1.18 2015/05/18 17:59:21 sshevtsov Exp $
#
# Makefile
#

INIT_FILE = db.conf
CGPQ_ROOT = /usr/share/CGPQ
ESQUEMAS = consorcio elevador

.PHONY: noproduccion
noproduccion: 
	@[ -f .desarrollo ] || ! echo "No se hace nada peligroso en produccion." 

.PHONY: dropdb
dropdb: noproduccion
	su -c 'dropdb fichada' postgres
	
.PHONY: createdb
createdb:
	su -c 'createdb fichada' postgres
	su -c 'createlang plpgsql fichada' postgres

.PHONY: createuser
createuser: 
	su -c 'createuser -r -s -D ufichada -P' postgres || true
	su -c "echo \"create role consorcio with nosuperuser  nocreatedb  nocreaterole login password 'c0ns0rc10'\" | psql fichada" postgres
	su -c "echo \"create role elevador with nosuperuser  nocreatedb  nocreaterole login password '3l3v4d0r'\" | psql fichada" postgres

.PHONY: createschema
createschema:
	su -c "echo \"create schema consorcio\" | psql fichada" postgres
	su -c "echo \"create schema elevador\" | psql fichada" postgres
	su -c "echo \"alter role elevador set search_path=elevador,public\" | psql fichada" postgres
	su -c "echo \"alter role consorcio set search_path=consorcio,public\" | psql fichada" postgres

.PHONY: initdb
initdb: createschema metadatos.sql estructura.sql funciones.sql vistas.sql noproduccion
	$(foreach esquema,$(ESQUEMAS), su -c "{ echo \"set search_path to $(esquema);\" ; cat estructura.sql funciones.sql vistas.sql ; } | psql fichada" postgres ; )
	su -c "cat metadatos.sql | psql fichada" postgres
	su -c "{ echo \"set search_path to consorcio;\" ; cat triggers.sql ; } | psql fichada" postgres


.PHONY: regenerarvistas
regenerarvistas: vistas.sql
	$(foreach esquema,$(ESQUEMAS), su -c "{ echo \"set search_path to $(esquema);\" ; cat vistas.sql ; } | psql fichada" postgres ; )


.PHONY: regenerarfunciones
regenerarfunciones: funciones.sql
	$(foreach esquema,$(ESQUEMAS), su -c "{ echo \"set search_path to $(esquema);\" ; cat funciones.sql ; } | psql fichada" postgres ; )

.PHONY: grants
grants:
	su -c "echo grant all on schema elevador to elevador | psql fichada" postgres
	su -c "echo grant all on schema consorcio to consorcio | psql fichada" postgres
	su -c "echo \"SELECT 'grant all on ' || schemaname || '.' || tablename || ' to elevador;' from pg_tables where schemaname='elevador'\" | psql -t fichada" postgres | su -c "psql fichada" postgres
	su -c "echo \"SELECT 'grant all on ' || schemaname || '.' || viewname || ' to elevador;' from pg_views where schemaname='elevador'\" | psql -t fichada" postgres | su -c "psql fichada" postgres
	su -c "echo \"SELECT 'grant all on ' || schemaname || '.' || tablename || ' to consorcio;' from pg_tables where schemaname='consorcio'\" | psql -t fichada" postgres | su -c "psql fichada" postgres
	su -c "echo \"SELECT 'grant all on ' || schemaname || '.' || viewname || ' to consorcio;' from pg_views where schemaname='consorcio'\" | psql -t fichada" postgres | su -c "psql fichada" postgres

.PHONY: dropgrants
dropgrants:
	su -c "echo revoke all on schema elevador to elevador | psql fichada" postgres
	su -c "echo revoke all on schema consorcio to consorcio | psql fichada" postgres
	su -c "echo \"SELECT 'revoke all on ' || schemaname || '.' || tablename || ' to elevador;' from pg_tables where schemaname='elevador'\" | psql -t fichada" postgres | su -c "psql fichada" postgres
	su -c "echo \"SELECT 'revoke all on ' || schemaname || '.' || viewname || ' to elevador;' from pg_views where schemaname='elevador'\" | psql -t fichada" postgres | su -c "psql fichada" postgres
	su -c "echo \"SELECT 'revoke all on ' || schemaname || '.' || tablename || ' to consorcio;' from pg_tables where schemaname='consorcio'\" | psql -t fichada" postgres | su -c "psql fichada" postgres
	su -c "echo \"SELECT 'revoke all on ' || schemaname || '.' || viewname || ' to consorcio;' from pg_views where schemaname='consorcio'\" | psql -t fichada" postgres | su -c "psql fichada" postgres


#.PHONY: regvistas
#regvistas: vistas.sql
#	cat vistas.sql | psql -U ufichada fichada


.PHONY: install
install :
	install -m 775 -d $(CGPQ_ROOT)
	install -m 664 funciones.php index.php index.html config.php procesar_fechas.php\
	  procesar_fechas.htmlt config.py login.html validar.php salir.php \
	  procesar_personal.php cambiar_clave.php cambiar_clave.html cambio_clave.php $(CGPQ_ROOT)
	install -m 775 insertarFichadas.pl insertarFichadas.py \
	  subir_purgados enviarMail $(CGPQ_ROOT)
	cp -r js $(CGPQ_ROOT)
	cp -r css $(CGPQ_ROOT)

#TODO: hacer el fullinstall
#.PHONY: fullinstall
#fullinstall : install install_db

