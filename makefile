all:	dev

dev:
	rsync -r ./ --exclude=/.* --exclude=makefile aatu@chracian-dev.net:/var/www/dev/

dist:
	rsync -r ./ --exclude=/.* --exclude=makefile aatu@chracian-dev.net:/var/www/B5CGM/
