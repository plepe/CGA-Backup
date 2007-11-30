<?
require "inc.php";
$wholepath="$main_path/$version/$m[1]";
if(substr($wholepath, strlen($wholepath)-1, 1)=="/") {
  $wholepath=substr($wholepath, 0, strlen($wholepath)-1);
}
ereg("^(.*)/([^\/]*)$", $wholepath, $m);
chdir($m[1]);
$p=popen("tar czf - $m[2]", "r");
Header("content-type: application/x-gzip");
while($r=fread($p, 1024)) {
  print($r);
}
