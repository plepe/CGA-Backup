<?
require "inc.php";
include "header.php";

$main_path=$_REQUEST["main_path"];
$path=$_REQUEST["path"];

print "CGA-Backup :: User $user :: $pathlist[$main_path]<br><hr>\n";

print "<a href='index.php'>Go to selection of backups</a><br>\n";
print "<a href='show_backup.php?main_path=$main_path'>Go to overview of backup</a><br>\n";

if($_REQUEST[show_hidden]) {
  $_SESSION[show_hidden]=($_REQUEST[show_hidden]=="yes"?1:0);
  session_register("show_hidden");
}

if($_REQUEST[show_duplicate]) {
  $_SESSION[show_duplicate]=($_REQUEST[show_duplicate]=="yes"?1:0);
  session_register("show_duplicate");
}

$dirlist=array();
$filelist=array();

$path=stripslashes($path);

$d=opendir("$main_path");
while($dir=readdir($d)) {
  if(is_dir("$main_path/$dir")) {
    if((substr($dir, 0, 1)!=".")) {
      $bak_list[]="$dir";
#      if(is_dir("$main_path/$dir/$path/")) {

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

/*
        $sd=opendir("$main_path/$dir/$path");
        while($sdir=readdir($sd)) {
  	if(($sdir!=".")&&($sdir!="..")) {
  	  if(is_dir("$main_path/$dir/$path/$sdir"))
  	  else
  	    $filelist[$sdir][]=$dir;
  	}
        }
	*/
      }
#    }
  }
}
      
print "<p>\n";
if($_SESSION[show_hidden]) {
  print "<a href='file_list.php?main_path=$main_path&path=$path&show_hidden=no'>Don't show hidden files</a><br>\n";
}
else {
  print "<a href='file_list.php?main_path=$main_path&path=$path&show_hidden=yes'>Show hidden files</a><br>\n";
}

if($_SESSION[show_duplicate]) {
  print "<a href='file_list.php?main_path=$main_path&path=$path&show_duplicate=no'>Don't show duplicate backups of a file</a><br>\n";
}
else {
  print "<a href='file_list.php?main_path=$main_path&path=$path&show_duplicate=yes'>Show duplicate backups of a file</a><br>\n";
}

print "Download this directory:\n";
foreach($bak_list as $b) {
  $p=popen("./rootisdir $main_path/$b/$path/", "r");
  $isdir=fgets($p, 1024);
  pclose($p);
  if($isdir) {
    print "<a href='download_directory.php/backup.tgz?main_path=$main_path&path=$path&version=$b'>$b</a>\n";
  } 
}

$arpath=explode("/", $path);
print "<p><b>Path: <a href='file_list.php?main_path=$main_path&path=/'>root</a>/";
$sump="";
foreach($arpath as $p) {
  if($p!="") {
    $sump="$sump/".rawurlencode($p);
    print "<a href='file_list.php?main_path=$main_path&path=$sump/'>$p</a>/";
  }
}
print "</b>\n";

print "<h4>List of directories</h4>\n";
ksort($dirlist);
foreach($dirlist as $dir=>$data) {
  if(($dir!=".")&&($dir!=".."))
    if(($_SESSION[show_hidden])||(substr($dir, 0, 1)!="."))
      print "<a href='file_list.php?main_path=$main_path&path=".rawurlencode("$path/$dir/")."'>$dir</a>\n";
      //print "<a href='file_list.php?main_path=$main_path&path=".
      //rawurlencode("$path/$dir")."'>$dir</a>\n";
}

print "<h4>List of files</h4>\n";
ksort($filelist);

print "<table>\n";
foreach($filelist as $file=>$baklist) {
  if(($_SESSION[show_hidden])||(substr($file, 0, 1)!=".")) {
    $r=($r+1)%2;
    print "<tr class='row$r'><td valign='top'><b>";
    print "$file\n";
    print "</b></td><td valign='top'>";
    print "<table>\n";

    $list=array(array(), array(), array());
    foreach($baklist as $day=>$b) {
      if(eregi("^[0-9]{8}$", $day)) {
	$list[1][$day]=$b;
      }
      elseif(eregi("^M[0-9]{6}$", $day)) {
	$list[2][$day]=$b;
      }
      elseif($day=="current") {
	$list[0][$day]=$b;
      }
    }
    for($i=0;$i<3;$i++) {
      krsort($list[$i]);
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
