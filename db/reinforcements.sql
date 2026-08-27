--
-- Reinforcements — Jump Point ENTRANCES (REINFORCEMENTS_PLAN.md §3.1)
--
-- Three columns on `tac_ship`, because each of them is written ONCE and never
-- revised: there is no history to reconstruct, `getTurnDeployed` sits on ~80 hot
-- call sites and must not parse an IndividualNotes list to answer, and POST-side
-- ships carry no loaded notes at all — which is exactly where `getTurnPlaced` is
-- called from. `slot` and `enhvalue` are the precedent.
--
--   reinforcement  bought as a reinforcement. Fixed at purchase, never changes.
--   arrivalturn    NULL = still in hyperspace. N = arrives and places on turn N.
--                  SERVER-WRITTEN ONLY (the deviation sweep at the end of the
--                  formation turn); never in a POST whitelist.
--   arrivalvia     the OPENER unit's id — a reinforcement ship, or a gate — that
--                  this unit is riding through. NULL = unassigned.
--
-- Keyed on the opener and not on the vortex deliberately: the vortex does not
-- exist yet when the manifest is named (it is created two phases later, and for
-- a gate it may never be created at all if the claim is lost). Keying on the
-- opener makes the refund automatic — a manifest that never gets a vortex is
-- simply never stamped.
--
-- ⚠️ `DBManager::submitShip`'s positional `INSERT INTO tac_ship VALUES(null, …)`
-- breaks the moment a column lands here. It is converted to named-column form in
-- the same commit as this migration; do not apply one without the other.
--

ALTER TABLE `tac_ship`
  ADD COLUMN IF NOT EXISTS `reinforcement` tinyint(1) NOT NULL DEFAULT 0 AFTER `enhvalue`,
  ADD COLUMN IF NOT EXISTS `arrivalturn`   int(11)             DEFAULT NULL AFTER `reinforcement`,
  ADD COLUMN IF NOT EXISTS `arrivalvia`    int(11)             DEFAULT NULL AFTER `arrivalturn`;
