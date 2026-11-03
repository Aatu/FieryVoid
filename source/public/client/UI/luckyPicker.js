'use strict';

/* OFFICERS_PLAN.md Stage 37 - the menu the Lucky Captain button opens on the player's own ship, from its Initial Orders tooltip and its Firing-phase
   one: his four once-per-battle feats ("He may not perform more than one in any given turn") in the rules' order (officers.LUCKY_FEATS) - feat 2, one
   feat with four named uses, FRAMED under its own name so a player sees that using one uses them all (user request, play test 4527; its heading says
   when it was spent, its row which use spent it). This phase's uses he may still make are picks - one at most, the one waiting marked, picked again
   or Cancel to take it back; the rest say why not (used, the other phase's, one a turn, a disengage the board forbids). The pick waits on his C&C
   (officers.setLuckyChoice) for the commit (CnC.doIndividualNotesTransfer); the server plays it (Officers::saveLuckyOrder / saveLuckyArm). A
   Firing-phase feat already armed shows as armed.

   The Analyst's picker's surface (styles/hexPicker.css .fv-missionpicker) in the Lucky Captain's green (.fv-missionpicker--lucky, the colour of his
   img/luckyCaptain.png button): a BODY-LEVEL popover anchored to the clicked button, closed by a click outside it, Escape, its x or a choice; a choice
   made after the phase moved on does nothing (officers.setLuckyChoice asks luckyPickable). */
window.LuckyPicker = (function () {

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
        officers.setLuckyChoice(ship, ability);
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

    //one use's row: its name with a chip (picked / armed, or why it cannot be picked now), and what it does
    function row(label, note, opts) {
        var r = make('button', 'fv-missionpicker__row' + (opts.grouped ? ' fv-missionpicker__row--grouped' : ''));
        r.type = 'button';
        if (opts.pending) r.className += ' fv-missionpicker__row--pending';
        if (!opts.onPick) r.className += ' fv-missionpicker__row--disabled';
        var text = make('span', 'fv-missionpicker__text');
        var top = make('span', 'fv-missionpicker__label', label);
        if (opts.chip) top.appendChild(make('span', 'fv-missionpicker__chip' + (opts.pending ? '' : ' fv-missionpicker__chip--muted'), opts.chip));
        text.appendChild(top);
        if (note) text.appendChild(make('span', 'fv-missionpicker__note', note));
        r.appendChild(text);
        if (opts.onPick) r.addEventListener('click', function (e) { e.stopPropagation(); opts.onPick(); });
        return r;
    }

    //why a feat cannot be picked now - its chip - or null when it can
    function stateChip(ship, entry, ability) {
        var spent = officers.luckySpent(entry, ability.id);
        if (spent) return 'used, turn ' + spent.turn;
        if (officers.luckyPhaseAbilities().indexOf(ability.id) === -1) return gamedata.gamephase == 1 ? 'firing phase' : 'initial orders';
        if (officers.luckyArmed(ship) || !officers.luckyMay(entry, ability.id)) return 'one a turn';
        if (ability.id === 'disengage') {
            var state = officers.luckyDisengage(ship);
            if (!state || !state.ok) return 'blocked';
        }
        return null;
    }

    //what a row says: the feat's line, and for a disengage the board's answer (the server's)
    function noteFor(ship, ability) {
        if (ability.id !== 'disengage' || gamedata.gamephase != 1) return ability.note;
        var state = officers.luckyDisengage(ship);
        if (!state) return ability.note;
        if (!state.ok) return 'Not now: ' + (state.why || 'an enemy is ahead of the ship') + '.';
        if (state.range === null || state.range === undefined) return 'No armed enemy can see him: success is automatic.';
        return 'Nearest armed enemy in sight at ' + state.range + ' hexes: a d20 of ' + Math.min(20, state.range) + ' or less disengages'
            + (state.range >= 20 ? ' (automatic).' : '.');
    }

    function costText(choice, armed) {
        var name = function (id) { return officers.luckyAbilityName(id); };
        if (armed) return name(armed) + ' is armed this turn - spent only if it comes into play.';
        if (gamedata.gamephase == 1) {
            return choice ? name(choice) + ' is ordered for this turn - spent as you commit.'
                : 'Initial Orders: one feat this turn, each once per battle.';
        }
        return choice ? name(choice) + ' will be armed as you commit - spent only if it comes into play.'
            : 'Firing phase: Selected feats only spent if actually used during Firing.';
    }

    /* one use's row: picked or armed (a pick taken back by clicking it again), else why not now - or, in a feat already spent ($spent, a frame's),
       "used" on the use that spent it and nothing on the others, all of them shut */
    function useRow(ship, entry, id, choice, armed, spent, grouped) {
        var ability = officers.LUCKY_ABILITIES.filter(function (a) { return a.id === id; })[0] || { id: id, name: id, note: '' };
        var mine = choice === id || armed === id;
        var chip;
        if (mine) chip = armed ? 'armed' : 'picked';
        else if (spent) chip = spent.ability === id ? 'used' : null;
        else chip = stateChip(ship, entry, ability);
        var onPick = null;
        if (mine && !armed) onPick = function () { choose(null); };
        else if (!mine && !spent && chip === null) onPick = function () { choose(id); };
        return row(ability.name, noteFor(ship, ability), { chip: chip, pending: mine, onPick: onPick, grouped: grouped });
    }

    //a feat with several uses (feat 2): a frame - its name, when it was spent, what it means - around its uses' rows
    function featGroup(ship, entry, feat, choice, armed) {
        var ids = officers.LUCKY_FEATS[feat];
        var spent = officers.luckySpent(entry, ids[0]);
        var g = make('div', 'fv-missionpicker__group');
        var head = make('div', 'fv-missionpicker__grouphead');
        var title = make('span', 'fv-missionpicker__label', officers.LUCKY_FEAT_NAMES[feat] || feat);
        if (spent) title.appendChild(make('span', 'fv-missionpicker__chip fv-missionpicker__chip--muted', 'used, turn ' + spent.turn));
        head.appendChild(title);
        if (officers.LUCKY_FEAT_NOTES[feat]) head.appendChild(make('span', 'fv-missionpicker__note', officers.LUCKY_FEAT_NOTES[feat]));
        g.appendChild(head);
        ids.forEach(function (id) { g.appendChild(useRow(ship, entry, id, choice, armed, spent, true)); });
        return g;
    }

    function render() {
        var ship = current.ship, el = current.el;
        var entry = officers.luckyEntry(ship);
        el.innerHTML = '';
        el.appendChild(header('Lucky Captain', ship.name));
        var choice = officers.luckyChoice(ship), armed = officers.luckyArmed(ship);
        el.appendChild(make('div', 'fv-missionpicker__cost', costText(choice, armed)));
        var list = make('div', 'fv-missionpicker__list');
        Object.keys(officers.LUCKY_FEATS).forEach(function (feat) {
            var ids = officers.LUCKY_FEATS[feat];
            list.appendChild(ids.length > 1 ? featGroup(ship, entry, feat, choice, armed) : useRow(ship, entry, ids[0], choice, armed, null, false));
        });
        el.appendChild(list);
        if (choice && !armed) {
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
        if (!window.officers || !officers.canLucky(ship)) return;
        var el = make('div', 'fv-missionpicker fv-missionpicker--lucky');
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
