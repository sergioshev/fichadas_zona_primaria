#!/usr/bin/perl
# Script para parsear el archivo de fichadas e insertar los datos 
# directamente en la base de datos.

use DBI;
use DBD::Pg;
use DateTime;

my $signups = {};

sub connectdb {
  my $dbh = DBI->connect(
       "dbi:Pg:dbname=fichada;host=localhost;port=5432",
       'ufichada','1f1ch4d4') ;
  if ( not defined($dbh)) {
    print( "No se pudo conectar con la base de datos\n" ) ;
    print ("Error:".$DBI::err."\n".$DBI::errstr."\n");
    return 0 ;
  }
  return $dbh;
}

sub seconds
{
  # 2013-05-03 09:54:11
  my $time = shift;
  my $expr='^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})';
  if ($time =~ /$expr/) {
    my $dt = DateTime->new(year   => $1,
                           month  => $2,
                           day    => $3,
                           hour   => $4,
                           minute => $5,
                           second => $6
                          );
    return $dt->epoch();
  }
  return 0;
}

sub parse_stdin
{ 
  # umbral en segundos para detectar las fichdas consecutivas
  # si 2 fichadas distan mas que $threshold segundos, entonces
  # la segunda es conciderada los suficientemente lejana y es
  # una salida o bien una nueva entrada.

  my $threshold = shift;
  while (defined($line = <STDIN>)) {
    if ($line =~ /^"([^"]+)","([^"]+)".*$/) {
      $user_id = $1;
      $time = $2;
      if (not defined($signups->{$user_id})) {
        $signups->{$user_id} = {};
        $signups->{$user_id}->{'time'} = [];
        $signups->{$user_id}->{'last_time'} = $time;
      }
      my $last_time_secs = seconds($signups->{$user_id}->{'last_time'});
      my $current_time_secs = seconds($time);
      if (($current_time_secs - $last_time_secs) > $threshold) {
        push(@{$signups->{$user_id}->{'time'}}, $signups->{$user_id}->{'last_time'});
      }
      $signups->{$user_id}->{'last_time'} = $time;
    }
  }
  for my $key (keys %$signups) {
    push(@{$signups->{$key}->{'time'}},$signups->{$key}->{'last_time'});
  }
}

my $dbhandler = &connectdb();
exit 1 if $dbhandler == 0;

# distancias menores a 120 segundos seran descartadas.
parse_stdin(120);

# volcado a la base de datos
my $sth = $dbhandler->prepare("select insertar_fichada(?,?,?);");
for my $key ( keys %$signups ) {
  foreach my $time (@{$signups->{$key}->{'time'}}) {
    eval {
      print "$key->$time\n";
      $sth->execute($time,$key,'ZONAPRIM')
    };
    if ($@) {
      warn $@;
    }
    $sth->finish;
  }
}  
$dbhandler->disconnect;
