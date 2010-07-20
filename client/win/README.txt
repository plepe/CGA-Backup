Thanks to cygwin you can also use CGA-Backup with Windows. As we used a shared cygwin-installation via samba, the clients had to set some registry keys to work correctly. They also saved the current keys, so it won't interfere with a local installation.

In our installation \\zwirn\cygwin was the directory which was used for the cygwin installation.

Copy the cgabackup-wrapper script to /usr/local/bin and change it to it's local path. The config is expected in c:\cgabackup.conf, as it should be local.

start_cygwin.bat starts a shell into this shared cygwin environment.
