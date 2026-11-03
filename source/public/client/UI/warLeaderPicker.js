'use strict';

/* OFFICERS_PLAN.md Stage 34 - the menu the War Leader button opens on the player's own ship, from its Initial Orders tooltip (user request
   2026-11-01: it replaced the C&C's React menu, WarLeaderMenu.js). "Once only once during the scenario, the war leader can do each of the
   following, but can only do one on any turn": the two given now - TAUNT and ANTICIPATE - are picks, one at most (the other picked replaces
   it), picked again or Cancel to take it back; the two of the Firing phase - WRATH and CENTER OF GLORY, from the War Leader icon there - are listed to say whether they are still his.
   Each row's chip says when it was used, or why it cannot be given now. The order waits on his C&C (officers.setWarLeaderOrder) for the commit
   (CnC.doIndividualNotesTransfer); the server checks it against its own rule before writing it (Officers::saveWarLeaderOrder).

   The Lucky Captain's picker's shape (luckyPicker.js, styles/hexPicker.css .fv-missionpicker) in the old menu's Narn red and gold
   (.fv-missionpicker--warleader): a BODY-LEVEL popover anchored to the clicked button, closed by a click outside it, Escape, its x or a choice;
   a choice made after the phase moved on does nothing (officers.setWarLeaderOrder asks the phase). */
window.WarLeaderPicker = (function () {

    var INSET = 8; //viewport clamp
    var current = null; //{ el, ship, onDone, anchor, onDocDown, onKey }

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

    function choose(ability) {
        if (!current) return;
        var ship = current.ship, onDone = current.onDone;
        close();
        officers.setWarLeaderOrder(ship, ability);
        if (onDone) onDone();
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

    /* one ability's row: its name with a chip (ordered, or when it was used / why it cannot be given now), and what it does. With one order waiting
       the other is still a pick - it REPLACES the waiting one (user request 2026-11-01: saves taking it back first, as the Lucky Captain's menu). */
    function row(ship, entry, ability, order) {
        var spent = officers.spentTurn(entry, ability.id);
        var mine = order === ability.id;
        var chip = null;
        if (spent !== null) chip = 'used, turn ' + spent;
        else if (mine) chip = 'ordered';
        else if (!ability.order) chip = 'firing phase';
        else if (!officers.warLeaderMay(entry, ability.id)) chip = 'one a turn';
        var onPick = null;
        if (mine) onPick = function () { choose(null); };
        else if (chip === null) onPick = function () { choose(ability.id); };

        var r = make('button', 'fv-missionpicker__row');
        r.type = 'button';
        if (mine) r.className += ' fv-missionpicker__row--pending';
        if (!onPick) r.className += ' fv-missionpicker__row--disabled';
        var text = make('span', 'fv-missionpicker__text');
        var top = make('span', 'fv-missionpicker__label', ability.name);
        if (chip) top.appendChild(make('span', 'fv-missionpicker__chip' + (mine ? '' : ' fv-missionpicker__chip--muted'), chip));
        text.appendChild(top);
        text.appendChild(make('span', 'fv-missionpicker__note', ability.note));
        r.appendChild(text);
        if (onPick) r.addEventListener('click', function (e) { e.stopPropagation(); onPick(); });
        return r;
    }

    function render() {
        var ship = current.ship, el = current.el;
        var entry = officers.warLeaderEntry(ship);
        var order = officers.warLeaderOrder(ship);
        el.innerHTML = '';
        el.appendChild(header(officers.label('OFF_WLEAD', ship), ship.name));
        el.appendChild(make('div', 'fv-missionpicker__cost', order
            ? officers.warLeaderAbilityName(order) + ' is ordered for this turn - spent as you commit.'
            : 'Initial Orders: one order this turn, each once per battle.'));
        var list = make('div', 'fv-missionpicker__list');
        officers.WARLEADER_MENU.forEach(function (ability) { list.appendChild(row(ship, entry, ability, order)); });
        el.appendChild(list);
        if (order) {
            var foot = make('div', 'fv-missionpicker__footer');
            var cancel = make('button', 'fv-missionpicker__button', 'Cancel');
            cancel.type = 'button';
            cancel.addEventListener('click', function (e) { e.stopPropagation(); choose(null); });
            foot.appendChild(cancel);
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
        if (!window.officers || !officers.canWarLeaderOrder(ship)) return;
        var el = make('div', 'fv-missionpicker fv-missionpicker--warleader');
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
        render();
        position(el, anchor);
        document.addEventListener('pointerdown', current.onDocDown, true);
        document.addEventListener('keydown', current.onKey, true);
    }

    return { open: open, close: close, isOpen: isOpen };
})();
