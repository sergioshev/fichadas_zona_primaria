
create or replace function insertar_usuario_generico(nuid integer) returns boolean as 
$$
  declare
    luid integer;
  begin
    select into luid uid from personas where uid=nuid;
    if not found then
      insert into personas (uid, nombre, apellido, dni) values 
        (nuid, 'UNDEF', 'UNDEF', 99999999);
      return 't';
    end if;
    return 'f';
  end;
$$ language 'plpgsql';

create or replace function insertar_fichada(
  nuid integer,
  fecha timestamp, 
  puesto char(8)
) returns boolean as
$$
  begin
    --perform insertar_usuario_generico(nuid);
    insert into fichada (uid, tfichada, puesto_control) values (nuid, fecha, puesto);
    return found;
  end;
$$
language 'plpgsql';

create or replace function pasar_a_historico(desde timestamp) returns void as
$$
  begin
    insert into historico_fichada select * from fichada where tfichada <= desde;
    delete from fichada where tfichada <= desde;
  end;
$$ language 'plpgsql';
