-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 23, 2014 at 12:57 PM
-- Server version: 5.5.35
-- PHP Version: 5.3.10-1ubuntu3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `B5CGM`
--

-- --------------------------------------------------------

--
-- Table structure for table `fx_helpmessages`
--

DROP TABLE IF EXISTS `fx_helpmessages`;
CREATE TABLE IF NOT EXISTS `fx_helpmessages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `HelpLocation` varchar(200) NOT NULL,
  `HelpImage` varchar(200) NOT NULL,
  `nextpageid` int(11) NOT NULL,
  PRIMARY KEY (`messageid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `fx_helpmessages`
--

INSERT INTO `fx_helpmessages` (`messageid`, `message`, `HelpLocation`, `HelpImage`, `nextpageid`) VALUES
(1, 'This is the place where you get an overview of all games that are running, waiting for players or where you can create a new game.\r\nAnd if you don''t like me you can just dismiss me in the upper right corner...\r\n<p>ACTIVE GAMES : this gives an overview of all active games.<//p><p>STARTING GAMES : You can join a starting game that has a free slot, just click on the game label.<//p><p>TUTORIAL:no help yet<//p><p>AUTOMATCH: no help yet<//p><p>CREATE GAME: You can create a new game (minimal 2 players required to play)<//p>', 'games.php', './img/vir2.jpg', 0),
(2, '<p>Click on the red text "take a slot" to assign yourself to that slot.</p>', 'creategame.php', './img/vir2.jpg', 0),
(3, '<p>Welcome to the main page of Fiery Void. \r\nA turn-based strategy game based on Babylon 5 Wars.</p><p> I am Vir Cotto, your personal assistant and I will help you in your quest to become emperor !</p><p> \r\nOn this page you have to enter your username and password so you can start to conquer the galaxy. If you don''t have any, please join the Facebook group and register on the <a href="reg.php">register</a> page.</p>', 'index.php', './img/vir1.jpg', 0),
(4, '<p>Here you can choose a user name and a password. </p><p> To be able to create an account you have to acquire the secret phrase.\r\nTo do this, please connect with us on our <a href="https://www.facebook.com/groups/218482691602941/" target="_blank">Facebook group page</a> and search it for the keyword "secret".</p>', 'reg.php', './img/vir2.jpg', 0),
(5, '<p>Here you can buy your fleet.</p> <p>Depending on the amount of points for this game you can buy ships and fighters for your army to conquer the galaxy</p>', 'gamelobby.php', './img/vir2.jpg', 0),
(6, '<p> Welcome on the battlefield. This is where epic battles are fought. </p>\r\n<p>I will give you an overview of things to do in this phase, and you can click for more help. Also on the button next to me you can find the rules of the game in a large downloadable pdf</p>\r\n<p>For deployment move your ships to the green deployment zone (=>N)</p>', 'gamephase-1p0', './img/vir2.jpg', 1),
(7, '<p><u>Movement Phase</u><p>\r\n<p>Based on initiative each ship moves in turn.(so sometimes you''ll have to wait for your enemy to complete his ship movement)</p><p> Just click the different arrows once your ship is selected to make it move, roll, pivot, ... and assign the correct amount of thrust in the SCS.</p>\r\n<p>(=>N)</p>', 'gamephase2p0', './img/vir2.jpg', 1),
(8, '<p>To move a ship, just click left on it, and then click the hex where you want it to be.You can move around on the map by clicking right and dragging, or you can zoom in and out by scrolling.</p><p>Now, click left on your ship and find the green deployment zone on the map and click left in this zone.</p><p>When your ship is positioned in the zone, you can change it''s facing by clicking on the green arrows. When you''re done deploying, click on the green V in the title bar.(=>N)</p><p><i>Hint: search for the red zone to see where the enemy deploys!</i></p>', 'gamephase-1p1', './img/vir2.jpg', 2),
(9, 'DUMMY !\r\n\r\n<p> Welcome on the battlefield. This is where epic battles are fought. </p>\r\n<p>On the button next to me you can find the rules of the game in a downloadable pdf format</p>\r\n<p>Interface explanation</p>\r\n<p>Game Turn Explanation</p>', 'hex.php', './img/vir2.jpg', 1),
(10, '<p>When you rightclick on your ship, it''s Ship Control Sheet (SCS) opens.</p><p>On the SCS you can see the state of the various ship components.For a detailed explanation of all items on a SCS click <a href="img/aogwarskitchensink.pdf" target="_blank">here</a>.</p><p>In this phase there is nothing you can do but look at it. (=>B)<p>', 'gamephase-1p2', './img/vir2.jpg', 0),
(11, '<p><u>Initial Orders Phase</u></p>\r\n<p>Power Resolution: <i>adjust output power on SCS</i></p><p>Ballistic Weapons Launch: <i>determine ballistic weapons targets</i></p><p>Electronic Warfare Allocation: <i>allocate EW points</i></p><p>(=>N)</p>', 'gamephase1p0', './img/vir2.jpg', 1),
(12, '<p><u>Power Resolution</u></p><p>Standard all ship systems are normally powered, except in case of damage. You can take two different actions:</p><p>Turn off certain shipsystems to free up power and put extra power in other systems that need it. In this case you will see the free power in your reactor, and you can apply it to systems that need it</p><p>Putting more than normal power in certain systems to get extra effects (engines, sensors, weapons)<p><p>(=>N)</p>', 'gamephase1p1', './img/vir2.jpg', 2),
(13, '<p><u>Ballistic Weapons Launch</u></p>', 'gamephase1p2', './img/vir2.jpg', 3),
(14, '<p><u>Electronic Warfare Allocation</u></p><p>EW points are produced by the ship sensors and are used to provide countermeasures for your own ship (Defensive EW) and to break through the countermeasures of other ships (Offensive EW)</p><p>You can change the assignment of EW points when you select your ship, in the box on the left lower corner.</p><p>Offensive EW has to be assigned to a specific target ship. To do this, just click on the enemy ship and increase the value in the box in the lower left.</p><p>(=>N)</p>', 'gamephase1p3', './img/vir2.jpg', 0),
(15, '<p><u>Basic Movement</u></p><p>In space movement is constant unless you change it by maneuvering.</p>\r\n<p>Standard you''re flying at a speed and direction witch is indicated in the big green arrow.If you do nothing, the ship will fly in that direction at the indicated speed.</p><p>To change this, each change is accompanied by a certain amount of thrust you have to put in to the corresponding thrusters.Be aware you can not assign more thrust than your engine is generating.</p>\r\n<p>(=>N)</p>\r\n', 'gamephase2p1', './img/vir2.jpg', 2),
(16, '<p><u>Advanced Movement</u></p>\r\nslide - 1 normal in between\r\nemergency rolls\r\nskindancing\r\nagile', 'gamephase2p2', './img/vir2.jpg', 3),
(17, '<p><u>Fighter Movement</u></p>\r\nnormal acc/dec + turn/snapturn\r\ncombat pivot\r\njinking', 'gamephase2p3', './img/vir2.jpg', 0),
(18, '<p><u>Fire orders</u></p><p>In this phase you decide which of your ship weapons fires upon which targets. Assign all weapons of all ships you want to use now.</p><p>Do this by hovering over on your weapon in the SCS, check if the other ship is in the firing arc.Left click on the weapon and then left click on the ship you want to fire upon.</p><p><i>Hint:  you actually want to get pretty close before firing.</i></p> \r\n<p>(=>N)</p>\r\n', 'gamephase3p0', './img/vir2.jpg', 1),
(19, '<p><u>Weapon Fire modes</u></p> \r\n<p>(=>N)</p>\r\n', 'gamephase3p1', './img/vir2.jpg', 0),
(20, '<p><u>Final orders</u></p><p>Now you see the results of the firing fase. Please inspect your ships to see resulting damage. If there is too much damage to continue you can always consider surrendering</p><p><i>No Surrender...No Retreat.</i></p> \r\n\r\n', 'gamephase4p0', './img/vir2.jpg', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
