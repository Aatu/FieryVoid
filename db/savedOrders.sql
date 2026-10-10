--
-- Save Orders — Stage 0 (SAVE_ORDERS_PLAN.md §2)
--
-- One row per player per game (ruling D4): the orders a player has given so far in the CURRENT
-- phase, parked without committing them. Written by saveOrders.php, read back by game.php for its
-- author only, and deleted when that player commits or surrenders. No game logic reads it - the
-- draft is an opaque client snapshot, valid for exactly one (game, player, turn, phase) and ignored
-- everywhere else, so a stale row is harmless.
--
--   savedat   Unix time from PHP's time(), formatted by the client in the player's own time zone.
--             Not a datetime written with NOW(): that would bring in the database server's clock and
--             zone, which on shared hosting need not match PHP's.
--   orders    The draft as JSON. Re-encoded server-side with json_encode's DEFAULT flags, so the
--             stored text is pure ASCII (\uXXXX escapes) - the connection charset is 3-byte utf8,
--             which cannot hold a 4-byte character. Capped at 1 MB by Manager::saveOrders.
--
-- ⚠️ APPLY THIS TO THE LIVE DB BEFORE UPLOADING THE PHP (plan trap T10). Live is a remote shared
-- MariaDB and emptyDatabase.sql never reaches it. The PHP survives a missing table (game.php
-- restores nothing, DBManager::deleteGames skips it, a commit still commits) and Save reports an
-- error, but nothing can be saved until this has run. Safe to re-run.
--

CREATE TABLE IF NOT EXISTS `tac_savedorders` (
  `gameid`   int(11)    NOT NULL,
  `playerid` int(11)    NOT NULL,
  `turn`     int(11)    NOT NULL,
  `phase`    int(11)    NOT NULL,
  `savedat`  int(11)    NOT NULL,
  `orders`   mediumtext NOT NULL,
  PRIMARY KEY (`gameid`, `playerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
