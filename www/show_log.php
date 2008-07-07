<?
require "inc.php";
include "header.php";

$main_path=$_REQUEST[main_path];
$logfile=$_REQUEST[logfile];

print "CGA-Backup :: User $user :: $pathlist[$main_path]<br><hr>\n";

print "<a href='index.php'>Go to selection of backups</a><br>\n";
print "<a href='show_backup.php?main_path=$main_path'>Go to overview of backup</a><br>\n";

print "Logfile of $logfile<p>";

print "<pre>\n";
# Zeile fuer Zeile einlesen
$l=fopen("$main_path/cgabackup-$logfile.log", "r");
while($r=fgets($l, 1024)) {
  # Nur HTML-Code schreiben
  print htmlentities($r);
}
print "</pre>\n";
