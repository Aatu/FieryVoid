all:	dev

dev:
	rsync -r ./ --exclude=/.* --exclude=build.xml --exclude=makefile aatu@chracian-dev.net:/var/www/dev/

dist:
	rsync -r ./source --exclude=/.* aatu@chracian-dev.net:/var/www/FieryVoid/
