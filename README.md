
# Introduction

Fiery Void is a turn based strategy game based on B5Wars tabletop game by Agents of Gaming.

# License

FV is licensed under GNU GPLv3

# Installing development environment

1. Install virtualbox https://www.virtualbox.org/
1. Install vagrant https://www.vagrantup.com/
1. Make sure you have ssh client and terminal
1. Type  `vagrant up`
1. Point your browser to localhost:8080/source/public

You can access the virtual box by:

1. Type `vagrant ssh`
1. Code is shared in /vagrant/ folder
1. Access database by typing: `mysql -uroot -proot`


# New Game.php and gamelobby.php bundling:

I created a scripts/bundle-legacy.js script that bundles the ~130 legacy JavaScript files into a single file (game.legacy.bundle.js). I also updated game.php to load this bundle by default, which should drastically reduce HTTP requests and eliminate the HTTP/2 errors. You can still load individual scripts for debugging by adding ?debug=1 to the URL. I updated package.json to include this in your build process (using yarn).

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

# Image Optimiser:
Open your terminal in the project root (c:\FV_env\FieryVoid).
Run the command:
yarn run optimize-images

As images are now saved in Index mode, if you want to edit them you'll need to change to RGB mode (otherwise you lose alot of functionality).  In GIMP this is easily done via by Image->Mode->RGB.

If you're finding image are not updating, sometimes using ‘?v=2’ after .png can force image refresh.
