all:	dev

dev:
	rsync -r ./source --exclude=/.* aatu@chracian-dev.net:/var/www/dev/

dist:
	rsync -r ./source --exclude=/.* aatu@chracian-dev.net:/var/www/FieryVoid

jazz_test:
	rsync -r ./source --exclude=/.* jazz@chracian-dev.net:/var/www/jazz_test/FieryVoid/
	
jazz_final:
	rsync -r ./source --exclude=/.* jazz@chracian-dev.net:/var/www/FieryVoid/
