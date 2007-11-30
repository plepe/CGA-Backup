<?
$quiet=1;
require "inc.php";
include "header.php";
include "date.php";

$progress_current=array();
$progress_total=array();

$stat=fopen("$main_path/statistic.current.progress", "r");
while($s=fgets($stat, 1024)) {
  $x=explode(" ", $s);
  $x[1]=substr($x[1], 0, 4)."-".substr($x[1], 4, 2)."-".substr($x[1], 6, 2);
  $progress_current[$x[1]]=$x[0];
}
fclose($stat);

$stat=fopen("$main_path/statistic.total.progress", "r");
while($s=fgets($stat, 1024)) {
  $x=explode(" ", $s);
  $x[1]=substr($x[1], 0, 4)."-".substr($x[1], 4, 2)."-".substr($x[1], 6, 2);
  $progress_total[$x[1]]=$x[0];
}
fclose($stat);

$x=array_values($progress_total);
rsort($x);
$highest_current=$x[0];

$highest=$highest_current;

$days=365;
$xpos=800;
$ypos=300;
$height=290;
$day_length=2;

$im = imagecreate(810, 330);
$col_back=imagecolorallocate($im, 255, 255, 255);
$col_current=imagecolorallocate($im, 255, 0, 0);
$col_total=imagecolorallocate($im, 0, 0, 255);
$col_coord=imagecolorallocate($im, 0, 0, 0);
$col_grid=imagecolorallocate($im, 200, 200, 200);
$last_current=0;

imageline($im, $xpos-$days*$day_length, 0, $xpos-$days*$day_length, $ypos, $col_coord);
imageline($im, $xpos-$days*$day_length, $ypos, $xpos, $ypos, $col_coord);
putenv("GDFONTPATH=/usr/share/fonts/truetype/ttf-dejavu/");

$unitindex=0;
$units=array("kB", "MB", "GB", "TB");
$meas=1;
while($meas*4<$highest) {
  $unitval="2";
  $meas=$meas*2;

  if($meas*4<$highest) {
    $meas=$meas/2*5;
    $unitval="5";
  }
  if($meas*4<$highest) {
    $meas=$meas*2;
    $unitval="10";
  }
  if($meas*4<$highest) {
    $meas=$meas*2;
    $unitval="20";
  }
  if($meas*4<$highest) {
    $meas=$meas/2*5;
    $unitval="50";
  }
  if($meas*4<$highest) {
    $meas=$meas*2;
    $unitval="100";
  }
  if($meas*4<$highest) {
    $meas=$meas*2;
    $unitval="200";
  }
  if($meas*4<$highest) {
    $meas=$meas/2*5;
    $unitval="500";
  }
  if($meas*4<$highest) {
    $meas=$meas/500*1024;
    $unitval="1";
    $unitindex++;
  }
}

for($i=1; $i<=3; $i++) {
  $y=$ypos-($i*$meas/$highest)*$height;
  imageline($im, $xpos-$days*$day_length+1, $y, $xpos, $y, $col_grid);
  imageline($im, $xpos-$days*$day_length-3, $y, $xpos-$days*$day_length-1, $y, $col_coord);
  imagettftext($im, 8, 0, $xpos-$days*$day_length-50, $y+3, $col_coord, "DejaVuSans", ($unitval*$i)." ".$units[$unitindex]);
}

unset($last_current);
unset($last_total);

for($i=-364;$i<=0;$i++) {
  $d=date_add(date_get_today(), $i);

  if(substr($d, 8, 2)=="01") {
    imageline($im, $xpos+$i*$day_length, 0, $xpos+$i*$day_length, $ypos-1, $col_grid);
    imageline($im, $xpos+$i*$day_length, $ypos, $xpos+$i*$day_length, $ypos+3, $col_coord);
    imagettftext($im, 8, 0, $xpos+($i)*$day_length+2, $ypos+10, $col_coord, "DejaVuSans", substr($d, 0, 7));
  }
 
  if($progress_current[$d]) {
    $x=$xpos+$i*$day_length;
    $y=$ypos-($progress_current[$d]/$highest)*$height;
    if($last_current) {
      imageline($im, $last_current[0], $last_current[1], $x, $y, $col_current);
    }
    $last_current=array($x, $y);
  }

  if($progress_total[$d]) {
    $x=$xpos+$i*$day_length;
    $y=$ypos-($progress_total[$d]/$highest)*$height;
    if($last_total) {
      imageline($im, $last_total[0], $last_total[1], $x, $y, $col_total);
    }
    $last_total=array($x, $y);
  }
}

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
