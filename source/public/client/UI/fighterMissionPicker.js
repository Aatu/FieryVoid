'use strict';

/* FIGHTER MISSIONS (FIGHTER_MISSIONS_PLAN.md §3.10 / D12) - the dropdown the Initial Orders tooltip's Fighter Mission
   button opens. Five rows, each with its one-line effect; the mission already selected is marked, the pending pick
   highlighted, and the cost of a change (free / -60 Ini) heads the list. Combat Space Patrol opens a second list: the
   key ship, nearest first.

   A BODY-LEVEL popover anchored to the clicked button, so a tooltip redraw cannot orphan it. Closed by a click
   outside it, Escape or its x - NOT by a choice any more (user, 2026-11-20): a pick redraws the menu in place, so the
   player sees the pending rail move and can go on to propagate it. A choice made after the phase moved on closes the
   menu and is ignored (fighterMissions.setPending asks canChoose too). Styled as .fv-missionpicker in
   styles/hexPicker.css, the hex stack picker's surface family.

   PROPAGATE (user, 2026-11-20): the button on the right of a row (img/propogateMission.png) gives that mission to
   EVERY flight of the player's fleet that may take a pick this turn, this one included - see propagate(). Combat
   Space Patrol's row has none: its button is on each key ship row, since the key ship is part of the pick. */
window.FighterMissionPicker = (function () {

    var INSET = 8; //viewport clamp
    var current = null; //{ el, ship, onDone, anchor, render, onDocDown, onKey }

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
        if (!fighterMissions.canChoose(current.ship)) { close(); return; }
        fighterMissions.setPending(current.ship, mission, key);
        afterPick(null);
    }

    /* The same pick for every flight of the player's fleet that may take one this turn (canChoose: own, eligible, on
       the board, Initial Orders open) - this one included. Each flight is judged on its own, exactly as if picked from
       its own menu: one already on that mission just keeps it (setPending clears its pending pick), and only a flight
       that is changing and did NOT arrive this turn pays the -60 Ini - the notice counts those, since the cost line at
       the top speaks for this flight alone. The server checks every claim as usual (FighterMissions::saveClaim). */
    function propagate(mission, key) {
        if (!current) return;
        if (!fighterMissions.canChoose(current.ship)) { close(); return; }
        var count = 0, paying = 0;
        for (var i in gamedata.ships) {
            var flight = gamedata.ships[i];
            if (!fighterMissions.canChoose(flight)) continue;
            fighterMissions.setPending(flight, mission, key);
            count++;
            if (fighterMissions.pending(flight) && !fighterMissions.isFreePick(flight)) paying++;
        }
        var label = fighterMissions.getLabel(mission);
        var keyShip = (mission === 'CSP' && key) ? gamedata.getShip(key) : null;
        if (keyShip) label += ' (' + keyShip.name + ')';
        afterPick(label + ' set for ' + count + (count === 1 ? ' flight' : ' flights')
            + (paying ? '; ' + paying + ' of them ' + (paying === 1 ? 'pays ' : 'pay ') + fighterMissions.CHANGE_PENALTY + ' Ini this turn' : ''));
    }

    /* After a pick the menu stays open and is drawn again where it stands, so the pending rail moves to the new pick;
       then the tooltip redraws so its status line shows it. No position() here: the tooltip's redraw replaces the
       button the menu was anchored to, and position() would then jump the menu to the middle of the screen. */
    function afterPick(notice) {
        current.render();
        if (notice) current.el.appendChild(make('div', 'fv-missionpicker__notice', notice));
        if (current.onDone) current.onDone();
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

    /* A row is a focusable div acting as a button, not a <button>: it now holds the Propagate button on its right, and
       a button inside a button is invalid HTML (browsers disagree over which one gets the click). Enter / Space pick it,
       as they did the <button>; a key pressed on the Propagate button is that button's own. */
    function row(label, note, opts) {
        var r = make('div', 'fv-missionpicker__row');
        if (opts.pending) r.className += ' fv-missionpicker__row--pending';
        if (opts.disabled) { r.className += ' fv-missionpicker__row--disabled'; r.setAttribute('aria-disabled', 'true'); }
        var text = make('span', 'fv-missionpicker__text');
        var top = make('span', 'fv-missionpicker__label', label);
        if (opts.chip) top.appendChild(make('span', 'fv-missionpicker__chip', opts.chip));
        text.appendChild(top);
        if (note) text.appendChild(make('span', 'fv-missionpicker__note', note));
        r.appendChild(text);
        if (opts.onPick) {
            r.setAttribute('role', 'button');
            r.tabIndex = 0;
            r.addEventListener('click', function (e) { e.stopPropagation(); opts.onPick(); });
            r.addEventListener('keydown', function (e) {
                if (e.target !== r || (e.key !== 'Enter' && e.key !== ' ')) return;
                e.preventDefault();
                e.stopPropagation();
                opts.onPick();
            });
        }
        if (opts.onPropagate) {
            var p = make('button', 'fv-missionpicker__propagate');
            p.type = 'button';
            p.title = 'Propagate to all Flights';
            p.setAttribute('aria-label', 'Propagate ' + label + ' to all Flights');
            p.addEventListener('click', function (e) { e.stopPropagation(); opts.onPropagate(); });
            r.appendChild(p);
        }
        return r;
    }

    function renderMissions() {
        var ship = current.ship, el = current.el;
        current.render = renderMissions;
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
                    if (m.id === 'CSP') openKeyShips();
                    else choose(m.id, 0);
                },
                //CSP propagates from its key ship rows instead - the key ship is part of the pick
                onPropagate: (m.id === 'CSP') ? null : function () { propagate(m.id, 0); }
            }));
        });
        el.appendChild(list);
    }

    //the key ship list is taller than the mission list, so going there places the menu again
    function openKeyShips() {
        renderKeyShips();
        position(current.el, current.anchor);
    }

    function renderKeyShips() {
        var ship = current.ship, el = current.el;
        current.render = renderKeyShips;
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
                onPick: function () { choose('CSP', c.ship.id); },
                //a key ship is any own-team ship on the board (keyShipValid), so it is a valid key for every flight
                onPropagate: function () { propagate('CSP', c.ship.id); }
            }));
        });
        el.appendChild(list);
    }

    /* Under the anchor (or above it when there is no room), clamped into the viewport. A menu already on screen whose
       anchor is gone stays where it is (clamped again for its new height): since a pick no longer closes the menu, the
       tooltip redraw after it replaces the button it was anchored to, and the key ship list opened after that would
       otherwise jump to the middle of the screen.
       While the card's ORDERS panel is on its LEFT (the card docked left of the hex picker), clearSpot() places it. */
    function position(el, anchor) {
        var vw = document.documentElement.clientWidth, vh = document.documentElement.clientHeight;
        var w = el.offsetWidth, h = el.offsetHeight;
        var left, top;
        if (anchor && anchor.isConnected) {
            var r = anchor.getBoundingClientRect();
            left = r.left;
            top = r.bottom + 4;
            if (top + h > vh - INSET) top = r.top - h - 4;
            var spot = (anchor.closest && anchor.closest('.fv-shipcard--side-left')) ? clearSpot(anchor, r, w, h, vw, vh) : null;
            if (spot) {
                left = spot.left;
                top = spot.top;
            }
        } else if (el.style.left && el.style.top) {
            left = parseFloat(el.style.left);
            top = parseFloat(el.style.top);
        } else {
            left = (vw - w) / 2;
            top = (vh - h) / 3;
        }
        left = Math.max(INSET, Math.min(left, vw - w - INSET));
        top = Math.max(INSET, Math.min(top, vh - h - INSET));
        el.style.left = Math.round(left) + 'px';
        el.style.top = Math.round(top) + 'px';
    }

    function box(node) {
        var b = node.getBoundingClientRect();
        return { l: b.left, t: b.top, r: b.right, b: b.bottom };
    }

    /* The ORDERS panel on the card's LEFT - the card docked left of the hex picker (.fv-shipcard--side-left). Opened
       under the tile and running right, the menu lay across the card and the picker (user, 2026-11-21). Tried in order,
       each clamped into the viewport: running LEFT from the tile (over the panel's own column only, as a right-hand
       panel's menu runs over its column); right of the card and picker; below them; above them. The first spot that
       then covers neither the card nor the picker wins; null (no clear spot) keeps the old placement. */
    function clearSpot(anchor, r, w, h, vw, vh) {
        var avoid = [];
        var root = anchor.closest('.shipNameContainer');
        var card = root && root.querySelector('.fv-shipcard__card');
        if (card) avoid.push(box(card));
        var pickers = document.querySelectorAll('.fv-hexpicker');
        for (var i = 0; i < pickers.length; i++) {
            if (pickers[i].getClientRects().length) avoid.push(box(pickers[i]));
        }
        if (!avoid.length) return null;
        var all = avoid.reduce(function (u, b) {
            return { l: Math.min(u.l, b.l), t: Math.min(u.t, b.t), r: Math.max(u.r, b.r), b: Math.max(u.b, b.b) };
        });
        var GAP = 8;
        var underTile = (r.bottom + 4 + h <= vh - INSET) ? r.bottom + 4 : r.top - h - 4;
        var tries = [
            { left: r.right - w, top: underTile },
            { left: all.r + GAP, top: r.top },
            { left: r.left, top: all.b + GAP },
            { left: r.left, top: all.t - GAP - h }
        ];
        for (var j = 0; j < tries.length; j++) {
            var left = Math.max(INSET, Math.min(tries[j].left, vw - w - INSET));
            var top = Math.max(INSET, Math.min(tries[j].top, vh - h - INSET));
            var menu = { l: left, t: top, r: left + w, b: top + h };
            var clear = avoid.every(function (b) {
                return menu.r <= b.l || menu.l >= b.r || menu.b <= b.t || menu.t >= b.b;
            });
            if (clear) return { left: left, top: top };
        }
        return null;
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
