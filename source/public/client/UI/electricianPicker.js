'use strict';

/* OFFICERS_PLAN.md Stage 36 - the menu the Initial Orders tooltip's Electrician button opens on the player's own flight: an Expert Electrician's
   points this turn (officers.shuntPoints - the maximum damage of every gun switched off, over the flight's guns, rounded half up) and two rows of
   - / + splitting them between the flight's offensive bonus (direct fire only) and its thrust, with All to OB / All to thrust / Cancel all buttons
   under them (Cancel all: play test 4526). What is left unplaced is
   put down at random by the server as Initial Orders close (Officers::saveElectricianShunt). The weapons are switched off with their own Power Off
   (offered while he serves - officers.mayShunt), so this menu only reads them.

   The Analyst's picker's surface (styles/hexPicker.css .fv-missionpicker) in the Electrician's yellow (.fv-missionpicker--electrician, the colour of
   his img/electrician.png button): a BODY-LEVEL popover anchored to the clicked button, closed by a click outside it, Escape or its x. It stays open
   while the player steps; the tooltip is redrawn once it closes after a change. A step made after the phase moved on does nothing
   (officers.stepShunt asks canElectrify). */
window.ElectricianPicker = (function () {

    var INSET = 8; //viewport clamp
    var current = null; //{ el, ship, onDone, anchor, changed, onDocDown, onKey }

    function close() {
        if (!current) return;
        var done = current.changed ? current.onDone : null;
        document.removeEventListener('pointerdown', current.onDocDown, true);
        document.removeEventListener('keydown', current.onKey, true);
        if (current.el.parentNode) current.el.parentNode.removeChild(current.el);
        current = null;
        if (done) done();
    }

    function isOpen() {
        return current !== null;
    }

    function make(tag, className, text) {
        var el = document.createElement(tag);
        if (className) el.className = className;
        if (text !== undefined && text !== null) el.textContent = text;
        return el;
    }

    //a change of the split: redraw this menu, and tell the tooltips and the ship window that the flight's thrust and OB moved
    function changed(didChange) {
        if (!current || !didChange) return;
        current.changed = true;
        render();
        if (window.webglScene && typeof webglScene.customEvent === 'function') webglScene.customEvent('SystemDataChanged', { ship: current.ship });
    }

    function header(title, subtitle) {
        var head = make('div', 'fv-missionpicker__header');
        var heading = make('span', 'fv-missionpicker__heading');
        heading.appendChild(make('span', 'fv-missionpicker__title', title));
        heading.appendChild(make('span', 'fv-missionpicker__subtitle', subtitle));
        head.appendChild(heading);
        var x = make('button', 'fv-missionpicker__close', '×');
        x.type = 'button';
        x.title = 'Close';
        x.addEventListener('click', function (e) { e.stopPropagation(); close(); });
        head.appendChild(x);
        return head;
    }

    function stepButton(text, title, enabled, onStep) {
        var b = make('button', 'fv-missionpicker__step', text);
        b.type = 'button';
        b.title = title;
        b.disabled = !enabled;
        b.addEventListener('click', function (e) { e.stopPropagation(); if (!b.disabled) onStep(); });
        return b;
    }

    //one stepper row: what the points buy, its value, and - / + for one point fewer / more
    function stepRow(label, note, value, field, split) {
        var r = make('div', 'fv-missionpicker__row');
        var text = make('span', 'fv-missionpicker__text');
        text.appendChild(make('span', 'fv-missionpicker__label', label));
        text.appendChild(make('span', 'fv-missionpicker__note', note));
        r.appendChild(text);
        r.appendChild(stepButton('-', 'One point fewer', split[field] > 0, function () { changed(officers.stepShunt(current.ship, field, -1)); }));
        r.appendChild(make('span', 'fv-missionpicker__value', value));
        r.appendChild(stepButton('+', 'One point more', split.free > 0, function () { changed(officers.stepShunt(current.ship, field, 1)); }));
        return r;
    }

    function footButton(text, onPick) {
        var b = make('button', 'fv-missionpicker__button', text);
        b.type = 'button';
        b.addEventListener('click', function (e) { e.stopPropagation(); onPick(); });
        return b;
    }

    function costText(split, state) {
        if (state.off === 0) return 'Switch weapons off in their Power settings (right-click Off: every craft\'s) to shunt their power';
        var line = split.points + ' point' + (split.points === 1 ? '' : 's') + ' from ' + state.off + ' weapon' + (state.off === 1 ? '' : 's')
            + ' switched off' + (state.fried > 0 ? ' (' + state.fried + ' fried)' : '');
        if (split.points <= 0) return line + ' - too little to make a point';
        return line + (split.free > 0 ? ' - ' + split.free + ' unplaced, put down at random as Initial Orders close' : ' - every point placed');
    }

    function render() {
        var ship = current.ship, el = current.el;
        var split = officers.shuntSplit(ship);
        var state = officers.shuntState(ship);
        el.innerHTML = '';
        el.appendChild(header('Expert Electrician', ship.name));
        el.appendChild(make('div', 'fv-missionpicker__cost', costText(split, state)));
        var list = make('div', 'fv-missionpicker__list');
        list.appendChild(stepRow('Offensive bonus', 'Direct fire only, this turn - 5 a point', '+' + split.ob * 5, 'ob', split));
        list.appendChild(stepRow('Thrust', 'This turn\'s movement - 1 a point', '+' + split.thrust, 'thrust', split));
        el.appendChild(list);
        el.appendChild(make('div', 'fv-missionpicker__warn', 'Each weapon switched off fries on a d' + officers.FRY_DIE
            + ' of 1 as the turn ends: lost for the battle, though its power is still shunted.'));
        if (split.points > 0) {
            var foot = make('div', 'fv-missionpicker__footer');
            foot.appendChild(footButton('All to OB', function () { changed(officers.allShunt(current.ship, 'ob')); }));
            foot.appendChild(footButton('All to thrust', function () { changed(officers.allShunt(current.ship, 'thrust')); }));
            foot.appendChild(footButton('Cancel all', function () { changed(officers.clearShunt(current.ship)); }));
            el.appendChild(foot);
        }
    }

    //under the anchor (or above it when there is no room), clamped into the viewport
    function position(el, anchor) {
        var vw = document.documentElement.clientWidth, vh = document.documentElement.clientHeight;
        var w = el.offsetWidth, h = el.offsetHeight;
        var left, top;
        if (anchor && anchor.isConnected) {
            var r = anchor.getBoundingClientRect();
            left = r.left;
            top = r.bottom + 4;
            if (top + h > vh - INSET) top = r.top - h - 4;
        } else {
            left = (vw - w) / 2;
            top = (vh - h) / 3;
        }
        left = Math.max(INSET, Math.min(left, vw - w - INSET));
        top = Math.max(INSET, Math.min(top, vh - h - INSET));
        el.style.left = Math.round(left) + 'px';
        el.style.top = Math.round(top) + 'px';
    }

    function open(ship, anchor, onDone) {
        close();
        if (!window.officers || !officers.canElectrify(ship)) return;
        var el = make('div', 'fv-missionpicker fv-missionpicker--electrician');
        el.setAttribute('role', 'menu');
        //the tooltip and the map must not see clicks that land in here
        ['mousedown', 'mouseup', 'click', 'pointerdown', 'contextmenu'].forEach(function (type) {
            el.addEventListener(type, function (e) { e.stopPropagation(); });
        });
        current = {
            el: el, ship: ship, onDone: onDone, anchor: anchor, changed: false,
            onDocDown: function (e) { if (current && !current.el.contains(e.target)) close(); },
            onKey: function (e) { if (e.key === 'Escape') { e.stopPropagation(); close(); } }
        };
        document.body.appendChild(el);
        render();
        position(el, anchor);
        document.addEventListener('pointerdown', current.onDocDown, true);
        document.addEventListener('keydown', current.onKey, true);
    }

    return { open: open, close: close, isOpen: isOpen };
})();
