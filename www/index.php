<?
require "inc.php";
include "header.php";

print "CGA-Backup :: User $user<br><hr>\n";

print "<p>You have access to the following directories:<ul>\n";
$c=fopen("conf", "r");
while($r=fgets($c, 1024)) {

  if(substr($r, 0, 1)=="=") {
    print "<h3>".substr($r, 1)."</h3>\n";
  }
  else {
    # Eintrag nach : aufsplitten
    $r=explode(":", $r);

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
	  print "<li> $r[3]</li>\n";
	  $list=array();
	  while($f=readdir($d)) {
	    $list[]=$f;
	  }
	}
	sort($list);

	foreach($list as $f) {
	  if(($f!=".")&&($f!="..")&&(is_dir("$p$f"))) {
	    # In die Pathlist aufnehmen
	    # $pathlist["$p$f"]=strtr($r[2], array("%u"=>$f));
	    print "<a href='show_backup.php?main_path=$p$f'>$f</a>\n";
	  }
	}
      }
      else {
	$r[1]=strtr($r[1], array("%u"=>$user));
	# In die Pathlist aufnehmen
	# $pathlist[$r[1]]=$r[2];
	print "<li> <a href='show_backup.php?main_path=$r[1]'>$r[2]</a></li>\n";
      }
    }
  }
}
fclose($c);

foreach($pathlist as $path=>$desc) {
}
print "</ul>\n";
