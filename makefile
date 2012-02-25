all:	dev

dev:
	rsync -r ./ --exclude=/.* --exclude=/img --exclude=/maps --exclude=/ships--exclude=makefile aatu@chracian-dev.net:/var/www/dev/

