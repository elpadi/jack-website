.PHONY: js

all:

db-export:
	mysqldump -uroot -pelpadi dahlen_jack > site/db-dump.sql
	mysqldump -uroot -pelpadi dahlen_jack_users > site/usersdb-dump.sql

ftp-sync: js
	echo "open jack" > /tmp/ftp-up
	echo "cd spread" >> /tmp/ftp-up
	cat ftp-up >> /tmp/ftp-up
	lftp -f /tmp/ftp-up

js:
	make -C public_html/js
