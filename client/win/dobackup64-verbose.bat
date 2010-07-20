@echo off
rem Modified MW 20020814:
rem now backs up cygwin registry in case cygwin is installed locally
rem then overwrites with the cygwin net registry
rem and restores the local cygwin registry after the backup script is run.


regedit /e c:\cygconf64.reg "HKEY_LOCAL_MACHINE\SOFTWARE\Wow6432Node\Cygnus Solutions"
regedit /s \\zwirn\cygwin\DOC\cygwin64.reg

\\zwirn\cygwin\bin\bash --login -e /usr/local/bin/cgabackupv.sh

regedit /s c:\cygconf64.reg
