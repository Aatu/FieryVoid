/* Where the system card (system/SystemCard.js) goes, once it has been drawn and measured.
 *
 * The same rule the lobby's click menu follows (besideWindow.js): BESIDE the ship window the icon sits in, flush
 * against its frame, on the side with more room - so the card never covers the window's own icons - and level
 * with the icon: its top on the icon's top in the upper half of the screen, its bottom on the icon's bottom in
 * the lower half. Then the step the old getPosition never took: the measured card is kept inside the screen.
 *
 * No room on either side of the window (a card opened wide, a phone, a window spanning the screen): the card goes to
 * the screen edge on the roomier side, so it covers as little of the window as it can. No window at all (an icon
 * outside one): beside the icon, on whichever side it fits. Either way it is clamped to the screen.
 *
 * All boxes are viewport rectangles (getBoundingClientRect). Returns { left, top } in px.
 */

export const EDGE = 8; //never nearer the screen's edge than this

export default function placeTooltip(iconBox, windowBox, size, view) {
    const width = size.width;
    const height = size.height;
    let left;

    if (windowBox) {
        const sides = [
            { room: view.width - windowBox.right - EDGE, left: windowBox.right, screenEdge: view.width - EDGE - width },
            { room: windowBox.left - EDGE, left: windowBox.left - width, screenEdge: EDGE },
        ].sort((a, b) => b.room - a.room);
        const side = sides.find(candidate => candidate.room >= width);
        left = side ? side.left : sides[0].screenEdge;
    } else {
        if (view.width - iconBox.right - EDGE >= width) left = iconBox.right;
        else if (iconBox.left - EDGE >= width) left = iconBox.left - width;
        else left = view.width - EDGE - width;
    }
    left = Math.max(EDGE, Math.min(left, view.width - EDGE - width));

    let top = iconBox.top > view.height / 2 ? iconBox.bottom - height : iconBox.top;
    top = Math.max(EDGE, Math.min(top, view.height - EDGE - height));

    return { left, top };
}
