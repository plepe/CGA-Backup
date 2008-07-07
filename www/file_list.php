<?
require "inc.php";
include "header.php";

$main_path=$_REQUEST["main_path"];
$path=$_REQUEST["path"];

print "CGA-Backup :: User $user :: $pathlist[$main_path]<br><hr>\n";

print "<a href='index.php'>Go to selection of backups</a><br>\n";
print "<a href='show_backup.php?main_path=$main_path'>Go to overview of backup</a><br>\n";

if($_REQUEST[set_show]) {
  $_SESSION[show_hidden]=($_REQUEST[show_hidden]=="on"?1:0);
  session_register("show_hidden");

  $_SESSION[show_duplicate]=($_REQUEST[show_duplicate]=="on"?1:0);
  session_register("show_duplicate");

  $_SESSION[show_backup]=$_REQUEST[show_backup];
}

$dirlist=array();
$filelist=array();
$bak_list=array();

$path=stripslashes($path);

# Fuer alle Backups durchgehen
$d=opendir("$main_path");
while($dir=readdir($d)) {
  if(is_dir("$main_path/$dir")) {
    if(eregi("^[0-9]{8}[a-z]?", $dir)) {
      $bak_list[]="$dir";

      # Verzeichnislisting im konkreten Verzeichnis durchgehen
      $sd=popen("./rootls \"$main_path/$dir/$path\"", "r");
      while($sdir=fgets($sd, 1024)) {
	$stat=explode("\t", $sdir);
//	  print "$stat[0]\t";
	switch($stat[1]&0170000) {
	  case 16384:
	    $dirlist["$stat[0]"][]=$dir;
	    break;
	  case 32768:
	    $filelist["$stat[0]"][$dir]=$stat;
	    break;
	  case 40960:
	    break;
	  default:
	    print "unknown filetype";
	}
	print "\n";
      }
    }
  }
}
rsort($bak_list);
      
print "<form action='file_list.php' method='get'>\n";
print "Show: ";
print "<input type='hidden' name='main_path' value='$main_path'>\n";
print "<input type='hidden' name='path' value='$path'>\n";
print "<input type='hidden' name='set_show' value='1'>\n";
print "<input type='checkbox' name='show_hidden' ".($_SESSION[show_hidden]?"checked='checked' ":"")." onChange='submit()'> Hidden files | ";
print "<input type='checkbox' name='show_duplicate' ".($_SESSION[show_duplicate]?"checked='checked' ":"")." onChange='submit()'> Don't combine same files | ";
print "Backup <select name='show_backup' onChange='submit()'>\n";
print "<option value=''>all</option>\n";
foreach($bak_list as $b) {
  print "<option";
  if($b==$_SESSION[show_backup])
    print " selected";
  print ">$b</option>\n";
}
print "</select>\n";
print "</form>\n";

print "<div class='dirlist_border'>";
print "<div class='dirlist_header'>";
$arpath=explode("/", $path);
print "<b>Path: <a href='file_list.php?main_path=$main_path&path=/'>root</a>/";
$sump="";
foreach($arpath as $p) {
  if($p!="") {
    $sump="$sump/".rawurlencode($p);
    print "<a href='file_list.php?main_path=$main_path&path=$sump/'>$p</a>/";
  }
}
print "</b>\n";

$print_list=array();
foreach($bak_list as $b) {
  $p=popen("./rootisdir \"$main_path/$b/$path/\"", "r");
  $isdir=fgets($p, 1024);
  pclose($p);
  if($isdir=="1\n") {
    $print_list[]="<a href='download_directory.php/backup.tgz?main_path=$main_path&path=$path&version=$b'>$b</a>";
  } 
}
print " (Download: ";
print implode(" ", $print_list);
print ")";
print "</div>\n";

print "<div class='dirlist_body'>\n";
print "<table border='0' cellspacing='0' cellpadding='2' width='100%'>\n";
ksort($dirlist);
foreach($dirlist as $dir=>$data) {
  if(($dir!=".")&&($dir!=".."))
    if(($_SESSION[show_hidden])||(substr($dir, 0, 1)!=".")) {
      if((!$_SESSION[show_backup])||(in_array($_SESSION[show_backup], $data))) {
	$r=($r+1)%2;
	print "<tr class='row$r'><td valign='top'><b>";

	print "<a href='file_list.php?main_path=$main_path&path=".rawurlencode("$path/$dir/")."'>$dir</a>\n";

	print "<td>\n";
	if($_SESSION[show_duplicate]) {
	  print "<table>\n";
	  usort($data, bakcmp);
	  foreach($data as $dir) {
	    print "<tr><td>$dir</td></tr>\n";
	  }
	  print "</table>\n";
	}
	else {
	  print "directory";
	}
	print "</td>\n";
	print "</tr>\n";
      }
    }
}

ksort($filelist);

foreach($filelist as $file=>$baklist) {
  if(($_SESSION[show_hidden])||(substr($file, 0, 1)!=".")) {
    $r=($r+1)%2;
    print "<tr class='row$r'><td valign='top'><b>";
    print "$file\n";
    print "</b></td><td valign='top'>";
    print "<table>\n";

    $list=array(array(), array(), array());
    foreach($baklist as $day=>$b) {
      if(eregi("^[0-9]{8}[a-z]?$", $day)) {
	$list[1][$day]=$b;
      }
    }
    for($i=0;$i<3;$i++) {
      uksort($list[$i], bakcmp);
    }
    $baklist=$list[0]+$list[1]+$list[2];

    $show_inodes=array();
    foreach($baklist as $day=>$b) {
      if(!in_array($b[4], $show_inodes)) {
	print "<tr><td width='100'>\n";
	print "<a href='download_file.php/".rawurlencode($file)."?main_path=$main_path".
	      "&path=".rawurlencode($path)."&file=".rawurlencode($file)."&backup=$day'>$day</a>";
	print "</td><td width='80'>\n";
	print $b[2];
	print "</td><td>\n";
	print strftime("%D %H:%M", $b[3]);
	print "</td></tr>\n";
	if(!$_SESSION[show_duplicate]) {
	  $show_inodes[]=$b[4]; // immer nur einen inode anzeigen, darum speichern
	}
      }
    }
    print "</table>\n";
    print "</td></tr>\n";
  }
}
print "</table>\n";
print "</div>\n";
