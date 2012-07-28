all:	dev

dev:
	rsync -r ./source --exclude=/.* aatu@chracian-dev.net:/var/www/dev/

dist:
	rsync -r ./source --exclude=/.* aatu@chracian-dev.net:/var/www/FieryVoid/
