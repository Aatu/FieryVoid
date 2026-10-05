--
-- Mine detection: live sweeping (MINE_DETECTION_PLAN.md §1.4, Stage 2)
--
-- One row per game, turn and unit: the hexes the server has made FINAL for a unit with a Detect
-- Mines rating as it moved (mineSweep.php, Manager::sweepMines). A swept hex is final whether or
-- not anything was found there - that is what stops a player probing a path and taking it back
-- (D10, D11) - so the unit's committed path must begin with these hexes, in order
-- (MovementGamePhase::process). Every row of a game is deleted when its Movement phase ends, and
-- by DBManager::deleteGames / leaveSlot.
--
--   hexes  JSON [[q, r], ...] - the hexes entered this turn, in order (the start hex is not listed).
--   moves  JSON - this turn's movement rows up to the one entering the last final hex, exactly as
--          the client posted them, so a reload mid-move can put them back (plan §1.4.6).
--   found  JSON [mine id, ...] - the mines found from the LAST final hex ([] when nothing was), so
--          a reply that never reached the client can be given again.
--
-- ⚠️ APPLY THIS TO THE LIVE DB BEFORE UPLOADING THE PHP (plan trap T16). Live is a remote shared
-- MariaDB and emptyDatabase.sql never reaches it. Without the table the PHP degrades safely - every
-- sweep request fails (finds then show when the unit commits, as before this feature), game.php
-- restores nothing, and deleteGames / leaveSlot / the end of Movement skip it - but nothing can be
-- swept until this has run. Safe to re-run.
--

CREATE TABLE IF NOT EXISTS `tac_minesweep` (
  `gameid`  int(11)    NOT NULL,
  `turn`    int(11)    NOT NULL,
  `shipid`  int(11)    NOT NULL,
  `hexes`   text       NOT NULL,
  `moves`   mediumtext NOT NULL,
  `found`   text       NOT NULL,
  PRIMARY KEY (`gameid`, `turn`, `shipid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
