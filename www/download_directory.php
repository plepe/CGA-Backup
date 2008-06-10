<?
require "inc.php";
$main_path=$_REQUEST["main_path"];
$path=$_REQUEST["path"];
$version=$_REQUEST["version"];
$wholepath="$main_path/$version/$path";
if(substr($wholepath, strlen($wholepath)-1, 1)=="/") {
  $wholepath=substr($wholepath, 0, strlen($wholepath)-1);
}
ereg("^(.*)/([^\/]*)$", $wholepath, $m);
$p=popen("/var/www/roottar \"$wholepath\"", "r");
Header("content-type: application/x-gzip");
while($r=fread($p, 1024)) {
  print($r);
}
