<?
require "inc.php";
$main_path=$_REQUEST["main_path"];
$path=$_REQUEST["path"];
$file=$_REQUEST["file"];
$backup=$_REQUEST["backup"];

Header("content-type: application/octet-stream");

$f=popen("./rootopen \"$main_path/$backup/$path/$file\"", "r");
while($r=fread($f, 1024)) {
  print $r;
}
pclose($f);
