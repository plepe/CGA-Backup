<?
require "inc.php";
include "header.php";
$max_rec=5;

print "CGA-Backup :: User $user<br><hr>\n";

function show($path) {
  global $total;
  global $current;
  global $texts;
  global $size;

  $f=fopen("$path/statistic", "r");

  $data=array();
  while($r=fgets($f, 1024)) {
    $r=chop($r);
    if(ereg("^\[(.*)\]", $r, $m))
      $type=$m[1];
    else {
      ereg("^([0-9]+)\t(.+)$", $r, $m);
      $data[$type][$m[2]]=$m[1];
    }
  }

  $t="<h4>$path</h4>";
  $t.=sprintf("Current Size: %.2f GB<br>\n", $data[realsize][current]/1024/1024/1024);
  $t.=sprintf("Total Size: %.2f GB<br>\n", $data[realsize][total]/1024/1024/1024);
  $t.=sprintf("Ueberhang: %.2f%%<br>\n", $data[realsize][total]/$data[realsize][current]*100);
  $texts[$path]=$t;
  $size[$path]=$data[realsize][current]/1024;

  $total+=$data[realsize][total]/1024;
  $current+=$data[realsize][current]/1024;
}

function rec($path, $depth=0) {
  global $max_rec;

  if(file_exists("$path/current")) {
    show($path);
  }
  else {
    if($rec>=$max_rec)
      return;

    $p=opendir($path);
    while($f=readdir($p)) {
      if(substr($f, 0, 1)!=".")
	if(is_dir("$path/$f")) {
	  rec("$path/$f", $depth+1);
	}
    }
  }
}

rec("/backup-cg");

asort($size);
foreach($size as $key=>$d) {
  print $texts[$key];
}

print "<h4>Total</h4>\n";
printf("Total: %.2f GB<br>\n", $total/1024/1024);
printf("Current: %.2f GB<br>\n", $current/1024/1024);
printf("Ueberhang: %.2f%%<br>\n", $total/$current*100);
