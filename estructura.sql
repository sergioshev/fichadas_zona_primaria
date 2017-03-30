/* $Id: estructura.sql,v 1.8 2015/05/16 15:06:33 root Exp $
 *
 * Estructura de la base de datos para las fichadas.
 */

--DROP TABLE puesto_control CASCADE;
CREATE TABLE puesto_control (
  nombre_corto char(8) PRIMARY KEY,
  nombre text
);

--drop table personas cascade;
create table personas (
  uid integer,
  nombre varchar,
  apellido varchar,
  dni integer,
  constraint pkey_uid primary key (uid)
);

--DROP TABLE fichada CASCADE;
CREATE TABLE fichada (
  tfichada timestamp,
  uid integer,
  puesto_control char(8),
  CONSTRAINT pkey_fichada
    PRIMARY KEY (tfichada, uid),
  constraint fkey_uid
    foreign key (uid)
    references personas(uid)
      on update cascade
      on delete restrict,
  CONSTRAINT fkey_puesto_control
    FOREIGN KEY(puesto_control)
    REFERENCES puesto_control
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);

DROP TABLE historico_fichada CASCADE;
CREATE TABLE historico_fichada (
  tfichada timestamp,
  uid integer,
  puesto_control char(8),
  CONSTRAINT pkey_fichada_historico
    PRIMARY KEY (tfichada, uid),
  constraint fkey_uid_historico
    foreign key (uid)
    references personas(uid)
      on update cascade
      on delete restrict,
  CONSTRAINT fkey_puesto_control_historico
    FOREIGN KEY(puesto_control)
    REFERENCES puesto_control
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);



--drop table personas_autorizadas cascade;
create table personas_autorizadas (
  nombre varchar,
  apellido varchar,
  dni integer,
  constraint pkey_dni primary key (dni)
);


--drop table mail_notificacion cascade;
create table mail_notificacion (
  evento varchar,
  fecha timestamp,
  nombre varchar,
  apellido varchar,
  dni integer,
  constraint pkey_mn_dni primary key (dni, fecha, evento)
);

