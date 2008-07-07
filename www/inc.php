<?
# Die einzelnen Zeilen der Config sind : getrennt
# Feld 1: Welcher Benutzer darf hier lesen?
#   "": alle
#   username: Benutzer <username> darf lesen
#   @group: alle, die in Gruppe sowieso sind duerfen lesen
# Feld 2: Fuer welchen Pfad gilt dies?
#   %u wird durch Benutzername ersetzt
#   *  gilt fuer alle matchenden Verzeichnisse
# Feld 3: Beschreibung fuer Eintrag
#   Auch hier werden %u und * analog ersetzt

# Gruppeninfo einlesen
$f=fopen("/var/www/etc/group.nis", "r");
while($r=fgets($f, 102400)) {
  if(substr($r, 0, 1)!="#") {
    $ar=explode(":", $r);
    if(sizeof($ar)>=4) {
      $group[$ar[0]]=explode(",", $ar[3]);
    }
  }
}

# Alle gueltigen Verzeichnisse werden im Array $pathlist gespeichert,
# wobei als Index der Pfad genommen wird und als Wert eine Beschreibung zu dem
# Eintrag

# Who am I?
$user=getenv("REMOTE_USER");

# Config einlesen
$c=fopen("conf", "r");
while($r=fgets($c, 1024)) {

  # Eintrag nach : aufsplitten
  $r=explode(":", $r);

  # Wenn der Benutzer passt
  # Wenn der Benutzer passt
  if($r[0]=="") {
    $acc=1;
  }
  else {
    $r[0]=explode(",", $r[0]);
    $acc=0;
    foreach($r[0] as $r0) {
      if((r0=="")||($r0==$user)||
	 ((substr($r0, 0, 1)=="@")&&(in_array($user, $group[substr($r0, 1)]))))
	$acc=1;
    }
  }

  if($acc) {
    # %u durch Benutzername ersetzen
    if(substr($r[1], strlen($r[1])-1)=="*") {
      $p=substr($r[1], 0, strlen($r[1])-1);
      if(@$d=opendir($p)) {
	while($f=readdir($d)) {
	  if(($f!=".")&&($f!="..")) {
	    # In die Pathlist aufnehmen
	    $pathlist["$p$f"]=strtr($r[2], array("%u"=>$f));
	  }
	}
      }
    }
    else {
      $r[1]=strtr($r[1], array("%u"=>$user));
      # In die Pathlist aufnehmen
      $pathlist[$r[1]]=$r[2];
    }
  }
}
fclose($c);

# Benutzerrechte ueberpruefen
if(($main_path)&&(in_array($main_path, array_keys($pathlist))===false)) {
  print "Permission denied\n";
  exit;
}
 
function bakcmp($a, $b) {
  eregi("([0-9]{8})([a-z]?)", $a, $a_);
  eregi("([0-9]{8})([a-z]?)", $b, $b_);

  if($a_[1]==$b_[1])
    return $a_[2]>$b_[2];
  else
    return $a<$b;
}

?>
