
# Introduction

Fiery Void is a turn based strategy game based on B5Wars tabletop game by Agents of Gaming.

# License

FV is licensed under GNU GPLv3

# Installing development environment

Make sure you have the following installed on your machine:

- Docker and Docker Compose
- Node.js and Yarn (for client-side frontend bundling) - Primarily Yarn.

1. Build and Start the Environment
Open a terminal in the project root folder (c:\FV_env\FieryVoid) and run:

docker-compose up -d --build
This will build and start the Nginx, PHP, and MariaDB containers in the background.

(Note: The database is automatically initialized from db/emptyDatabase.sql on the very first start and securely saved to a persistent Docker volume).

Note: you might need to delete this from docker-compose.yml file: 

"develop:
      watch:
        
action: sync
        path: ./source
        target: /usr/src/fieryvoid/source
        ignore:
node_modules/"

2. Accessing the Application
Web Interface: Point your browser to http://localhost

Database Access: You can connect to the database from a local client at localhost:3306 with the following credentials:
User: root
Password: fieryvoid (or possibly just leave blank)
Database: B5CGM

3. Setup Client-Side Development (Yarn)
Since FieryVoid bundles legacy code, you'll need to install the Node dependencies locally so things recompile when you make edits by running these commands in project root folder:


# First time setup
yarn install
# To watch and auto-rebuild JS files while editing
yarn watch:legacy
# To do a one-off build
yarn build


4. Interacting with the PHP Container
If you need to run server-side scripts (like to generate new staticship files after adding new units, or changing class variables on server) or explore the backend container, you can drop into the PHP container's bash shell:

docker-compose exec php bash

Then navigate to /usr/src/current and run 'php generateStaticShipFile.php'

5. Troubleshooting / Clean Rebuild
If your containers get out of sync or you need to cleanly force a rebuild of the environment, use:

docker-compose up -d --build --force-recreate

6. Set-up database connection if you need it

Current devs use SQL Workbench  here https://www.filehorse.com/download-mysql-workbench/download/

Then set-up connection using details from mariadb part of you Docker container.

Make sure the stack is up:


docker compose ps
Confirm the mariadb container shows Up and the port mapping is 0.0.0.0:3306->3306/tcp.

Find the DB credentials. They're in the Fiery Void repo — check docker-compose.yml (look for MYSQL_ROOT_PASSWORD, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE) and/or any .env file the compose file references. Whatever values are there are what Workbench needs.

In MySQL Workbench → "+" next to MySQL Connections:

Field	Value
Connection Name	Fiery Void (Docker)
Connection Method	Standard (TCP/IP)
Hostname	127.0.0.1
Port	3306
Username	from compose (often root or fieryvoid)
Password	"Store in Vault…" → paste from compose/.env
Default Schema	leave blank, or the DB name from compose
Click Test Connection → should succeed → OK.

Open the connection and the Fiery Void schema(s) will show up in the left-hand SCHEMAS panel. Browse tables, run queries, etc. — same as any local DB.


## VAGRANT IS NO LONGER USED! ##

Here were the old instructions for posterity

1. Install virtualbox https://www.virtualbox.org/
1. Install vagrant https://www.vagrantup.com/
1. Make sure you have ssh client and terminal
1. Type  `vagrant up`
1. Point your browser to localhost:8080/source/public

You can access the virtual box by:

1. Type `vagrant ssh`
1. Code is shared in /vagrant/ folder
1. Access database by typing: `mysql -uroot -proot`


# Game.php and gamelobby.php js bundling:

I created a scripts/bundle-legacy.js script that bundles the ~130 legacy JavaScript files into a single file (game.legacy.bundle.js). I also updated game.php to load this bundle by default, which should drastically reduce HTTP requests and eliminate the HTTP/2 errors. You can still load individual scripts for debugging by adding &debug=1 to the URL. I updated package.json to include this in your build process (using yarn).

Because I've set up the build script to read game.php directly, you don't need to do anything special to maintain the bundle list!

How to add new files:

Just add your new script tag to game.php inside the if ($debug) block (where all the others are).
Run yarn build.
The script will automatically find your new file in game.php and bake it into the bundle.

How to rebuild:

Since I added the legacy build to your main build script in package.json, just running yarn build (or npm run build) will handle everything for you. It builds your vite app AND rebuilds this legacy bundle in one go.
I've updated the walkthrough with these instructions for future reference.

How to edit client side now:

You'll need to run yarn watch:legacy in FV folder when editing client files now, similar to how we run yarn watch when editing React files.

When uploading your branch to gitHub it's best not to include these files, as they will need to be regenerated anyway for the branch being deployed live and it makes merging easier not to have several versions of the bundles.

Minification (from 3.6.2026):
The bundler now depends on esbuild to minify the legacy bundles, (already present via Vite, so a normal yarn install covers it), and yarn watch:legacy intentionally produces un-minified output for debuggability while yarn build minifies.

# Image Optimiser:

Images are optimised on Web Server by navigating to https://fieryvoid.eu/game/source/public/mass_optimizer.php or https://fieryvoid.eu/testInstance/source/public/mass_optimizer.php

As a result, users can work with full png images, and then this script can be run on game and testInstance Server whenever required.

On local server, images are simply not optimised, although in theroy you could run the mass_optimser script to do so.


# Sticky Images
If you're finding images are not updating after you changed them, you can sometimes add ‘?v=2’ after .png to force image refresh.


# Adding faction defaults shuttles: 

1. Define the subclass in Shuttle.php
Subclass Shuttle and override setShuttleDefaults(). Set $this->phpclass, $this->shipClass, $this->faction, and any stat overrides. Use FlyerProtectorate (line 162) as the template:

2. Register it in the autoload classmap — do not skip this
'flyerxyz' => '/server/model/ships/Shuttle.php',

3. Add the faction → class entry in HangarOps
In HangarOps.php:202, add to $factionShuttleMap:
'Some Faction' => 'FlyerXYZ',

4. Add the tooltip display name
shuttleDisplayNameFor() — add a case 'FlyerXYZ': return 'Flyer';. Without it the hangar tooltip falls through to default and labels the craft "Shuttle".  This may be fine if it’s a generic shuttle unit for faction

5. Check eviction priority
evictionPriorityFor() returns priority 10 for things whose phpclass contains the substring "shuttle". A name like Flyer does not contain "shuttle", so it would fall through to the class_exists probe and get treated as a fighter (priority 1000+). That's why line 469 has an explicit Flyer/FlyerProtectorate check. If your new class name doesn't contain "shuttle", add it to that line so it's evicted as a shuttle, not as a fighter.

6. Art (only if needed)
getImage() keys off $this->faction. If your faction string already matches an existing case you're done; otherwise add a case returning your [imagePath, iconPath]. (Setting $this->faction correctly in step 1 is what makes this work automatically.)

7. Client preload — automatic, nothing to do
game.php:109-112 calls shuttleClassForFactionName() over $factionShuttleMap, so once step 3 is done the blueprint preloads for any game containing that faction's carrier. No client wiring needed.
Test
Quick test before considering it done: add a carrier of the faction to a game, then surrender or save/reload — that's the deserialize path that exercises the autoload entry. If it survives a surrender, all four touchpoints are wired.

