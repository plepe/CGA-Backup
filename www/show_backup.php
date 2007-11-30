<?
require "inc.php";
include "header.php";

print "CGA-Backup :: User $user :: $pathlist[$main_path]<br><hr>\n";

print "<a href='index.php'>Go to selection of backups</a><br>\n";

# Hier koennen die Dateien dann runtergeladen werden
print "<a href='file_list.php?main_path=$main_path&path=/'>Go to filelist</a><br>\n";

$dirlist=array();
$dirlist_with_log=array();
$monthlist=array();

# Das backup.cfg einlesen
$arpath=explode("/", $main_path);
$sump="";
$cfg=array();
foreach($arpath as $p) {
  if($p!="") {
    $sump="$sump/$p";
    if(file_exists("$sump/backup.cfg")) {
      $f=fopen("$sump/backup.cfg", "r");
      while($r=fgets($f, 1024)) {
	$r=chop($r);
	if(ereg("^(.*)=(.*)$", $r, $m)) {
	  $cfg[$m[1]]=$m[2];
	}
      }
      fclose($f);
    }
  }
}

# Informationen ueber backup.cfg ausgeben
print "<h4>Status</h4>\n";
//print "<table><tr><td valign='top'>\n";
print "Incremental backups are kept for $cfg[daily] days.<br>\n";
print "Monthly full backups are kept for $cfg[monthly] months.<br>\n";
print "Quarterly full backups are kept for $cfg[quarterly] months.<br>\n";

# Fuer jedes Verzeichnis ....
$d=opendir("$main_path");
while($dir=readdir($d)) {
  if(eregi("cgabackup-([0-9]*).log", $dir, $m)) {
    $dirlist_with_log[$m[1]]=$m[1];
  }
  elseif(eregi("^([0-9]*)$", $dir, $m)) {
    $dirlist[$m[1]]=$m[1];
  }
  elseif(eregi("^M([0-9]*)$", $dir, $m)) {
    $monthlist[$m[1]]=$m[1];
  }
}

$all_dirs=$dirlist_with_log+$dirlist;
arsort($all_dirs);

$stat=fopen("$main_path/statistic", "r");
$mode="???";
while($s=fgets($stat, 1024)) {
  $s=rtrim($s);
  if(eregi("^([0-9]*)\t(.*)", $s, $m)) {
    $statistic[$mode][$m[2]]=$m[1];
  }
  elseif(eregi("^\[(.*)\]", $s, $m)) {
    $mode=$m[1];
  }
  elseif($s=="") {
  }
  else {
    print "Couldn't parse '$s'<br>\n";
  }
}
fclose($stat);
printf("All backups take %.2f MB disk space.<br>\n", $statistic[realsize][total]/1024.0);

print "<img src='g_statistic_backup.php?main_path=$main_path'><br>\n";
//print "</td><td valign='top'>\n";
print "<h4>Configuration</h4>\n";
if(file_exists("$main_path/current/.cgabackup")) {
  $f=popen("./rootopen $main_path/current/.cgabackup", "r");
  unset($resume);
  while($r=fgets($f, 1024)) {
    $r=chop($r);
    if($resume) {
      if(substr($r, strlen($r)-1)=="\\") {
        $r=substr($r, 0, strlen($r)-1);
        $data[$resume].=$r;
      }
      else {
        $data[$resume].=$r;
        unset($resume);
      }
    }
    else {
      if(eregi("^#", $r)||eregi("^[ \t]*$", $r)) {
      }
      elseif(eregi("^([^ \t]+)[ \t]+(.*)$", $r, $m)) {
        if(substr($m[2], strlen($m[2])-1)=="\\") {
  	$resume=$m[1];
  	$m[2]=substr($m[2], 0, strlen($m[2])-1);
        }
        $data[$m[1]]=$m[2];
      }
    }
  }
  print "The following files/directories are excluded (in your current backup):<br>\n";
  //print $data[BACKUP];
  if(ereg("EXCLUDE[ \t](.*)$", $data[BACKUP], $m)) {
    print $m[1];
  }
  //print "</td></tr></table>\n";
}
else {
  print "No files are excluded.";
}

print "<h4>Available incremental backups</h4>\n";
print "<small>In this backups are files that has been replaced on this day. The name of the backup is created of the date in form YYYYMMDD. In the logfile you can check what has been done on this day.</small>\n<p>";
# Alle (inc.) Backups auflisten
print "<table class='backuplist'>\n";
print "<tr><td class='h_backuplist'>Name</td><td class='h_sizeinfo'>Size</td>\n";
foreach($all_dirs as $day=>$data) {
  print "<tr><td class='backuplist'>$data";
  if($dirlist_with_log[$data]) {
    print " (see <a href='show_log.php?main_path=$main_path&logfile=$data'>logfile</a>)</td>";
  }
  else {
    print " (seems not to be complete)</td>";
  }
  if(isset($statistic[fullsize][$day])||(!is_dir("$main_path/$day"))) {
    printf("<td class='sizeinfo'>%.2f MB</td>\n", 
	   $statistic[fullsize][$day]/1024.0);
  }
  else {
    print "<td class='sizeinfo'>(not updated yet)</td>\n"; 
  }
  print "</tr>\n";
}
print "</table>\n";

print "<h4>Available full backups</h4>\n";
print "<small>In the beginning of each month a copy of the 'current' backup is made and called in the form 'M'YYYYMM. 'current' is always an exact copy of the original directory (at the time of the backup).</small>\n<p>";

//print_r($statistic);
print "<table class='backuplist'>\n";
print "<tr><td class='h_backuplist'>Name</td><td class='h_sizeinfo'>Size</td><td class='h_sizeinfo'>Effective Size</td>\n";
if(file_exists("$main_path/current")) {
  print "<tr><td class='backuplist'>current</td>\n";
  printf("<td class='sizeinfo'>%.2f MB</td><td class='sizeinfo'>%.2f MB</td>\n", 
         $statistic[fullsize][current]/1024.0,
         $statistic[realsize][current]/1024.0);
  print "</tr>\n";
}

$monthlist_sort=array_keys($monthlist);
rsort($monthlist_sort);
foreach($monthlist_sort as $month) {
  print "<tr><td class='backuplist'>M$month</td>\n";
  if(isset($statistic[fullsize]["M$month"])) {
    printf("<td class='sizeinfo'>%.2f MB</td><td class='sizeinfo'>%.2f MB</td>\n", 
	   $statistic[fullsize]["M$month"]/1024.0,
	   $statistic[realsize]["M$month"]/1024.0);
  }
  else {
    print "<td class='sizeinfo' colspan='2' style='text-align: center;'>(not updated yet)</td>\n"; 
  }
  print "</tr>\n";
}
print "</table>\n";
  /*
  if(is_dir("$main_path/$dir")) {
    if((substr($dir, 0, 1)!=".")&&($dir!="current")) {
      # Anzeigen und schreiben, ob auch ein Logfile existiert
      print "Backup of $dir";
      if(!file_exists("$main_path/cgabackup-$dir.log")) {
        print " (seems not to be complete)";
      }
      else {
        print " (see <a href='show_log.php?main_path=$main_path&logfile=$dir'>logfile</a>)";
      }
      print "<br>\n";
    }
  }
}
*/
