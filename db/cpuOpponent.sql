--
-- CPU opponent - Stage T0 (TACTICAL_AI_PLAN.md sections 3.1 and 4)
--
-- Everything here is ADDITIVE and NULLABLE, so it can be applied to the live DB long before the
-- `cpuOpponent` feature flag is turned on, and nothing changes for an ordinary game:
--
--   player.is_cpu            1 = a bot account. Bots are ordinary rows in `player` (the CPU is a
--                            player - plan P1) with a password that can never match a login.
--   tac_playeringame.cpu     NULL = a human seat. Otherwise JSON {level, personality, seed}: the
--                            difficulty of THIS seat in THIS game (per game, not per account).
--   tac_ai_state             The bot's plan for a turn, so Firing agrees with what Movement set up.
--   tac_ai_log               Why the bot did what it did, and every AI defect (plan P7). Capped per
--                            game by the writer (DBManager::aiLog).
--
-- ⚠️ APPLY THIS TO THE LIVE DB BEFORE TURNING THE FLAG ON. Live is a remote shared MariaDB and
-- emptyDatabase.sql never reaches it. With the flag off the PHP never reads any of this, so the
-- order of "upload the PHP" and "run this file" does not matter until then.
-- Safe to re-run.
--

ALTER TABLE `player`
  ADD COLUMN IF NOT EXISTS `is_cpu` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `tac_playeringame`
  ADD COLUMN IF NOT EXISTS `cpu` varchar(255) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `tac_ai_state` (
  `gameid`   int(11)    NOT NULL,
  `playerid` int(11)    NOT NULL,
  `turn`     int(11)    NOT NULL,
  `data`     mediumtext NOT NULL,
  PRIMARY KEY (`gameid`, `playerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tac_ai_log` (
  `id`       int(11)      NOT NULL AUTO_INCREMENT,
  `gameid`   int(11)      NOT NULL,
  `playerid` int(11)      NOT NULL,
  `turn`     int(11)      NOT NULL,
  `phase`    int(11)      NOT NULL,
  `kind`     varchar(20)  NOT NULL,
  `message`  text         NOT NULL,
  `loggedat` int(11)      NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ai_log_game` (`gameid`, `kind`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- The bot accounts. '!' is not a password_hash() string and not a MySQL PASSWORD() string
-- ('*' + 40 hex), so DBManager::verifyPlayerPassword can never match it: a bot cannot log in.
-- One game may seat several different bots; one bot may sit in any number of games.
-- Each insert is skipped when the username is already taken (a re-run, or a human of that name -
-- in which case that bot simply does not exist; a human row is never flagged).
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Alpha', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Alpha');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Bravo', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Bravo');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Charlie', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Charlie');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Delta', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Delta');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Echo', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Echo');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Foxtrot', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Foxtrot');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Golf', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Golf');
INSERT INTO `player` (`username`, `password`, `accesslevel`, `is_cpu`)
  SELECT 'CPU Hotel', '!', 1, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `player` WHERE `username` = 'CPU Hotel');
