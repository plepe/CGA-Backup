since v2.3.4
------------
* cgabackup: When starting, check if backup (with the exact same cmdline) is
  already running. If yes, abort.
* cgabackup: new parameter -w. Wait indefinitely for backup host to come online
  (while trying every 5 minutes).
