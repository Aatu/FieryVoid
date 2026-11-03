'use strict';

/* OFFICERS_PLAN.md Stage 35 - the dropdown the Initial Orders tooltip's Analyse button opens on an enemy unit: an Expert Analyst's
   scans (officers.analystScansFor - all five at a ship, two at a flight), each with what it shows; the one waiting at this unit
   highlighted, and a Cancel scan button under the list while one waits anywhere. One scan a turn, so a pick here replaces a scan
   waiting at another unit.

   The Fighter Missions picker's surface (styles/hexPicker.css .fv-missionpicker) in the Analyst's violet (.fv-missionpicker--analyst,
   the colour of his img/analyst.png button): a BODY-LEVEL popover anchored to the clicked button, closed by a click outside it,
   Escape, or a choice; a choice made after the phase moved on is ignored (officers.setAnalystScan asks canAnalyse). */
window.AnalystPicker = (function () {

    var INSET = 8; //viewport clamp
    var current = null; //{ el, ship, target, onDone, anchor, onDocDown, onKey }

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
        var ship = current.ship, target = current.target, onDone = current.onDone;
        close();
        officers.setAnalystScan(ship, target, ability);
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

    function row(label, note, opts) {
        var r = make('button', 'fv-missionpicker__row');
        r.type = 'button';
        if (opts.pending) r.className += ' fv-missionpicker__row--pending';
        var text = make('span', 'fv-missionpicker__text');
        var top = make('span', 'fv-missionpicker__label', label);
        if (opts.chip) top.appendChild(make('span', 'fv-missionpicker__chip', opts.chip));
        text.appendChild(top);
        if (note) text.appendChild(make('span', 'fv-missionpicker__note', note));
        r.appendChild(text);
        if (opts.onPick) r.addEventListener('click', function (e) { e.stopPropagation(); opts.onPick(); });
        return r;
    }

    function render() {
        var ship = current.ship, target = current.target, el = current.el;
        el.innerHTML = '';
        el.appendChild(header('Expert Analyst', 'Scan ' + target.name));
        var waiting = officers.analystScan(ship);
        var elsewhere = !!waiting && String(waiting.t) !== String(target.id);
        var other = elsewhere && typeof gamedata.getShip === 'function' ? gamedata.getShip(waiting.t) : null;
        //a scan waiting at another unit is not in this list, so the line above it says where - and that a pick here replaces it
        el.appendChild(make('div', 'fv-missionpicker__cost', elsewhere
            ? officers.analystScanLabel(waiting.a, other) + ' scan waiting at ' + (other ? other.name : 'another unit') + '; a pick here replaces it'
            : 'One scan a turn; its result shows as Initial Orders close'));
        var list = make('div', 'fv-missionpicker__list');
        officers.analystScansFor(target).forEach(function (ability) {
            var isPending = !!waiting && waiting.a === ability && String(waiting.t) === String(target.id);
            list.appendChild(row(officers.analystScanLabel(ability, target), officers.analystScanNote(ability, target), {
                chip: isPending ? 'ordered' : null, pending: isPending,
                onPick: function () { choose(ability); }
            }));
        });
        el.appendChild(list);
        if (waiting) {
            var foot = make('div', 'fv-missionpicker__footer');
            var cancel = make('button', 'fv-missionpicker__button', 'Cancel scan');
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

    function open(ship, target, anchor, onDone) {
        close();
        if (!window.officers || !officers.canAnalyse(ship, target)) return;
        var el = make('div', 'fv-missionpicker fv-missionpicker--analyst');
        el.setAttribute('role', 'menu');
        //the tooltip and the map must not see clicks that land in here
        ['mousedown', 'mouseup', 'click', 'pointerdown', 'contextmenu'].forEach(function (type) {
            el.addEventListener(type, function (e) { e.stopPropagation(); });
        });
        current = {
            el: el, ship: ship, target: target, onDone: onDone, anchor: anchor,
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
