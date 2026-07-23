//Shared hit-chart builder (SHIPWINDOW_REDESIGN_PLAN.md Stage 1b): the single source for
//the per-section hit percentages so ShipInfo (text lines) and HitChartPanel (flyout
//tables) never drift. Returns [{ location, name, entries: [{ name, chance }] }] in
//hit-chart order; entries carry the d20 percentage per system with any retargeting
//prefix ("Structure:") stripped, exactly as both legacy renderers did.
const buildHitChart = (ship) => {
    if (!ship.hitChart) return [];

    const names = ["Primary", "Front", "Aft", "Port", "Starboard"];
    let toDo = 5; //(almost) always try to show all 5 sections, there may be holes

    if (ship.base && !ship.smallBase) {
        names[1] = "Sections";
        toDo = 2;
    } else if (ship.SixSidedShip) {
        names[31] = "Port Front";
        names[32] = "Port Aft";
        names[41] = "Starboard Front";
        names[42] = "Starboard Aft";
        toDo = 43;
    }

    const chart = [];

    for (let i = 0; i < toDo; i++) {
        if (ship.hitChart[i] === undefined) {
            continue; //no appropriate entry, skip it
        }

        const entries = [];
        let current = 0;

        for (const key in ship.hitChart[i]) {
            const chance = Math.floor((key - current) / 20 * 100);
            current = key;

            let name = ship.hitChart[i][key];
            const n = name.indexOf(":");
            if (n > 0) { //hide retargeting to a different section
                name = name.substring(n + 1);
            }

            entries.push({ name: name, chance: chance });
        }

        chart.push({ location: i, name: names[i], entries: entries });
    }

    return chart;
};

export default buildHitChart;
