/* Wheel handling that can actually cancel the page scroll.
 *
 * React 18 attaches ONE `wheel` listener per root container and registers it as PASSIVE
 * (along with `touchstart` / `touchmove`), so `e.preventDefault()` inside an `onWheel`
 * prop is a no-op: the ticker's value changes AND the page scrolls underneath it (user
 * report 2026-08-08, pre-battle damage menus). The only way to stop the scroll is a
 * native listener registered with { passive: false } on the element itself.
 *
 * Usage — pass the result of bind() as a React `ref`:
 *
 *     this.wheelRef = nonPassiveWheel(e => this.step(e.deltaY < 0 ? 1 : -1));
 *     ...
 *     <input ref={this.wheelRef} />
 *
 * The returned callback ref attaches on mount and detaches when React hands it null on
 * unmount, so there is nothing to clean up in componentWillUnmount. Build it ONCE per
 * bound element (in the constructor, or cached per row key) — a ref callback created
 * inline in render() is a new function every pass and React would detach/reattach the
 * listener on every keystroke.
 */
const nonPassiveWheel = (handler) => {
    let bound = null;

    const onWheel = (e) => {
        e.preventDefault();
        handler(e);
    };

    return (node) => {
        if (bound === node) return;

        if (bound) bound.removeEventListener('wheel', onWheel, { passive: false });
        bound = node;
        if (bound) bound.addEventListener('wheel', onWheel, { passive: false });
    };
};

export default nonPassiveWheel;
