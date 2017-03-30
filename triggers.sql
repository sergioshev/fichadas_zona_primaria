set search_path=consoricio;

create or replace function f_record_op() returns trigger as
$$
  declare
    datos personas_autorizadas%rowtype;
    
  begin
    if TG_OP = 'DELETE' then 
      perform * from mail_notificacion where dni = old.dni limit 1;
      if found then
        delete from mail_notificacion where dni = old.dni;
      end if;  
      insert into mail_notificacion (evento, fecha, nombre, apellido, dni) values (TG_OP, current_timestamp::timestamp, old.nombre, old.apellido, old.dni);
    else
      if TG_OP = 'UPDATE' then
        perform * from mail_notificacion where dni = old.dni limit 1;
        if found then
          delete from mail_notificacion where dni = old.dni;
        end if;
        insert into mail_notificacion (evento, fecha, nombre, apellido, dni) values (TG_OP, current_timestamp::timestamp, new.nombre, new.apellido, new.dni);
      else
        perform * from mail_notificacion where dni = new.dni limit 1;
        if found then
          delete from mail_notificacion where dni = new.dni;
        end if;
        insert into mail_notificacion (evento, fecha, nombre, apellido, dni) values (TG_OP, current_timestamp::timestamp, new.nombre, new.apellido, new.dni);
      end if;
    end if;
    return null;
  end
$$
language 'plpgsql';

create trigger t_record_op after insert or update or delete on personas_autorizadas for each row execute procedure f_record_op();
