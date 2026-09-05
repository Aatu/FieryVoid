-- Reinforcements Stage 9 — THE ENTRANCE/EXIT TERMINOLOGY SWAP (REINFORCEMENTS_PLAN.md Stage 9).
--
-- Up to Stage 8 the code called the BLUE vortex an "entrance" (units enter the battle through it)
-- and the YELLOW one an "exit" (units leave the battle through it). That is backwards from the way
-- a jump point is named in the fiction and in the artwork, which has always been the other way
-- round (img/ships/JumpPointExit.png is the BLUE one). Stage 9 swaps the two words:
--
--     BLUE   = jump point EXIT      - a doorway OUT of hyperspace; reinforcements arrive through it
--     YELLOW = jump point ENTRANCE  - a doorway INTO hyperspace;   units leave the battle through it
--
-- ⚠️ THE YELLOW CLASS WAS DELIBERATELY NOT RENAMED. It stays `SpawnJumpPoint`, so no identifier
-- anywhere is recycled to mean the opposite of what it used to mean. Only the blue subclass and the
-- two arrival damageclasses change, and both are renamed to names that never existed before.
--
-- Two persisted strings move, and nothing else about these rows is wrong:
--
--   tac_ship.phpclass        'SpawnJumpPointEntrance' -> 'SpawnJumpPointExit'
--   tac_fireorder.damageclass 'jumpentry' -> 'jumpexit', 'gateentry' -> 'gateexit'
--
-- ⚠️ RUN THIS WITH THE DEPLOY, NOT AFTER IT. phpclass is what BaseShip reconstruction calls
-- `new $phpclass(...)` with, so a row still saying 'SpawnJumpPointEntrance' after the class file is
-- renamed is a fatal on every load of that game. The damageclass rows are gentler - a stale one
-- reads as an ordinary ballistic order and draws a red hex - but a replay of that turn is wrong
-- until it is fixed.

UPDATE `tac_ship`
   SET `phpclass` = 'SpawnJumpPointExit'
 WHERE `phpclass` = 'SpawnJumpPointEntrance';

UPDATE `tac_fireorder`
   SET `damageclass` = 'jumpexit'
 WHERE `damageclass` = 'jumpentry';

UPDATE `tac_fireorder`
   SET `damageclass` = 'gateexit'
 WHERE `damageclass` = 'gateentry';
