<?
require "inc.php";

print "Hi $user<br>\n";


if($main_path) {
  if(in_array($main_path, $pathlist)===false) {
    print "<h4>$main_path$path</h4>";

    if($logfile) {
      $l=fopen("$main_path/cgabackup-$logfile.log", "r");
      while($r=fgets($l, 1024)) {
	print "$r<br>\n";
      }
    }
    elseif($path) {
      $d=opendir("$main_path");
      while($dir=readdir($d)) {
	if(is_dir("$main_path/$dir")) {
	  if((substr($dir, 0, 1)!=".")) {
	    if(is_dir("$main_path/$dir/$path")) {
	      $sd=opendir("$main_path/$dir/$path");
	      while($sdir=readdir($sd)) {
		if(($sdir!=".")&&($sdir!="..")) {
		  if(is_dir("$main_path/$dir/$path/$sdir"))
		    $filelist["$sdir/"][]=$dir;
		  else
		    $filelist[$sdir][]=$dir;
		}
	      }
	    }
	  }
	}
      }
      
      print "<table>\n";
      foreach($filelist as $file=>$baklist) {
	print "<tr><td><b>";
	if(substr($file, $file-1)=="/") {
	  print "<a href='?main_path=$main_path&path=$path/$file'>$file</a>\n";
	}
	else {
	  print "$file";
	}
	print "</b></td><td>";
	foreach($baklist as $b) {
	  print "$b ";
	}
	print "</td></tr>\n";
      }
      print "</table>\n";
    }
    else {
      print "<a href='?main_path=$main_path&path=/'>Go to filelist</a><br>\n";
      
      $d=opendir("$main_path");
      while($dir=readdir($d)) {
	if(is_dir("$main_path/$dir")) {
	  if((substr($dir, 0, 1)!=".")&&($dir!="current")) {
	    print "Backup of $dir";
	    if(!file_exists("$main_path/cgabackup-$dir.log")) {
	      print " (seems not to be complete)";
	    }
	    else {
	      print " (see <a href='?main_path=$main_path&logfile=$dir'>logfile</a>)";
	    }
	    print "<br>\n";
	  }
	}
      }
    }

  }
  else {
    print "Permission denied";
  }
}
else {
  foreach($pathlist as $p) {
    print "<a href='?main_path=$p'>$p</a><br>";
  }
}
