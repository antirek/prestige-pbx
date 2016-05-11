#!/usr/bin/perl
use DBI;


open ( CONFIG , '< /var/lib/asterisk/agi-bin/var.txt');
 while (defined (my $variable = <CONFIG>)) {
    chomp($variable);
    ($key,$val)=split('=',$variable);
    if ( $key eq 'asteriskmysql'){  $asteriskmysql = $val;}
    if ( $key eq 'asteriskpasswd'){  $asteriskpasswd = $val;}
    if ( $key eq 'asteriskdbname'){  $asteriskdbname = $val; }
 }
close(CONFIG);

my $dbh = DBI->connect("DBI:mysql:$asteriskdbname:localhost", "$asteriskmysql","$asteriskpasswd");

my $query="select id,wav from masscall where wav is not null and end_describe is null";
$sth = $dbh->prepare("$query");
$sth->execute;

while ($ref =  $sth->fetchrow_arrayref) {
#    print "11 $file\n";
    $file="$$ref[1]-in.wav";
    $rnd=int(rand(100)*100);
    $var= `/usr/bin/flac -f -s $file -o /tmp/$rnd.flac  --channels=1`;
    $line=`/usr/local/bin/speech-recog-cli.pl /tmp/$rnd.flac |grep utterance |sed  's/utterance *: //'`;
    chomp($line);
    $var= `/bin/rm -f /tmp/$rnd.flac`;
    $query2="update masscall set end_describe=\"$line\" where id=$$ref[0]";
    $sth2 = $dbh->prepare("$query2");
    $sth2->execute;
#    print "22 $line\n";
}
