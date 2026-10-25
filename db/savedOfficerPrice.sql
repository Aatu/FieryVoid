-- Officer prices in saved fleets (OFFICERS_PLAN.md §9.9, 2026-10-15).
--
-- ⚠️ APPLY BEFORE DEPLOYING THE CODE THAT READS IT. Saving a fleet writes this column, and
-- loading one selects it: without it a save fails and a load comes back with no
-- enhancements at all.
--
-- A saved unit's enhancements are ONE lump, tac_saved_ship.enhvalue, so nothing could say
-- how much of it an officer cost. On 2026-10-15 the fighter officers were re-priced (scaled
-- by the flight's size; the Evader and the Expert Motivator no longer charged per craft), and
-- a reloaded fleet has to swap the old price for the new one. From now on every OFFICER row
-- keeps its own price here (Officers::rowPrice at save time); every other row stays NULL.
--
-- Existing rows are left NULL on purpose: Manager::loadSavedFleet reads NULL as "saved under
-- the Stage 4 prices" (Officers::legacyPrice), which is what every row saved before this
-- change was charged. Additive and nullable, so safe on a populated database and safe to
-- apply while the old code is still running (it never names the column, so its rows stay
-- NULL and are read the same way).

ALTER TABLE `tac_saved_enh` ADD COLUMN IF NOT EXISTS `enhvalue` DECIMAL(10,2) NULL DEFAULT NULL AFTER `enhname`;
