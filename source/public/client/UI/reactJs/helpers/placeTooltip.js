/* Where the system card (system/SystemCard.js) goes, once it has been drawn and measured.
 *
 * The same rule the lobby's click menu follows (besideWindow.js): BESIDE the ship window the icon sits in, flush
 * against its frame, on the side with more room - so the card never covers the window's own icons - and level
 * with the icon: its top on the icon's top in the upper half of the screen, its bottom on the icon's bottom in
 * the lower half. Then the step the old getPosition never took: the measured card is kept inside the screen.
 *
 * No room on either side (a narrow screen, a card opened wide, a window spanning the screen): ABOVE or BELOW the
 * window, the roomier first, in line with the icon - its left on the icon's left in the left half of the screen, its
 * right on the icon's right in the right half (user request 2026-11-12: on narrow screens the card covered the window
 * with room to spare above or below it). No room on any side: of those four places, the one that covers least of
 * the window without covering the icon itself.
 *
 * No window at all (an icon outside one): the same four places around the icon. Either way the card is clamped to
 * the screen.
 *
 * All boxes are viewport rectangles (getBoundingClientRect). Returns { left, top } in px.
 */

export const EDGE = 8; //never nearer the screen's edge than this

const overlap = (a, b) => Math.max(0, Math.min(a.right, b.right) - Math.max(a.left, b.left))
    * Math.max(0, Math.min(a.bottom, b.bottom) - Math.max(a.top, b.top));

export default function placeTooltip(iconBox, windowBox, size, view) {
    const width = size.width;
    const height = size.height;
    const clampLeft = left => Math.max(EDGE, Math.min(left, view.width - EDGE - width));
    const clampTop = top => Math.max(EDGE, Math.min(top, view.height - EDGE - height));
    const around = windowBox || iconBox;

    const level = iconBox.top > view.height / 2 ? iconBox.bottom - height : iconBox.top;
    const inLine = iconBox.left > view.width / 2 ? iconBox.right - width : iconBox.left;
    const roomier = (a, b) => b.room - a.room;

    const beside = [
        { room: view.width - EDGE - around.right, left: around.right, top: level },
        { room: around.left - EDGE, left: around.left - width, top: level },
    ].sort(roomier);
    const stacked = [
        { room: view.height - EDGE - around.bottom, left: inLine, top: around.bottom },
        { room: around.top - EDGE, left: inLine, top: around.top - height },
    ].sort(roomier);

    //a place that fits covers nothing, so the first that fits wins; past that, the least covered (ties keep the order)
    let best = null;
    beside.concat(stacked).forEach(place => {
        const left = clampLeft(place.left);
        const top = clampTop(place.top);
        const card = { left: left, top: top, right: left + width, bottom: top + height };
        const coversIcon = overlap(card, iconBox) > 0 ? 1 : 0;
        const coversWindow = windowBox ? overlap(card, windowBox) : 0;
        if (!best || coversIcon < best.coversIcon || (coversIcon === best.coversIcon && coversWindow < best.coversWindow)) {
            best = { left: left, top: top, coversIcon: coversIcon, coversWindow: coversWindow };
        }
    });

    return { left: best.left, top: best.top };
}
