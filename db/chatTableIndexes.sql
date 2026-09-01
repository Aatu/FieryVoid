--
-- chat table: add a (gameid, id) index and a (time) index
--
-- ⚠️ THIS IS HYGIENE, NOT A FIX. It will NOT resolve the `1040 Too many connections`
-- ⚠️ bursts of 2026-09-01. Do not run it expecting that. There is no urgency here at
-- ⚠️ all; it is safe to leave unapplied indefinitely.
--
-- WHAT HAPPENED, AND WHY THIS FILE READS LIKE A CLIMBDOWN
-- ------------------------------------------------------
-- These indexes were originally written as the fix for those bursts. That diagnosis was
-- WRONG and is recorded here so nobody rebuilds it from the same bad premise.
--
-- The reasoning was: `chat` is MyISAM with only PRIMARY KEY (`id`), so
-- deleteOldChatMessages()'s non-sargable `DELETE ... WHERE DATE_ADD(time, INTERVAL 3
-- DAY) < NOW()` full-scans the table under a MyISAM TABLE-LEVEL WRITE lock, which
-- blocks every concurrent chat poll while each one holds its own MySQL connection.
--
-- That mechanism does not exist on live. It was read out of `db/emptyDatabase.sql`, a
-- MySQL 5.7.20 / Ubuntu 16.04 dump of the OLD `B5CGM` database, and then "confirmed"
-- against the local Docker DB -- which `docker/mariadb/Dockerfile:9` SEEDS FROM THAT
-- SAME DUMP. Two copies of one stale file, mistaken for two sources.
--
-- The live table (phpMyAdmin export, 2026-09-01) is:
--
--   ENGINE=InnoDB  CHARSET=utf8mb3  -- already InnoDB. No table lock. Ever.
--   ~13 rows                        -- 3-day retention genuinely works
--   PRIMARY KEY (`id`) only         -- the one part that was right
--
-- Thirteen rows. The "expensive full scan" reads thirteen rows. Both halves of the
-- argument are dead.
--
-- ⚠️ `db/emptyDatabase.sql` IS NOT THE LIVE SCHEMA and the local Docker DB cannot
-- ⚠️ corroborate it. Live is `u253336_b5cgm` on `sql-005.webh.cloud` -- a REMOTE,
-- ⚠️ SHARED MariaDB 11.4.5 instance. The only way to see the live schema from a dev
-- ⚠️ box is to ask for a phpMyAdmin export.
--
-- WHAT IS STILL WORTH DOING, AND WHY IT IS SMALL
-- ----------------------------------------------
-- `WHERE gameid = ? AND id > ? ORDER BY id DESC LIMIT 15` has no index on `gameid`, so
-- it walks the primary key filtering row by row. At 13 rows that is free. It stops
-- being free only if retention is ever lengthened or the table is allowed to grow, and
-- an index that is right at any size costs nothing to add now. `KEY (time)` is the same
-- argument for the purge.
--
-- The companion change in DBManager::deleteOldChatMessages() -- rewriting the predicate
-- so the column is not wrapped in a function -- is worth keeping on its own merits: a
-- non-sargable predicate is a latent defect regardless of today's row count, and the
-- rewrite selects exactly the same rows. It is already applied.
--
-- NOT INCLUDED, DELIBERATELY
-- --------------------------
--   * No ENGINE clause. Live is already InnoDB; naming it again would force a pointless
--     full table rebuild.
--   * No charset change. Live is utf8mb3 (3-byte) and must stay that way --
--     ChatManager::encodeAstralCharacters() exists precisely because of it. Moving to
--     utf8mb4 is a separate decision and must not ride along here.
--


-- ---------------------------------------------------------------------------
-- STEP 0 -- Check. If `gameid_id` and `time` already appear, stop; re-running
-- step 1 would fail on "Duplicate key name".
-- ---------------------------------------------------------------------------

SHOW CREATE TABLE `chat`;
SELECT COUNT(*) AS total_rows FROM `chat`;


-- ---------------------------------------------------------------------------
-- STEP 1 -- Add the indexes.
--
-- One ALTER, so one pass. On a table this size it is instant. No `IF NOT EXISTS`
-- on the keys: MariaDB accepts it but MySQL does not, and step 0 is the guard.
-- ---------------------------------------------------------------------------

ALTER TABLE `chat`
    ADD KEY `gameid_id` (`gameid`, `id`),
    ADD KEY `time` (`time`);


-- ---------------------------------------------------------------------------
-- STEP 2 -- Verify the keys exist.
--
-- Do NOT bother EXPLAINing this table. At ~13 rows the optimiser will often pick a
-- full scan no matter what indexes exist, because scanning 13 rows genuinely is
-- cheaper than an index lookup -- and that is the correct choice, not a failure.
-- These indexes are insurance against future growth, not a present-day speedup.
-- ---------------------------------------------------------------------------

SHOW CREATE TABLE `chat`;


-- ---------------------------------------------------------------------------
-- ROLLBACK
-- ---------------------------------------------------------------------------
--
-- ALTER TABLE `chat` DROP KEY `gameid_id`, DROP KEY `time`;
--
