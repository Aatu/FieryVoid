'use strict';

/*
 * window.ShipTooltip — the SHIP CARD (SHIP_TOOLTIP_REDESIGN_PLAN.md).
 *
 * One jQuery surface in three forms:
 *   - the hover PEEK (no menu): read-only, `pointer-events: none`, so it never swallows a click (D8);
 *   - the PINNED card (a menu): actions, a ✕, and a head band that drags it (D10);
 *   - the stack grid, for a hover over several units (createForMultipleShips).
 *
 * The card is built in two steps. buildModel() reads the game - every number is today's helper,
 * moved here VERBATIM from the old createForSingleShip - and returns plain data; the renderer turns
 * that into DOM. Exposed as ShipTooltip.buildModel so a node harness can check the model alone.
 *
 * ⚠️ THE PUBLIC SURFACE IS UNCHANGED, because PhaseStrategy, SelectFromShips, the menus and
 * weaponManager all reach into it: the constructor signature (one optional trailing `options`),
 * show / reposition / destroy / close / onClose / update / refreshTargeting / refreshButtons /
 * isForAnyOf, and the fields element / ships / menu / ballisticsMenu / selectedShip. The HOOK
 * CLASSES too: root `shipNameContainer`, `namecontainer` (+`--stack`), `fire`/`targeting`,
 * `ballistics`/`incoming`, `buttons`, `menu`/`action-buttons`/`info`, `tt-close`.
 *
 * ⚠️ trap 1 of the plan: ONE THROWING CALL DELETES THE WHOLE CARD (it runs inside the constructor,
 * before show()), and `selectedShip` really is null - Initial Orders with nothing selected, a gate
 * signal, a fleet in hyperspace. Everything new in here is null-safe; see readSafely for the reads
 * that touch a unit's systems, which a WAITING player does not have.
 */
window.ShipTooltip = function () {

    // `.fire` and `.ballistics` are each carried by TWO divs - the heading and the content beneath
    // it - which is why the headings also carry `tt-head`, and why update() clears `.fire.targeting`
    // and never a bare `.fire` (a bare wipe takes the heading's <span> with it, for good).
    /* Round 12 (user, 2026-11-16): the root is now a bare flex ROW - the card box (`__card`: the
       border, the rail, the scroll) and, beside it, the ORDERS panel (`__side`). The panel has to be
       outside the card's scroll box or the card would clip it, and it has to be INSIDE the root so
       that everything which measures or moves `element` - place, placeDocked, keepInView, the head
       drag, the picker's placeTooltipClear - takes the panel along without knowing it is there.
       The constructor moves `.buttons` into the panel (not on the in-sheet card, which keeps it). */
    var HTML = '<div class="shipNameContainer fv-shipcard">'
        + '<div class="fv-shipcard__card">'
        + '<button type="button" class="tt-close" aria-label="Close" title="Close">✕</button>'
        + '<div class="namecontainer fv-shipcard__head"></div>'
        + '<div class="fv-shipcard__body"></div>'
        + '<div class="fire tt-head"><span>TARGETING</span></div>'
        + '<div class="fire targeting"></div>'
        + '<div class="fv-shipcard__primary"></div>'
        + '<div class="ballistics tt-head"><span>INCOMING</span></div>'
        + '<div class="ballistics incoming"></div>'
        + '<div class="buttons"></div>'
        + '<div class="fv-shipcard__foot"></div>'
        + '</div>'
        + '<div class="fv-shipcard__side">'
        + '<div class="fv-shipcard__sidehead"><span class="fv-shipcard__sidetitle">Orders</span>'
        + '<button type="button" class="fv-shipcard__sidetoggle"><span class="fv-shipcard__chev"></span></button></div>'
        + '</div>'
        + '</div>';

    /* The Walkers' Energy Draining Field. The one purple already in the game's UI is
       EWIconContainer's COLOR_JAM (210, 80, 255), used for the other "your unit is suppressed
       this turn" state, so the EDF's messages borrow it rather than inventing a second purple.
       Kept in step with the shipWindow status banner in ShipWindow.js, which uses the same value.
       (The chip itself takes it from shipTooltip.css .fv-shipcard__chip--suppressed.) */
    var EDF_PURPLE = '#d250ff';

    var EDGE = 8;          // viewport clamp inset, the picker's EDGE_MARGIN
    var DRAG_SLOP = 3;     // px of movement before a press on the head becomes a drag
    var MINUS = '−';  // a real minus sign: "−15", not "-15", in every chip and cell

    function clamp(value, low, high) {
        if (high < low) return low;
        return Math.min(Math.max(value, low), high);
    }

    /* 90% opacity on the whole card (user, 2026-11-16), with the player's own switch: the OPTIONS
       tab's "See-through ship tooltips" (gameOptions.js, on by default). The class goes on the card
       and on its dock caret, which lives in <body> apart from it; shipTooltip.css holds the number
       and leaves the in-sheet card solid (there is no map under it). Without gameOptions (a page
       that does not load it, a harness) the default stands. */
    var TRANSLUCENT = 'fv-shipcard--translucent';

    function isTranslucent() {
        try {
            return !window.gameOptions || gameOptions.get('translucentShipTooltips') !== false;
        } catch (e) {
            return true;
        }
    }

    /* Round 12 (user, 2026-11-16): what is folded away - the ORDERS panel beside the card (`side`),
       the Movement section (`move`), your own unit's EW section (`ew`) and - round 13 - its Fire
       orders (`fire`). Every phase STARTS from its defaults: the panel IN (round 13, user 2026-11-16:
       folded to its tab in every phase; it was out), Movement folded in the Firing phase only (it is
       the least used there, and TARGETING is the part that grows), EW and Fire orders open. What the
       player then opens or closes
       stays that way on every card for the rest of the phase - a card is rebuilt on every EW click
       and retarget, and the next unit clicked should not undo the choice - and a new phase (or
       turn) starts from its defaults again. Never stored past the page. */
    var foldState = null;

    function folds() {
        var phase = window.gamedata ? gamedata.gamephase : null;
        var key = (window.gamedata ? gamedata.turn : '') + ':' + phase;
        if (!foldState || foldState.key !== key) {
            foldState = { key: key, side: true, move: phase == 3, ew: false, fire: false };
        }
        return foldState;
    }

    function ShipTooltip(selectedShip, ships, position, showTargeting, menu, hexagon, ballisticsMenu, options) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        this.ships.sort(shipManager.hasBetterInitive); //so they're displayed in Ini order
        this.position = position;
        //TODO: selected ship might be destroyed
        this.selectedShip = selectedShip;
        this.showTargeting = showTargeting;
        this.hexagon = hexagon;
        this.menu = menu;
        this.ballisticsMenu = ballisticsMenu;
        this.options = options || {};
        /* {picker, shipId[, container]} when a hex picker ROW opened this card (Stages 6-7): beside
           the picker, level with the row (D5), or - with `container` - inside the picker's sheet in
           the list's place (D9). PhaseStrategy.showShipTooltip only passes the open picker's. */
        this.dock = this.options.dock || null;
        this.inSheet = !!(this.dock && this.dock.container);
        //Per instance and never remembered: a dragged card stops re-anchoring until it closes.
        this.dragged = false;
        //Below or above its unit, decided once and then kept while it fits (see place()).
        this.verticalSide = null;
        //Which side of the picker a docked card took - sticky too (see placeDocked).
        this.dockSide = null;
        this.caretElement = null;

        this.element.on('mousedown mouseup mouseover mousemove mouseout', function (e) {
            e.preventDefault(); e.stopPropagation();
        });

        this.element.toggleClass(TRANSLUCENT, isTranslucent());

        /* The ORDERS panel (round 12): the PINNED card's tiles go beside it, in a column that folds
           in and out. Not on the card inside the picker's sheet - there is no room beside it, and
           the sheet scrolls the card itself - which keeps its tiles inline as before. The panel is
           only drawn while there are tiles (renderActions sets fv-shipcard--has-side). */
        this.cardElement = this.element.children('.fv-shipcard__card');
        this.sideElement = this.element.children('.fv-shipcard__side');
        if (menu && !this.inSheet) {
            this.element.find('.buttons').appendTo(this.sideElement);
            bindSideToggle.call(this);
        } else {
            this.sideElement.remove();
            this.sideElement = null;
        }
        bindFoldToggle.call(this);

        if (menu) {
            /* The ✕ is for the PINNED card only - the one a click on a unit leaves standing, which
               sits over the hex you are trying to target or over the movement UI (user report
               2026-08-30). The peek is click-through, so an ✕ there could never be pressed. */
            this.element.addClass('fv-shipcard--pinned shipNameContainer--closable');
            this.element.find('.tt-close').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.close();
            }.bind(this));
            //The ORDERS panel's head band drags the card the same way as the card's own.
            bindHeadDrag.call(this, this.element.find('.namecontainer, .fv-shipcard__sidehead'));
        } else {
            /* D8: the peek is click-through. It used to destroy itself on any mouse event inside
               it, because a box under the cursor that swallowed the events was in the way of the
               map; `pointer-events: none` (shipTooltip.css) lets the map have them instead. */
            this.element.addClass('fv-shipcard--peek');
            this.element.find('.tt-close').remove();
        }

        if (ships.length > 1) {
            this.element.addClass('fv-shipcard--stack');
            createForMultipleShips.call(this, this.ships);
        } else {
            createForSingleShip.call(this, this.ships[0]);
        }

        this.show();
    }

    ShipTooltip.prototype.show = function () {
        if (this.inSheet) {
            //D9: the card IS the sheet's content now - no position of its own, no drag, no ✕ (the
            //sheet's own ✕ and "‹ N units in hex" do those jobs). The picker hides its list.
            this.element.addClass('fv-shipcard--insheet').appendTo(this.dock.container);
            reveal.call(this);
            if (typeof this.dock.picker.onCardDocked === 'function') this.dock.picker.onCardDocked(this);
            //AFTER onCardDocked: until the picker swaps its list for the card, the card is not laid out
            alignEwRows.call(this);
            return;
        }
        this.element.appendTo('body');
        reveal.call(this);
        alignEwRows.call(this);
        this.place();
    };

    /* NOT jQuery's show(): that writes the TAG's default display (`block` for a div), and the root is
       a flex row now (the card and its ORDERS panel side by side, round 12). */
    function reveal() {
        this.element.css('display', 'flex');
    }

    /* Round 8 (user, 2026-11-12): every EW row's icon starts where "DEFENCE" does in the cell above.
       That label is CENTRED in its cell, so where it starts depends on the font the platform drew it
       in - it is MEASURED here rather than guessed in CSS, and handed to the rows as --tt-ew-indent
       (shipTooltip.css puts it on the icon's left margin). Needs the card in the DOM, so it runs
       from show() and after every re-render (update, refreshButtons). Only beside the two-cell grid:
       a lone centred Defence cell (an enemy mine) sits mid-card, and its rows stay flush left. */
    function alignEwRows() {
        var node = this.element[0];
        var label = this.element.find('.fv-shipcard__cells--n2 .fv-shipcard__cell[data-cell="defence"] .fv-shipcard__cl')[0];
        //The first row that is laid out: a folded EW section's rows have no box (round 12) - the
        //fold toggle runs this again when it opens.
        var row = null, rowRect = null;
        this.element.find('.fv-shipcard__ewr').each(function () {
            if (row) return;
            var rect = this.getBoundingClientRect();
            if (rect.width) { row = this; rowRect = rect; }
        });
        if (!label || !row || !label.firstChild || !document.createRange) {
            if (!label || !this.element.find('.fv-shipcard__ewr').length) node.style.removeProperty('--tt-ew-indent');
            return;
        }
        var range = document.createRange();
        range.selectNodeContents(label);
        var textRect = range.getBoundingClientRect();
        if (!textRect.width) return; //not laid out (yet)
        node.style.setProperty('--tt-ew-indent', Math.max(0, textRect.left - rowRect.left).toFixed(2) + 'px');
    }

    ShipTooltip.prototype.reposition = function (position) {
        if (position) {
            this.position = position;
        }

        this.place();

        //Truthy, or the onZoom/onScroll callback filter unregisters it.
        return true;
    };

    /* Where the card goes (plan §4.6): centred below its unit as it always was - but MEASURED, and
       never off-screen. When there is no room below and there is room above, it goes above; the
       side is then STICKY, the picker's rule (SelectFromShips.positionSelf), so a zoom or a scroll
       that brings the unit back near the edge cannot flip it across the unit mid-gesture. It only
       moves when its side no longer fits and the other one does. A dragged card is the player's to
       place, and stays where it was put. */
    ShipTooltip.prototype.place = function () {
        if (this.dragged || this.inSheet) return;

        if (this.dock) {
            if (this.dock.picker && !this.dock.picker.destroyed) {
                placeDocked.call(this);
                return;
            }
            this.undock(true);
        }

        var point = viewportPoint(this.position);
        if (!point) return;

        var yOffset = clamp(window.coordinateConverter.getHexHeightViewport() / 2, 20, 100);

        var node = this.element[0];
        var width = node.offsetWidth;
        var height = node.offsetHeight;
        var viewW = window.innerWidth;
        var viewH = window.innerHeight;

        var below = point.y + yOffset;
        var above = point.y - yOffset - height;
        var fitsBelow = below + height <= viewH - EDGE;
        var fitsAbove = above >= EDGE;

        var side = this.verticalSide;
        if (!side) {
            side = (fitsBelow || !fitsAbove) ? 'below' : 'above';
        } else if (side === 'below' && !fitsBelow && fitsAbove) {
            side = 'above';
        } else if (side === 'above' && !fitsAbove && fitsBelow) {
            side = 'below';
        }
        this.verticalSide = side;

        //The CARD is centred on its unit; the ORDERS panel (round 12) hangs off its right edge, and
        //`width` - the whole row - is what the clamp keeps on screen, so near the right edge the
        //card moves left to make room for the panel.
        var cardWidth = (this.cardElement && this.cardElement[0] && this.cardElement[0].offsetWidth) || width;
        var left = point.x - cardWidth / 2;
        var top = side === 'below' ? below : above;

        this.element.css({
            left: clamp(left, EDGE, viewW - width - EDGE) + 'px',
            top: clamp(top, EDGE, viewH - height - EDGE) + 'px'
        });
    };

    var DOCK_GAP = 8;     // px between the picker and a docked card - the caret fills it
    var CARET_HALF = 8;   // half the caret triangle's height (the picker's own caret size)

    /* Docked beside the hex picker (D5, plan §6.3), like a submenu: on the side of the picker AWAY
       from its hex - picker.placement says which side of the hex the picker took - top level with
       the row (−8px), clamped, and a caret on the card's edge pointing at the row.
       Everything is DERIVED from the picker's rectangle and the row's, every time: the picker never
       writes a position onto the card (trap 7); it only asks the card to look again (its
       placeTooltipClear). The side is sticky per card (trap 8): it changes only when it no longer
       fits and the other side does. */
    function placeDocked() {
        var picker = this.dock.picker;
        var node = this.element[0];
        var width = node.offsetWidth;
        var height = node.offsetHeight;
        var viewW = window.innerWidth;
        var viewH = window.innerHeight;

        var pickerRect = picker.element[0].getBoundingClientRect();
        var shipId = String(this.dock.shipId);
        var row = picker.element.find('.fv-hexpicker__row').filter(function () {
            return this.getAttribute('data-ship-id') === shipId;
        })[0];
        var rowRect = row ? row.getBoundingClientRect() : null;
        //A row scrolled out of the list, or folded away, has nothing for the caret to point at.
        if (rowRect && picker.listElement && picker.listElement[0]) {
            var listRect = picker.listElement[0].getBoundingClientRect();
            if (rowRect.bottom <= listRect.top || rowRect.top >= listRect.bottom) rowRect = null;
        }

        var spots = { right: pickerRect.right + DOCK_GAP, left: pickerRect.left - DOCK_GAP - width };
        var fits = function (x) { return x >= EDGE && x + width <= viewW - EDGE; };
        var away = picker.placement === 'left' ? 'left' : 'right';
        var other = function (side) { return side === 'left' ? 'right' : 'left'; };

        var side = this.dockSide;
        if (!side) {
            side = fits(spots[away]) ? away : (fits(spots[other(away)]) ? other(away) : away);
        } else if (!fits(spots[side]) && fits(spots[other(side)])) {
            side = other(side);
        }
        this.dockSide = side;

        var left = clamp(spots[side], EDGE, viewW - width - EDGE);
        var anchorTop = rowRect ? rowRect.top : pickerRect.top;
        var top = clamp(anchorTop - 8, EDGE, viewH - height - EDGE);

        /* Docked on the picker's LEFT, the ORDERS panel (round 12) goes on the card's far side - it
           would otherwise sit between the card and the picker, and the caret would come out of the
           panel. `width` is the whole row either way, so the spots above already make room for it.
           (Set before the caret is placed: it reads the same left / width.) */
        this.element.toggleClass('fv-shipcard--side-left', side === 'left');
        this.element.addClass('fv-shipcard--docked').css({ left: left + 'px', top: top + 'px' });

        /* The caret. A separate element in <body>, because the card is its own scroll container and
           would clip anything drawn outside its border. It sits in the gap, on the edge facing the
           picker, outside the 3px rail (§11.1 R2.2), in the rail's colour. */
        var caretY = rowRect ? rowRect.top + rowRect.height / 2 : null;
        if (caretY === null || caretY < top + CARET_HALF || caretY > top + height - CARET_HALF) {
            if (this.caretElement) this.caretElement.hide();
            return;
        }
        if (!this.caretElement) {
            this.caretElement = jQuery('<div class="fv-shipcard-caret"></div>').appendTo('body');
        }
        this.caretElement
            .toggleClass('fv-shipcard-caret--left', side === 'right')    //card right of the picker: points left
            .toggleClass('fv-shipcard-caret--right', side === 'left')
            .toggleClass(TRANSLUCENT, this.element.hasClass(TRANSLUCENT))   //the card's own opacity
            .css({
                left: (side === 'right' ? left - CARET_HALF : left + width) + 'px',
                top: (caretY - CARET_HALF) + 'px'
            })
            .show();
        this.caretElement[0].style.setProperty('--tt-rail', this.element[0].style.getPropertyValue('--tt-rail') || 'var(--fv-line-scs)');
    }

    /* Pull the card back inside the viewport WITHOUT re-anchoring it - for a card that has just
       changed size under the player (update()). Re-anchoring there would undo the hex picker's
       avoidance nudge and make the card jump; this only moves it when it now hangs off an edge. */
    ShipTooltip.prototype.keepInView = function () {
        var node = this.element[0];
        if (!node.offsetWidth || !node.offsetHeight || !node.getBoundingClientRect) return;
        var rect = node.getBoundingClientRect();
        var left = clamp(rect.left, EDGE, window.innerWidth - node.offsetWidth - EDGE);
        var top = clamp(rect.top, EDGE, window.innerHeight - node.offsetHeight - EDGE);
        if (left !== rect.left || top !== rect.top) {
            this.element.css({ left: left + 'px', top: top + 'px' });
        }
    };

    /* The picker this card was docked beside is closing (SelectFromShips.destroy): drop the dock and
       go back to the card's own place under its unit. `quiet` skips the re-place, for a call from
       inside place() itself. */
    ShipTooltip.prototype.undock = function (quiet) {
        this.dock = null;
        this.dockSide = null;
        this.element.removeClass('fv-shipcard--docked fv-shipcard--side-left');
        if (this.caretElement) {
            this.caretElement.remove();
            this.caretElement = null;
        }
        if (!quiet) this.place();
    };

    ShipTooltip.prototype.destroy = function () {
        this.element.remove();
        if (this.caretElement) {
            this.caretElement.remove();
            this.caretElement = null;
        }
        //The sheet shows its list again once its card has gone, however it went.
        if (this.dock && this.dock.picker && typeof this.dock.picker.onCardGone === 'function') {
            this.dock.picker.onCardGone(this);
        }
    };

    /* Dismiss THE WAY THE OWNER WOULD. destroy() only takes the element out of the DOM -
       PhaseStrategy.shipTooltip would go on pointing at the corpse, and onMouseOverShip's
       `if (!this.shipTooltip || !this.shipTooltip.menu)` guard would then refuse to build any
       further tooltip for the rest of the phase. onClose is set by PhaseStrategy.showShipTooltip
       to its own hideShipTooltip, which clears that handle (and restores the coarse-pointer
       highlights). The bare destroy() is the fallback for any other owner. */
    ShipTooltip.prototype.close = function () {
        if (typeof this.onClose === 'function') {
            this.onClose();
        } else {
            this.destroy();
        }
    };

    //Unreferenced since the card replaced the line list; kept so the public surface is unchanged.
    ShipTooltip.prototype.addEntryElement = function (value, condition) {
        if (condition === false || condition === 0 || condition === null) return;
        this.element.find('.fv-shipcard__body').append(jQuery('<div class="entry"><span>' + value + '</span></div>'));
    };

    ShipTooltip.prototype.update = function (ship, selectedShip) {
        if (selectedShip) {
            this.selectedShip = selectedShip;
        }

        if (selectedShip && this.menu) {
            this.menu.selectedShip = selectedShip;
            this.menu.currentInfo = "";
        }

        //The INCOMING list's interceptor is the selected unit as well, so it has to follow the selection too. Without
        //this, selecting a flight's guns while a ship's tooltip stayed open left that SHIP as the interceptor, and the
        //flight's guns were offered against shots at the ship as the ship's own - no Defensive Intercept check at all
        //(game 4507: the orders were written as the ship's systems of the same ids, and the server dropped them).
        if (selectedShip && this.ballisticsMenu) {
            this.ballisticsMenu.selectedShip = selectedShip;
        }

        jQuery(".buttons", this.element).html("");
        jQuery(".namecontainer", this.element).html("").removeClass('namecontainer--stack');
        //⚠️ `.fire.targeting`, NOT `.fire`. The TARGETING heading and the rows beneath it are two
        //sibling divs that SHARE the `fire` class (see HTML above), so a bare `.fire` wipe deletes
        //the heading's <span> as well - and nothing ever puts it back, because
        //weaponManager.targetingShipTooltip fills only `.targeting`. The INCOMING pair below was
        //always cleared by its content class alone; this is the same rule applied to its twin.
        jQuery(".fire.targeting", this.element).html("");
        jQuery(".fv-shipcard__body, .fv-shipcard__foot, .fv-shipcard__primary", this.element).html("");
        jQuery(".incoming", this.element).html("");

        if (this.ships.length > 1) {
            createForMultipleShips.call(this, this.ships);
        } else {
            createForSingleShip.call(this, this.ships[0]);
        }

        //A docked card re-derives its place (its caret follows the new height); any other keeps it.
        alignEwRows.call(this);
        if (this.dock) this.place(); else this.keepInView();
    };

    /* Re-render ONLY the TARGETING half — the heading and the rows under it — against the
       CURRENT weapon selection. Split out of createForSingleShip so a weapon going on or off
       can refresh this in place: a full update() would rebuild the name, the button row and the
       INCOMING list as well, and PhaseStrategy.onSystemDataChanged deliberately will not do that
       under whatever the player is clicking.

       ⚠️ `showTargeting` is a SNAPSHOT — PhaseStrategy builds the tooltip with
       selectedShipHasSelectedWeapons and it goes stale the moment the selection changes, which it
       does constantly: targeting a ship CONSUMES the selection, and the player can click a weapon
       off at any time. So re-ask rather than trust it. Without this a stale `true` leaves the
       TARGETING heading standing over an empty section, because weaponManager.targetingShipTooltip
       returns immediately once nothing is selected — it only ever LOOKED right because the old
       bare `.fire` wipe in update() took the heading's own <span> with it as collateral damage.
       (selectedShip may be null here; selectedShipHasSelectedWeapons reads gamedata.selectedSystems
       and ignores the ship it is handed.) */
    ShipTooltip.prototype.refreshTargeting = function () {
        //The stack tooltip has no targeting half: createForMultipleShips never shows `.fire`, and
        //there is no single ship to work out a bearing and a hit chance against.
        if (this.ships.length !== 1) return;

        var ship = this.ships[0];
        this.showTargeting = shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip);

        if (gamedata.rules && gamedata.rules.friendlyFire === 1) {
            if (this.selectedShip && this.showTargeting && this.selectedShip.id != ship.id) {
                weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
                this.element.find(".fire").css({ "display": "block", "visibility": "visible" });
            } else {
                this.element.find(".fire").css("display", "none");
            }
        } else {
            if (this.selectedShip && gamedata.isEnemy(ship, this.selectedShip) && this.showTargeting) { //Old version before allied targeting
                weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
                this.element.find(".fire").css({ "display": "block", "visibility": "visible" });
            } else if (this.selectedShip && gamedata.canTargetAlly(ship) && this.showTargeting) {//30 June 2024 - DK - Added for Ally targeting.
                weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
                this.element.find(".fire").css({ "display": "block", "visibility": "visible" });
            } else {
                this.element.find(".fire").css("display", "none");
            }
        }
    };

    /* Re-run the menu's condition lists and re-place its buttons. Every button that can act on a
       weapon asks gamedata.selectedSystems whether it has one - hasWeaponsSelected,
       hasHexWeaponsSelected, FFWeaponSelected, hasSplitWeaponFiringOrder - so "Target Weapons" and
       "Remove a Firing Order" would otherwise sit there inert after the player emptied the
       selection, and stay missing after they refilled it (user report 2026-08-24). The conditions
       were always right; nothing was asking them again.

       The buttons now live in SEVERAL places (plan §5.3): the EW steppers in their rows, the Target
       button under TARGETING, Ship details in the footer, everything else as captioned tiles. So
       this rebuilds the card's body and footer from a fresh model and then hands every visible
       button to its slot - the rows the steppers sit in are part of the body. Safe to run from
       inside a button's own click handler: onClick calls the action and returns without touching
       the element (shipTooltipMenu.js), and update() has always destroyed these buttons the same
       way on every retarget. */
    ShipTooltip.prototype.refreshButtons = function () {
        //Same two guards the render site had: no menu on a plain hover tooltip, and
        //createForMultipleShips never draws a button row for a stack.
        if (!this.menu || this.ships.length !== 1) return;

        this.model = buildModel(this.ships[0], this.selectedShip);
        renderBody.call(this, this.model);
        renderFoot.call(this, this.model);
        renderActions.call(this);
        alignEwRows.call(this);
        //The ORDERS panel can come or go here (round 12), which changes the row's width.
        if (this.dock) this.place(); else this.keepInView();
    };

    ShipTooltip.prototype.isForAnyOf = function (ships) {
        ships = [].concat(ships)

        if (this.ships.length > 1) {
            return false;
        }

        return this.ships.some(function (ship) {
            return ships.includes(ship)
        })

    }

    // =========================================================================
    //  Single unit
    // =========================================================================

    function createForSingleShip(ship) {
        this.model = buildModel(ship, this.selectedShip);

        renderHead.call(this, this.model);
        renderBody.call(this, this.model);
        renderFoot.call(this, this.model);

        this.refreshTargeting();

        this.ballisticsMenu.renderTo(ship, this.element);

        if (this.menu) {
            renderActions.call(this);
        }
    }

    // =========================================================================
    //  The model — plain data, every number one of today's helpers
    // =========================================================================

    /* A read that walks a unit's SYSTEMS. A WAITING player gets no ship data (plan trap 15), and a
       throw here would take the whole card down (trap 1) - so the read falls back instead. */
    function readSafely(read, fallback) {
        try {
            return read();
        } catch (e) {
            ShipTooltip.lastReadError = e; //for a console session: what a card quietly fell back on
            return fallback;
        }
    }

    function fmt(value) {
        var n = Number(value);
        if (!isFinite(n)) return '—';
        return String(Math.round(n * 100) / 100).replace('-', MINUS);
    }

    function signed(value) {
        var n = Number(value) || 0;
        return n < 0 ? MINUS + Math.abs(n) : String(n);
    }

    function isMaskedMine(ship) {
        if (!ship.mine) return false;
        var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
        return !!(stealthSystem && !stealthSystem.isMineRevealed(ship));
    }

    function countFighters(ship) {
        var total = 0;
        if (ship.systems && typeof ship.systems.forEach === 'function') {
            ship.systems.forEach(function (fighter) { if (fighter) total++; });
        }
        return total;
    }

    /* SHIP_TOOLTIP_REDESIGN_PLAN.md §4.1 + §11.1 R2.6. The header carries no type or faction: the
       CLASS only when the name does not already contain it ("Ka'Tal" → "G'Quan Heavy Cruiser";
       "Mishakur Dreadnought #3" → nothing), and a flight's strength. ⚠️ An unrevealed mine's
       class and art both identify the type the name masking exists to hide (trap 12). */
    //Round 3 (user, 2026-11-10): the header is ONE row, so the sub line rides on the name's line -
    //a flight's strength as a short "4/6" (it must never be the part that is cut), the class as text
    //that gives way to the name.
    function buildIdentity(ship) {
        var masked = isMaskedMine(ship);
        var name = masked ? "Mine" : ship.name;
        var sub = '';
        var subTitle = '';
        var subKind = '';

        if (!masked) {
            if (ship.flight) {
                var total = countFighters(ship);
                if (total > 0) {
                    var active = shipManager.systems.getActiveFighterCount(ship);
                    sub = active + '/' + total;
                    subTitle = active + ' of ' + total + ' fighters';
                    subKind = 'count';
                }
            } else {
                var shipClass = String(ship.shipClass || '');
                if (shipClass && String(name).toLowerCase().indexOf(shipClass.toLowerCase()) === -1) {
                    sub = shipClass;
                    subTitle = shipClass;
                    subKind = 'class';
                }
            }
        }

        return {
            name: name,
            masked: masked,
            art: masked ? null : (ship.imagePath || null),
            sub: sub,
            subTitle: subTitle,
            subKind: subKind,
            allyClass: getAllyClass(ship),
            nameStyle: getNameStyle(ship),
            rail: getRailColor(ship)
        };
    }

    function chip(chips, text, family, extra) {
        var entry = { text: text, family: family };
        if (extra) {
            if (extra.title) entry.title = extra.title;
            if (extra.color) entry.color = extra.color;
        }
        chips.push(entry);
    }

    /* Every status line the tooltip used to run together into one bold-italic sentence, as a chip
       each (plan §4.3). The predicates are today's, in today's order; only what they emit changed.
       Families map to classes in shipTooltip.css that keep today's hues. */
    function buildChips(ship, movement) {
        var chips = [];
        var jinking = shipManager.movement.getJinking(ship) * 5;

        //add info of flight-wide criticals!
        if (ship.flight === true) {
            //get first fighter in flight
            var firstFighter = shipManager.systems.getSystem(ship, 1);
            var sensorDown = firstFighter ? shipManager.criticals.hasCritical(firstFighter, "tmpsensordown") : 0;
            if (sensorDown > 0) {
                sensorDown = sensorDown * 5;
                chip(chips, 'OB ' + MINUS + sensorDown, 'caution', { title: 'OB temporarily lowered by ' + sensorDown });
            }
            var iniDown = firstFighter ? shipManager.criticals.hasCritical(firstFighter, "tmpinidown") : 0;
            if (iniDown > 0) {
                iniDown = iniDown * 5;
                chip(chips, 'Ini ' + MINUS + iniDown, 'caution', { title: 'Initiative temporarily lowered by ' + iniDown });
            }
            //HK Jamming: UNCONTROLLED on the turn the crit is in effect. Uncontrolled is a oneturn
            //crit rolled on turn T (turnend = T+1); it is in effect on T+1, so show it when
            //crit.turn + 1 === current turn. Gated on remoteControl (very rare) so ordinary flights
            //skip the crit scan entirely.
            if (ship.remoteControl && firstFighter && uncontrolledInEffect(firstFighter)) {
                chip(chips, 'Uncontrolled', 'danger');
            }
            /* WALKERS_OF_SIGMA_PLAN.md 2.2 - "even if the fighter/shuttle does not drop out, it
               will not be able to shoot the next turn". Firing::withdrawGroundedFighterFireOrders
               enforces it on the advance path, so without this the player declares a full turn's
               shooting and only finds out when the orders vanish (user request 2026-09-04).
               PURPLE (EDF_PURPLE), matching EWIconContainer's COLOR_JAM. */
            if (firstFighter && edfGroundedInEffect(firstFighter)) {
                chip(chips, 'Energy drained', 'suppressed', { title: 'Energy Drained — cannot fire' });
            }
        }

        if (ship.base && ship.movement && ship.movement[1]) {
            var direction;

            if (ship.movement[1].value === -1) {
                direction = "port";
            } else if (ship.movement[1].value === 1) {
                direction = "starboard";
            }

            if (direction) {
                chip(chips, 'Rotating ' + direction, 'neutral', { title: 'Rotation towards ' + direction });
            }
        }

        var rollPivotModifier = 0;
        if (jinking > 0) chip(chips, 'Evasion ' + MINUS + jinking, 'accent', { title: 'Evasion: ' + MINUS + jinking + ' to hit' }); //Ships can jink too now - DK Oct 2025
        if (shipManager.movement.isPivoting(ship) !== 'no') chip(chips, 'Pivoting', 'caution');
        if (ship.agile && (!ship.flight)) {
            if (shipManager.movement.hasRolled(ship)) {
                chip(chips, 'Has rolled', 'caution');
                if (!ship.ignoreManoeuvreMods) rollPivotModifier -= 15;
            }
        } else if ((!ship.flight)) {
            if (shipManager.movement.isRolling(ship)) {
                chip(chips, 'Rolling', 'caution');
                if (!ship.ignoreManoeuvreMods) rollPivotModifier -= 15;
            }
        }
        if ((!ship.flight) && shipManager.movement.isRolled(ship)) chip(chips, 'Rolled', 'caution'); //fighters don't roll, no point looking for it
        if ((!ship.flight) && shipManager.movement.isHalfPhased(ship)) { //fighters don't half phase, no point looking for it
            chip(chips, 'Half-phased', 'caution');
            rollPivotModifier -= 50;
        }
        /* EXTENDED_TURNS_PLAN.md Stage 5 (D8) - public, from the begin until the completion.
           getExtendedTurnStatus is the reader shared with the ship window's banners, which also
           carry a cancellation; the card shows only the turn in progress. */
        var extendedTurn = (!ship.flight) ? shipManager.movement.getExtendedTurnStatus(ship) : null; //fighters cannot make one (R1)
        if (extendedTurn && !extendedTurn.cancelled) {
            chip(chips, extendedTurn.text, 'caution');
        }
        /* §11.1 R2.5: the current turn delay had its only home in the SPEED cell's sub line, which
           is gone - so it is a chip, shown on the same condition the "(delay n)" was. */
        if (movement && movement.currDelay > 0) {
            chip(chips, 'Turn delay ' + movement.currDelay, 'caution');
        }
        if (ship.trueStealth) {
            //Two turns can qualify now that placement and arrival are separate: the turn the unit
            //picks its entry hex, and the turn it actually arrives (when a late slot gets its own
            //Pre-Turn phase). Neither has a meaningful detection result to report.
            if (gamedata.gamephase == -1 && (shipManager.getTurnPlaced(ship) == gamedata.turn
                || shipManager.getTurnDeployed(ship) == gamedata.turn)) {
                chip(chips, 'Deploying', 'good'); //Always say undetected on Deployment phase.
            } else {
                //A Shading Field / Cloaking Device still toggleable this phase is answered by
                //shipManager.getStealthToggleForecast - which is what isDetected would return
                //anyway, so take it directly rather than sweeping the enemy fleet twice. It has to
                //win outright: the stored detected/detectedNew arrays the own-ship fallback below
                //reads still hold the LAST committed check, and would otherwise pin the card to
                //"Detected" for the whole Pre-Turn phase no matter how the player toggles.
                var forecast = shipManager.getStealthToggleForecast(ship);
                var isShipDetected = (forecast !== null) ? forecast : shipManager.isDetected(ship);
                var stealthSys = null;

                if (ship.mine) {
                    stealthSys = shipManager.systems.getSystemByName(ship, "mineStealth");
                } else if (ship.faction == "Torvalus Speculators") {
                    stealthSys = shipManager.systems.getSystemByName(ship, "ShadingField");
                } else if (shipManager.getSpecialAbilityStealth(ship, "Cloaking")) {
                    stealthSys = shipManager.systems.getSystemByName(ship, "CloakingDevice");
                } else if (shipManager.getSpecialAbilityStealth(ship, "Stealth")) {
                    stealthSys = shipManager.systems.getSystemByName(ship, "stealth");
                }

                if (!isShipDetected && forecast === null && ship.team == gamedata.getPlayerTeam()) {
                    if (stealthSys) {
                        if (Array.isArray(stealthSys.detected) && stealthSys.detected.length > 0) {
                            isShipDetected = true;
                        } else if (stealthSys.detected === true) {
                            isShipDetected = true;
                        } else if (Array.isArray(stealthSys.detectedNew) && stealthSys.detectedNew.length > 0) {
                            isShipDetected = true;
                        } else if (stealthSys.detectedNew === true) {
                            isShipDetected = true;
                        }
                    }
                }

                if (isShipDetected) {
                    var detectedTeamsStr = "";
                    //forecast !== null means the verdict came from the pending toggle, not from a
                    //committed check - detectedNew still lists the teams from LAST time, so naming
                    //them here would be wrong. The plain "Detected" is the honest answer.
                    if (forecast === null && ship.team == gamedata.getPlayerTeam()) { //Only own player needs to see full team list that's detected their ship.
                        // Check if we have more than 2 teams in the game
                        var uniqueTeams = [];
                        for (var i in gamedata.slots) {
                            var team = parseInt(gamedata.slots[i].team, 10);
                            if (team > 0 && !uniqueTeams.includes(team)) {
                                uniqueTeams.push(team);
                            }
                        }

                        if (uniqueTeams.length > 2) {
                            var detectedArray = [];
                            if (stealthSys && Array.isArray(stealthSys.detectedNew)) {
                                detectedArray = stealthSys.detectedNew;
                            } else if (stealthSys && Array.isArray(stealthSys.detected)) {
                                detectedArray = stealthSys.detected;
                            }

                            if (detectedArray.length > 0) {
                                // Ensure unique team numbers
                                var uniqueDetectedTeams = [];
                                for (var j = 0; j < detectedArray.length; j++) {
                                    var detectedTeam = parseInt(detectedArray[j], 10);
                                    if (detectedTeam > 0 && !uniqueDetectedTeams.includes(detectedTeam)) {
                                        uniqueDetectedTeams.push(detectedTeam);
                                    }
                                }

                                // Sort team numbers for readability
                                uniqueDetectedTeams.sort(function (a, b) { return a - b; });

                                if (uniqueDetectedTeams.length > 0) {
                                    detectedTeamsStr = " (Teams: " + uniqueDetectedTeams.join(", ") + ")";
                                }
                            }
                        }
                    }

                    chip(chips, 'Detected' + detectedTeamsStr, 'danger'); //Notify player that their Stealth ship is detected.
                } else {
                    chip(chips, 'Undetected', 'good');
                }
            }
        }

        if (gamedata.gamephase == 3 && ship.skinDancing) {
            if (Object.values(ship.skinDancing).includes(true)) {
                chip(chips, 'Skin dancing', 'good'); //the unit is skin dancing this turn
            } else if (Object.values(ship.skinDancing).includes("Aborted")) {
                chip(chips, 'Skin dance aborted', 'caution');
            } else if (Object.values(ship.skinDancing).includes("Failed")) {
                chip(chips, 'Skin dance failed', 'danger');
            }
        }

        /* JUMP_POINTS_PLAN.md Stage 6 - this unit has declared a jump point, or has plotted a
           jump-out it has not committed yet. Yellow, the jump-point colour used everywhere else in
           the feature. See shipManager.isJumpingToHyperspace for what counts and why. */
        if (shipManager.isJumpingToHyperspace(ship)) {
            chip(chips, 'Jumping', 'jump', { title: 'Jumping to Hyperspace' });
        }

        /* WALKERS_OF_SIGMA_PLAN.md 3.18 (Stage 20) - an Extra-Dimensional Jump Drive abduction standing
           against this unit, as of the last resolved turn. Purple, the colour its map marker uses.
           Public (EdjdAbduction::publish): the power-turns delivered are the resolution's own record. */
        if (typeof JumpEngine !== 'undefined' && typeof JumpEngine.getAbductionChain === 'function') {
            var abduction = JumpEngine.getAbductionChain(ship.id);
            if (abduction) {
                var abductionText = JumpEngine.formatAbductionHalves(abduction.total) + '/' + abduction.cost;
                chip(chips, 'Abducted ' + abductionText, 'abduct', { title: 'Being abducted: ' + abductionText + ' power-turns' });
            }
        }

        /* WALKERS_OF_SIGMA_PLAN.md 3.14c (Stage 19, user request 2026-09-12) - a unit ordered into
           or out of a hangar this Firing phase, and a Waymarker riding a Traveler's aft through its
           two-turn procedure. Benign cyan, like Hangar Operations, Just Launched and Arrival
           Scatter: something the unit is DOING, not damage. shipManager.getHangarManoeuvre is the
           single reader shared with the ship window's banner. */
        var hangarManoeuvre = shipManager.getHangarManoeuvre(ship);
        if (hangarManoeuvre) {
            chip(chips, hangarManoeuvre.text, 'benign');
        }

        /* ⚠️ SUPPRESSED FOR A DOCKING RIDE. A Waymarker mid-dock IS `attached`, so without this it
           would read "Attached to Traveler [Aft]" in boarding green - which describes a docking
           manoeuvre as an enemy pod on the hull. The chip above replaces it for the whole ride. */
        if (ship.attached && Object.keys(ship.attached).length > 0 && !ship.detached
            && !(hangarManoeuvre && hangarManoeuvre.riding)) {
            var targetId = Object.keys(ship.attached)[0];
            var location = Object.values(ship.attached)[0];
            var locationTip = '';

            if (location == 1) {
                locationTip = 'Front';
            } else if (location == 2) {
                locationTip = 'Aft';
            } else if (location == 3 || location == 31 || location == 32) {
                locationTip = 'Port';
            } else if (location == 4 || location == 41 || location == 42) {
                locationTip = 'Starboard';
            }

            var targetShip = gamedata.getShip(targetId);
            if (targetShip) {
                chip(chips, 'Attached to ' + targetShip.name + ' [' + locationTip + ']', 'good');
            }
        }

        /* ⚠️ And the same suppression from the CARRIER's side: every id in hasAttached that is one of
           our own docking riders has to drop out before "Being boarded" is justified. A Traveler
           with a Waymarker on its aft and a real pod on its bow must still say it. */
        if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
            var boarders = Object.keys(ship.hasAttached).filter(function (attachedId) {
                var rider = gamedata.getShip(attachedId);
                return !(rider && shipManager.isDockingRider(rider));
            });
            if (boarders.length > 0) {
                chip(chips, 'Being boarded', 'caution', { title: 'Ship is being Boarded!' });
            }
        }
        if (ship.flight === true) {
            var launchFighter = shipManager.systems.getSystem(ship, 1);
            if (launchFighter && shipManager.criticals.hasCritical(launchFighter, "LaunchedThisTurn")) {
                chip(chips, 'Just launched', 'benign');
            }
        } else if (shipManager.criticals.hasCriticalInAnySystem(ship, "LCVLaunchedThisTurn")) {
            //LCV Rails: an LCV launched from a rail carries LCVLaunchedThisTurn on
            //its CnC (the -50 init that turn). Show the same "Just launched".
            chip(chips, 'Just launched', 'benign');
        }
        if (shipManager.criticals.hasCriticalInAnySystem(ship, "HangarOperations")) {
            chip(chips, 'Hangar operations', 'benign');
        }
        /* REINFORCEMENTS_PLAN.md STAGE 9 - this unit came out of hyperspace off course and is
           disordered for the turn it arrives on (user request 2026-08-29). The same benign family
           as Hangar Operations and Just Launched: all three are initiative penalties from something
           the unit just DID, not damage. The figure is the server's own (getArrivalIniPenalty). */
        var arrivalIni = shipManager.getArrivalIniPenalty(ship);
        if (arrivalIni !== 0) {
            chip(chips, 'Arrival scatter (' + signed(arrivalIni) + ' Ini)', 'benign');
        }
        /* Triad Command Node (TRIAD_ADVANCED_FEATURES_PLAN.md §4, §9) - the +10 and the swap, for the node and
           its partner. A notice like the others (user request 2026-10-09); the total it produces is in the INI
           cell. Yellow, the card's --fv-warn. */
        if (!ship.flight && typeof CnC !== 'undefined' && typeof CnC.getTooltipLine === 'function') {
            var commandNodeLine = CnC.getTooltipLine(ship);
            if (commandNodeLine !== '') chip(chips, commandNodeLine, 'warn');
        }
        if (ship.flight === true) {
            rollPivotModifier -= 5 * shipManager.movement.combatPivotPenalty(ship); //-15 after a Redline Pilot's pivot without the thrust (OFFICERS_PLAN.md Stage 32)
        } else if (ship.osat) {
            if (shipManager.movement.hasTurned(ship)) rollPivotModifier -= 5;
        } else {
            if (shipManager.movement.hasPivotedForShooting(ship) && (!ship.ignoreManoeuvreMods)) rollPivotModifier -= 15;
        }
        if (rollPivotModifier != 0) chip(chips, 'Firing ' + signed(rollPivotModifier), 'caution', { title: 'Firing modifier: ' + rollPivotModifier }); //from roll/pivot/combat pivot

        if (ship.hasNavigator === true) chip(chips, 'Navigator', 'neutral', { title: 'Ballistic navigator aboard' });
        /* ESCORTING RETIRED 2026-10-22 (FIGHTER_MISSIONS_PLAN.md): the Defensive Intercept mission replaced the
           escort rule, so a flight's ballistic cover now reads in the mission chip below. */
        //a flight's fighter mission, for every viewer (missions are always known; the CSP key ship is own team only)
        var missionLine = (ship.flight && window.fighterMissions) ? fighterMissions.tooltipLine(ship) : null;
        if (missionLine) chip(chips, missionLine.text, 'mission', { color: missionLine.color });
        if (ship.agile) chip(chips, 'Agile', 'good');

        return chips;
    }

    /* The movement figures, verbatim from the old Speed / Turn / Thrust lines. Null for a mine,
       which never had them. */
    function buildMovement(ship) {
        if (ship.mine) return null;

        var currDelay = shipManager.movement.calculateCurrentTurndelay(ship);
        var speed = shipManager.movement.getSpeed(ship);
        var baseTurnCost = shipManager.movement.getTurnCost(ship);
        if (ship.submarine && shipManager.movement.isGoingBackwards(ship)) baseTurnCost = baseTurnCost * 1.33;
        //LCV Rails: each docked LCV adds +1 thrust to this turn's turn cost AND
        //turn delay (a flat surcharge on the per-turn value, NOT the rate shown
        //in the title). Matches the movement engine's getDockedLcvTurnSurcharge.
        var lcvTurnSurcharge = shipManager.movement.getDockedLcvTurnSurcharge(ship);
        //Turn cost is never less than 1 (matches the movement engine's
        //Math.max(1, ...) / speed-0 = 1-thrust rule); turn DELAY is genuinely 0 at
        //speed 0 (a stationary ship has no delay), so it is not clamped.
        var turncost = Math.max(1, Math.ceil(speed * baseTurnCost)) + lcvTurnSurcharge;
        //ELITE / POOR CREW: the same flat modifier the movement engine applies (-1 per Elite
        //level, floored at 1; +1 per Poor level), on the DELAY only - a crew's training does
        //not change what a turn costs in thrust. Applied before the LCV surcharge, matching
        //movement.js calculateTurndelayAtMove.
        //OFFICERS_PLAN.md Stage 30: an Expert Quartermaster's +10% on the raw delay first, as the movement engine
        var turnDelayCost = shipManager.movement.applyCrewTurnDelay(ship, shipManager.movement.getQuartermasterTurnDelay(ship,
            Math.ceil(speed * shipManager.movement.getTurnDelayCost(ship)))) + lcvTurnSurcharge;

        return {
            currDelay: currDelay,
            speed: speed,
            turnCost: turncost,
            turnRate: shipManager.movement.getTurnCost(ship),
            turnDelay: turnDelayCost,
            turnDelayRate: shipManager.movement.getTurnDelayCost(ship),
            thrustLeft: Math.max(shipManager.movement.getRemainingEngineThrust(ship), 0), //EngineShorted can make this go negative.
            thrustFull: shipManager.movement.getFullEngineThrust(ship),
            pivot: ship.pivotcost,
            roll: ship.rollcost,
            accel: ship.accelcost,
            combatPivot: ship.flight ? shipManager.movement.getCombatPivotCost(ship) : null
        };
    }

    /* Ini order and total (§11.1 R2.5: `−20 (1st)`), and the base/mod pair the footer prints on
       every card. Triad Command Node (TRIAD_ADVANCED_FEATURES_PLAN.md §4): during Initial Orders a
       nominated node is shown AS IT WILL BE once applied - the order and total, for every ship it
       re-ranks. A fighter mission's -60 / CSP +5 is applied when Initial Orders close, on top of
       the loaded modifiers (FIGHTER_MISSIONS_PLAN.md §3.6). */
    function buildIni(ship) {
        var iniProjection = (typeof CnC !== 'undefined' && typeof CnC.getIniativeProjection === 'function') ? CnC.getIniativeProjection() : null;
        if (iniProjection && !iniProjection[ship.id]) iniProjection = null;
        var order = iniProjection ? CnC.getProjectedIniativeOrder(ship, iniProjection) : shipManager.getIniativeOrder(ship);
        var total = iniProjection ? iniProjection[ship.id].iniative : ship.iniative;
        var mod = Number(ship.iniativeadded) + ((ship.flight && window.fighterMissions) ? fighterMissions.iniativeModifier(ship) : 0);
        return { order: order, total: total, base: ship.iniativebonus, mod: mod };
    }

    //Scout Penetration: both profiles -1 against every shot (FIGHTER_MISSIONS_PLAN.md §3.5); the printed profile stays in the title
    function buildDefence(ship) {
        var missionProfile = (ship.flight && window.fighterMissions) ? fighterMissions.profileModifier(ship) : 0;
        //Amended because Mindrider Constrained EW can create over 2 decimal places! DK - 20.7.24
        var fDef = weaponManager.calculateBaseHitChange(ship, Number(ship.forwardDefense) + missionProfile) * 5;
        fDef = parseFloat(fDef.toFixed(2));
        var sDef = weaponManager.calculateBaseHitChange(ship, Number(ship.sideDefense) + missionProfile) * 5;
        sDef = parseFloat(sDef.toFixed(2));
        return { fwd: fDef, side: sDef, baseFwd: ship.forwardDefense * 5, baseSide: ship.sideDefense * 5 };
    }

    /* The facing (Stage 3): which of the target's sections faces the selected unit, from the same
       helper the TARGETING list's old "-PORT-" line used - and which Defence number the shot meets,
       the one the card lights up. */
    var SECTION_LABEL = { 1: 'FWD', 2: 'AFT', 3: 'PORT', 4: 'STBD', 31: 'P.FWD', 32: 'P.AFT', 41: 'S.FWD', 42: 'S.AFT', 0: 'PRI' };

    /* The four PROFILE arcs: forward profile from 330-30 and 150-210, side profile everywhere else -
       ⚠️ MIRROR of weaponManager.getShipDefenceFacingFromHeading, the bearing the hit chance reads
       the profile from. They pick the lit Defence number on EVERY non-base unit (user, 2026-11-19):
       an MCV's / HCV's FWD section spans 270-90, so a section-based light (the old rule) lit the
       forward number for a shot from 60° that meets the side profile. A bearing exactly on a
       boundary lights both, as before.
       They are also a fighter flight's four armour arcs - ⚠️ MIRROR of FighterFlight::getLocations
       (PHP), which puts every arc on location 0, so fillLocationsGUI sends a flight NO outerSections
       and getShipHittingSide always answered "nothing" (the card showed no Facing on a flight). The
       arc still picks the ARMOUR a hit meets (fighter.php getArmourBase), so the card names it as
       it names a ship's section (user, 2026-11-16). Listed in a ship's section order (FWD, AFT,
       PORT, STBD - getShipHittingSide sorts by location id) so a boundary reads the same way on
       both. ⚠️ OSAT::getLocations (PHP) is the SAME four arcs and profiles, all on location 0 too,
       so an OSAT also arrives with no outerSections and is named from this table (user, 2026-11-19). */
    var PROFILE_ARCS = [
        { label: 'FWD', min: 330, max: 30, fwd: true },
        { label: 'AFT', min: 150, max: 210, fwd: true },
        { label: 'PORT', min: 210, max: 330, fwd: false },
        { label: 'STBD', min: 30, max: 150, fwd: false }
    ];

    //The PROFILE_ARCS the selected unit sits in (two on a boundary); null when the bearing can't be read.
    function profileArcsAt(source, ship) {
        var bearing = readSafely(function () { return mathlib.getCompassHeadingOfShip(ship, source); }, null);
        var heading = readSafely(function () { return shipManager.getShipHeadingAngle(ship); }, null);
        if (bearing === null || heading === null) return null;
        return PROFILE_ARCS.filter(function (arc) {
            return mathlib.isInArc(bearing, mathlib.addToDirection(arc.min, heading), mathlib.addToDirection(arc.max, heading));
        });
    }

    function buildFlightFacing(source, ship) {
        var arcs = profileArcsAt(source, ship);
        if (!arcs || !arcs.length) return null;
        return {
            label: arcs.map(function (arc) { return arc.label; }).join(' / '),
            fwd: arcs.some(function (arc) { return arc.fwd; }),
            side: arcs.some(function (arc) { return !arc.fwd; })
        };
    }

    function buildFacing(source, ship) {
        if (ship.flight) return buildFlightFacing(source, ship);
        //An OSAT has only a Primary (see PROFILE_ARCS) - its armour arcs still pick the profile a shot meets.
        if (ship.osat && !(ship.outerSections && ship.outerSections.length)) return buildFlightFacing(source, ship);
        var sections = readSafely(function () { return weaponManager.getShipHittingSide(source, ship); }, null);
        if (!sections || !sections.length) return null;
        //An MCV / HCV lists one section once per arc it owns (FWD at 330-30 AND 30-90), so a shot on
        //that line read "FWD / FWD" - name each section once.
        var seen = [];
        var labels = [];
        sections.forEach(function (location) {
            if (seen.indexOf(location) >= 0) return;
            seen.push(location);
            labels.push(SECTION_LABEL[location] || 'OTHER');
        });
        //The lit number: a base meets its forward profile from every side (weaponManager.getShipDefenceValue);
        //anything else the profile of the arc the shot comes from.
        if (ship.base) return { label: labels.join(' / '), fwd: true, side: false };
        var arcs = profileArcsAt(source, ship) || [];
        return {
            label: labels.join(' / '),
            fwd: arcs.some(function (arc) { return arc.fwd; }),
            side: arcs.some(function (arc) { return !arc.fwd; })
        };
    }

    /* Enemy DEW in Initial Orders (D6, §11.1 R2.5): another player's EW for THIS turn is hidden until
       they commit, so getEWLeft answers with their whole pool as if it were all DEW. Unknown is
       "no row of theirs for this turn yet" - a committed unit always has at least its DEW row. */
    function isEwUnknown(ship) {
        if (gamedata.gamephase != 1 || gamedata.isMyShip(ship)) return false;
        var entries = ship.EW || [];
        for (var i in entries) {
            if (entries[i] && entries[i].turn == gamedata.turn) return false;
        }
        return true;
    }

    // ── EW rows ──────────────────────────────────────────────────────────────────
    // Icons are the user's EW art (§11.1 R2.0, 40x40 shown at 20px). Label colours are the ship
    // window's own map (window.EW_LABEL_COLORS, published by ShipWindowEw.js - one map, two readers).
    var EW_TYPE = {
        /*'DEW': { label: 'DEW', icon: 'DEW', color: 'DEW' },*/
        'CCEW': { label: 'CCEW', icon: 'CCEW', color: 'CCEW', scope: 'self' },
        'BDEW': { label: 'BDEW', icon: 'BDEW', color: 'BDEW', scope: 'self' },
        //Round 8 (user, 2026-11-12): the icons moved in under "DEFENCE", and these three no longer fit -
        //a SHORT label on the row, the `full` name on hover and wherever else a name is spoken.
        'Detect Stealth': { label: 'Det. stealth', full: 'Detect stealth', icon: 'DetectSEW', color: 'Detect Stealth', scope: 'self' },
        'Detect Mines': { label: 'Det. mines', full: 'Detect mines', icon: 'MDEW', color: 'Detect Mines', scope: 'self' },
        'Support DEW': { label: 'Supp. DEW', full: 'Support DEW', icon: 'SDEW', color: 'SDEW' },
        'OEW': { label: 'OEW', icon: 'OEW', color: 'OEW', scope: 'target' },
        'DIST': { label: 'DIST', icon: 'DIST', color: 'DIST', scope: 'target' },
        'JAM': { label: 'Jamming', icon: 'JAM', color: 'JAM', scope: 'target' },
        'SOEW': { label: 'SOEW', icon: 'SOEW', color: 'SOEW', scope: 'target' },
        'SDEW': { label: 'SDEW', icon: 'SDEW', color: 'SDEW', scope: 'target' }
    };
    //DEW always first (§11 item 11), then the rest.
    var SELF_EW_ORDER = ['DEW', 'CCEW', 'BDEW', 'Detect Stealth', 'Detect Mines', 'Support DEW'];
    var TARGET_EW_ORDER = ['OEW', 'DIST', 'JAM', 'SOEW', 'SDEW'];

    /* One EW value, as the card prints it. Today's tooltip figures where it had one (OEW raw, DIST
       per third/quarter, BDEW x0.25); the ship window's getAmount for the rows the tooltip never
       printed (SDEW). `source` is the unit spending, `target` the unit it is spent on. */
    function ewValue(type, source, target) {
        switch (type) {
            /*case 'DEW': return ew.getDefensiveEW(source);*/
            case 'CCEW': return ew.getCCEW(source);
            case 'BDEW': return parseFloat((ew.getEWByType('BDEW', source) * 0.25).toFixed(2));
            case 'Detect Stealth': return ew.getEWByType('Detect Stealth', source);
            case 'Detect Mines': return ew.getDetectMEW(source);
            case 'Support DEW': return parseFloat(ew.getSupportedDEW(source).toFixed(2));
            case 'DIST':
                //Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
                return ew.getOffensiveEW(source, target, "DIST") / (shipManager.hasSpecialAbility(source, "ConstrainedEW") ? 4 : 3);
            case 'SDEW':
                var sdew = ew.getOffensiveEW(source, target, 'SDEW');
                return shipManager.hasSpecialAbility(source, "ConstrainedEW") ? Math.round(sdew * 0.333 * 3) / 3 : sdew * 0.5;
            default:
                return ew.getOffensiveEW(source, target, type);
        }
    }

    function ewRow(type, value) {
        return { type: type, value: value };
    }

    /* ── Stats a critical has changed (user request 2026-11-16) ─────────────────────────────────────
       Drawn in the critical orange (--fv-crit), as the system card does (SystemInfo critStats). The
       numbers already carry the effects - this only says WHICH ones a critical in force touched. Each
       list mirrors where its number comes from:
         Ini Mod  - BaseShip::getCommonIniModifiers: the C&C's crits (none on an OSAT), a flight's on its
                    first craft. Exact class names: the server's hasCritical matches no subclass (so the
                    one-turn comms crit is listed by name - the server counts it since 2026-11-16).
         Thrust   - movement.js getFullEngineThrust: the engines' output (their output crits and drains),
                    a tractor hold (swtargetheld) on any system; a Shadow's pilot pain (BioDrive output);
                    a flight's freethrust, less the EDF drain on its first craft (getEffectiveFreeThrust).
         Defence  - ProfileIncreased on the C&C, which BaseShip::stripForJson adds to the profiles sent.
         DEW      - ew.getScannerOutput: the scanners' output crits and drains, RestrictedEW /
                    tmpsensordown on the primary C&C, and a BSG hybrid's SensorLoss.
       Ships and bases; a flight has Ini and Thrust only. */
    var INI_CRITS = ['CommunicationsDisrupted', 'CommunicationsDisruptedOneTurn', 'ReducedIniativeOneTurn', 'ReducedIniative', 'swtargetheld', 'tmpinidown',
        'ShadowPilotPain', 'HangarOperations', 'LCVLaunchedThisTurn', 'EdfIniDrain', 'EMStormInitiative'];
    var FLIGHT_INI_CRITS = ['tmpinidown', 'LaunchedThisTurn', 'ReducedIniativeOneTurn', 'ReducedIniative', 'Uncontrolled',
        'EdfIniDrain', 'EMStormInitiative'];
    var ENGINE_CRIT = /^(Output(Halved|Reduced)\w*|EdfThrustDrain|ForcedOffline(OneTurn|ForTurns))$/;
    var SCANNER_CRIT = /^(Output(Halved|Reduced)\w*|EdfEwDrain|EMStormSensors|ForcedOffline(OneTurn|ForTurns))$/;

    function critStats(ship) {
        var criticals = shipManager.criticals;
        var inForce = function (system) { return system ? criticals.getAllCriticals(system) : []; };
        var hasAny = function (system, classes) {
            return inForce(system).some(function (crit) { return classes.indexOf(crit.phpclass) !== -1; });
        };
        var matches = function (system, pattern) {
            return inForce(system).some(function (crit) { return pattern.test(crit.phpclass); });
        };
        var stats = { ini: false, thrust: false, defence: false, dew: false };

        if (ship.flight) {
            var sample = criticals.getSampleCraft(ship);
            stats.ini = hasAny(sample, FLIGHT_INI_CRITS);
            stats.thrust = hasAny(sample, ['EdfThrustDrain']);
            return stats;
        }

        var cnc = shipManager.systems.getSystemByName(ship, 'cnC');
        stats.ini = !ship.osat && hasAny(cnc, INI_CRITS);
        stats.defence = hasAny(cnc, ['ProfileIncreased']);
        stats.thrust = hasAny(cnc, ['ShadowPilotPain']);
        stats.dew = hasAny(shipManager.getPrimaryCnC(ship), ['RestrictedEW', 'tmpsensordown'])
            || hasAny(shipManager.systems.getSystemByName(ship, 'BSGHybrid'), ['SensorLoss']);
        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (!system) continue;
            if ((system.name == 'engine' || system.outputType === 'thrust') && matches(system, ENGINE_CRIT)) stats.thrust = true;
            if (hasAny(system, ['swtargetheld'])) stats.thrust = true;
            if (system.outputType === 'EW' && matches(system, SCANNER_CRIT)) stats.dew = true;
        }
        return stats;
    }

    /* §4.1-§4.2 with §11 / §11.1. Plain data, no DOM. `selectedShip` may be null. */
    function buildModel(ship, selectedShip) {
        var terrain = gamedata.isTerrain(ship.shipSizeClass, ship.userid);
        var kind = terrain ? 'terrain' : (ship.mine ? 'mine' : (ship.flight ? 'flight' : (ship.base ? 'base' : 'ship')));
        var own = gamedata.isMyShip(ship);
        var team = gamedata.isMyorMyTeamShip(ship);
        //The unit the "From" section measures from. Not the card's own unit.
        var source = (selectedShip && selectedShip !== ship) ? selectedShip : null;

        var movement = readSafely(function () { return buildMovement(ship); }, null);
        var defence = readSafely(function () { return buildDefence(ship); }, null);
        var ini = (kind === 'mine') ? null : readSafely(function () { return buildIni(ship); }, null);
        //the stats a critical in force has changed, drawn in the crit orange (critStats)
        var critHit = (kind === 'mine' || kind === 'terrain') ? {} : readSafely(function () { return critStats(ship); }, {});

        var model = {
            ship: ship,
            kind: kind,
            own: own,
            team: team,
            phase: gamedata.gamephase,
            identity: buildIdentity(ship),
            //hull: buildHull(ship),   //D4 RULED NO (plan §11 item 9) - see buildHull; restore with renderHull
            chips: readSafely(function () { return buildChips(ship, movement); }, []),
            movement: movement,
            defence: defence,
            ini: ini,
            cells: [],
            rel: null,
            ew: null,
            fire: null,
            fireAfterMovement: false,
            sections: []
            //footer: Ini base / mod moved to the Movement section's heading (round 5) - see sections[].ini
        };

        // ── From ‹selected› ──
        if (source) {
            var rel = {
                source: source,
                sourceName: source.name,
                sourceColor: getNameColor(source),
                range: readSafely(function () { return mathlib.getDistanceBetweenShipsInHex(source, ship); }, null),
                facing: (kind === 'mine' || terrain) ? null : buildFacing(source, ship),
                rows: []
            };
            //OEW: the old line's condition, mine and ship branches alike
            if (!own && ship.flight !== true && source.flight !== true) {
                var oew = readSafely(function () { return ewValue('OEW', source, ship); }, 0);
                //User 2026-11-18: no 0 rows (Initial Orders' steppers bring OEW / DIST back to allocate)
                if (oew > 0) rel.rows.push(ewRow('OEW', oew));
            }
            if (kind !== 'mine' && ship.flight !== true && shipManager.isElint(source)) {
                var dist = readSafely(function () { return ewValue('DIST', source, ship); }, 0);
                if (dist > 0) rel.rows.push(ewRow('DIST', dist));
            }
            model.rel = rel;
        }

        // ── Cells ──
        var defenceCell = defence ? {
            key: 'defence', label: 'Defence (F-S)',
            fwd: defence.fwd, side: defence.side,
            baseFwd: defence.baseFwd, baseSide: defence.baseSide,
            hot: (model.rel && model.rel.facing) ? model.rel.facing : null,
            crit: !!critHit.defence,
            title: 'Defence fwd ' + defence.fwd + '% (base ' + defence.baseFwd + '), side ' + defence.side + '% (base ' + defence.baseSide + ')'
                + (critHit.defence ? ' - profile raised by a critical' : '')
        } : { key: 'defence', label: 'Defence', value: '—' };

        var unknownEw = (kind === 'ship' || kind === 'base') && isEwUnknown(ship);
        var dewValue = (kind === 'ship' || kind === 'base') ? readSafely(function () { return ew.getDefensiveEW(ship); }, null) : null;
        var dewCell = {
            key: 'dew', label: 'DEW',
            value: dewValue === null ? '—' : (unknownEw ? '≤ ' + fmt(dewValue) : fmt(dewValue)),
            crit: !!critHit.dew,
            title: (unknownEw ? 'Not committed yet: at most ' + fmt(dewValue) : 'Defensive EW')
                + (critHit.dew ? ' - sensors reduced by a critical' : '')
        };
        /* Round 3 (user, 2026-11-10, after Stages 2-7): Ini and Speed moved into the Movement section
           on every card, own and enemy alike, so the cells are the two COMBAT numbers - and the card
           could narrow. Terrain has no Movement section (no initiative), so its speed stays a cell. */
        if (kind === 'mine') {
            model.cells.push(defenceCell);
            if (team) model.cells.push({ key: 'sig', label: 'Signature', value: String(ship.signature) });
        } else if (kind === 'terrain') {
            model.cells.push(defenceCell, { key: 'speed', label: 'Speed', value: movement ? String(movement.speed) : '—' });
        } else if (kind === 'flight') {
            var armour = readSafely(function () { return shipManager.systems.getFlightArmour(ship); }, null);
            model.cells.push(defenceCell, {
                key: 'armor', label: 'Armor',
                value: armour ? String(armour).split(' / ').join('·') : '—',
                title: armour ? 'Armour (fwd / side / aft): ' + armour : ''
            });
        } else {
            model.cells.push(defenceCell, dewCell);
        }

        // ── EW ── Every phase, your own unit's too. (It used to be left off while your own ship was
        // plotting; user 2026-11-18: the Movement card should show the same EW section as Firing does.
        // Read-only there - EW is set in Initial Orders, so the menu carries no steppers.)
        if (kind === 'ship' || kind === 'base') {
            var rows = [];
            if (own) {
                //the old DEW/CCEW line, Support DEW, Detect Mines, and ELINT's Detect Stealth / Blanket DEW
                /*var dewRow = ewRow('DEW', dewValue);
                dewRow.crit = !!critHit.dew; //the same number as the DEW cell, so the same colour
                rows.push(dewRow);*/
                rows.push(ewRow('CCEW', readSafely(function () { return ewValue('CCEW', ship); }, 0)));
                if (shipManager.isElint(ship)) {
                    rows.push(ewRow('BDEW', readSafely(function () { return ewValue('BDEW', ship); }, 0)));
                    if (gamedata.isStealthPresent) rows.push(ewRow('Detect Stealth', readSafely(function () { return ewValue('Detect Stealth', ship); }, 0)));
                }
                var mdew = readSafely(function () { return ewValue('Detect Mines', ship); }, 0);
                if (mdew > 0) rows.push(ewRow('Detect Mines', mdew));
                var support = readSafely(function () { return ewValue('Support DEW', ship); }, 0);
                if (support > 0) rows.push(ewRow('Support DEW', support));
            } else {
                //Anyone else's: only what is actually allocated - the DEW is in its cell already
                ['CCEW', 'BDEW', 'Detect Stealth', 'Detect Mines', 'Support DEW'].forEach(function (type) {
                    var value = readSafely(function () { return ewValue(type, ship); }, 0);
                    if (value > 0) rows.push(ewRow(type, value));
                });
            }
            //User 2026-11-18: a 0 row is not shown, own unit's too. In Initial Orders a type you can
            //still allocate comes back with its steppers (findOrMakeEwRow).
            rows = rows.filter(function (row) { return row.value > 0; });
            //Round 12 (user, 2026-11-16): your own unit's EW section folds. User 2026-11-18: anyone
            //else's too, and it holds only the unit's OWN EW (OEW / DIST spent on it are in From).
            if (rows.length) model.ew = { own: own, rows: rows };
        }

        // ── Own unit: fire orders ──
        if (own && kind !== 'mine' && kind !== 'terrain' && (gamedata.gamephase == 3 || gamedata.gamephase == 5)) {
            model.fire = readSafely(function () { return buildFireOrders(ship); }, null);
        }

        /* ── Movement, on EVERY ship, base and flight - own or not (round 3, user 2026-11-10: "it's
           important info"). Four rows of two: Speed + Ini, Thrust + Accel, Turn cost + Turn delay,
           Pivot + Roll (a flight: Pivot + Combat pivot). The rates are in the titles. It sits below
           Electronic warfare in every phase (user 2026-11-18; while your own ship was plotting it used
           to come straight after the cells).
           Round 4 (user, 2026-11-10): Speed before Ini, and the order in the brackets is the bare
           movement-order number - `−20 (1)`, not `(1st)` - as the Order of Battle and the picker print it. */
        if (movement && (kind === 'ship' || kind === 'base' || kind === 'flight')) {
            var iniValue = (ini && ini.order) ? '(' + ini.order + ') ' + signed(ini.total) : '—';
            //Round 5 (user, 2026-11-10): Thrust and Speed swapped - Thrust + Ini, then Speed + Accel.
            //Round 6 (user, 2026-11-12): the Ini base / mod pair is the FIRST row (it was the footer's,
            //then chips in a MOVEMENT heading - the heading is gone), Ini mod above the Initiative it adds to.
            var spec = [];
            if (ini) {
                spec.push({ label: 'Ini Base', value: signed(ini.base), title: 'Initiative base ' + ini.base },
                    { label: 'Ini Mod', value: signed(ini.mod), crit: !!critHit.ini,
                        title: 'Initiative modifier ' + ini.mod + (critHit.ini ? ' - includes critical effects' : '') });
            }
            spec.push(
                { label: 'Thrust', value: movement.thrustLeft + '/' + movement.thrustFull, crit: !!critHit.thrust,
                    title: 'Thrust remaining / full' + (critHit.thrust ? ' - reduced by a critical' : '') },
                { label: 'Initiative', value: iniValue, title: (ini && ini.order) ? 'Initiative ' + ini.total + ', order ' + ini.order : 'No initiative order' },
                { label: 'Speed', value: movement.speed },
                { label: 'Accel', value: movement.accel, title: 'Acceleration cost' },
                //User 2026-11-19: each led by its base rate in dim brackets - `(0.66) 4`.
                { label: 'Turn Cost', value: movement.turnCost, base: movement.turnRate, title: 'Turn cost ' + movement.turnCost + ' (rate ' + movement.turnRate + ')' },
                { label: 'Turn Delay', value: movement.turnDelay, base: movement.turnDelayRate, title: 'Turn delay ' + movement.turnDelay + ' (rate ' + movement.turnDelayRate + ')' },
                { label: 'Pivot', value: movement.pivot, title: 'Pivot cost' }
            );
            spec.push(ship.flight
                ? { label: 'Combat Pivot', value: movement.combatPivot, title: 'Combat pivot cost' }
                : { label: 'Roll', value: movement.roll, title: 'Roll cost' });
            //No heading (round 6, user 2026-11-12) - the rows say what they are. Round 12 (user,
            //2026-11-16): the section FOLDS (folded by default in the Firing phase), and a folded
            //section needs something to say what it is and to open it again - so the heading is
            //back, as the fold button, as on Electronic warfare and Fire orders.
            model.sections.push({ key: 'move', title: 'Movement', fold: 'move', spec: spec });
        }
        //Round 12 (user, 2026-11-16): in the Firing phase your unit's Fire orders sit under Movement;
        //round 13 (user, 2026-11-16): in Pre-Firing too - so wherever Fire orders show at all.
        model.fireAfterMovement = gamedata.gamephase == 3 || gamedata.gamephase == 5;

        return model;
    }

    /* Own unit, Pre-Firing / Firing (§4.2): where its guns are pointed this turn, one row per target.
       Your own orders only. Interception is not a target. */
    function buildFireOrders(ship) {
        var byTarget = [];
        weaponManager.getAllFireOrders(ship).forEach(function (fire) {
            if (!fire || fire.turn != gamedata.turn) return;
            if (String(fire.type || '').toLowerCase().indexOf('intercept') !== -1) return;
            var key = (fire.targetid != null && fire.targetid != -1) ? 'ship' + fire.targetid : 'hex';
            var entry = null;
            for (var i = 0; i < byTarget.length; i++) {
                if (byTarget[i].key === key) { entry = byTarget[i]; break; }
            }
            if (!entry) {
                var target = key === 'hex' ? null : gamedata.getShip(fire.targetid);
                entry = { key: key, name: target ? target.name : 'Hex', color: target ? getNameColor(target) : null, count: 0 };
                byTarget.push(entry);
            }
            entry.count++;
        });
        return { targets: byTarget };
    }

    /* The hull strip (§4.1). D4 RULED NO (plan §11 item 9, user 2026-11-09): BUILT, and left out of
       the model and the renderer in case that ruling is revisited. Restore = uncomment the `hull:`
       line in buildModel and the renderHull call in renderBody. Fighter pips went with it (they are
       a health bar too); a flight's "4 of 6" stays in the header. */
    var HULL_ORDER = [1, 31, 41, 3, 4, 32, 42, 2, 0];
    function buildHull(ship) {
        if (ship.flight || ship.mine || gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return null;
        var bars = [];
        HULL_ORDER.forEach(function (location) {
            var structure = readSafely(function () { return shipManager.systems.getStructureSystem(ship, location); }, null);
            if (!structure || !structure.maxhealth) return;
            var left = structure.maxhealth - readSafely(function () { return damageManager.getDamage(ship, structure); }, 0);
            bars.push({ label: SECTION_LABEL[location], left: Math.max(0, left), max: structure.maxhealth });
        });
        return bars.length ? bars : null;
    }

    // =========================================================================
    //  Rendering
    // =========================================================================

    function ewColor(colorKey) {
        var map = window.EW_LABEL_COLORS;
        return (map && map[colorKey]) || 'var(--fv-text-accent)';
    }

    function iconUrl(name) {
        var path = 'img/' + name + '.png';
        return (window.AssetManager && typeof AssetManager.getSmartImagePath === 'function')
            ? AssetManager.getSmartImagePath(path) : path;
    }

    //An <img> that falls back to the plain PNG when the WebP twin is not on the server yet.
    function iconImg(name, className) {
        var fallback = 'img/' + name + '.png';
        return jQuery('<img alt="">').addClass(className).attr('src', iconUrl(name))
            .on('error', function () {
                if (this.getAttribute('src') !== fallback) this.setAttribute('src', fallback);
            });
    }

    function renderHead(model) {
        var head = this.element.find('.namecontainer');
        var identity = model.identity;

        //Art, one row high (round 3). ⚠️ Never a masked mine's: its silhouette names the type (trap 12).
        var art;
        if (identity.art && window.AssetManager) {
            art = jQuery('<img class="fv-shipcard__art" alt="">')
                .attr('src', window.AssetManager.getSmartImagePath(identity.art))
                .on('error', function () {
                    jQuery(this).replaceWith('<span class="fv-shipcard__art fv-shipcard__art--generic"></span>');
                });
        } else {
            art = jQuery('<span class="fv-shipcard__art fv-shipcard__art--generic"></span>');
        }
        /* Round 5 (user, 2026-11-10): no art on the PINNED card (the one a click opens - it has a
           menu); the hover peek keeps it, to the RIGHT of the name (appended after `who` below).
           The pinned card's art, as it was, left of the name: */
        //if (this.menu) head.append(art);

        var who = jQuery('<div class="fv-shipcard__who"></div>');
        //`name value <allegiance>`: tactical.css paints .mine/.ally/.enemy/.terrain .name for a
        //2-team participant; getNameStyle's inline colour paints observers and 3+ teams (trap 11).
        who.append(jQuery('<span class="name value ' + identity.allyClass + '"' + identity.nameStyle + '></span>')
            .text(identity.name).attr('title', identity.name));
        if (identity.sub) {
            who.append(jQuery('<span class="fv-shipcard__sub"></span>').addClass('fv-shipcard__sub--' + identity.subKind)
                .text(identity.sub).attr('title', identity.subTitle));
        }
        head.append(who);
        if (!this.menu) head.append(art);

        //The rail and the border, from the same two-arm gate as the name (§11 item 3).
        this.element[0].style.setProperty('--tt-rail', identity.rail);
    }

    function cellElement(cell) {
        var element = jQuery('<div class="fv-shipcard__cell"></div>').attr('data-cell', cell.key);
        if (cell.title) element.attr('title', cell.title);
        element.append(jQuery('<div class="fv-shipcard__cl"></div>').text(cell.label));
        //a critical changed it: the value in the crit orange (critStats; shipTooltip.css .fv-shipcard__cv--crit)
        var value = jQuery('<div class="fv-shipcard__cv"></div>').toggleClass('fv-shipcard__cv--crit', !!cell.crit);
        if (cell.key === 'defence' && cell.fwd !== undefined) {
            //F · S, the number on the facing side bright (Stage 3) - both on a boundary
            var hot = cell.hot;
            //User 2026-11-18: each followed by its base profile (before EW) in brackets - `25 (50)`.
            //Decimal profiles (Mindrider Constrained EW: `63.75`) do not fit the half cell that way
            //(measured 141px in 114px) - there the brackets go on a line of their own below.
            var hasBase = cell.baseFwd !== undefined && cell.baseSide !== undefined;
            var split = hasBase && (Math.round(cell.fwd) !== cell.fwd || Math.round(cell.side) !== cell.side);
            var base = function (n) { return jQuery('<span class="fv-shipcard__defb"></span>').text((split ? '' : '') + '(' + fmt(n) + ')'); };
            value.append(jQuery('<span class="fv-shipcard__def"></span>').toggleClass('fv-shipcard__def--hot', !!(hot && hot.fwd)).text(fmt(cell.fwd)));
            if (hasBase && !split) value.append(base(cell.baseFwd));
            value.append(jQuery('<span class="fv-shipcard__sep"></span>').text('·'));
            value.append(jQuery('<span class="fv-shipcard__def"></span>').toggleClass('fv-shipcard__def--hot', !!(hot && hot.side)).text(fmt(cell.side)));
            if (hasBase && !split) value.append(base(cell.baseSide));
            if (hot) value.addClass('fv-shipcard__cv--facing');
            if (split) {
                element.append(value);
                element.append(jQuery('<div class="fv-shipcard__defbl"></div>')
                    .append(base(cell.baseFwd), jQuery('<span class="fv-shipcard__sep"></span>').text('·'), base(cell.baseSide)));
                return element;
            }
        } else {
            value.text(cell.value);
            if (cell.dim) value.append(jQuery('<i></i>').text(' ' + cell.dim));
        }
        element.append(value);
        return element;
    }

    /* `foldKey` (round 12): the heading becomes a button that folds the section away to its heading
       line - the state is folds()[foldKey], shared by every card for the rest of the phase. The click
       is delegated from the card (bindFoldToggle), so it survives every re-render. */
    function sectionElement(key, title, foldKey) {
        var section = jQuery('<div class="fv-shipcard__sec"></div>').addClass('fv-shipcard__sec--' + key);
        if (title && foldKey) {
            var button = jQuery('<button type="button" class="fv-shipcard__fold"></button>')
                .append('<span class="fv-shipcard__chev"></span>')
                .append(jQuery('<span class="fv-shipcard__foldt"></span>').text(title));
            section.attr('data-fold', foldKey).append(jQuery('<div class="fv-shipcard__mh"></div>').append(button));
            applyFold(section, !!folds()[foldKey]);
        } else if (title) {
            section.append(jQuery('<div class="fv-shipcard__mh"></div>').append(jQuery('<span></span>').text(title)));
        }
        return section;
    }

    function applyFold(section, folded) {
        var button = section.children('.fv-shipcard__mh').children('.fv-shipcard__fold');
        var title = button.children('.fv-shipcard__foldt').text();
        section.toggleClass('fv-shipcard__sec--folded', folded);
        button.attr('aria-expanded', folded ? 'false' : 'true')
            .attr('title', (folded ? 'Show ' : 'Hide ') + title.toLowerCase());
    }

    function bindFoldToggle() {
        var self = this;
        this.cardElement.on('click', '.fv-shipcard__fold', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var section = jQuery(this).closest('.fv-shipcard__sec');
            var key = section.attr('data-fold');
            if (!key) return;
            var state = folds();
            state[key] = !state[key];
            applyFold(section, state[key]);
            afterResize.call(self);
        });
    }

    /* The card changed size under the player (a fold, the panel in or out): re-measure the EW indent
       (an opened EW section has rows to measure again) and stay on screen the way update() does - a
       docked card re-derives its place, any other only moves if it now hangs off an edge. */
    function afterResize() {
        alignEwRows.call(this);
        if (this.dock) this.place(); else this.keepInView();
    }

    function specElement(items) {
        var spec = jQuery('<div class="fv-shipcard__spec"></div>');
        items.forEach(function (item) {
            var line = jQuery('<div></div>');
            if (item.title) line.attr('title', item.title);
            var label = jQuery('<span></span>').text(item.label);
            if (item.color) label.css('color', item.color);
            var value = jQuery('<b></b>').toggleClass('fv-shipcard__crit', !!item.crit) //critStats
                .text(item.value === undefined || item.value === null ? '—' : String(item.value));
            //a base rate in dim brackets to the LEFT of the value (Turn Cost / Turn Delay); --based
            //lets the brackets give way first in a tight cell, never the value itself
            if (item.base !== undefined && item.base !== null) {
                value.addClass('fv-shipcard__based')
                    .prepend(jQuery('<span class="fv-shipcard__specb"></span>').text('(' + fmt(item.base) + ')'));
            }
            line.append(label, value);
            spec.append(line);
        });
        return spec;
    }

    /* One EW row: icon, label, then the [24px][32px value][24px] block every row ends in, so the
       values form one column whether or not a row has steppers (§11 item 11). The −/+ slots are
       filled by renderActions when the menu offers them. */
    function ewRowElement(row) {
        var type = EW_TYPE[row.type] || { label: row.type, icon: 'DEW', color: row.type };
        var element = jQuery('<div class="fv-shipcard__ewr"></div>').attr('data-ew', row.type);
        //One colour per row (round 3, user 2026-11-10): the label AND its −/+ buttons wear it.
        element[0].style.setProperty('--ew-color', ewColor(type.color));
        element.append(iconImg(type.icon, 'fv-shipcard__ewi'));
        element.append(jQuery('<span class="fv-shipcard__ewl"></span>').text(type.label).attr('title', type.full || type.label));
        element.append(jQuery('<span class="fv-shipcard__vb"></span>')
            .append('<span class="fv-shipcard__sp fv-shipcard__sp--remove"></span>')
            .append(jQuery('<span class="fv-shipcard__sv"></span>').toggleClass('fv-shipcard__sv--crit', !!row.crit).text(fmt(row.value)))
            .append('<span class="fv-shipcard__sp fv-shipcard__sp--add"></span>'));
        return element;
    }

    function renderBody(model) {
        var body = this.element.find('.fv-shipcard__body').empty();

        //renderHull.call(this, body, model.hull);   //D4 RULED NO - see buildHull

        if (model.chips.length) {
            var chips = jQuery('<div class="fv-shipcard__chips"></div>');
            model.chips.forEach(function (entry) {
                var element = jQuery('<span class="fv-shipcard__chip"></span>')
                    .addClass('fv-shipcard__chip--' + entry.family).text(entry.text);
                if (entry.title) element.attr('title', entry.title);
                if (entry.color) element.css('color', entry.color);
                chips.append(element);
            });
            body.append(chips);
        }

        /* Round 6 (user, 2026-11-12): "From ‹selected›" and Range / Facing sit ABOVE the cells - the
           facing is what lights one of the Defence numbers. User 2026-11-18: the EW the selected unit
           spends on this one (OEW, DIST) is the From section's last rows (`.fv-shipcard__relew`), so
           Electronic warfare below holds only this unit's own EW. */
        if (model.rel) {
            var rel = sectionElement('rel', null);
            var heading = jQuery('<div class="fv-shipcard__mh"></div>');
            heading.append(jQuery('<span></span>').text('From ')
                .append(jQuery('<span class="fv-shipcard__from"></span>').text(model.rel.sourceName).css('color', model.rel.sourceColor)));
            rel.append(heading);
            var spec = [{ label: 'Range', value: model.rel.range === null ? '—' : model.rel.range }];
            //Facing starts where Movement's second column does (round 5), so a boundary reads
            //"FWD/PORT" - without the spaces - to fit the half-width column.
            if (model.rel.facing) spec.push({ label: 'Facing', value: model.rel.facing.label.split(' / ').join('/'), title: 'The section that faces ' + model.rel.sourceName });
            rel.append(specElement(spec));
            if (model.rel.rows.length) {
                var relEw = jQuery('<div class="fv-shipcard__relew"></div>');
                model.rel.rows.forEach(function (row) { relEw.append(ewRowElement(row)); });
                rel.append(relEw);
            }
            body.append(rel);
        }

        if (model.cells.length) {
            var cells = jQuery('<div class="fv-shipcard__cells"></div>')
                .addClass('fv-shipcard__cells--n' + model.cells.length);
            model.cells.forEach(function (cell) { cells.append(cellElement(cell)); });
            body.append(cells);
        }

        var renderSections = function () {
            model.sections.forEach(function (section) {
                body.append(sectionElement(section.key, section.title, section.fold).append(specElement(section.spec)));
            });
        };

        if (model.ew) {
            //Folds on every card (user 2026-11-18), open by default.
            var ewSection = sectionElement('ew', 'Electronic warfare', 'ew');
            model.ew.rows.forEach(function (row) { ewSection.append(ewRowElement(row)); });
            body.append(ewSection);
        }

        //Fire orders: after Movement in Pre-Firing and Firing (rounds 12-13), otherwise after EW.
        if (!model.fireAfterMovement) renderFire(body, model);
        renderSections();
        if (model.fireAfterMovement) renderFire(body, model);
    }

    function renderFire(body, model) {
        if (model.fire) {
            //Folds (round 13, user 2026-11-16), open by default - folds().fire.
            var fire = sectionElement('fire', 'Fire orders', 'fire');
            if (!model.fire.targets.length) {
                fire.append(jQuery('<div class="fv-shipcard__none"></div>').text('None yet'));
            } else {
                model.fire.targets.forEach(function (target) {
                    var line = jQuery('<div class="fv-shipcard__order"></div>');
                    line.append(jQuery('<span class="fv-shipcard__ordername"></span>').text(target.name).css('color', target.color || ''));
                    line.append(jQuery('<b></b>').text('×' + target.count));
                    fire.append(line);
                });
            }
            body.append(fire);
        }
    }

    /* The footer holds the Ship details button alone now (renderActions puts it there), full width.
       The peek has no button, so its footer is empty and takes no room (`:empty` in the CSS). */
    function renderFoot(model) {
        this.element.find('.fv-shipcard__foot').empty();
    }

    /* Kept for the day D4 is revisited (see buildHull). The ship window's fill colours; length
       carries the information. */
    function renderHull(body, hull) {
        if (!hull) return;
        var strip = jQuery('<div class="fv-shipcard__hull"></div>');
        hull.forEach(function (bar) {
            var pct = bar.max ? Math.round(bar.left / bar.max * 100) : 0;
            strip.append(jQuery('<div class="fv-shipcard__hullbar"></div>').attr('title', bar.label + ' ' + bar.left + ' / ' + bar.max)
                .append(jQuery('<span class="fv-shipcard__hulll"></span>').text(bar.label))
                .append(jQuery('<span class="fv-shipcard__hullt"></span>').append(jQuery('<span class="fv-shipcard__hullf"></span>').css('width', pct + '%'))));
        });
        body.append(strip);
    }
    void renderHull; void buildHull; //deliberately unreferenced - see buildHull

    // =========================================================================
    //  Actions (plan §5)
    // =========================================================================

    /* The menu's visible buttons, each to its slot:
         - `group` + `role` (the nine EW pairs): a −/+ stepper in that type's EW row, in the "From"
           section for an EW type spent ON this unit, in the EW section for one it spends on itself.
           A row the model did not carry is made here. If only one half passed its conditions, the
           other is drawn DISABLED so the row keeps its shape.
         - `kind: 'primary'` (Target weapons): the full-width button under TARGETING - only while
           a row there says a selected weapon can be aimed at this unit (targetableCount).
         - `kind: 'clear'` (Remove All EW): "Clear all EW", at the foot of the EW section.
         - `kind: 'footer'` (Ship details): the footer button.
         - everything else: a captioned tile in the ORDERS panel beside the card (round 12) - or,
           on the card inside the picker's sheet, under an Orders heading in the card as before.
       Every button is bound by the menu's own bindAction, so click, right-click max and the 500ms
       long press behave exactly as they did. ⚠️ The menu's conditions are not touched: which
       buttons exist is getVisibleButtons - renderTo's own filter. */
    function renderActions() {
        var self = this;
        var menu = this.menu;
        menu.shipTooltip = this;

        var visible = menu.getVisibleButtons();

        var buttonsSlot = this.element.find('.buttons').empty();
        var primarySlot = this.element.find('.fv-shipcard__primary').empty();
        var foot = this.element.find('.fv-shipcard__foot');
        foot.find('.fv-shipcard__btn').remove();

        var menuElement = jQuery(ShipTooltipMenu.template);
        var tiles = menuElement.find('.action-buttons');
        var steppers = {};
        var tileCount = 0;

        visible.forEach(function (buttonData) {
            if (buttonData.group && buttonData.role) {
                steppers[buttonData.group] = steppers[buttonData.group] || {};
                steppers[buttonData.group][buttonData.role] = buttonData;
            } else if (buttonData.kind === 'primary') {
                //User, 2026-11-16: no Target button while none of the selected weapons can be aimed at
                //this unit - every TARGETING row says why not (Not in arc, Not in range, …) instead.
                var targetable = targetableCount.call(self);
                if (targetable > 0) primarySlot.append(primaryButton.call(self, buttonData, targetable));
            } else if (buttonData.kind === 'clear') {
                clearButtonSlot.call(self).append(clearButton.call(self, buttonData));
            } else if (buttonData.kind === 'footer') {
                foot.prepend(footerButton.call(self, buttonData));
            } else {
                tiles.append(tileButton.call(self, buttonData, menuElement));
                tileCount++;
            }
        });

        Object.keys(steppers).forEach(function (group) {
            var row = findOrMakeEwRow.call(self, group);
            if (row) attachStepper.call(self, row, group, steppers[group]);
        });

        greyUnavailableEwRows.call(this, steppers);

        jQuery(".info", menuElement).html(menu.currentInfo);
        if (this.sideElement) {
            //Round 12: the tiles live in the ORDERS panel beside the card (its head band says
            //"Orders"), and with no tile there is no panel at all - not even its toggle.
            if (tileCount > 0) buttonsSlot.append(menuElement);
            setSide.call(this, tileCount);
        } else if (tileCount > 0) {
            menuElement.prepend(jQuery('<div class="fv-shipcard__mh"></div>').append(jQuery('<span></span>').text('Orders')));
            buttonsSlot.append(menuElement);
        } else if (menu.currentInfo) {
            buttonsSlot.append(menuElement);
        }
    }

    /* The ORDERS panel (round 12, user 2026-11-16): beside the card, a column of the tiles, with a
       small in / out toggle in its head band - IN (folded to its tab) by default since round 13, and
       the choice kept for the rest of the phase (folds().side). Folded, only the toggle shows, a tab on the card's edge. Drawn
       only while the menu offers a tile. Round 13: it hangs from the rule under the card's head
       band (shipTooltip.css), not beside the head band. */
    function setSide(tileCount) {
        var has = tileCount > 0;
        var folded = has && !!folds().side;
        this.element.toggleClass('fv-shipcard--has-side', has).toggleClass('fv-shipcard--side-folded', folded);
        var label = (folded ? 'Show orders' : 'Hide orders') + (has ? ' (' + tileCount + ')' : '');
        this.sideElement.find('.fv-shipcard__sidetoggle')
            .attr({ 'aria-expanded': folded ? 'false' : 'true', 'aria-label': label, title: label });
    }

    function bindSideToggle() {
        var self = this;
        this.sideElement.find('.fv-shipcard__sidetoggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var state = folds();
            state.side = !state.side;
            setSide.call(self, self.element.find('.buttons .fv-shipcard__tile').length);
            afterResize.call(self);
        });
    }

    /* Round 4 (user, 2026-11-10): an EW row whose allocation is NOT open right now is greyed out -
       e.g. DIST from an ELINT already running Blanket DEW, Detect Stealth beside BDEW, an ELINT
       function out of range.

       "Not open right now" is read off the menu itself, so no rule is restated here: the menu
       CARRIES that EW type's steppers (whatever their conditions say) and NEITHER half of them
       passed. A menu that never carries them - Movement, Waiting, the Firing menu outside the late
       EW window - greys nothing, so a committed OEW in the Firing phase stays a plain value.
       The scope rule keeps it to the one unit doing the allocating: a self type (CCEW, BDEW, …) only
       on the selected unit's own card, a targeted type (OEW, DIST, …) only on another unit's card
       with a unit selected. Your other ships' EW sections are information, not options. */
    function greyUnavailableEwRows(steppers) {
        var menu = this.menu;
        var ship = this.ships[0];
        var source = menu.selectedShip;
        var carried = {};
        menu.getAllButtons().forEach(function (buttonData) {
            if (buttonData.group) carried[buttonData.group] = true;
        });

        this.element.find('.fv-shipcard__ewr').each(function () {
            var group = this.getAttribute('data-ew');
            var type = EW_TYPE[group];
            if (!type || !type.scope || !carried[group] || steppers[group]) return;
            var mine = type.scope === 'self' ? (source && source === ship) : (source && source !== ship);
            if (!mine) return;
            jQuery(this).addClass('fv-shipcard__ewr--off').attr('title', (type.full || type.label) + ': not available now');
        });
    }

    //The caption a tile or button shows: its label (a string or a function of the menu), else a
    //static info string, else nothing.
    function captionOf(buttonData, menu) {
        var label = buttonData.label;
        if (typeof label === 'function') label = label.call(menu);
        if (label) return String(label);
        return (typeof buttonData.info === 'string') ? buttonData.info : '';
    }

    function infoOf(buttonData, menu) {
        return (typeof buttonData.info === 'function') ? buttonData.info.call(menu) : buttonData.info;
    }

    function tileButton(buttonData, menuElement) {
        var menu = this.menu;
        var caption = captionOf(buttonData, menu);
        var info = infoOf(buttonData, menu);
        var element = jQuery('<button type="button" class="fv-shipcard__tile"></button>').attr('data-action', buttonData.className);
        //The icon keeps its own class, so every icon rule in shipTooltip.css (and the officers'
        //masked art) still paints it. The caption is what touch was missing (§1).
        element.append(jQuery('<span class="fv-shipcard__icon"></span>').addClass(buttonData.className));
        element.append(jQuery('<span class="fv-shipcard__cap"></span>').text(caption));
        if (typeof buttonData.info === 'string') element.attr('title', buttonData.info);
        menu.bindAction(element, buttonData, this);
        //A dynamic info reports STATE (fighterMissionInfo, luckyInfo, jealousElintInfo, …), so it
        //keeps the line under the tiles.
        if (typeof buttonData.info === 'function') menu.bindInfo(element, menuElement, info);
        return element;
    }

    /* How many selected weapons can be aimed at this unit (§5.2): the TARGETING rows above that carry
       a hit chance - the weapons weaponManager itself judged able to shoot at it (in arc, in range,
       line of sight, not hex-targeted, not linked away) - plus a Walker drive's Abduction, which
       targets the unit without one. Counted off that list rather than re-asked, so the button can
       never promise a weapon the list says cannot fire. A ≤0% row counts only for a BALLISTIC
       weapon (user, 2026-11-16) - weaponManager.targetShip launches a ballistic at 0% or less and
       declares nothing else below 1%; weaponManager marks the row's number `tt-ballistic`. A
       grouped row counts every weapon in it (data-count).
       A HIDDEN list counts nothing: refreshTargeting hides it (inline display) without emptying it,
       so its rows may belong to an older selection. */
    function targetableCount() {
        var list = this.element.find('.fire.targeting');
        if (!list.length || list[0].style.display === 'none') return 0;
        var count = 0;
        list.find('.tt-row').each(function () {
            var row = jQuery(this);
            if (row.find('.posHitchange, .negHitchange.tt-ballistic, .tt-abduction').length) count += Number(row.attr('data-count')) || 1;
        });
        return count;
    }

    function primaryLabel(count) {
        if (count === 1) return 'Target selected weapon';
        //return count > 1 ? 'Target ' + count + ' weapons' : 'Target weapons';
        return 'Target selected weapons';
    }

    function primaryButton(buttonData, count) {
        var menu = this.menu;
        var label = buttonData.label ? captionOf(buttonData, menu) : primaryLabel(count);
        var element = jQuery('<button type="button" class="fv-shipcard__fire"></button>').attr('data-action', buttonData.className);
        element.append(jQuery('<span class="fv-shipcard__well"></span>').append(jQuery('<span class="fv-shipcard__icon"></span>').addClass(buttonData.className)));
        element.append(jQuery('<span class="fv-shipcard__lab"></span>').text(label));
        var info = infoOf(buttonData, menu);
        if (info) element.attr('title', info);
        menu.bindAction(element, buttonData, this);
        return element;
    }

    //Same well + label build as the Target button (user, 2026-11-13: same size and layout), in
    //the footer's own colours.
    function footerButton(buttonData) {
        var menu = this.menu;
        var element = jQuery('<button type="button" class="fv-shipcard__btn"></button>').attr('data-action', buttonData.className);
        element.append(jQuery('<span class="fv-shipcard__well"></span>').append(jQuery('<span class="fv-shipcard__icon"></span>').addClass(buttonData.className)));
        element.append(jQuery('<span class="fv-shipcard__lab"></span>').text(captionOf(buttonData, menu)));
        var info = infoOf(buttonData, menu);
        if (info) element.attr('title', info);
        menu.bindAction(element, buttonData, this);
        return element;
    }

    //Clear all EW sits small in the EW section's own heading row, pinned right (round 3, user
    //2026-11-10) - it used to take a row of its own under the steppers.
    function clearButtonSlot() {
        var heading = ensureSection.call(this, 'ew').children('.fv-shipcard__mh').first();
        var slot = heading.find('.fv-shipcard__clr');
        if (!slot.length) {
            slot = jQuery('<span class="fv-shipcard__clr"></span>');
            heading.append(slot);
        }
        return slot;
    }

    function clearButton(buttonData) {
        var element = jQuery('<button type="button" class="fv-shipcard__clear"></button>')
            .attr('data-action', buttonData.className).text(captionOf(buttonData, this.menu) || 'Clear EW');
        var info = infoOf(buttonData, this.menu);
        if (info) element.attr('title', info);
        this.menu.bindAction(element, buttonData, this);
        return element;
    }

    //The box an EW type's rows belong in, made if the model did not draw one.
    function ensureSection(key) {
        var body = this.element.find('.fv-shipcard__body');
        if (key === 'relew') {
            //EW spent ON this unit: the From section's last rows (user 2026-11-18). With no From
            //section (no unit selected) one is made, headed plain "From", where renderBody puts it.
            var box = body.find('.fv-shipcard__relew');
            if (box.length) return box.first();
            var rel = body.find('.fv-shipcard__sec--rel');
            if (!rel.length) {
                rel = sectionElement('rel', 'From');
                var cells = body.find('.fv-shipcard__cells');
                if (cells.length) rel.insertBefore(cells.first()); else body.append(rel);
            }
            box = jQuery('<div class="fv-shipcard__relew"></div>');
            rel.first().append(box);
            return box;
        }

        var section = body.find('.fv-shipcard__sec--' + key);
        if (section.length) return section;
        section = sectionElement('ew', 'Electronic warfare', 'ew');
        var anchor = body.find('.fv-shipcard__cells');
        if (!anchor.length) anchor = body.find('.fv-shipcard__sec--rel');
        if (anchor.length) section.insertAfter(anchor.last()); else body.prepend(section);
        return section;
    }

    function findOrMakeEwRow(group) {
        var type = EW_TYPE[group];
        if (!type) return null;
        var row = this.element.find('.fv-shipcard__ewr[data-ew="' + group + '"]');
        if (row.length) return row.first();

        var targetScope = type.scope === 'target';
        var section = ensureSection.call(this, targetScope ? 'relew' : 'ew');
        var ship = this.ships[0];
        var source = this.menu.selectedShip;
        //The stepper's own subject: the selected unit spending, on this card's unit or on itself.
        var value = readSafely(function () {
            return targetScope ? ewValue(group, source, ship) : ewValue(group, source || ship);
        }, 0);
        row = ewRowElement(ewRow(group, value));

        //Keep the DEW-first order: insert before the first row that sorts after this one.
        var order = targetScope ? TARGET_EW_ORDER : SELF_EW_ORDER;
        var rank = order.indexOf(group);
        var before = null;
        section.find('.fv-shipcard__ewr').each(function () {
            if (before) return;
            if (order.indexOf(this.getAttribute('data-ew')) > rank) before = jQuery(this);
        });
        if (before) row.insertBefore(before); else section.append(row);
        return row;
    }

    function attachStepper(row, group, halves) {
        var self = this;
        var type = EW_TYPE[group] || { label: group };
        row.addClass('fv-shipcard__ewr--live');
        [['remove', MINUS, '.fv-shipcard__sp--remove'], ['add', '+', '.fv-shipcard__sp--add']].forEach(function (half) {
            var role = half[0];
            var buttonData = halves[role];
            var element = jQuery('<button type="button" class="fv-shipcard__sb"></button>').text(half[1])
                .attr('aria-label', (role === 'add' ? 'Add ' : 'Remove ') + (type.full || type.label));
            if (buttonData) {
                element.attr('data-action', buttonData.className);
                var info = infoOf(buttonData, self.menu);
                if (info) element.attr('title', info);
                self.menu.bindAction(element, buttonData, self);
            } else {
                //The standard stepper: a − at nothing to remove is there, and off (§5.3)
                element.prop('disabled', true);
            }
            row.find(half[2]).replaceWith(element);
        });
    }

    // =========================================================================
    //  Head drag (D10)
    // =========================================================================

    /* Drag the pinned card by its head band. Per instance and never remembered - the picker's rule:
       the next card opens on its own unit, so it can never appear somewhere unrelated to the click.
       Once dragged it stops re-anchoring on zoom and scroll (place() returns early), and the hex
       picker stops nudging it (SelectFromShips.placeTooltipClear). */
    function bindHeadDrag(handle) {
        var self = this;
        var origin = null;

        handle.on('pointerdown', function (e) {
            if (self.inSheet) return; //the sheet is the thing that moves
            if (jQuery(e.target).closest('.tt-close, .fv-shipcard__sidetoggle').length) return;
            var event = e.originalEvent || e;
            if (event.button !== undefined && event.button !== 0) return;
            var rect = self.element[0].getBoundingClientRect();
            origin = { x: event.clientX, y: event.clientY, left: rect.left, top: rect.top, moved: false };
            //Capture on the element that received the press, so a fast drag that outruns the head
            //keeps feeding us moves.
            try { this.setPointerCapture(event.pointerId); } catch (err) { /* optional */ }
        });

        handle.on('pointermove', function (e) {
            if (!origin) return;
            var event = e.originalEvent || e;
            var dx = event.clientX - origin.x;
            var dy = event.clientY - origin.y;

            if (!origin.moved) {
                if (Math.abs(dx) < DRAG_SLOP && Math.abs(dy) < DRAG_SLOP) return; //click jitter
                origin.moved = true;
                self.dragged = true;
                self.element.addClass('fv-shipcard--dragged');
                //Pulled away from the picker: no longer docked, and its caret would point at nothing.
                //The ORDERS panel stays on the side it was on - flipping it now would jump the card
                //out from under the pointer.
                if (self.dock) {
                    var sideLeft = self.element.hasClass('fv-shipcard--side-left');
                    self.undock(true);
                    self.element.toggleClass('fv-shipcard--side-left', sideLeft);
                }
            }

            var node = self.element[0];
            self.element.css({
                left: clamp(origin.left + dx, EDGE, window.innerWidth - node.offsetWidth - EDGE) + 'px',
                top: clamp(origin.top + dy, EDGE, window.innerHeight - node.offsetHeight - EDGE) + 'px'
            });
        });

        handle.on('pointerup pointercancel', function (e) {
            var event = e.originalEvent || e;
            try { this.releasePointerCapture(event.pointerId); } catch (err) { /* optional */ }
            origin = null;
        });
    }

    // =========================================================================
    //  Stack (several units under one cursor)
    // =========================================================================

    // The hover tooltip for a STACKED hex — several units under one cursor, none of them
    // committed to yet. A grid of the silhouettes the map draws, each on the hex picker's 3px
    // allegiance rail, with a flight's active fighter count printed over its art. That answers
    // "what is in this hex" at a glance and — deliberately — nothing more: this surface is a
    // PREVIEW. Identifying a particular unit is the picker's job, one click away
    // (SELECT_FROM_SHIPS_PLAN.md §2.8). Touch has no hover, so this only ever appears for a mouse.
    function createForMultipleShips(ships) {
        var grid = jQuery('<div class="tt-stack"></div>');

        ships.forEach(function (ship) {
            var shipNameDisplay = ship.name;
            var masked = false;
            if (ship.mine) {
                var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
                if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                    shipNameDisplay = "Mine";
                    masked = true;
                }
            }

            var cell = jQuery('<div class="tt-stack__cell"></div>').addClass(getAllyClass(ship));
            //Observers and 3+-team games override the rail and count colours per team,
            //exactly as the picker's rows do. Inline beats the allegiance class.
            var teamVars = getTeamColorVars(ship);
            if (teamVars) cell.attr('style', teamVars);

            cell.append(jQuery('<span class="tt-stack__bar"></span>'));

            //⚠️ A masked mine's imagePath still identifies the mine type that masking the
            //NAME exists to hide — the same trap the picker's thumbnails have to dodge.
            //It gets the generic glyph, never its own art.
            if (!masked && ship.imagePath && window.AssetManager) {
                //Same URL the map texture already fetched, so this is an HTTP cache hit
                //rather than a new download.
                cell.append(jQuery('<img class="tt-stack__art">')
                    .attr('alt', shipNameDisplay)
                    .attr('src', window.AssetManager.getSmartImagePath(ship.imagePath))
                    //Fall back to the glyph rather than hiding: in a grid with no names
                    //beside it, an empty cell says nothing at all.
                    .on('error', function () {
                        jQuery(this).replaceWith('<span class="tt-stack__art tt-stack__art--generic"></span>');
                    }));
            } else {
                cell.append(jQuery('<span class="tt-stack__art tt-stack__art--generic"></span>'));
            }

            if (ship.flight === true) {
                cell.append(jQuery('<span class="tt-stack__count"></span>')
                    .text(shipManager.systems.getActiveFighterCount(ship)));
            }

            grid.append(cell);
        });

        this.element.find('.namecontainer').addClass('namecontainer--stack').append(grid);

        jQuery(".ballistics", this.element).hide();
    }

    // =========================================================================
    //  Allegiance
    // =========================================================================

    function viewportPoint(position) {
        if (!position || !window.coordinateConverter) return null;
        if (position instanceof hexagon.Offset) {
            return window.coordinateConverter.fromHexToViewport(position);
        }
        return window.coordinateConverter.fromGameToViewPort(position);
    }

    function getAllyClass(ship) {
        //Let's make allied team ships blue text, and terrain white - DK May 2025
        return gamedata.isTerrain(ship.shipSizeClass, ship.userid) ? 'terrain' : (gamedata.isMyShip(ship) ? 'mine' : (gamedata.isMyorMyTeamShip(ship) ? 'ally' : 'enemy'));
    }

    // What the CSS allegiance classes in tactical.css paint, as values rather than as
    // classes. The BRIGHT tier (2026-08-20) — these must track .mine.name / .ally.name /
    // .enemy.name exactly. Terrain has no bright twin and stays neutral.
    var ALLEGIANCE_NAME = {
        mine: 'var(--fv-own-bright)',
        ally: 'var(--fv-ally-bright)',
        enemy: 'var(--fv-enemy-bright)',
        terrain: 'var(--fv-neutral)'
    };

    // The card's border and rail (§11 item 3): the SIGNAL tier, the hex picker's rail colours.
    var ALLEGIANCE_RAIL = {
        mine: 'var(--fv-own-signal)',
        ally: 'var(--fv-ally-signal)',
        enemy: 'var(--fv-enemy-signal)',
        terrain: 'var(--fv-neutral-signal)'
    };

    // THE gate, in one place: does this ship take the absolute per-team palette, or the
    // relative mine/ally/enemy CSS classes? Mirrors the fleetList / combat-log scheme
    // (see gamedata.getFleetHeaderColorRGB):
    //   - Terrain: neutral — always the CSS 'terrain' class, never a team colour.
    //   - 2-team participant: relative mine/ally/enemy from getAllyClass, so your own
    //     fleet reads green whichever team number you happen to be.
    //   - Observer, OR 3+-team participant: absolute per-team palette, so each team is
    //     identifiable rather than everyone collapsing to two colours.
    function usesTeamColor(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
        return !(gamedata.isPlayerInGame() && gamedata.getDistinctTeamCount() === 2);
    }

    function teamRgb(ship) {
        var rgb = gamedata.getTeamColorRGB(ship.team);
        return 'rgb(' + Math.round(rgb[0]) + ',' + Math.round(rgb[1]) + ',' + Math.round(rgb[2]) + ')';
    }

    // TONED (user, 2026-11-21: the card's colours read "a bit rich", and should blend with the
    // page): every allegiance colour the card paints - its rail and border (and so the head
    // wash, the tinted labels and button lines, which shipTooltip.css derives from the rail),
    // the name, and other units' names in From / Fire orders - is mixed a step toward grey, on
    // BOTH arms of the gate alike. The knobs, --tt-chroma and --tt-grey, are in shipTooltip.css
    // on the card and its caret. Card-only: the map, fleet list, combat log, hex picker rows
    // and the stack grid keep the team palette as it is. Without color-mix() nothing changes.
    var CAN_TONE = !!(window.CSS && CSS.supports && CSS.supports('color', 'color-mix(in srgb, red 50%, blue)'));

    function toned(color) {
        return CAN_TONE ? 'color-mix(in srgb, ' + color + ' var(--tt-chroma), var(--tt-grey))' : color;
    }

    // Inline colour for the ship name. Empty string on the class path — the CSS class
    // from getAllyClass is already carrying it, and returning nothing is what lets it -
    // unless the colour is toned (above): a class cannot be toned for one surface, so then
    // both arms take the inline colour.
    // The two arms agree on BRIGHTNESS as well as on scheme (2026-08-20): keep them level.
    function getNameStyle(ship) {
        if (!usesTeamColor(ship) && !CAN_TONE) {
            return '';
        }
        return ' style="color:' + getNameColor(ship) + ';"';
    }

    // The colour the name will ACTUALLY render in — the same decision as getNameStyle,
    // but resolved to a value on both arms. The "From" heading paints a selected unit's
    // name with it.
    function getNameColor(ship) {
        if (!usesTeamColor(ship)) {
            return toned(ALLEGIANCE_NAME[getAllyClass(ship)] || 'var(--fv-text)');
        }
        return toned(teamRgb(ship));
    }

    // The rail and border colour - the same gate again. The raw team colour on the team arm, as
    // the picker's rows put it on their rail (getTeamColorVars): a full-chroma value reads as a
    // signal precisely because there is so little of it. Toned since 2026-11-21 (above).
    function getRailColor(ship) {
        if (!usesTeamColor(ship)) {
            return toned(ALLEGIANCE_RAIL[getAllyClass(ship)] || 'var(--fv-line-scs)');
        }
        return toned(teamRgb(ship));
    }

    // The stack grid's two per-cell channels, for the observer / 3+-team case only.
    // Byte-for-byte the same idea as SelectFromShips.getTeamColorVars: the RAW team
    // colour on the 3px rail, and a tone-mapped twin on the count badge, so an
    // arbitrary team colour lands in the same brightness band as the four fixed tints.
    function getTeamColorVars(ship) {
        if (!usesTeamColor(ship)) return '';

        var raw = gamedata.getTeamColorRGB(ship.team);
        var toned = (typeof gamedata.getMidTeamColorRGB === 'function')
            ? gamedata.getMidTeamColorRGB(ship.team)
            : raw;

        return '--row-bar:rgb(' + Math.round(raw[0]) + ',' + Math.round(raw[1]) + ',' + Math.round(raw[2]) + ');'
            + '--row-name:rgb(' + Math.round(toned[0]) + ',' + Math.round(toned[1]) + ',' + Math.round(toned[2]) + ');';
    }

    // HK Jamming: true when the flight's sample fighter carries an Uncontrolled crit
    // that is in effect THIS turn. Uncontrolled is a oneturn crit rolled on turn T
    // (turnend = T+1) and active on T+1, so match crit.turn + 1 === current turn.
    function uncontrolledInEffect(firstFighter) {
        if (!firstFighter || !firstFighter.criticals) return false;
        for (var i in firstFighter.criticals) {
            var crit = firstFighter.criticals[i];
            if (crit.phpclass === "Uncontrolled" && (crit.turn + 1) === gamedata.turn) {
                return true;
            }
        }
        return false;
    }

    /* Energy Draining Field: the flight cannot fire this turn. EdfFighterGrounded is a `oneturn`
       crit rolled in turn T's Critical Hit step (turnend = T+1) and in effect on T+1, so the test
       is the same crit.turn + 1 === current turn as Uncontrolled above. Read off the flight's
       sample fighter, which is where EdfExposure hangs every flight-wide record. */
    function edfGroundedInEffect(firstFighter) {
        if (!firstFighter || !firstFighter.criticals) return false;
        for (var i in firstFighter.criticals) {
            var crit = firstFighter.criticals[i];
            if (crit.phpclass === "EdfFighterGrounded" && (crit.turn + 1) === gamedata.turn) {
                return true;
            }
        }
        return false;
    }

    // For node harnesses (plan §7): the model alone, no DOM.
    ShipTooltip.buildModel = buildModel;
    ShipTooltip.EDF_PURPLE = EDF_PURPLE;

    //The OPTIONS tab's switch, for the cards already open (gameOptions.js applyShipTooltipOpacity).
    ShipTooltip.applyTranslucency = function (on) {
        jQuery('.shipNameContainer.fv-shipcard, .fv-shipcard-caret').toggleClass(TRANSLUCENT, !!on);
    };

    return ShipTooltip;
}();
