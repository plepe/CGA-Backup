@echo off
rem Modified MW 20020814:
rem now backs up cygwin registry in case cygwin is installed locally
rem then overwrites with the cygwin net registry
rem and restores the local cygwin registry after the backup script is run.


regedit /e c:\cygconf.reg "HKEY_LOCAL_MACHINE\SOFTWARE\Cygnus Solutions"
regedit /s \\zwirn\cygwin\DOC\cygwin.reg

\\zwirn\cygwin\bin\bash --login -e /usr/local/bin/cgabackup.sh

regedit /s c:\cygconf.reg
