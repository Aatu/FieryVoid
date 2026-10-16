/* Where the lobby's system menu (Add Enhancements & Damage) opens: BESIDE the ship window it was opened
 * from, not over it. It used to open at the clicked icon, which put it on top of the window's own systems
 * and stats (user request 2026-10-16).
 *
 * `windowBox` is the window's on-screen rectangle (getBoundingClientRect, so a scaled window reads as it is
 * drawn) and `iconBox` the clicked icon's. The menu goes on the side of the window with more room, FLUSH
 * against the window's frame (no gap - user request 2026-10-16), level with the icon: its top on the icon's
 * top in the upper half of the screen, its bottom on the icon's bottom in the lower half - the flip
 * SystemInfoMenu's getPosition makes, so it grows away from the nearer edge.
 *
 * Returns the CSS offsets for SystemInfoMenu's absolutely-positioned tooltip ({left|right, top|bottom}, in
 * px), or null when there is no window or neither side has room for the menu (ApplyDamageMenu's max-width
 * plus the screen-edge margin - a phone, or a window spanning the screen), so the caller falls back to its
 * old placement.
 */

export const MENU_WIDTH = 300; //ApplyDamageMenu's Container max-width
export const EDGE_MARGIN = 8;  //never nearer the screen's edge than this

export default function besideWindow(iconBox, windowBox, viewWidth, viewHeight) {
    if (!iconBox || !windowBox) return null;
    const roomRight = viewWidth - windowBox.right - EDGE_MARGIN;
    const roomLeft = windowBox.left - EDGE_MARGIN;
    if (Math.max(roomRight, roomLeft) < MENU_WIDTH) return null;

    const position = {};
    if (roomRight >= roomLeft) {
        position.left = windowBox.right;
    } else {
        position.right = viewWidth - windowBox.left;
    }
    if (iconBox.top > viewHeight / 2) {
        position.bottom = Math.max(EDGE_MARGIN, viewHeight - iconBox.bottom);
    } else {
        position.top = Math.max(EDGE_MARGIN, iconBox.top);
    }
    return position;
}
