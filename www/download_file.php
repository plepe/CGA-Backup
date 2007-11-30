<?
require "inc.php";

Header("content-type: application/octet-stream");

$f=popen("./rootopen \"$main_path/$backup/$path/$file\"", "r");
while($r=fread($f, 1024)) {
  print $r;
}
pclose($f);
