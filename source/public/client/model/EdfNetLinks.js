"use strict";

/* =========================================================================================
   WALKERS OF SIGMA-957 - ENERGY DRAINING NET LINKING, CLIENT MIRROR
   WALKERS_OF_SIGMA_PLAN.md 3.7 (Stage 7). Server twin: source/server/handlers/EdfNetLinks.php.

   WHY THIS EXISTS. The server resolves Net linking in TacGamedata::setEdfHexes() and publishes
   the result as gamedata.edfNetHexes - which is correct, authoritative, and always one commit
   behind the player. A Net's whole tactical question is "where do I put this ship", and answering
   it by moving, committing, and looking is not answering it at all (user request, 2026-09-05). So
   the movement phase recomputes the field locally from the PLOTTED positions, and the player sees
   the corridors and the filled area form as they drag.

   ⭐⭐ THIS IS ADVISORY, AND THAT IS THE WHOLE SAFETY ARGUMENT FOR MIRRORING IT AT ALL. Nothing
   here decides anything: the to-hit penalty, the drain, own-fleet immunity and overlap collapse
   all read gamedata.edfHexes, which only ever comes from the server. If this file and the PHP
   ever disagree, the symptom is a preview that redraws slightly on commit - not a rule resolved
   two ways. Keep it that way; never let a rule read this module's output.

   ⚠️⚠️ IT IS STILL A MIRROR, AND MIRRORS ROT. Every rule below is a line-for-line port of the PHP,
   including the geometry lifted out of HexZone (whose tolerances are subtle enough to have cost a
   real investigation once - see the notes on touchTolerance). If you change one side, change the
   other, and re-run the differential: tests/replay/walkersStage7Harness.php has a mode that feeds
   the same random boards through both and compares the hex sets.

   ⚠️ The geometry is ported rather than borrowed on purpose. hexgrid.hexCoToPixel works in SCREEN
   pixels (scaled by Config.HEX_SIZE); the server's Mathlib::hexCoToPixel lays out hexes of
   circumradius 1, and every tolerance in HexZone is expressed in THAT space. Mixing them would
   silently scale every distance test by 50.
   ========================================================================================= */

window.EdfNetLinks = (function () {

    var LINK_RANGE = 3;        //"at most three hexes away from each other"
    var FILL_MIN_NETS = 3;     //a closed area needs three
    var MAX_FILL_SWEEP = 4000; //defensive; see the PHP

    /* ---------------------------------------------------------------- geometry ------- */

    /* Identical to OffsetCoordinate::toCube() and to HexRegion.offsetToCube. `r & 1` behaves the
       same in JS and PHP for negative r (both are two's complement), which matters: half the board
       has negative coordinates. */
    function offsetToCube(hex) {
        var x = hex.q - ((hex.r + (hex.r & 1)) / 2);

        return { x: x, y: -x - hex.r, z: hex.r };
    }

    /* CubeCoordinate::distanceTo. round() in the PHP is a no-op for integer input. */
    function distance(a, b) {
        var ca = offsetToCube(a);
        var cb = offsetToCube(b);

        return Math.max(Math.abs(ca.x - cb.x), Math.abs(ca.y - cb.y), Math.abs(ca.z - cb.z));
    }

    /* Mathlib::hexCoToPixel - pointy-top hexes of circumradius 1, NOT screen pixels. */
    function hexCoToPixel(hex) {
        return { x: Math.sqrt(3) * (hex.q - 0.5 * (hex.r & 1)), y: 1.5 * hex.r };
    }

    /* HexZone::touchTolerance. The greatest distance a line of this direction may pass from a hex
       centre while still touching that hex - which runs from sqrt(3)/2 = 0.866 for a line at
       0/60 degrees up to 1.0 for one clipping a corner at 30/90. ⚠️ A fixed sqrt(3)/2 misses every
       corner-clipping line; that is not a simplification, it is the bug this replaced. */
    function touchTolerance(abx, aby) {
        var len = Math.sqrt(abx * abx + aby * aby);

        if (len <= 1e-9) return Math.sqrt(3) / 2; //no line to take a normal from

        var nx = -aby / len;
        var ny = abx / len;
        var s = Math.sqrt(3) / 2;

        return Math.max(Math.abs(ny), Math.abs(nx * s + ny * 0.5), Math.abs(nx * s - ny * 0.5));
    }

    /* HexZone::pointToSegmentDistance. */
    function pointToSegmentDistance(p, a, b) {
        var abx = b.x - a.x;
        var aby = b.y - a.y;
        var lenSq = abx * abx + aby * aby;

        if (lenSq <= 1e-9) return Math.sqrt(Math.pow(p.x - a.x, 2) + Math.pow(p.y - a.y, 2));

        var t = ((p.x - a.x) * abx + (p.y - a.y) * aby) / lenSq;

        t = Math.max(0, Math.min(1, t));

        var cx = a.x + t * abx;
        var cy = a.y + t * aby;

        return Math.sqrt(Math.pow(p.x - cx, 2) + Math.pow(p.y - cy, 2));
    }

    /* HexZone::hull - monotone chain. ⚠️ `<= 0` in the cross test, so collinear middles are
       DISCARDED; containsUnit's edge walk is what covers the degenerate hull that leaves. */
    function hull(points) {
        if (points.length < 3) return points.slice();

        var sorted = points.slice().sort(function (a, b) {
            if (a.x !== b.x) return a.x - b.x;

            return a.y - b.y;
        });

        var cross = function (O, A, B) {
            return (A.x - O.x) * (B.y - O.y) - (A.y - O.y) * (B.x - O.x);
        };

        var lower = [];

        sorted.forEach(function (p) {
            while (lower.length >= 2 && cross(lower[lower.length - 2], lower[lower.length - 1], p) <= 0) lower.pop();
            lower.push(p);
        });

        var upper = [];

        sorted.slice().reverse().forEach(function (p) {
            while (upper.length >= 2 && cross(upper[upper.length - 2], upper[upper.length - 1], p) <= 0) upper.pop();
            upper.push(p);
        });

        lower.pop();
        upper.pop();

        return lower.concat(upper);
    }

    /* HexZone::pointInPolygon. The 1e-9 denominator guard is load-bearing, not defensive: a
       horizontal edge otherwise divides by zero. */
    function pointInPolygon(p, polygon) {
        var n = polygon.length;

        if (n < 3) return false;

        var inside = false;

        for (var i = 0, j = n - 1; i < n; j = i++) {
            var xi = polygon[i].x, yi = polygon[i].y;
            var xj = polygon[j].x, yj = polygon[j].y;
            var denom = (yj - yi) || 1e-9;
            var intersect = ((yi > p.y) !== (yj > p.y)) && (p.x < (xj - xi) * (p.y - yi) / denom + xi);

            if (intersect) inside = !inside;
        }

        return inside;
    }

    /* HexZone::containsUnit - is this hex inside, or touched by, the zone the positions define.
       ⚠️ The `+ 1e-9` on every tolerance is not decoration: a unit exactly on the line lands
       precisely ON the tolerance, where bare floating point decides by ~1e-15. */
    function containsUnit(unitPos, positions) {
        if (positions.length < 2) return false;

        var unitPx = hexCoToPixel(unitPos);
        var px = positions.map(hexCoToPixel);

        if (positions.length === 2) {
            var tolerance = touchTolerance(px[1].x - px[0].x, px[1].y - px[0].y) + 1e-9;

            return pointToSegmentDistance(unitPx, px[0], px[1]) <= tolerance;
        }

        var shape = hull(px);

        if (pointInPolygon(unitPx, shape)) return true;

        //A hex the boundary merely CLIPS is in, exactly as the two-point rule counts a line that
        //touches. This walk is also what answers the all-collinear case that hull() degenerates.
        var sides = shape.length;

        for (var i = 0, j = sides - 1; i < sides; j = i++) {
            var edgeTolerance = touchTolerance(shape[i].x - shape[j].x, shape[i].y - shape[j].y) + 1e-9;

            if (pointToSegmentDistance(unitPx, shape[j], shape[i]) <= edgeTolerance) return true;
        }

        return false;
    }

    /* ---------------------------------------------------------------- corridors ------ */

    /* Every shortest-path corridor between two hexes, as the hexes STRICTLY BETWEEN them. */
    function corridorCandidates(a, b) {
        var span = distance(a, b);

        if (span < 2) return [];

        var paths = [];

        walk(a, b, span, [], paths);
        paths.forEach(sortHexes);

        return paths;
    }

    function walk(current, target, remaining, sofar, paths) {
        if (remaining <= 1) {
            paths.push(sofar);

            return;
        }

        mathlib.getNeighbouringHexes(current, 1).forEach(function (neighbour) {
            var hex = { q: neighbour.q, r: neighbour.r };

            if (distance(hex, target) !== remaining - 1) return;

            walk(hex, target, remaining - 1, sofar.concat([hex]), paths);
        });
    }

    /* The ranking, in order: most enemy units standing in the corridor, then most hexes not
       already this team's field, then lowest (q,r) hex by hex.
       ⚠️ Rule 3 exists solely to make the order TOTAL - see the PHP. Compared numerically, never
       as a joined string, or the answer would depend on where on the map the fleet is flying. */
    function pickCorridor(a, b, team, coverage, occupancy) {
        var candidates = corridorCandidates(a, b);

        if (!candidates.length) return [];
        if (candidates.length === 1) return candidates[0];

        var best = null;
        var bestEnemies = -1;
        var bestNew = -1;

        candidates.forEach(function (path) {
            var enemies = 0;
            var fresh = 0;

            path.forEach(function (hex) {
                var key = hex.q + ',' + hex.r;

                if (occupancy[key]) {
                    Object.keys(occupancy[key]).forEach(function (otherTeam) {
                        if (parseInt(otherTeam, 10) !== team) enemies += occupancy[key][otherTeam];
                    });
                }

                if (!(coverage[key] && coverage[key][team])) fresh++;
            });

            if (best === null
                || enemies > bestEnemies
                || (enemies === bestEnemies && fresh > bestNew)
                || (enemies === bestEnemies && fresh === bestNew && compareHexes(path, best) < 0)) {
                best = path;
                bestEnemies = enemies;
                bestNew = fresh;
            }
        });

        return best;
    }

    function sortHexes(hexes) {
        hexes.sort(function (a, b) {
            if (a.q !== b.q) return a.q - b.q;

            return a.r - b.r;
        });
    }

    function compareHexes(a, b) {
        var shared = Math.min(a.length, b.length);

        for (var i = 0; i < shared; i++) {
            if (a[i].q !== b[i].q) return a[i].q - b[i].q;
            if (a[i].r !== b[i].r) return a[i].r - b[i].r;
        }

        return a.length - b.length;
    }

    /* ---------------------------------------------------------------- closed areas --- */

    /* The 2-CORE: repeatedly drop every Net with fewer than two links inside the group, so what
       is left is exactly the Nets that lie on a cycle - i.e. that "form a closed area".
       ⚠️ A CHAIN IS NOT AN AREA, which is the bug game 4338 found: three Waymarkers linked
       #1-#2-#3 in a line filled hexes that lay beside the chain rather than inside anything.
       The pruning must ITERATE - dropping a chain's ends leaves its middle with no links. */
    function closedCore(group, links) {
        var core = {};

        group.forEach(function (index) { core[index] = true; });

        var changed = true;

        while (changed) {
            changed = false;
            Object.keys(core).forEach(function (key) {
                var index = parseInt(key, 10);
                var degree = 0;

                if (links[index]) {
                    Object.keys(links[index]).forEach(function (neighbour) {
                        if (core[neighbour]) degree++;
                    });
                }

                if (degree < 2) {
                    delete core[index];
                    changed = true;
                }
            });
        }

        return Object.keys(core).map(function (key) { return parseInt(key, 10); });
    }

    function fillClosedAreas(teamNets, links, team, coverage, added, refusals) {
        var seen = {};

        for (var start = 0; start < teamNets.length; start++) {
            if (seen[start]) continue;

            var group = [];
            var stack = [start];

            seen[start] = true;

            while (stack.length) {
                var index = stack.pop();

                group.push(index);

                if (!links[index]) continue;

                Object.keys(links[index]).forEach(function (neighbour) {
                    if (seen[neighbour]) return;
                    seen[neighbour] = true;
                    stack.push(parseInt(neighbour, 10));
                });
            }

            group = closedCore(group, links);

            if (group.length < FILL_MIN_NETS) continue;

            group.sort(function (a, b) { return a - b; });

            var positions = group.map(function (index) { return teamNets[index].pos; });
            var shipId = teamNets[group[0]].shipId;
            var cap = 2 * group.length - 1;
            var candidates = areaCandidates(positions, team, coverage, refusals);

            if (candidates === null) continue;

            var keys = Object.keys(candidates);

            if (keys.length > cap) {
                refusals.push('team ' + team + ': ' + group.length + ' linked Nets enclose '
                            + keys.length + ' unfilled hexes, over the cap of ' + cap);
                continue;
            }

            keys.forEach(function (key) { addHex(added, coverage, candidates[key], team, shipId); });
        }
    }

    function areaCandidates(positions, team, coverage, refusals) {
        var minQ = positions[0].q, maxQ = positions[0].q;
        var minR = positions[0].r, maxR = positions[0].r;

        positions.forEach(function (pos) {
            minQ = Math.min(minQ, pos.q); maxQ = Math.max(maxQ, pos.q);
            minR = Math.min(minR, pos.r); maxR = Math.max(maxR, pos.r);
        });

        //Grown by one on every side, because containsUnit counts a hex the hull merely TOUCHES.
        minQ--; maxQ++; minR--; maxR++;

        var sweep = (maxQ - minQ + 1) * (maxR - minR + 1);

        if (sweep > MAX_FILL_SWEEP) {
            refusals.push('team ' + team + ': refused a closed-area sweep of ' + sweep + ' hexes');

            return null;
        }

        var candidates = {};

        for (var r = minR; r <= maxR; r++) {
            for (var q = minQ; q <= maxQ; q++) {
                var key = q + ',' + r;

                if (coverage[key] && coverage[key][team]) continue;

                var hex = { q: q, r: r };

                if (!containsUnit(hex, positions)) continue;

                candidates[key] = hex;
            }
        }

        return candidates;
    }

    /* ---------------------------------------------------------------- entry point ---- */

    function addHex(added, coverage, hex, team, shipId) {
        var key = hex.q + ',' + hex.r;

        if (coverage[key] && coverage[key][team]) return;

        if (!coverage[key]) coverage[key] = {};
        coverage[key][team] = 1;

        added.push({ q: hex.q, r: hex.r, team: team, shipId: shipId });
    }

    /**
     * @param nets      [{shipId, systemId, team, pos:{q,r}}] - already filtered for destroyed /
     *                  in-hyperspace / offline, exactly as setEdfHexes() filters before calling
     *                  the PHP.
     * @param coverage  {"q,r": {team: 1}} - the field as it stands BEFORE linking, i.e. every
     *                  disc plus the Nets' own hexes. MUTATED, so hand in a scratch copy.
     * @param occupancy {"q,r": {team: count}} - where the units are, for tie-break rule 1.
     * @return          [{q, r, team, shipId}]
     */
    function resolve(nets, coverage, occupancy) {
        if (!nets || nets.length < 2) return [];

        var byTeam = {};

        nets.forEach(function (net) {
            if (!byTeam[net.team]) byTeam[net.team] = [];
            byTeam[net.team].push(net);
        });

        var added = [];
        var refusals = [];

        Object.keys(byTeam).map(Number).sort(function (a, b) { return a - b; }).forEach(function (team) {
            var teamNets = byTeam[team];

            if (teamNets.length < 2) return;

            //The stable order everything below depends on - see the PHP.
            teamNets.sort(function (a, b) {
                if (a.shipId !== b.shipId) return a.shipId - b.shipId;

                return a.systemId - b.systemId;
            });

            var links = {};

            for (var i = 0; i < teamNets.length; i++) {
                for (var j = i + 1; j < teamNets.length; j++) {
                    if (distance(teamNets[i].pos, teamNets[j].pos) > LINK_RANGE) continue;

                    if (!links[i]) links[i] = {};
                    if (!links[j]) links[j] = {};
                    links[i][j] = true;
                    links[j][i] = true;

                    var shipId = teamNets[i].shipId; //lower id of the pair, by the sort above

                    pickCorridor(teamNets[i].pos, teamNets[j].pos, team, coverage, occupancy)
                        .forEach(function (hex) { addHex(added, coverage, hex, team, shipId); });
                }
            }

            fillClosedAreas(teamNets, links, team, coverage, added, refusals);
        });

        resolve.refusals = refusals;

        return added;
    }

    return {
        resolve: resolve,
        LINK_RANGE: LINK_RANGE,
        FILL_MIN_NETS: FILL_MIN_NETS,
        //exported for the differential test only - nothing in the app should need these
        distance: distance,
        containsUnit: containsUnit,
        corridorCandidates: corridorCandidates,
        closedCore: closedCore
    };
})();
