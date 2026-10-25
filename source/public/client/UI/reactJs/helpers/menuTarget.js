/* The system the lobby's open system menu (Add Enhancements & Damage) is editing, so its icon in the ship
 * window can wear a highlight while the menu is open - which system is being enhanced or damaged is then
 * plain to see (user request 2026-10-16).
 *
 * SystemInfoMenu sets it while it shows a menu beside a ship window (the lobby - game.php passes no
 * window and is untouched) and clears it when it closes; SystemIcon asks isMenuTarget. Each change
 * repaints the open ship windows, which do not otherwise re-render when a menu opens.
 *
 * The SHIP is matched by object: the lobby's windows hold the fleet's own ship objects
 * (shipWindowManager.open), so a store blueprint sharing an id with a fleet ship never lights up.
 */

let target = null;

export function setMenuTarget(ship, system) {
    const next = (ship && system) ? { ship, systemId: system.id } : null;
    if (next === target || (next && target && next.ship === target.ship && next.systemId === target.systemId)) return;
    target = next;
    if (window.shipWindowManagerReact) window.shipWindowManagerReact.update();
}

export function isMenuTarget(ship, system) {
    return target !== null && Boolean(ship) && Boolean(system) && target.ship === ship && target.systemId === system.id;
}
