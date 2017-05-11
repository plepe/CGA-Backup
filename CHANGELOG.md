v2.3.6, release 2017-05-11
----------------------------
* Bugfix

v2.3.5, released 2017-05-08
---------------------------
* cgabackup: When starting, check if backup (with the exact same cmdline) is
  already running. If yes, abort.
* cgabackup: new parameter -w. Wait indefinitely for backup host to come online
  (while trying every 5 minutes).
* cgabackup: new option 'exclude' for wildcard directories -> don't do any backups for these subdirectories. E.g. `dir=/home/*, exclude=foo bar` will skip the directories /home/foo and /home/bar.
* cgabackup: Ignore partial transfer error due to vanished source files.
* cgabackup-mysql: password does not get passed per cmdline to mysql. BREAKING CHANGE! rewrite config, install 'crudini'.
* cgabackup-mysql: check permissions
