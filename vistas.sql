create or replace view fichadas_lotes as
  select uid, tfichada, puesto_control, ntile(buckets) over w as bucket
    from (
      select
        uid,
        tfichada,
        puesto_control,
        ((count(*) over (partition by uid)-1)/2+1)::int as buckets
      from fichada ) as fcb
    window w as (
      partition by uid order by tfichada rows between unbounded preceding and unbounded following
    );

create or replace view fichadas_io as
  select distinct
    uid,
    first_value(tfichada) over w as entrada,
    case
      when last_value(tfichada) over w = first_value(tfichada) over w
        then null
      else
        last_value(tfichada) over w end as salida,
     first_value(puesto_control) over w as puesto_control_entrada,
     case
      when last_value(tfichada) over w = first_value(tfichada) over w
        then null
      else
        last_value(puesto_control) over w end as puesto_control_salida
  from fichadas_lotes window w AS (partition by uid, bucket);

create or replace view fichada_ext as 
  select 
    f.entrada as fecha_entrada, 
    f.salida as fecha_salida,
    f.uid,
    p.nombre,
    p.apellido,
    p.dni,
    f.puesto_control_entrada,
    f.puesto_control_salida,
    pue.nombre as nombre_puesto_entrada,
    pue2.nombre as nombre_puesto_salida
  from (select * from fichadas_io order by uid,entrada desc) as f
    left join personas p on (f.uid=p.uid)
    left join puesto_control pue on (f.puesto_control_entrada=pue.nombre_corto)
    left join puesto_control pue2 on (f.puesto_control_salida=pue2.nombre_corto);

create or replace view fichada_sin_salida as select * from fichada_ext 
  where fecha_salida is null;

create or replace view fichada_con_salida as select * from fichada_ext 
  where fecha_salida is not null;

