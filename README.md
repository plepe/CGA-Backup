CGA-Backup is basically an rsync wrapper, doing backups via rsh/ssh to a remote host. Every backup is a modification of the previous backup, therefore only changed files has to be transfered.

INSTALLATION
============
On the client(s)
----------------
Clone the repository to some path, e.g. /usr/local. Then copy and rename the client/cgabackup.conf-dist to /etc/cgabackup.conf and link the client/cgabackup script to /usr/local/bin:
```sh
cd /usr/local
git clone https://github.com/plepe/CGA-Backup.git

cp /usr/local/CGA-Backup/client/cgabackup.conf-dist /etc/cgabackup.conf

ln -s /usr/local/CGA-Backup/client/cgabackup /usr/local/bin/cgabackup
```

Edit the `/etc/cgabackup.conf` with your favorite editor.

Additionally in the root of every directory which will be backuped (e.g. `/home/USER` if `dir=/home/*` is one of the directories in `/etc/cgabackup.conf`), a file `.cgabackup` may be created with user specific parameters:
```
BACKUP_MAILTO		user@example.com
BACKUP_SUCCESSMAIL	0
BACKUP_ERRORMAIL	1
BACKUP_EXCLUDE		./tmp/ *.mp3
```

* `BACKUP_MAILTO`: CGA-Backup can send mails if a backup is successful or fails. BACKUP_MAILTO specifies the mailto address (default: directory-name@hostname).
* `BACKUP_SUCCESSMAIL`: If 1, an email will be sent, even if it is successful (default: 0).
* `BACKUP_ERRORMAIL`: If 1, an email will be sent if it has failed (default: 0).
* `BACKUP_EXCLUDE`: List of directories or wildcards which not will be backuped. In the specified case the 'tmp' directory in the root of the directory and all files ending on `.mp3` will not be backuped.

On the server
-------------
Clone the repository to some path, e.g. /usr/local. Then copy and rename the server/cgabackup-server.conf-dist to /etc/cgabackup-server.conf and link the scripts to /usr/local/bin:
```sh
cd /usr/local
git clone https://github.com/plepe/CGA-Backup.git

cp /usr/local/CGA-Backup/server/cgabackup-server.conf-dist /etc/cgabackup-server.conf

ln -s /usr/local/CGA-Backup/server/cgabackup-cleanup /usr/local/bin/
ln -s /usr/local/CGA-Backup/server/cgabackup-build-statistic /usr/local/bin/
ln -s /usr/local/CGA-Backup/server/cgabackup-pre /usr/local/bin/
ln -s /usr/local/CGA-Backup/server/cgabackup-post /usr/local/bin/
ln -s /usr/local/CGA-Backup/server/cgabackup-info /usr/local/bin/
```

* `cgabackup-pre` and `cgabackup-post` are run before/after each backup from client side `cgabackup`.
* `cgabackup-cleanup` removes outdated backups. You should run it every now and then from crontab.
* `cgabackup-build-statistic` calculates disk space usage of each backup. You should run it after the backups of the day are finished.

=== Run cgabackup ===
Run 'cgabackup' on the client. You might want to create a crontab entry.

On the server for each configured backup a directory will be created, which contains the followings files/directories:

Example: Directory `/backup/homes/foo`:
* **20120622/** - The (full) backup from Juny 22nd, 2012.
* **20121025/** - The (full) backup from October 25th, 2012.
* **20121025a/** - A previous backup from October 25th, 2012 - it got renamed when the second backup was performed.
* **20121102_incomplete/** - A backup from November 2nd, 2012 which was interrupted.
* **cgabackup-20120622.log** - The logfile from the backup of Juny 22nd, 2012.
* **cgabackup-20121025.log** - The logfile from the backup of October 25th, 2012.
* **lock** - If this file exists a backup is taking place. It contains the pid of the backup process.
* **last_backup** - A symbolic link pointing to the most current complete backup (20121025 in this example)
* **statistic** - Contains disk space statistics, calculated in three different ways: fullsize (disk space usage for each backup), realsize (disk space change in comparison to the last backup - files which stayed the same are not included), backrealsize (disk space change calculcated backwards - from the current to the first backup)
* **statistic.last_backup.progress** - See how the size of the backup increases
* **statistic.total.progress** - See how the total size (all backups together) increases
* **cleanup.conf** - You may create this file to override configuration for `cgabackup-cleanup` (Use `server/cleanup.conf-dist` as template - see file for default values).
