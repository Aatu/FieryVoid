--
-- Pre-battle damage & fleet damage persistence (PREBATTLE_DAMAGE_PLAN.md §4.1)
--
-- Two tables that let a saved fleet carry the battle damage and critical effects
-- its ships ended a previous game with. Both mirror `tac_saved_ammo` exactly (same
-- key shape, same double FK, same cascade) so `deleteSavedFleet` keeps working
-- untouched - deleting a tac_saved_list row cascades these away with the rest.
--
-- `kind` is what lets fighter ORDINALS live in the same table as system ids:
--   kind = 0  ->  `ref` is a ShipSystem id (ordinary ships; ids are pure ctor order)
--   kind = 1  ->  `ref` is a fighter ordinal 1..flightSize (flights; per-fighter
--                 system ids do not exist in the lobby - see plan §1.1)
--   kind = 2  ->  `ref` is a MINE ordinal 1..bulkbuy (a bulk mine purchase is one
--                 lobby object plus a number, exactly like a flight; the damage
--                 always lands on that copy's Structure)
--

CREATE TABLE IF NOT EXISTS `tac_saved_damage` (
  `listid`    INT(11)    NOT NULL,
  `shipid`    INT(11)    NOT NULL,          -- tac_saved_ship.id
  `kind`      TINYINT(1) NOT NULL DEFAULT 0,-- 0 = systemid, 1 = fighter ordinal
  `ref`       INT(11)    NOT NULL,          -- systemid | ordinal
  `damage`    INT(11)    NOT NULL DEFAULT 0,
  `destroyed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`listid`,`shipid`,`kind`,`ref`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_dmg_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_dmg_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `tac_saved_crit` (
  `listid`   INT(11)      NOT NULL,
  `shipid`   INT(11)      NOT NULL,
  `kind`     TINYINT(1)   NOT NULL DEFAULT 0,
  `ref`      INT(11)      NOT NULL,
  `type`     VARCHAR(100) NOT NULL,
  `amount`   INT(11)      NOT NULL DEFAULT 1,
  `param`    INT(11)               DEFAULT NULL,
  PRIMARY KEY (`listid`,`shipid`,`kind`,`ref`,`type`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_crit_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_crit_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Existing installs: `param` was added 2026-08-08 so PARAM-CARRYING criticals can be
-- carried between battles (DamageReductionReduced - Phased Gravitic Torpedo phasing -
-- keeps its magnitude in the crit's param, not in the number of crits).
-- INT, not tac_critical's varchar(200): the only params allowed into a saved fleet are
-- plain integers (PreBattleDamage::$paramCriticals is the allow-list), and a narrow
-- column is one more thing stopping an array/object param from ever getting here.
ALTER TABLE `tac_saved_crit`
  ADD COLUMN IF NOT EXISTS `param` INT(11) DEFAULT NULL AFTER `amount`;

-- Bulk purchases (mines) are ONE saved-ship row carrying a count, exactly as the lobby
-- buys them. Without this column a fleet saved with 10 mines reloaded as a single mine
-- (bulkBuy was written into the POST, read by getSavedShipsFromJSON, and then dropped on
-- the floor because tac_saved_ship had nowhere to put it) - user report 2026-08-08.
-- DEFAULT 1 so every existing row, and every ship that is not a bulk purchase, reads
-- back as the one unit it has always been.
ALTER TABLE `tac_saved_ship`
  ADD COLUMN IF NOT EXISTS `bulkbuy` INT(11) NOT NULL DEFAULT 1 AFTER `flightsize`;
