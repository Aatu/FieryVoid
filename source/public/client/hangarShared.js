"use strict";

// ============================================================================
// Shared client-side hangar helpers — the SINGLE mirror of the server-side
// HangarOps eligibility / box-accounting gates (each function names its PHP
// counterpart). Extracted 2026-07-12 from three drifting per-file copies in
// DeploymentDock.js / shipTooltipFireMenu.js / confirm.js — see
// HANGAR_OPERATIONS_PLAN.md §10 ("Flagged for a future refactor" → Tier-2
// shared-helpers extraction).
//
// LOAD ORDER: game.php AND gamelobby.php load this file BEFORE every consumer
// (DeploymentDock.js aliases these at script-evaluation time; confirm.js /
// shipTooltipFireMenu.js resolve them at call time). Everything here is PURE —
// no gamedata / shipManager / jQuery access — so nothing else must load first.
//
// baseSystems.js (Hangar.refreshHangarTooltip's nested boxesPerCraftOf) keeps
// its own tiny copy ON PURPOSE: the model files stay self-contained so a page
// that loads only the model layer never depends on this file.
// ============================================================================

window.HangarShared = (function () {

    // A system the fighter dock/launch pipeline treats as a hangar bay:
    // ordinary hangar, Catapult (Stage 16) or Fighter Rail. NOT DockingCollar
    // (LCV rails bypass the fighter stash pipeline) — LCV checks gate on
    // isLCVRail / name 'dockingCollar' separately. NOTE a ShadowHangar keeps
    // name === 'hangar', so it passes this check — callers that must exclude
    // integrated-fighter bays gate on sys.isShadowHangar themselves.
    function isDockHangar(sys) {
        return !!(sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail'));
    }

    function isCatapultSys(sys) {
        return !!(sys && (sys.isCatapult || sys.name === 'catapult'));
    }

    // Effective capacity in BOXES: a catapult is a single-fighter rail (flat 1,
    // its extra boxes are structural HP and it operates regardless of damage);
    // an ordinary hangar/rail is maxhealth minus damage that got past armour.
    // Mirrors HangarOps::effectiveCapacity (PHP).
    function effectiveHangarBoxes(hangar) {
        if (!hangar) return 0;
        if (isCatapultSys(hangar)) return 1;
        var netDamage = 0;
        if (Array.isArray(hangar.damage)) {
            hangar.damage.forEach(function (d) {
                netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
            });
        }
        return Math.max(0, parseInt(hangar.maxhealth, 10) - netDamage);
    }

    // Hangar boxes a single craft occupies. A unitSize<1 craft (Vorlon Assault
    // Fighter et al.) needs more than one box each; a unitSize>1 ultralight
    // (Zorth) packs several per box and costs a FRACTIONAL 1/unitSize boxes
    // (0.5). Mirrors HangarOps::boxesPerCraftForClass (PHP).
    function boxesPerCraftFromUnitSize(unitSize) {
        var u = (unitSize != null) ? parseFloat(unitSize) : 1;
        if (u > 0 && u < 1) return Math.ceil(1 / u);   // superheavy: >1 box/craft
        if (u > 1) return 1 / u;                        // ultralight: fractional box/craft
        return 1;
    }

    // Per-craft box cost of a stored hangarUsage entry: the server stamps
    // boxesPerCraft on such entries (float: fractional 0.5 round-trips);
    // fall back to deriving from unitSize. Mirrors HangarOps::boxesPerCraftForEntry.
    function boxesPerCraftForEntry(entry) {
        if (entry && entry.boxesPerCraft) {
            var b = parseFloat(entry.boxesPerCraft);
            return b > 0 ? b : 1;
        }
        return boxesPerCraftFromUnitSize(entry ? entry.unitSize : 1);
    }

    // Boxes occupied by an entry/flight in $hangar — a catapult counts craft
    // 1:1; ordinary hangars charge the per-craft box cost. NOTE: does NOT read
    // multi-bay occupancy lists — callers that need occupancy-aware accounting
    // (the firing-phase dock dialog, window.hangarUsedBoxesOnBay) layer that on
    // top themselves.
    function entryBoxesInHangar(hangar, entry) {
        var n = parseInt(entry.flightSize || 1, 10);
        return isCatapultSys(hangar) ? n : n * boxesPerCraftForEntry(entry);
    }

    function craftBoxesInHangar(hangar, count, unitSize) {
        var n = parseInt(count || 0, 10);
        return isCatapultSys(hangar) ? n : n * boxesPerCraftFromUnitSize(unitSize);
    }

    // Mirrors HangarOps::trueSizeOf (PHP): an explicit hangarRequired wins;
    // generic 'fighters'/'normal' falls back to jinkinglimit-based
    // classification (the same buckets checkChoices() in gamelobby.js uses).
    function categoryForFlight(flight) {
        var req = String(flight.hangarRequired || '').trim();
        var lower = req.toLowerCase();
        if (lower === '' || lower === 'fighters' || lower === 'normal') {
            var jink = parseInt(flight.jinkinglimit || 0, 10);
            if (jink >= 99) return 'ultralight';
            if (jink >= 10) return 'light';
            if (jink >= 8)  return 'medium';
            if (jink >= 6)  return 'heavy';
            return 'medium';
        }
        return req;
    }

    // Mirrors HangarOps::hangarAcceptsFighterClass (PHP). A hangar that declares
    // a non-empty allowedFighterClasses list accepts ONLY flights whose phpclass
    // is in it (e.g. the GaimSuom's Reska-only bays); empty/absent = unrestricted.
    function hangarAcceptsFighterClass(sys, flight) {
        if (!sys) return false;
        var allowed = sys.allowedFighterClasses;
        if (!Array.isArray(allowed) || allowed.length === 0) return true;   //unrestricted bay
        var cls = flight ? String(flight.phpclass) : '';
        return allowed.indexOf(cls) !== -1;
    }

    // Mirrors HangarOps::bayReservesFighterClass (PHP). True only when the bay
    // SPECIFICALLY reserves this flight's class (non-empty allow-list containing
    // it) — used to sort fill order so a Reska fills the Suom's Reska-only bay
    // before spilling into the universal primary that the medium Koist needs.
    function bayReservesFighterClass(sys, flight) {
        if (!sys) return false;
        var allowed = sys.allowedFighterClasses;
        if (!Array.isArray(allowed) || allowed.length === 0) return false;   //unrestricted — not a reservation
        var cls = flight ? String(flight.phpclass) : '';
        return allowed.indexOf(cls) !== -1;
    }

    // Mirrors HangarOps::bayReservesFlight (PHP). The auto-fill PRIORITY predicate:
    // a bay is reserved for this flight by its phpclass allow-list OR by an EXACT
    // hangarType category match (a bay typed 'ultralight' is reserved for ultralight
    // flights). Reserved bays fill ahead of universal bays that merely accept the
    // flight via the size hierarchy. "Exact" is deliberate — a 'light' bay accepts
    // ultralights but does NOT reserve them; universal 'fighters'/'normal' reserve
    // nothing. Ordering only, never eligibility.
    function bayReservesFlight(sys, flight) {
        if (!sys) return false;
        if (bayReservesFighterClass(sys, flight)) return true;
        var hType = String(sys.hangarType || '').toLowerCase().trim();
        if (hType === '' || hType === 'fighters' || hType === 'normal') return false;
        return hType === String(categoryForFlight(flight)).toLowerCase().trim();
    }

    function lowerKeys(obj) {
        var out = {};
        for (var k in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, k)) {
                out[String(k).toLowerCase()] = obj[k];
            }
        }
        return out;
    }

    // Mirrors HangarOps::hangarAcceptsCategory (PHP) — combat-fighter size
    // hierarchy plus shuttle/BP compatibility. Universal 'fighters'/'normal'
    // slots derive their permissions from the ship's $fighters declaration when
    // ship is provided (handles multi-category carriers like Decurion/Falenna).
    // Keep in sync with the server helper so the eligibility gates match
    // end-of-turn validation.
    function hangarAcceptsCategory(hangarType, category, ship) {
        var hType = String(hangarType || '').toLowerCase().trim();
        var cat   = String(category   || '').toLowerCase().trim();
        if (hType === '' || cat === '') return false;
        var rank = { ultralight: 1, light: 2, medium: 3, heavy: 4 };

        if (hType === cat) return true;
        if (rank[hType] && rank[cat]) return rank[cat] <= rank[hType];
        if ((cat === 'shuttles' || cat === 'minesweeping shuttles') && rank[hType]) return true;

        //Breaching Pods: dedicated BP slot (exact-match above), Assault Shuttle
        //slot, or ANY combat fighter slot (heavy/medium/light/ultralight).
        if (cat === 'breaching pods') {
            if (hType === 'assault shuttles') return true;
            if (rank[hType]) return true;
        }

        if (hType === 'fighters' || hType === 'normal') {
            if (cat === 'shuttles' || cat === 'minesweeping shuttles') return true;
            if (!ship || !ship.fighters) {
                if (rank[cat]) return true;
                return false;
            }
            var declared = lowerKeys(ship.fighters);
            if (rank[cat]) {
                if (declared['normal']) return true;
                var sizes = ['heavy', 'medium', 'light', 'ultralight'];
                for (var i = 0; i < sizes.length; i++) {
                    if (!declared[sizes[i]]) continue;
                    if (rank[cat] <= rank[sizes[i]]) return true;
                }
                return false;
            }
            if (cat === 'assault shuttles') return !!declared['assault shuttles'];
            if (cat === 'breaching pods') {
                if (declared['breaching pods']) return true;
                if (declared['assault shuttles']) return true;
                if (declared['normal']) return true;
                if (declared['heavy']) return true;
                if (declared['medium']) return true;
                if (declared['light']) return true;
                if (declared['ultralight']) return true;
                return false;
            }
            //Categories bound to DEDICATED systems never ride a universal fighter
            //bay: 'superheavy' lives in the catapult, 'lcvs' on DockingCollar rails
            //(mirrors the PHP guard — Stormfalcon's superheavy declaration must not
            //open its 14-box hangar to the Sky Serpent).
            if (cat === 'superheavy' || cat === 'lcvs') return false;
            //Custom combat category (e.g. 'Hunter-Killers'): universal bay accepts it
            //when the ship declares that exact category. allowedFighterClasses still
            //gates the actual phpclass.
            if (declared[cat]) return true;
            return false;
        }
        return false;
    }

    // Mirrors HangarOps::isCustomCombatCategory (PHP). A category the fleet-
    // builder routes through its isolated per-name pool (currently only
    // 'Hunter-Killers'), NOT the shared size hierarchy or shuttle/BP families.
    function isCustomCombatCategory(category) {
        var cat = String(category || '').toLowerCase().trim();
        if (cat === '') return false;
        var shared = { 'heavy':1,'medium':1,'light':1,'ultralight':1,'normal':1,
            'shuttles':1,'minesweeping shuttles':1,'cargo shuttles':1,'medical shuttles':1,
            'lifeboats':1,'assault shuttles':1,'breaching pods':1,'superheavy':1,'lcvs':1 };
        return !shared[cat];
    }

    // Hangar display label for the dock dialogs: catapults and rails are
    // numbered across the carrier ("Catapult N" / "Fighter Rail N"); ordinary
    // hangars get a ship-location prefix (Main/Front/Aft/Port/Stbd) with
    // per-prefix disambiguation, grouping SixSidedShip sub-locations (31/32,
    // 41/42) under one prefix. (The LAUNCH dialog keeps its own sequential
    // labeller — different, stateful shape.)
    function hangarLabelFor(carrier, hangar) {
        if (hangar && (hangar.isCatapult || hangar.name === 'catapult')) {
            var cats = carrier.systems.filter(function (s) { return s && (s.isCatapult || s.name === 'catapult'); });
            if (cats.length <= 1) return 'Catapult';
            return 'Catapult ' + (cats.indexOf(hangar) + 1);
        }
        if (hangar && (hangar.isRail || hangar.name === 'fighterRail')) {
            var rails = carrier.systems.filter(function (s) { return s && (s.isRail || s.name === 'fighterRail'); });
            if (rails.length <= 1) return 'Fighter Rail';
            return 'Fighter Rail ' + (rails.indexOf(hangar) + 1);
        }
        var groupOf = function (l) {
            if (l === 31 || l === 32) return 3;
            if (l === 41 || l === 42) return 4;
            return l;
        };
        var prefix = (function (loc) {
            var l = parseInt(loc, 10);
            if (l === 0) return 'Main';
            if (l === 1) return 'Front';
            if (l === 2) return 'Aft';
            if (l === 3 || l === 31 || l === 32) return 'Port';
            if (l === 4 || l === 41 || l === 42) return 'Stbd';
            return 'Hangar';
        })(hangar.location);
        var siblings = carrier.systems.filter(function (s) {
            if (!s || s.name !== 'hangar') return false;     //hangars only — catapults/rails labelled separately
            return groupOf(parseInt(s.location, 10)) === groupOf(parseInt(hangar.location, 10));
        });
        if (siblings.length <= 1) return prefix + ' Hangar';
        var idx = siblings.indexOf(hangar);
        return prefix + ' Hangar ' + (idx + 1);
    }

    function hangarLabelByIdFor(carrier, hangarId) {
        for (var i = 0; i < carrier.systems.length; i++) {
            var sys = carrier.systems[i];
            if (isDockHangar(sys) && sys.id === hangarId) return hangarLabelFor(carrier, sys);
        }
        return 'Hangar';
    }

    return {
        isDockHangar:              isDockHangar,
        isCatapultSys:             isCatapultSys,
        effectiveHangarBoxes:      effectiveHangarBoxes,
        boxesPerCraftFromUnitSize: boxesPerCraftFromUnitSize,
        boxesPerCraftForEntry:     boxesPerCraftForEntry,
        entryBoxesInHangar:        entryBoxesInHangar,
        craftBoxesInHangar:        craftBoxesInHangar,
        categoryForFlight:         categoryForFlight,
        hangarAcceptsFighterClass: hangarAcceptsFighterClass,
        bayReservesFighterClass:   bayReservesFighterClass,
        bayReservesFlight:         bayReservesFlight,
        hangarAcceptsCategory:     hangarAcceptsCategory,
        isCustomCombatCategory:    isCustomCombatCategory,
        hangarLabelFor:            hangarLabelFor,
        hangarLabelByIdFor:        hangarLabelByIdFor
    };
})();
