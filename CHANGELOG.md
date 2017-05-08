since v2.3.4
------------
* cgabackup: When starting, check if backup (with the exact same cmdline) is
  already running. If yes, abort.
* cgabackup: new parameter -w. Wait indefinitely for backup host to come online
  (while trying every 5 minutes).
* cgabackup: new option 'exclude' for wildcard directories -> don't do any backups for these subdirectories. E.g. `dir=/home/*, exclude=foo bar` will skip the directories /home/foo and /home/bar.
