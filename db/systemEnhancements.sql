--
-- System Enhancements persistence (WEAPON_ENHANCEMENTS_PLAN.md §3.3, D1)
--
-- Two ADDITIVE tables, one mirroring `tac_enhancements` (games) and one mirroring
-- `tac_saved_enh` (fleet lists), each plus a `systemid`.
--
-- D1: this is deliberately NOT a primary-key migration on the two existing tables.
-- A per-system enhancement needs `systemid` in the key - six Twin Arrays each with
-- Gunsights is six rows, not one - and rebuilding the PK of the two tables that every
-- existing game and every saved fleet reads on every load is the largest blast radius
-- available for the smallest gain. Additive tables leave every existing query, the
-- replay corpus and the deploy path untouched.
--
-- D13: `sysname` is NOT part of the key. The id is the identity; the name is the
-- INTEGRITY CHECK. System ids are pure constructor order, so if a contributor inserts a
-- system mid-constructor every id after it shifts by one and a stored id silently names a
-- DIFFERENT system. A row whose stored name no longer matches the system now at that id is
-- DROPPED on load, never repaired by hunting for the name elsewhere - see plan §4.7.1.
--
-- `enhvalue` is the price PAID (D4/D10), stored so a purchase can be refunded exactly.
-- DECIMAL to match tac_saved_ship.enhvalue (see db/fractionalEnhancementValue.sql).
-- ⚠️ mysqli returns DECIMAL as a PHP STRING - cast on read.
--

CREATE TABLE IF NOT EXISTS `tac_sys_enhancements` (
  `gameid`      INT(11)       NOT NULL,
  `shipid`      INT(11)       NOT NULL,
  `systemid`    INT(11)       NOT NULL,
  `sysname`     VARCHAR(50)   NOT NULL,          -- D13: the system's ->name, verified on load
  `enhid`       VARCHAR(10)   NOT NULL,
  `numbertaken` INT(11)       NOT NULL,
  `enhname`     VARCHAR(50)   NOT NULL,
  `enhvalue`    DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gameid`,`shipid`,`systemid`,`enhid`),
  KEY `idx_shipid` (`shipid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tac_saved_sysenh` (
  `listid`      INT(11)       NOT NULL,
  `shipid`      INT(11)       NOT NULL,
  `systemid`    INT(11)       NOT NULL,
  `sysname`     VARCHAR(50)   NOT NULL,
  `enhid`       VARCHAR(10)   NOT NULL,
  `numbertaken` INT(11)       NOT NULL,
  `enhname`     VARCHAR(255)  NOT NULL,
  `enhvalue`    DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`listid`,`shipid`,`systemid`,`enhid`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_sysenh_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_sysenh_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
