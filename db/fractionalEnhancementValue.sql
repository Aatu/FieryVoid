-- Fractional enhancement values (2026-08-10).
--
-- `enhvalue` holds a unit's PER-UNIT enhancement cost. It was INT, but not every
-- enhancement is priced in whole points: MINE_DMG ("Extra Damage") costs 0.5 per level,
-- so five levels is 2.5 per mine. Rounding that to 3 on the way in made a saved fleet
-- reload DEARER than it was bought, and made tac_saved_list.points (which is summed from
-- the real, unrounded cost) disagree with the figure the lobby recomputes on load —
-- e.g. list 96 listed at 2949 and loaded at 2952.
--
-- Both columns change: tac_ship feeds pointCostEnh for a LIVE game, and saving a fleet out
-- of a live game reads it back, so leaving that one INT would reintroduce the same drift on
-- the next save.
--
-- Widening only. Existing whole-point rows are already valid DECIMAL values and are
-- unaffected, so this is safe to apply to a populated database.

ALTER TABLE `tac_saved_ship` MODIFY `enhvalue` DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE `tac_ship`       MODIFY `enhvalue` DECIMAL(10,2) NOT NULL DEFAULT 0;

-- Backfill for rows written before the change. The loss is EXACTLY recoverable: MINE_DMG
-- is the only enhancement in the game priced in halves (0.5/level, Enhancements.php), so a
-- stored value is half a point too high if and only if its MINE_DMG level count is ODD —
-- every other enhancement on the unit contributes a whole number. Even counts, and units
-- with no MINE_DMG at all, were never rounded and are left alone.
--
-- Only saved fleets are corrected. tac_ship rows belong to games already in progress,
-- where the figure is a display total on the fleet list and rewriting it under a running
-- game buys nothing.

UPDATE `tac_saved_ship` s
JOIN `tac_saved_enh` e
  ON e.shipid = s.id AND e.enhid = 'MINE_DMG' AND (e.numbertaken % 2) = 1
SET s.enhvalue = s.enhvalue - 0.5;

-- tac_saved_list.points is stored, not derived, and was summed from the REAL cost, so it
-- was already right — it is the reload that moved. Nothing to correct there.
