'use strict';

/* FIGHTER MISSIONS (FIGHTER_MISSIONS_PLAN.md §3.10 / D12) - the dropdown the Initial Orders tooltip's Fighter Mission
   button opens. Five rows, each with its one-line effect; the mission already selected is marked, the pending pick
   highlighted, and the cost of a change (free / -60 Ini) heads the list. Combat Space Patrol opens a second list: the
   key ship, nearest first.

   A BODY-LEVEL popover anchored to the clicked button, so a tooltip redraw cannot orphan it. Closed by a click
   outside it, Escape, or a choice; a choice made after the phase moved on is ignored (fighterMissions.setPending asks
   canChoose). Styled as .fv-missionpicker in styles/hexPicker.css, the hex stack picker's surface family. */
window.FighterMissionPicker = (function () {

    var INSET = 8; //viewport clamp
    var current = null; //{ el, ship, onDone, onDocDown, onKey }

    function close() {
        if (!current) return;
        document.removeEventListener('pointerdown', current.onDocDown, true);
        document.removeEventListener('keydown', current.onKey, true);
        if (current.el.parentNode) current.el.parentNode.removeChild(current.el);
        current = null;
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

    function choose(mission, key) {
        if (!current) return;
        var ship = current.ship, onDone = current.onDone;
        close();
        if (!fighterMissions.canChoose(ship)) return;
        fighterMissions.setPending(ship, mission, key);
        if (onDone) onDone();
    }

    //the title on top, the flight it is for underneath
    function header(title, subtitle, onBack) {
        var head = make('div', 'fv-missionpicker__header');
        if (onBack) {
            var back = make('button', 'fv-missionpicker__back', '\u2039');
            back.type = 'button';
            back.title = 'Back to the missions';
            back.addEventListener('click', function (e) { e.stopPropagation(); onBack(); });
            head.appendChild(back);
        }
        var heading = make('span', 'fv-missionpicker__heading');
        heading.appendChild(make('span', 'fv-missionpicker__title', title));
        heading.appendChild(make('span', 'fv-missionpicker__subtitle', subtitle));
        head.appendChild(heading);
        var x = make('button', 'fv-missionpicker__close', '\u00d7');
        x.type = 'button';
        x.title = 'Close';
        x.addEventListener('click', function (e) { e.stopPropagation(); close(); });
        head.appendChild(x);
        return head;
    }

    function costLine(ship) {
        var free = fighterMissions.isFreePick(ship);
        return make('div', 'fv-missionpicker__cost' + (free ? ' fv-missionpicker__cost--free' : ''),
            free ? 'Arrived this turn: No initiative penalty'
                 : 'Costs ' + fighterMissions.CHANGE_PENALTY + ' Ini this turn; new mission applies from next turn');
    }

    function row(label, note, opts) {
        var r = make('button', 'fv-missionpicker__row');
        r.type = 'button';
        if (opts.pending) r.className += ' fv-missionpicker__row--pending';
        if (opts.disabled) { r.disabled = true; r.className += ' fv-missionpicker__row--disabled'; }
        var text = make('span', 'fv-missionpicker__text');
        var top = make('span', 'fv-missionpicker__label', label);
        if (opts.chip) top.appendChild(make('span', 'fv-missionpicker__chip', opts.chip));
        text.appendChild(top);
        if (note) text.appendChild(make('span', 'fv-missionpicker__note', note));
        r.appendChild(text);
        if (opts.onPick) r.addEventListener('click', function (e) { e.stopPropagation(); opts.onPick(); });
        return r;
    }

    function renderMissions() {
        var ship = current.ship, el = current.el;
        el.innerHTML = '';
        el.appendChild(header('Fighter Mission', ship.name));
        el.appendChild(costLine(ship));
        var list = make('div', 'fv-missionpicker__list');
        var sel = fighterMissions.selected(ship);
        var pending = fighterMissions.pending(ship);
        fighterMissions.REGISTRY.forEach(function (m) {
            var chip = (sel.m === m.id) ? 'selected' : null;
            var isPending = !!pending && pending.m === m.id;
            list.appendChild(row(m.label, m.summary, {
                chip: chip, pending: isPending,
                onPick: function () {
                    if (m.id === 'CSP') renderKeyShips();
                    else choose(m.id, 0);
                }
            }));
        });
        el.appendChild(list);
    }

    function renderKeyShips() {
        var ship = current.ship, el = current.el;
        el.innerHTML = '';
        el.appendChild(header('Combat Space Patrol', 'Key ship for ' + ship.name, renderMissions));
        el.appendChild(make('div', 'fv-missionpicker__cost',
            'Within ' + fighterMissions.CSP_RANGE + ' hexes of it: +' + fighterMissions.CSP_INI + ' Ini and wins ties'));
        var list = make('div', 'fv-missionpicker__list');
        var sel = fighterMissions.selected(ship);
        var pending = fighterMissions.pending(ship);
        var candidates = fighterMissions.keyShipCandidates(ship);
        if (!candidates.length) {
            list.appendChild(row('No friendly ship on the board', null, { disabled: true }));
        }
        candidates.forEach(function (c) {
            var dist = Math.round(c.distance) + (Math.round(c.distance) === 1 ? ' hex' : ' hexes');
            list.appendChild(row(c.ship.name, dist + (c.inZone ? '' : ' \u2014 outside zone'), {
                chip: (sel.m === 'CSP' && sel.k === Number(c.ship.id)) ? 'selected' : null,
                pending: !!pending && pending.m === 'CSP' && Number(pending.k) === Number(c.ship.id),
                onPick: function () { choose('CSP', c.ship.id); }
            }));
        });
        el.appendChild(list);
        position(current.el, current.anchor);
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
        if (!window.fighterMissions || !fighterMissions.canChoose(ship)) return;
        var el = make('div', 'fv-missionpicker');
        el.setAttribute('role', 'menu');
        //the tooltip and the map must not see clicks that land in here
        ['mousedown', 'mouseup', 'click', 'pointerdown', 'contextmenu'].forEach(function (type) {
            el.addEventListener(type, function (e) { e.stopPropagation(); });
        });
        current = {
            el: el, ship: ship, onDone: onDone, anchor: anchor,
            onDocDown: function (e) { if (current && !current.el.contains(e.target)) close(); },
            onKey: function (e) { if (e.key === 'Escape') { e.stopPropagation(); close(); } }
        };
        document.body.appendChild(el);
        renderMissions();
        position(el, anchor);
        document.addEventListener('pointerdown', current.onDocDown, true);
        document.addEventListener('keydown', current.onKey, true);
    }

    return { open: open, close: close, isOpen: isOpen };
})();
