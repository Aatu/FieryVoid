/*Fighter/shuttle complement of a ship as display rows for the lobby datasheet
  (ShipNotesPanel) — the port of legacy shipwindow.js updateNotes lines 423-530
  (SHIPWINDOW_REDESIGN_PLAN.md Stage 3b). The legacy copy becomes unreachable in the
  lobby the moment the React window takes over, so this is the only live version;
  the legacy file is deleted wholesale in Stage 4.

  Returns an array of strings, e.g. ["6 Reska Light Fighters", "2 Shuttles"].
  Read-only: never mutates ship or its systems (blueprints share static data —
  see arch_client_system_shared_reference).*/

const capitalize = (words) =>
    words.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');

const buildComplement = (ship) => {
    const rows = [];

    if (!ship || ship.flight) return rows;

    const fighters = ship.fighters || {};

    if (Object.keys(fighters).length > 0) {
        //Per-bay fighter-class allow-list (arch_hangar_class_allowlist): a
        //class-restricted bay reserves some of a size category's slots for a specific
        //fighter (e.g. the Suom's aft bay reserves 6 of its 6 "light" slots for
        //Reska). List those reserved slots by the fighter's real name and subtract
        //them from the generic size line. getReservedFighterComposition returns one
        //row PER restricted bay, so rows naming the same fighter within a size
        //category are merged (the Roka's two 3-Reska bays read "6 Reska ..."), while
        //bays restricted to a DIFFERENT fighter keep their own row. Rows are cloned
        //before accumulating so the shared system-derived objects are never mutated.
        const reservedByCat = {};
        if (shipManager.systems.shipHasRestrictedHangar(ship)) {
            const reservedRows = shipManager.systems.getReservedFighterComposition(ship);
            for (let rr = 0; rr < reservedRows.length; rr++) {
                const rcat = reservedRows[rr].category;
                if (!reservedByCat[rcat]) reservedByCat[rcat] = [];
                const catRows = reservedByCat[rcat];
                let merged = false;
                for (let mr = 0; mr < catRows.length; mr++) {
                    if (catRows[mr].phpclass === reservedRows[rr].phpclass) {
                        catRows[mr].count += reservedRows[rr].count;
                        merged = true;
                        break;
                    }
                }
                if (!merged) {
                    catRows.push({
                        category: reservedRows[rr].category,
                        phpclass: reservedRows[rr].phpclass,
                        displayName: reservedRows[rr].displayName,
                        count: reservedRows[rr].count,
                        isGroup: reservedRows[rr].isGroup
                    });
                }
            }
        }

        for (const i in fighters) {
            const amount = fighters[i];
            const capitalizedType = capitalize(i);

            //Emit reserved-fighter lines for this size category first, then whatever
            //generic capacity is left over. Clamp the reserved count to the declared
            //amount so a designer mismatch can't make the generic line go negative.
            const reservedHere = reservedByCat[i];
            if (reservedHere && (i === "heavy" || i === "medium" || i === "light")) {
                let remaining = amount;
                for (let rh = 0; rh < reservedHere.length; rh++) {
                    const rCount = Math.min(reservedHere[rh].count, remaining);
                    if (rCount <= 0) continue;
                    //Single-class bay: "6 Reska Light Fighters". Multi-class bay:
                    //standalone group label, pluralized — "24 Hunter-Killers".
                    if (reservedHere[rh].isGroup) {
                        rows.push(rCount + " " + reservedHere[rh].displayName + "s");
                    } else {
                        rows.push(rCount + " " + reservedHere[rh].displayName + " " + capitalizedType + " Fighters");
                    }
                    remaining -= rCount;
                }
                if (remaining > 0) {
                    rows.push(remaining + " " + capitalizedType + " Fighters");
                }
                continue;
            }

            if (i === "normal") {
                rows.push(amount + " Fighters");
            } else if (i === "superheavy" || i === "heavy" || i === "medium" || i === "light" || i === "ultralight") {
                rows.push(amount + " " + capitalizedType + " Fighters");
            } else if (i === "shuttles" || i === "minesweeping shuttles" || i === "cargo shuttles" || i === "lifeboats" || i === "medical shuttles" || i === "presidential shuttle" || i === "yacht") {
                //Auto-populated free shuttles — listed via getDefaultShuttleComposition below.
                continue;
            } else {
                //something other than fighters
                rows.push(amount + " " + capitalizedType);
            }
        }
    }

    //Leftover hangar capacity is auto-filled with shuttles (or minesweeping shuttles
    //when minesweeperbonus > 0) — same rule as HangarOps::populateInitialHangarUsage
    //step 3 on the server.
    const defaultShuttles = shipManager.systems.getDefaultShuttleComposition(ship);
    for (let s = 0; s < defaultShuttles.length; s++) {
        rows.push(defaultShuttles[s].count + " " + defaultShuttles[s].type);
    }

    return rows;
};

export default buildComplement;
