<?
require "inc.php";
include "header.php";

$main_path=$_REQUEST["main_path"];

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

# Fuer jedes Verzeichnis ....
$d=opendir("$main_path");
while($dir=readdir($d)) {
  if(eregi("cgabackup-([0-9]*).log", $dir, $m)) {
    $dirlist_with_log[$m[1]]=$m[1];
  }
  elseif(eregi("^([0-9]*[a-z]?)$", $dir, $m)) {
    $dirlist[$m[1]]=$m[1];
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

print "<h4>Available Backups</h4>\n";
print "<small>Effective size is the space that is needed on the disk additionally to the next newer backup.</small>\n";

//print_r($statistic);
print "<table class='backuplist'>\n";
print "<tr><td class='h_backuplist'>Name</td><td class='h_backuplist'>Backuptime</td><td class='h_backuplist'>Logfile</td><td class='h_sizeinfo'>Size</td><td class='h_sizeinfo'>Effective Size</td>\n";
#if(file_exists("$main_path/current")) {
#  print "<tr><td class='backuplist'>current</td>\n";
#  printf("<td class='sizeinfo'>%.2f MB</td><td class='sizeinfo'>%.2f MB</td>\n", 
#         $statistic[fullsize][current]/1024.0,
#         $statistic[realsize][current]/1024.0);
#  print "</tr>\n";
#}

$dirlist_sort=array_keys($dirlist);
usort($dirlist_sort, bakcmp);
foreach($dirlist_sort as $dir) {
  print "<tr><td class='backuplist'>$dir</td>\n";
  print "<td class='sizeinfo'>";
  $s=stat("$main_path/$dir");
  print strftime("%Y-%m-%d %H:%M", $s[mtime]);
  print "</td>\n";
  print "<td class='sizeinfo'>";
  if(file_exists("$main_path/cgabackup-$dir.log")) {
    print "<a href='show_log.php?main_path=$main_path&logfile=$dir'>view logfile</a>";
  }
  else {
    print "no logfile";
  }
  print "</td>\n";
  if(isset($statistic[fullsize]["$dir"])) {
    printf("<td class='sizeinfo'>%.2f MB</td><td class='sizeinfo'>%.2f MB</td>\n", 
	   $statistic[fullsize]["$dir"]/1024.0,
	   $statistic[realsize]["$dir"]/1024.0);
  }
  else {
    print "<td class='sizeinfo' colspan='2' style='text-align: center;'>(not updated yet)</td>\n"; 
  }
  print "</tr>\n";
}
print "</table>\n";
# Informationen ueber backup.cfg ausgeben
print "<h4>Status</h4>\n";
//print "<table><tr><td valign='top'>\n";
print "All backups are kept for $cfg[daily] days.<br>\n";
print "Incomplete backups are kept for $cfg[incomplete] days.<br>\n";
print "First backup of Week is kept for $cfg[weekly] weeks.<br>\n";
print "First backup of Month is kept for $cfg[monthly] months.<br>\n";
print "First backup of each quarter of a year is kept for $cfg[quarterly] quarters.<br>\n";

printf("All backups take %.2f MB disk space.<br>\n", $statistic[realsize][total]/1024.0);

print "<img src='g_statistic_backup.php?main_path=$main_path'><br>\n";
//print "</td><td valign='top'>\n";
print "<h4>Configuration</h4>\n";
if(file_exists("$main_path/last_backup/.cgabackup")) {
  $f=popen("./rootopen $main_path/last_backup/.cgabackup", "r");
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


