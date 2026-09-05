'use strict';

/*
 * window.SelectFromShips — the Hex Stack Picker.
 *
 * See SELECT_FROM_SHIPS_PLAN.md. This popup has exactly two jobs:
 *   (A) disambiguate  — "several units are stacked here; which one did you mean?"
 *   (B) offer hex actions — during Deployment, "place the selected unit here" /
 *       "dock it into one of the carriers here".
 *
 * It used to be a clickable copy of the hover ShipTooltip: it shared that surface's
 * .shipNameContainer class, and therefore its opacity:0.65, over a moving starfield, with
 * ~19px bare-text rows and no viewport clamping. It is now what it always was — a context
 * menu for a hex — and lives in its own .fv-hexpicker namespace with its own stylesheet
 * (styles/hexPicker.css), so no tooltip rule is touched and the two surfaces are no longer
 * coupled by accident.
 *
 * The DEPLOY / DOCK eligibility blocks below are carried across VERBATIM from the old
 * implementation; they encode several hard-won rulings and only the DOM they emit changed.
 */
window.SelectFromShips = function () {

    var HTML = '<div class="fv-hexpicker"></div>';

    // ── Thresholds ──────────────────────────────────────────────────────────────
    // Grouping keys on CATEGORY COUNT; density keys on UNIT COUNT; run-collapsing keys
    // on RUN LENGTH. Three independent knobs on purpose, so any one can be retuned
    // without disturbing the others. The geometry they select between lives in the
    // tuning-knob block at the top of hexPicker.css.
    var GROUP_MIN_CATEGORIES = 2;   // headers appear at ANY stack size once 2+ buckets fill
    var DENSE_AT_UNITS = 8;   // >8 UNITS in the hex -> the dense tier. Keep in
    //                                 step with --hp-dense-at in hexPicker.css.
    var COLLAPSE_RUN_MIN = 3;   // consecutive identical rows collapse from 3 up
    var LONG_PRESS_MS = 500;   // matches webglScene's canvas long-press exactly
    var SHEET_MAX_WIDTH = 765;   // px; see useSheet()
    var EDGE_MARGIN = 8;   // viewport clamp inset
    var CARET_HALF = 8;   // half the caret triangle. It runs down a VERTICAL edge, so
    //                                 this is half its height, not half its width.
    var TOUCH_SLOP = 10;  // px of movement that cancels a long press
    var TOOLTIP_GAP = 10;  // gap left when the hover tooltip has to be slid clear
    var FLIP_SLACK = 24;  // px of SPARE room the far side must offer before a flip

    var CATEGORY_ORDER = ['ship', 'flight', 'mine', 'terrain'];
    var CATEGORY_LABEL = { ship: 'Ships', flight: 'Flights', mine: 'Mines', terrain: 'Terrain' };

    var INI_TITLE = 'Initiative order';

    // Which of the two shapes this instance takes. An anchored popover is the wrong form
    // on a phone: the anchor is under the finger that opened it, and there is no room
    // above a hex near the screen edge. The width test catches small windows; the pointer
    // test catches tablets that are wide but still finger-driven. Either alone is enough.
    function useSheet() {
        var coarse = !!(window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
        return coarse || window.innerWidth <= SHEET_MAX_WIDTH;
    }

    function clamp(value, low, high) {
        if (high < low) return low;
        return Math.min(Math.max(value, low), high);
    }

    // Was the interaction being handled driven by a finger rather than a mouse?
    //
    // `contextmenu` carries no pointerType of its own, so this reads the type recorded by
    // the most recent pointerdown on a row. The media query is only the fallback for a
    // contextmenu that somehow arrives without one: (pointer: coarse) describes the
    // PRIMARY input, so it is true on a phone or tablet and false on a touchscreen laptop
    // being driven with its mouse — which is the distinction wanted here.
    function isTouchInteraction() {
        if (this.lastPointerType) return this.lastPointerType !== 'mouse';
        return !!(window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
    }

    // =========================================================================
    //  Construction
    // =========================================================================

    function HexPicker(selectedShip, ships, payload, phaseStrategy) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        this.ships.sort(byInitiativeOrder); //ascending Initiative order, matching the INI column
        this.position = payload.hex;
        this.payload = payload;
        this.selectedShip = selectedShip;
        this.phaseStrategy = phaseStrategy;

        this.sheet = useSheet();
        this.destroyed = false;
        // Both are PER-INSTANCE and deliberately not remembered between openings: a
        // player who folded "Mines" once must never later open a picker with units
        // silently hidden.
        this.foldedGroups = {};
        this.expandedRuns = {};
        this.longPressTimer = null;
        this.longPressFired = false;
        this.previousFocus = null;
        this.hoveredShipId = null;
        //Which side of the hex the card sits on, once positionSelf() has decided. Null
        //until the first placement; see positionSelf for why it is then STICKY.
        this.placement = null;
        //The hex's viewport point as of the last placement. reflow() re-places the caret
        //from it after a fold, without re-running the side decision.
        this.anchorPoint = null;
        //Set once the header has been dragged. Per-instance: the next opening re-anchors
        //on its own hex, so a dragged position is never remembered.
        this.dragged = false;

        // Kept verbatim from the classic picker. These are what stop a drag that starts
        // on the popup from panning the map, and stop the hover tooltip flickering
        // underneath it. They do NOT cover touchmove, so the list still scrolls on touch.
        this.element.on('mousedown', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseup', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseover', function (e) {
            e.preventDefault(); e.stopPropagation();
        });
        //preventDefault only, deliberately NOT stopPropagation. webglScene binds its mouse
        //handlers to #pagecontainer, which is a SIBLING of this card (both are appended to
        //body), so map hover never sees these events in the first place — while a
        //document-level listener that does care, such as a jQuery-UI drag already in
        //progress, would stall as the cursor crossed the card.
        this.element.on('mousemove', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseout', function (e) {
            e.preventDefault(); e.stopPropagation();
        });

        this.element
            .attr('role', 'menu')
            .attr('tabindex', '-1')
            .attr('aria-label', 'Units in hex');

        if (this.sheet) {
            this.element.addClass('fv-hexpicker--sheet');
        }
        // Density keys on how crowded the HEX is, not on how many rows survived
        // collapsing — a 20-unit stack that folds down to 8 rows is still a dense hex.
        if (this.ships.length > DENSE_AT_UNITS) {
            this.element.addClass('fv-hexpicker--dense');
        }

        this.actions = buildActions.call(this);
        this.model = buildModel.call(this);

        render.call(this);

        this.show();
    }

    HexPicker.prototype.show = function () {
        this.previousFocus = document.activeElement;

        this.element.appendTo('body');
        // positionSelf() reads offsetWidth/offsetHeight, which forces layout with the
        // card still at opacity:0 — so adding the class immediately after is a real
        // transition start, not a same-frame no-op. No rAF needed (and none wanted: a
        // backgrounded tab would leave the card permanently invisible).
        this.positionSelf();
        this.element.addClass('fv-hexpicker--in');

        var self = this;
        this.keyHandler = function (event) { onKeyDown.call(self, event); };
        // Bound on DOCUMENT rather than window so that stopPropagation() here beats
        // webglScene's window-level keydown — otherwise Space would activate a row AND
        // toggle replay play/pause.
        jQuery(document).on('keydown', this.keyHandler);

        try {
            this.element[0].focus({ preventScroll: true });
        } catch (e) {
            this.element[0].focus();
        }
    };

    HexPicker.prototype.reposition = function (position) {
        if (position) {
            this.position = position;
        }

        //Returning truthy matters: the onZoomCallbacks/onScrollCallbacks filter keeps
        //only callbacks that return truthy, so returning undefined silently unregisters.
        if (this.destroyed) {
            return true;
        }

        this.positionSelf();
        //⚠️ This is what stops the hover tooltip jumping mid-zoom. PhaseStrategy's
        //onZoom/onScroll callback list is [repositionTooltip, positionMovementUI,
        //repositionSelectFromShips], so the tooltip has ALREADY been re-anchored on its
        //own hex by the time we get here — any avoidance nudge applied when the row was
        //first hovered is gone. Re-deriving it now makes the answer the same on every
        //step of the gesture instead of only on the first.
        this.placeTooltipClear();

        return true;
    };

    HexPicker.prototype.destroy = function () {
        if (!this.destroyed) {
            this.destroyed = true;
            this.cancelLongPress();
            if (this.keyHandler) {
                jQuery(document).off('keydown', this.keyHandler);
                this.keyHandler = null;
            }
        }

        this.element.remove();

        var previous = this.previousFocus;
        this.previousFocus = null;
        if (previous && previous !== document.body && document.contains(previous)) {
            try { previous.focus({ preventScroll: true }); } catch (e) { /* best effort */ }
        }
    };

    // Close initiated from inside the card (✕ / Esc). Hands the phaseStrategy a chance to
    // drop its reference first, so a later reposition() can't reach a dead instance.
    HexPicker.prototype.close = function () {
        if (this.phaseStrategy && typeof this.phaseStrategy.hideSelectFromShips === 'function') {
            this.phaseStrategy.hideSelectFromShips(this);
        }
        this.destroy();
    };

    //Unreferenced today, kept as a harmless stub so the public surface is unchanged.
    HexPicker.prototype.addEntryElement = function (value, condition) {
        if (condition === false || condition === 0 || condition === null) return;
    };

    HexPicker.prototype.update = function (ship, selectedShip) {

    };

    // =========================================================================
    //  Positioning
    // =========================================================================

    // Measure the card BEFORE a re-render, so reflow() below can hold its top steady
    // across the size change. Null for a still-docked sheet, where the bottom edge is
    // pinned by CSS and the sheet grows upward on its own — but a sheet that has been
    // DRAGGED runs on left/top like a card and needs the same treatment.
    HexPicker.prototype.captureBox = function () {
        if (this.sheet && !this.dragged) return null;
        return { top: this.element[0].getBoundingClientRect().top };
    };

    // Re-place the card after folding a group or expanding a run — WITHOUT re-anchoring.
    //
    // Calling positionSelf() here is what made the card jump: it re-runs the side
    // decision from scratch, so a card that had flipped to the hex's LEFT because it was
    // too wide to fit on the right would flip back the moment a fold changed anything.
    // The player folds one group and the whole menu leaps across the hex.
    //
    // Instead hold the TOP, which is how every collapsible panel behaves — and, because
    // the caret is re-placed from the anchor afterwards, ALSO what keeps the caret on the
    // hex: the hex is at a fixed screen position, so a card whose top has not moved still
    // meets it at the same absolute height. The caret only disappears if a fold shrinks
    // the card past the hex entirely, which takes folding away more than half of it.
    HexPicker.prototype.reflow = function (before) {
        if (!before) return;

        var node = this.element[0];
        var width = node.offsetWidth;
        var height = node.offsetHeight;

        var left = clamp(node.getBoundingClientRect().left,
            EDGE_MARGIN, window.innerWidth - width - EDGE_MARGIN);
        var top = clamp(before.top,
            EDGE_MARGIN, window.innerHeight - height - EDGE_MARGIN);

        this.element.css({ left: left + 'px', top: top + 'px' });

        if (!this.dragged) {
            placeCaret.call(this, top, height);
        }
    };

    // Keep the caret level with the hex AFTER the clamp; hide it when the anchor ended up
    // outside the card, where a caret would be pointing at nothing. The card is anchored
    // beside its hex, so this runs down the card's vertical edge — `top` and `height` are
    // the card's, and the anchor is the hex's viewport point recorded by positionSelf.
    function placeCaret(top, height) {
        if (!this.caretElement || !this.anchorPoint) return;

        var caretY = this.anchorPoint.y - top;
        if (caretY < CARET_HALF * 2 || caretY > height - CARET_HALF * 2) {
            this.caretElement.hide();
        } else {
            this.caretElement.show().css('top', (caretY - CARET_HALF) + 'px');
        }
    }

    HexPicker.prototype.positionSelf = function () {
        // A sheet that re-anchored on pinch-zoom would jump around under the finger, and
        // repositionSelectFromShips fires on every zoom AND every scroll. The same call
        // is what would yank a dragged card back onto its hex, so a drag opts out too.
        if (this.sheet || this.dragged) {
            return;
        }

        var position = this.position;
        var point;
        if (position instanceof hexagon.Offset) {
            point = window.coordinateConverter.fromHexToViewport(position);
        } else {
            point = window.coordinateConverter.fromGameToViewPort(position);
        }

        // THE CARD SITS BESIDE ITS HEX, NOT ABOVE OR BELOW IT.
        //
        // Above/below is the obvious placement for a popover and it was the wrong one
        // here, because the hover ShipTooltip is anchored BELOW the hex too — so the two
        // surfaces were competing for one strip of screen. The picker won by shoving the
        // tooltip out of the way (placeTooltipClear used to move it unconditionally), and
        // that shove lasted exactly until the next zoom or scroll step: PhaseStrategy's
        // onZoomCallbacks re-anchor the tooltip on its own hex, so it snapped back under
        // the card and stayed there. Vacating the vertical strip entirely is what lets
        // the tooltip simply open where it always opens.
        //
        // Half the hex's WIDTH, since the gap being cleared is now a horizontal one.
        // Clamped exactly as the old yOffset was: at extreme zoom a raw half-hex is
        // either a hairline or half the screen.
        var xOffset = clamp(window.coordinateConverter.getHexWidthViewport() / 2, 20, 100);

        var node = this.element[0];
        var width = node.offsetWidth;
        var height = node.offsetHeight;

        // How much width each side of the hex can actually hold.
        var roomRight = (window.innerWidth - EDGE_MARGIN) - (point.x + xOffset);
        var roomLeft = (point.x - xOffset) - EDGE_MARGIN;

        // The left/right choice is STICKY, and that is the whole point of this block.
        //
        // positionSelf() runs on EVERY zoom step and every scroll, and re-deciding from
        // scratch each time is what made the card hop across its own hex mid-pinch: zoom
        // changes the hex's viewport position AND xOffset (half the hex width, clamped
        // 20..100), so a card that had flipped left because it did not fit right would
        // find it fitted again a step later, jump across, then jump back on the next
        // step. The player was doing one continuous gesture and the menu answered with
        // two different layouts.
        //
        // So: decide once, on the first placement — prefer the RIGHT. After that keep
        // that side for as long as it still holds the card, and require FLIP_SLACK px of
        // SPARE room on the far side before moving, so a hex parked on the boundary
        // cannot oscillate between two answers that are both marginal. Neither side
        // fitting keeps the current one and lets the clamp below deal with it, which is
        // the least surprising of the bad options.
        var fitsRight = width <= roomRight;
        var fitsLeft = width <= roomLeft;
        var onLeft;

        if (!this.placement) {
            onLeft = !fitsRight;
        } else if (this.placement === 'right') {
            onLeft = !fitsRight && (width + FLIP_SLACK <= roomLeft);
        } else {
            onLeft = !(!fitsLeft && (width + FLIP_SLACK <= roomRight));
        }
        this.placement = onLeft ? 'left' : 'right';

        var left = onLeft ? point.x - xOffset - width : point.x + xOffset;
        //Level with the hex rather than hanging off it: the caret then lands in the
        //middle of the card's edge, and a fold has to eat more than half the card before
        //the anchor drops off it (see reflow).
        var top = point.y - height / 2;

        left = clamp(left, EDGE_MARGIN, window.innerWidth - width - EDGE_MARGIN);
        top = clamp(top, EDGE_MARGIN, window.innerHeight - height - EDGE_MARGIN);

        this.element.css({ left: left + 'px', top: top + 'px' });
        this.element
            .toggleClass('fv-hexpicker--left', onLeft)
            .toggleClass('fv-hexpicker--right', !onLeft);

        this.anchorPoint = point;
        placeCaret.call(this, top, height);
    };

    // =========================================================================
    //  Model
    // =========================================================================

    function categoryOf(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return 'terrain';
        if (ship.mine) return 'mine';
        if (ship.flight) return 'flight';
        return 'ship';
    }

    // The movement GROUP number the Order of Battle prints down its left edge, not the
    // raw initiative total — ships that move together share one, which is the thing
    // actually worth knowing in a stacked hex. null (rendered as an em dash) for anything
    // with no group of its own; see SimultaneousMovementRule.getMovementGroup, which is
    // also what the movement-phase map badge numbers itself from.
    function iniOrderOf(ship) {
        return window.SimultaneousMovementRule.getMovementGroup(ship);
    }

    // List order matches the INI column: group 1 (the first to move) at the top, counting
    // up. getIniativeOrder numbers the groups from the LOWEST raw initiative total, so
    // ascending INI order means ascending raw initiative — the exact reverse of
    // shipManager.hasWorseInitiveSort, which this list used to use. Units with no INI of
    // their own (mines, terrain, not-yet-deployed) sort on their raw total like everything
    // else; mines and terrain are in their own categories anyway.
    function byInitiativeOrder(a, b) {
        if (a.iniative < b.iniative) return -1;
        if (a.iniative > b.iniative) return 1;
        if (a.iniativebonus < b.iniativebonus) return -1;
        if (a.iniativebonus > b.iniativebonus) return 1;
        //Ids can arrive as strings (spawned units), so compare them numerically. Keeping
        //a stable tail also keeps a bulk buy's "#1, #2, #3" in number order.
        return parseInt(a.id, 10) - parseInt(b.id, 10);
    }

    function describe(ship) {
        var category = categoryOf(ship);
        var label = ship.name;
        var masked = false;

        if (ship.mine) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
            if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                label = "Mine";
                masked = true;
            }
        }

        var chips = [];
        var deployTurn = shipManager.getTurnDeployed(ship);
        if (deployTurn > gamedata.turn) chips.push('Deploys T' + deployTurn);
        if (masked) chips.push('Unrevealed');
        if (category === 'terrain') chips.push('Terrain');

        // A masked mine's shipClass and imagePath BOTH still identify the mine type that
        // the name masking exists to hide. Suppress the class line and swap the
        // silhouette for a generic glyph — the text masking is the only reason adding art
        // here is safe at all. The same rule applies to a collapsed run's summary row.
        var shipClass = masked ? '' : String(ship.shipClass || '');
        //Don't repeat the name as its own class line (terrain and bulk stems do this).
        if (shipClass === label) shipClass = '';

        var descriptor = {
            ship: ship,
            category: category,
            label: label,
            masked: masked,
            //Replay-aware active-fighter count; shared with the hover ShipTooltip's
            //stack grid, which prints the same number over the flight's silhouette.
            count: ship.flight ? shipManager.systems.getActiveFighterCount(ship) : null,
            shipClass: shipClass,
            chips: chips,
            art: masked ? null : ship.imagePath,
            allyClass: getAllyClass(ship),
            teamVars: getTeamColorVars(ship),
            ini: iniOrderOf(ship)
        };

        // The comparison key is the RENDERED CONTENT, not the ship type or phpclass: two
        // mines are "the same" only when the player would see the same row twice. Art is
        // part of the key only when it is actually drawn — two masked mines of different
        // types both render the generic glyph and so must still be able to merge.
        descriptor.key = [
            category, label, shipClass, chips.join('|'), descriptor.allyClass,
            descriptor.teamVars,
            descriptor.ini === null ? '-' : descriptor.ini,
            descriptor.count === null ? '-' : descriptor.count,
            masked ? 'MASKED' : (descriptor.art || '')
        ].join('');

        return descriptor;
    }

    // Consecutive rows that would render IDENTICALLY are not a choice, they are a
    // repetition. In practice this can essentially only fire on an unscanned enemy
    // minefield: individually-bought ships take a player-supplied name, bulk purchases are
    // server-numbered "#1, #2, …" by numberBulkCopy, and the INI value is part of the key.
    function collapseRuns(list) {
        var items = [];
        var i = 0;
        while (i < list.length) {
            var j = i + 1;
            while (j < list.length && list[j].key === list[i].key) j++;

            if (j - i >= COLLAPSE_RUN_MIN) {
                //Index-qualified so two runs that happen to share a key stay independent.
                items.push({ type: 'run', id: i + '' + list[i].key, members: list.slice(i, j) });
            } else {
                for (var k = i; k < j; k++) items.push({ type: 'row', descriptor: list[k] });
            }
            i = j;
        }
        return items;
    }

    function buildModel() {
        var descriptors = this.ships.map(describe);

        var buckets = {};
        CATEGORY_ORDER.forEach(function (category) { buckets[category] = []; });
        descriptors.forEach(function (descriptor) { buckets[descriptor.category].push(descriptor); });

        var groups = [];
        CATEGORY_ORDER.forEach(function (category) {
            if (!buckets[category].length) return;
            groups.push({
                category: category,
                label: CATEGORY_LABEL[category],
                count: buckets[category].length,
                items: collapseRuns(buckets[category])
            });
        });

        return {
            groups: groups,
            showHeaders: groups.length >= GROUP_MIN_CATEGORIES
        };
    }

    // =========================================================================
    //  Deployment actions — eligibility carried across VERBATIM
    // =========================================================================

    function buildActions() {
        var actions = [];

        // --- "DEPLOY HERE" DURING DEPLOYMENT PHASE ---
        if (gamedata.gamephase == -1 && this.phaseStrategy.selectedShip) {
            // Guarantee we don't present the deployment option if the unit is ALREADY right here
            var rawPos = shipManager.getShipPosition(this.phaseStrategy.selectedShip);
            var parsedSelectedPos = new hexagon.Offset(rawPos);

            // Replicate the exact isBlocked occupancy check from DeploymentPhaseStrategy to explicitly deny the button appearing when dropping onto illegal stacked hexes
            var isBlocked = false;
            var shipsInHex = shipManager.getShipsInSameHex(this.phaseStrategy.selectedShip, this.payload.hex);

            var hasTerrain = shipsInHex.some(function (s) {
                return gamedata.isTerrain(s.shipSizeClass, s.userid) || (s.Huge > 0 && s.Huge <= 3);
            });

            //LCVs are exempt from the same-hex occupancy block (mirrors
            //DeploymentPhaseStrategy.onHexClicked) so an undocked LCV can be placed
            //in the carrier's hex. Terrain still blocks them.
            var selIsLcvUnit = !this.phaseStrategy.selectedShip.flight && !this.phaseStrategy.selectedShip.mine
                && String(this.phaseStrategy.selectedShip.hangarRequired || '').toLowerCase() === 'lcvs';

            /* REINFORCEMENTS_PLAN.md STAGE 7 - an arrival bypasses BOTH blocks, exactly as it does
               in DeploymentPhaseStrategy.onHexClicked (which this whole check is a copy of - keep
               the two in step). The vortex it must stand in is terrain, so `hasTerrain` alone would
               hide this button on the only hex the unit is allowed to occupy; and a wave stacks in
               that one hex by rule (§2.4), so the one-ship-per-hex block has to go too. */
            if (shipManager.isArrivingReinforcement(this.phaseStrategy.selectedShip)) {
                isBlocked = false;
            } else if (hasTerrain) {
                isBlocked = true;
            } else if (!(this.phaseStrategy.selectedShip.mine || this.phaseStrategy.selectedShip.flight || selIsLcvUnit)) {
                isBlocked = shipsInHex.some(function (s) { return !(s.mine || s.flight); });
            }

            //The LCV dock shortcut surfaces this popup WITHOUT pre-validating the
            //deploy position (docking needs none), so the DEPLOY button must check
            //the selected ship's deploy zone itself — otherwise it would offer to
            //place an LCV on a hex outside its deployment area. Non-LCV callers
            //already validated upstream; the check is a cheap belt-and-braces there.
            var posLegal = (typeof window.validateDeploymentPositionForShip !== 'function')
                ? true
                : window.validateDeploymentPositionForShip(this.phaseStrategy.selectedShip, this.payload.hex);

            if (!isBlocked && posLegal && (!parsedSelectedPos || parsedSelectedPos.q !== this.payload.hex.q || parsedSelectedPos.r !== this.payload.hex.r)) {
                var selectedName = this.phaseStrategy.selectedShip.name;
                actions.push({
                    kind: 'deploy',
                    label: 'DEPLOY ' + selectedName.toUpperCase(),
                    note: '',
                    onActivate: function () {
                        this.phaseStrategy.onHexClicked(this.payload);
                        this.destroy();
                    }.bind(this)
                });
            }

            // Stage 7 (Hangar Ops): if the player has a flight selected and the
            // clicked ship(s) include friendly carriers with hangars that fit
            // the flight, expose a single DOCK button. If exactly one carrier
            // is eligible, clicking it auto-docks into that carrier's first
            // compatible hangar. If multiple carriers are eligible (Issue 6),
            // clicking opens a sub-picker dialog so the player picks which
            // carrier — mirrors the Firing-Phase Dock flow, minus splitting.
            if (this.phaseStrategy.selectedShip.flight && window.DeploymentDock) {
                var flight = this.phaseStrategy.selectedShip;
                var eligibleCarriers = [];
                this.ships.forEach(function (s) {
                    if (!s || s.flight) return;
                    if (!gamedata.isMyShip(s)) return;
                    if (!window.DeploymentDock.shipHasOpenableDockDialog(s)) return;
                    //shipHasOpenableDockDialog only asks about the CARRIER. A deploy-docked flight
                    //starts inside the hangar, so the two must join the board on the SAME turn —
                    //and "both being placed this phase" does not imply that, since turn-1 and
                    //turn-2 units are both placed on turn 1. See DeploymentDock.arrivesOnSameTurn.
                    if (typeof window.DeploymentDock.arrivesOnSameTurn === 'function'
                        && !window.DeploymentDock.arrivesOnSameTurn(s, flight)) return;
                    //Same slot/owner, matching findPendingFlightsForCarrier (the tooltip route) and
                    //HangarOps::validateDeployBayOrders, which rejects a cross-slot dock outright.
                    //Without this the button offers a dock the server refuses at commit time.
                    if (parseInt(s.slot, 10) !== parseInt(flight.slot, 10)) return;
                    if (parseInt(s.userid, 10) !== parseInt(flight.userid, 10)) return;
                    //A bay-by-bay eligible list (whole flight fits one bay) is the
                    //common case. But the carrier's rails/bays are a COMBINED pool:
                    //a flight bigger than any single bay (e.g. 9 fighters vs 6-box
                    //rails) is still dockable if the carrier's TOTAL free space
                    //holds it — auto-distribute spreads it across bays on click.
                    //Surface the DOCK button on combined capacity, not just
                    //single-bay fit, so it doesn't disappear when space remains.
                    var eligible = window.DeploymentDock.eligibleHangarsForFlight(s, flight);
                    if (eligible.length === 0) {
                        var plan = window.DeploymentDock.distributeFlightAcrossHangars(s, flight);
                        if (plan.length === 0) return;        //even combined capacity can't hold it
                        //Report each used bay's REAL free box count (not just the
                        //allocated slice) so the carrier-picker readout shows true
                        //headroom rather than a misleading exact-fit total.
                        eligible = plan.map(function (p) {
                            var freeBoxes = (typeof window.DeploymentDock.hangarFreeBoxes === 'function')
                                ? window.DeploymentDock.hangarFreeBoxes(p.hangar)
                                : p.count;
                            return { hangar: p.hangar, capacity: freeBoxes };
                        });
                    }
                    eligibleCarriers.push({ ship: s, hangars: eligible });
                });

                if (eligibleCarriers.length > 0) {
                    actions.push({
                        kind: 'dock',
                        label: 'DOCK ' + flight.name.toUpperCase(),
                        note: eligibleCarriers.length > 1 ? eligibleCarriers.length + ' carriers' : '',
                        onActivate: function () {
                            if (eligibleCarriers.length === 1) {
                                var carrier = eligibleCarriers[0].ship;
                                if (window.DeploymentDock.autoQueueDockOnCarrier(carrier, flight)) {
                                    if (typeof window.refreshDeploymentUIForDeployStart === 'function') {
                                        window.refreshDeploymentUIForDeployStart();
                                    }
                                    //Issue 2: hand selection over to the carrier
                                    //so the now-invisible flight isn't lingering
                                    //as the selectedShip.
                                    if (typeof window.selectShipInDeploymentPhase === 'function') {
                                        window.selectShipInDeploymentPhase(carrier);
                                    }
                                }
                            } else if (window.confirm
                                && typeof window.confirm.hangarDeployDockCarrierPicker === 'function') {
                                window.confirm.hangarDeployDockCarrierPicker(flight, eligibleCarriers);
                            }
                            this.destroy();
                        }.bind(this)
                    });
                }
            }

            // LCV Rails: an LCV can share the carrier's deploy hex (Issue 2), and
            // deploy-docks onto a free LCV rail. When an LCV is the selected ship
            // and the clicked ship(s) include LCV-capable carriers with a free rail,
            // expose a DOCK button. Exactly ONE free rail total → auto-queue; more
            // than one → the per-rail picker dialog so the player chooses the exact
            // rail (Issue 3).
            var selLcv = this.phaseStrategy.selectedShip;
            if (selLcv && !selLcv.flight
                && String(selLcv.hangarRequired || '').toLowerCase() === 'lcvs'
                && window.DeploymentDock
                && typeof window.DeploymentDock.carrierAcceptsLcvDeployDock === 'function') {
                var lcvCarriers = [];
                var totalFreeRails = 0;
                this.ships.forEach(function (s) {
                    if (window.DeploymentDock.carrierAcceptsLcvDeployDock(s, selLcv)) {
                        var free = window.DeploymentDock.freeLcvDeployRailCount(s, selLcv.id);
                        lcvCarriers.push({ ship: s, free: free });
                        totalFreeRails += free;
                    }
                });
                if (lcvCarriers.length > 0) {
                    actions.push({
                        kind: 'dock',
                        label: 'DOCK ' + selLcv.name.toUpperCase(),
                        note: lcvCarriers.length > 1 ? lcvCarriers.length + ' carriers' : '',
                        onActivate: function () {
                            //One free rail in the whole hex → no choice to make, queue it.
                            //Otherwise let the player pick the exact rail.
                            if (totalFreeRails === 1 && lcvCarriers.length === 1) {
                                var carrier = lcvCarriers[0].ship;
                                if (window.DeploymentDock.queueLcvDeployDock(carrier, selLcv)) {
                                    if (typeof window.refreshDeploymentUIForDeployStart === 'function') {
                                        window.refreshDeploymentUIForDeployStart();
                                    }
                                    if (typeof window.selectShipInDeploymentPhase === 'function') {
                                        window.selectShipInDeploymentPhase(carrier);
                                    }
                                }
                            } else if (window.confirm
                                && typeof window.confirm.lcvDeployDockCarrierPicker === 'function') {
                                window.confirm.lcvDeployDockCarrierPicker(selLcv, lcvCarriers);
                            }
                            this.destroy();
                        }.bind(this)
                    });
                }
            }
        }

        return actions;
    }

    // =========================================================================
    //  Render
    // =========================================================================

    function render() {
        var self = this;

        this.element.empty();

        // Grab bar — sheet only, hidden by CSS on the card.
        var grab = jQuery('<div class="fv-hexpicker__grab"></div>');
        this.element.append(grab);

        // ── Header ──
        var header = jQuery('<div class="fv-hexpicker__header"></div>');
        var unitWord = this.ships.length === 1 ? 'unit' : 'units';
        var title = jQuery('<span class="fv-hexpicker__title"></span>')
            .text(this.ships.length + ' ' + unitWord + ' in hex - Click to Select');          
        //var hex = this.position;
        //if (hex && hex.q !== undefined && hex.r !== undefined) {
            //title.append(jQuery('<span class="fv-hexpicker__coords"></span>')
            //    .text(hex.q + '·' + hex.r));
        //}
        header.append(title);

        var close = jQuery('<button type="button" class="fv-hexpicker__close" aria-label="Close">✕</button>')
            .on('click', function (e) {
                e.stopPropagation();
                self.close();
            });
        header.append(close);
        //The grab bar joins the drag handle in sheet mode — it is the affordance that
        //says "this moves", so it had better be one of the things you can move it by.
        bindHeaderDrag.call(this, this.sheet ? header.add(grab) : header);
        this.element.append(header);

        // ── Actions (Deployment only) ──
        if (this.actions.length) {
            var actionBlock = jQuery('<div class="fv-hexpicker__actions"></div>');
            this.actions.forEach(function (action) {
                var button = jQuery('<div class="fv-hexpicker__action"></div>')
                    .addClass('fv-hexpicker__action--' + action.kind)
                    .attr('role', 'menuitem')
                    .attr('tabindex', '-1')
                    .append(jQuery('<span class="fv-hexpicker__actionlabel"></span>').text(action.label))
                    .on('click', function (e) {
                        e.stopPropagation();
                        action.onActivate();
                    });
                if (action.note) {
                    button.append(jQuery('<span class="fv-hexpicker__actionnote"></span>').text(action.note));
                }
                actionBlock.append(button);
            });
            this.element.append(actionBlock);
        }

        // ── List ──
        this.listElement = jQuery('<div class="fv-hexpicker__list"></div>');
        this.element.append(this.listElement);
        renderList.call(this);

        // ── Hint footer ──
        //this.element.append(jQuery('<div class="fv-hexpicker__hint"></div>')
        //    .text(this.sheet ? 'Tap to select · hold for details'
        //        : 'Click to select · icon for details'));

        // ── Caret ──
        this.caretElement = jQuery('<div class="fv-hexpicker__caret"></div>');
        this.element.append(this.caretElement);
    }

    function renderList() {
        var self = this;
        this.listElement.empty();

        this.model.groups.forEach(function (group) {
            var container = jQuery('<div class="fv-hexpicker__group"></div>');
            var folded = !!self.foldedGroups[group.category];

            if (self.model.showHeaders) {
                if (folded) container.addClass('fv-hexpicker__group--folded');
                container.append(buildGroupHead.call(self, group, folded));
            }

            //A folded group emits no rows at all rather than hiding them with CSS. The
            //list is rebuilt from the model on every fold anyway, so there is nothing to
            //preserve — and it keeps "what can be focused" a question about the DOM
            //rather than about layout, which is what :visible would have made it.
            if (folded) {
                self.listElement.append(container);
                return;
            }

            group.items.forEach(function (item) {
                if (item.type === 'row') {
                    container.append(buildRow.call(self, item.descriptor));
                } else if (self.expandedRuns[item.id]) {
                    item.members.forEach(function (descriptor) {
                        container.append(buildRow.call(self, descriptor));
                    });
                } else {
                    container.append(buildRunRow.call(self, item));
                }
            });

            self.listElement.append(container);
        });
    }

    function buildGroupHead(group, folded) {
        var self = this;
        return jQuery('<button type="button" class="fv-hexpicker__grouphead"></button>')
            .attr('role', 'menuitem')
            .attr('tabindex', '-1')
            .attr('aria-expanded', folded ? 'false' : 'true')
            .append(jQuery('<span class="fv-hexpicker__fold"></span>').text(folded ? '▶' : '▼'))
            .append(jQuery('<span></span>').text(group.label))
            .append(jQuery('<span class="fv-hexpicker__groupcount"></span>').text(group.count))
            .on('click', function (e) {
                e.stopPropagation();
                var before = self.captureBox();
                self.foldedGroups[group.category] = !self.foldedGroups[group.category];
                renderList.call(self);
                self.reflow(before);
            });
    }

    // Shared skeleton for a real row and for a collapsed run's summary row.
    function buildRowShell(descriptor) {
        var row = jQuery('<div class="fv-hexpicker__row"></div>')
            .addClass(descriptor.allyClass)
            .attr('role', 'menuitem')
            .attr('tabindex', '-1');

        if (descriptor.teamVars) {
            row.attr('style', descriptor.teamVars);
        }

        row.append(jQuery('<span class="fv-hexpicker__bar"></span>'));

        //The art sits in a wrapper so the fighter-count badge has something to be
        //positioned against — an <img> cannot hold children. The wrapper is what carries
        //the reserved box; the art fills it.
        var thumb = jQuery('<span class="fv-hexpicker__thumb"></span>');

        if (descriptor.art && window.AssetManager) {
            //Same URL the map texture already fetched, so this is an HTTP cache hit
            //rather than a new download. The box is reserved in CSS: the card is
            //positioned from its MEASURED height right after append, so art that loaded
            //late and grew a row would drift the card off its anchor.
            thumb.append(jQuery('<img class="fv-hexpicker__art" alt="">')
                .attr('src', window.AssetManager.getSmartImagePath(descriptor.art))
                .on('error', function () { jQuery(this).css('visibility', 'hidden'); }));
        } else {
            thumb.append(jQuery('<span class="fv-hexpicker__art fv-hexpicker__art--generic"></span>'));
        }

        //A flight's living fighters, printed over the bottom-right of its silhouette
        //rather than in front of its name — the same badge the hover tooltip's stack grid
        //uses, so the two surfaces say it the same way. It leaves the name line to the
        //name, which is what the player is reading the row for.
        //
        //`> 0` rather than `!== null` keeps the old truthiness gate: a flight with no
        //fighters left should not be on the map at all, and a bare "0" over the art would
        //read as an alarm rather than as information.
        if (descriptor.count > 0) {
            thumb.append(jQuery('<span class="fv-hexpicker__count"></span>').text(descriptor.count));
        }

        row.append(thumb);

        return row;
    }

    //`count` here is a REPETITION count — the collapsed-run row's "13 x Mine" — and it
    //leads the name, sharing its font, because the count is the thing being counted and
    //the two read as one phrase. A flight's FIGHTER count is a different statement about
    //a single unit and is a badge over the art instead (see buildRowShell); real rows
    //therefore pass no count at all.
    function buildText(descriptor, label, count) {
        var text = jQuery('<span class="fv-hexpicker__text"></span>');

        text.append(jQuery('<span class="fv-hexpicker__name"></span>')
            .text(count ? count + ' x ' + label : label));

        var meta = jQuery('<span class="fv-hexpicker__meta"></span>');
        if (descriptor.shipClass) {
            meta.append(jQuery('<span class="fv-hexpicker__class"></span>').text(descriptor.shipClass));
        }
        descriptor.chips.forEach(function (chip) {
            meta.append(jQuery('<span class="fv-hexpicker__chip"></span>').text(chip));
        });
        if (meta.children().length) {
            text.append(meta);
        }

        return text;
    }

    function buildIni(descriptor, sheet) {
        var cell = jQuery('<span class="fv-hexpicker__ini"></span>')
            .text(descriptor.ini === null ? '—' : descriptor.ini);

        // Desktop only, and the touch path stays COMPLETELY INERT: no handler of any
        // kind here, so a press on this ~20px target next to the details button falls
        // straight through to the row and previews / long-presses like everywhere else.
        if (!sheet) {
            cell.attr('title', INI_TITLE);
        }
        return cell;
    }

    function buildRow(descriptor) {
        var self = this;
        var ship = descriptor.ship;

        var row = buildRowShell(descriptor);
        //No count in the name: a flight's fighter count is the badge over its art now.
        row.append(buildText(descriptor, descriptor.label, null));
        row.append(buildIni(descriptor, this.sheet));

        var details = jQuery('<button type="button" class="fv-hexpicker__details"></button>')
            .attr('tabindex', '-1')
            .attr('aria-label', 'Open ship details')
            .on('click', function (e) {
                e.stopPropagation();
                self.phaseStrategy.onShipRightClicked(ship, self.payload);
            });
        //NOTE: no pointerdown guard here. It used to stop propagation so the row's
        //long-press could not arm underneath the button and open the ship window twice on
        //a slow tap — but the long press no longer opens anything, and letting the press
        //through means the row records its pointer type (see isTouchInteraction) and shows
        //its preview even when the press starts on the button.
        row.append(details);

        row.on('click', function () {
            //A long press has already acted; the synthetic click that follows must not
            //also select.
            if (self.longPressFired) {
                self.longPressFired = false;
                return;
            }
            self.activateShip(ship);
        });

        //mouseenter/mouseleave rather than mouseover/mouseout: rows now have children,
        //and the bubbling pair would re-fire on every internal boundary, flickering the
        //map highlight as the cursor crossed from the thumbnail to the name.
        //
        //The hoveredShipId guard is the second half of the flicker fix: showShipTooltip
        //DESTROYS and REBUILDS the tooltip on every call, so any path that re-enters the
        //same row turns into a visible on/off cycle. Re-entering the row we are already
        //on is never work.
        //pointerenter fires BEFORE the compatibility mouse events below and carries the
        //pointerType they lack, so this is what lets mouseenter/mouseleave tell a real
        //mouse hover from a finger's echo. It also keeps lastPointerType fresh on a hybrid
        //device where the player switches between the trackpad and the screen.
        row.on('pointerenter', function (e) {
            var event = e.originalEvent || e;
            if (event.pointerType) self.lastPointerType = event.pointerType;
        });

        row.on('mouseenter', function () {
            //⚠️ After a tap or a long press, the browser replays the gesture as SIMULATED
            //mouse events — mouseover/mouseenter on the row it landed on. Those were
            //re-running this handler after the touch teardown had already finished, which
            //is why the tooltip came straight back up, and why a row the finger had left
            //could still be holding its heading/facing arrows while the next one lit up.
            //On touch, pointerdown is the only preview path.
            if (isTouchInteraction.call(self)) return;

            if (self.hoveredShipId === ship.id) return;
            self.hoveredShipId = ship.id;
            self.phaseStrategy.onMouseOverShip(ship, self.payload);
            self.placeTooltipClear();
        });
        row.on('mouseleave', function () {
            if (isTouchInteraction.call(self)) return;
            if (self.hoveredShipId === ship.id) self.hoveredShipId = null;
            self.phaseStrategy.onMouseOutShips(ship, self.payload);
        });

        row.on('contextmenu', function (e) {
            //Always swallow it, so the browser's own context menu never appears over the card.
            e.preventDefault();
            e.stopPropagation();

            //⚠️ A long press on a touchscreen fires `contextmenu` all by itself. That — not
            //the long-press timer, which stopped opening the ship window — is why the ship
            //window kept appearing on a held press. `contextmenu` is a MOUSE gesture here;
            //on touch the details button is the only route to the ship window.
            if (isTouchInteraction.call(self)) return;

            self.phaseStrategy.onShipRightClicked(ship, self.payload);
        });

        bindTouch.call(this, row, ship);

        return row;
    }

    function buildRunRow(item) {
        var self = this;
        var descriptor = item.members[0];

        var row = buildRowShell(descriptor).addClass('fv-hexpicker__run');
        //The run length DOES lead the name — "13 x Mine" is how many units collapsed
        //here, a different statement from the badge the shell may have put over the art
        //(how many fighters are in EACH of them). Both can be true at once: three
        //identical 6-fighter flights read "3 x Nial Flight" with a "6" on the silhouette.
        row.append(buildText(descriptor, descriptor.label, item.members.length));
        row.append(buildIni(descriptor, this.sheet));
        row.append(jQuery('<span class="fv-hexpicker__expand"></span>').text('Show ' + item.members.length));

        row.on('click', function (e) {
            e.stopPropagation();
            var before = self.captureBox();
            self.expandedRuns[item.id] = true;
            renderList.call(self);
            self.reflow(before);
        });

        return row;
    }

    HexPicker.prototype.activateShip = function (ship) {
        if (gamedata.gamephase === -1) {
            if (this.phaseStrategy.selectedShip) {
                this.phaseStrategy.deselectShip(this.phaseStrategy.selectedShip);
            }
            this.phaseStrategy.selectShip(ship, this.payload);
            this.destroy();
        } else {
            this.phaseStrategy.onShipClicked(ship, this.payload);
            //Only close when the click actually CHANGED the selection. A targeting click
            //leaves the picker open so the player can keep working the stack.
            if (this.phaseStrategy.selectedShip && this.phaseStrategy.selectedShip.id === ship.id) {
                this.destroy();
            }
        }

        //Selecting or targeting REBUILDS the tooltip — showShipTooltip destroys and
        //recreates it — and the replacement is a different size, since it carries a
        //targeting block and a button row. It lands in the tooltip's own usual place
        //below the hex, which is now free, so there is nothing to correct in the normal
        //case; this only re-derives the avoidance nudge for the one where the bigger box
        //reaches under the card. A no-op once the card has been destroyed.
        this.placeTooltipClear();
    };

    // =========================================================================
    //  Hover tooltip placement
    // =========================================================================

    // Keep the hover ShipTooltip clear of the card — but ONLY when it is actually in the
    // way. Doing nothing is the whole point of this function.
    //
    // It used to move the tooltip unconditionally, because the card was anchored above or
    // below its hex and the tooltip anchors below it, so the two always collided. The
    // trouble is that the tooltip re-anchors itself on its hex on every zoom and scroll
    // step (PhaseStrategy.repositionTooltip), so a placement written here survived
    // exactly one frame and then snapped back — the tooltip appeared to leap across the
    // screen the moment the player touched the zoom. Now that the card sits BESIDE the
    // hex, the tooltip's usual place below the hex is normally free, and the fix for the
    // jump is to stop moving it: the position is then identical before, during and after
    // the gesture because nothing is overriding it.
    //
    // The overlap case remains real at low zoom, where a wide tooltip can still reach
    // under a card only 20px clear of the hex. Then the smallest correction that works:
    // slide the tooltip horizontally to the card's far side, keeping its vertical
    // relationship to the hex. reposition() re-derives this on every step, so it is
    // stable through a gesture rather than being undone by it.
    //
    // Sheet mode has no side to sit beside — it owns the whole bottom edge — so a
    // collision there lifts the tooltip above the sheet instead.
    HexPicker.prototype.placeTooltipClear = function () {
        if (this.destroyed) return;

        var tooltip = this.phaseStrategy && this.phaseStrategy.shipTooltip;
        if (!tooltip || !tooltip.element || !tooltip.element.length) return;

        var node = tooltip.element[0];
        var width = node.offsetWidth;
        var height = node.offsetHeight;
        if (!width || !height) return;

        var tip = node.getBoundingClientRect();
        var card = this.element[0].getBoundingClientRect();
        if (!card.width || !card.height) return;

        //Not in the way: leave it EXACTLY where ShipTooltip put it. This is the common
        //case now, and it is the one the player notices — an untouched tooltip cannot
        //jump, because there is nothing for the next reposition to undo.
        if (tip.right <= card.left || tip.left >= card.right
            || tip.bottom <= card.top || tip.top >= card.bottom) {
            return;
        }

        var left = tip.left;
        var top = tip.top;

        if (this.sheet) {
            top = card.top - TOOLTIP_GAP - height;
        } else {
            left = (this.placement === 'left')
                ? card.right + TOOLTIP_GAP
                : card.left - TOOLTIP_GAP - width;

            //Pushed off the screen instead of clear of the card — try the other way
            //before falling back on the clamp, which would leave it overlapping.
            if (left < EDGE_MARGIN || left + width > window.innerWidth - EDGE_MARGIN) {
                var alternative = (this.placement === 'left')
                    ? card.left - TOOLTIP_GAP - width
                    : card.right + TOOLTIP_GAP;
                if (alternative >= EDGE_MARGIN
                    && alternative + width <= window.innerWidth - EDGE_MARGIN) {
                    left = alternative;
                }
            }
        }

        tooltip.element.css({
            left: clamp(left, EDGE_MARGIN, window.innerWidth - width - EDGE_MARGIN) + 'px',
            top: clamp(top, EDGE_MARGIN, window.innerHeight - height - EDGE_MARGIN) + 'px'
        });
    };

    // =========================================================================
    //  Header drag
    // =========================================================================

    // Drag the card by its header. Position is per-instance and deliberately NOT
    // remembered: every opening re-anchors on the hex it belongs to, so the picker can
    // never appear somewhere unrelated to the click that summoned it.
    //
    // Once dragged the card stops auto-repositioning — otherwise the next zoom or scroll
    // would snap it straight back to the hex and undo the drag — and its caret is hidden,
    // because it is no longer pointing at anything.
    // `handle` is the header plus, in sheet mode, the grab bar above it.
    //
    // Works in BOTH shapes. The sheet used to be excluded because it is pinned by
    // left/right/bottom and a drag would fight the safe-area inset — the fix for that is
    // to UNPIN it on the first real movement (below) rather than to refuse the gesture,
    // since a bottom sheet you cannot move is exactly as obstructive as a card you cannot.
    function bindHeaderDrag(handle) {
        var self = this;
        var origin = null;

        handle.on('pointerdown', function (e) {
            if (jQuery(e.target).closest('.fv-hexpicker__close').length) return;
            var event = e.originalEvent || e;
            var rect = self.element[0].getBoundingClientRect();
            origin = {
                x: event.clientX, y: event.clientY,
                left: rect.left, top: rect.top, moved: false
            };
            //Capture on the element that received the press, so a fast drag that outruns
            //the handle keeps feeding us moves.
            try { this.setPointerCapture(event.pointerId); } catch (err) { /* optional */ }
            e.preventDefault();
        });

        handle.on('pointermove', function (e) {
            if (!origin) return;
            var event = e.originalEvent || e;
            var dx = event.clientX - origin.x;
            var dy = event.clientY - origin.y;

            if (!origin.moved) {
                if (Math.abs(dx) < 3 && Math.abs(dy) < 3) return;   //ignore click jitter
                origin.moved = true;
                self.dragged = true;
                self.element.addClass('fv-hexpicker--dragged');
                if (self.caretElement) self.caretElement.hide();

                //Unpin a sheet the moment it actually moves: freeze the width it had been
                //given by left/right + max-width, drop the edge anchors, and let it run on
                //left/top like a card from here on.
                if (self.sheet) {
                    self.element.css({
                        width: self.element[0].offsetWidth + 'px',
                        right: 'auto',
                        bottom: 'auto',
                        marginLeft: 0,
                        marginRight: 0,
                        left: origin.left + 'px',
                        top: origin.top + 'px'
                    });
                }
            }

            var width = self.element[0].offsetWidth;
            var height = self.element[0].offsetHeight;
            self.element.css({
                left: clamp(origin.left + dx, EDGE_MARGIN, window.innerWidth - width - EDGE_MARGIN) + 'px',
                top: clamp(origin.top + dy, EDGE_MARGIN, window.innerHeight - height - EDGE_MARGIN) + 'px'
            });
        });

        handle.on('pointerup pointercancel', function (e) {
            var event = e.originalEvent || e;
            try { this.releasePointerCapture(event.pointerId); } catch (err) { /* optional */ }
            origin = null;
        });
    }

    // =========================================================================
    //  Touch parity — "press to preview, lift to select"
    // =========================================================================

    function bindTouch(row, ship) {
        var self = this;

        row.on('pointerdown', function (e) {
            var event = e.originalEvent || e;
            //Recorded BEFORE the mouse early-return: the contextmenu handler needs to know
            //the type of the last press whichever kind it was.
            self.lastPointerType = event.pointerType || '';
            if (event.pointerType === 'mouse') return;

            //The hover preview, recovered on touch for zero extra taps: the highlight is
            //bound to pointerdown and the action stays on click.
            self.hoveredShipId = ship.id;
            self.phaseStrategy.onMouseOverShip(ship, self.payload);
            self.placeTooltipClear();

            //Clear any previous press FIRST: cancelLongPress() drops the origin, so
            //recording this press's origin before it would leave nothing for the
            //movement test to compare against — and a list scroll could then never
            //cancel the press it started with.
            self.cancelLongPress();
            self.longPressFired = false;
            self.longPressOrigin = { x: event.clientX, y: event.clientY };
            self.longPressTimer = setTimeout(function () {
                self.longPressTimer = null;
                //Long press PINS the preview: the tooltip stays up after the finger
                //lifts, and the click that would otherwise follow is suppressed, so
                //inspecting a unit never commits a selection.
                //
                //It deliberately does NOT open the ship window. On a touchscreen that
                //is the details button's job — an explicit, visible target — because a
                //long press that jumped straight to a full window made a slow tap on a
                //crowded list an easy way to land somewhere you did not ask for.
                self.longPressFired = true;
                if (navigator.vibrate) navigator.vibrate(30);
            }, LONG_PRESS_MS);
        });

        row.on('pointermove', function (e) {
            var event = e.originalEvent || e;
            if (!self.longPressTimer || !self.longPressOrigin) return;
            if (Math.abs(event.clientX - self.longPressOrigin.x) > TOUCH_SLOP
                || Math.abs(event.clientY - self.longPressOrigin.y) > TOUCH_SLOP) {
                //Scrolling the list, not pressing a row.
                self.cancelLongPress();
                self.endTouchPreview(ship);
            }
        });

        row.on('pointerup pointercancel pointerleave', function (e) {
            var event = e.originalEvent || e;
            if (event.pointerType === 'mouse') return;
            self.cancelLongPress();
            //The preview clears on lift whether or not the press became a long one: the
            //gesture is "hold to look", so letting go ends the look. longPressFired stays
            //set — it is what suppresses the click that follows, so inspecting a unit
            //still never commits a selection.
            self.endTouchPreview(ship);
        });
    }

    // End a touch preview: drop the highlight AND take the tooltip down.
    //
    // onMouseOutShips resets highlights and EW but deliberately does NOT hide the
    // tooltip — on the map that is the canvas mouse-out path's job, and it fires when the
    // cursor leaves the board. A finger lifting off a DOM row produces no canvas event at
    // all, so nothing was tearing the tooltip down and it sat there until the next map tap.
    //
    // Touch path ONLY. On desktop the equivalent hide would also catch the PERSISTENT
    // targeting tooltip that a click puts up (showShipTooltip with hide=false), and kill
    // it the moment the cursor left the row. Touch is safe because pointerup runs BEFORE
    // the click that would create such a tooltip.
    HexPicker.prototype.endTouchPreview = function (ship) {
        //Ignore a STALE end. Dragging a finger from one row to the next can deliver row A's
        //pointerleave after row B's pointerdown has already started B's preview, and tearing
        //down then would cancel the preview that just began — or, with the highlight reset
        //landing between B's two calls, leave both rows lit at once.
        if (ship && this.hoveredShipId !== null && this.hoveredShipId !== ship.id) return;

        this.hoveredShipId = null;
        this.phaseStrategy.onMouseOutShips(ship, this.payload);

        if (this.phaseStrategy.shipTooltip
            && typeof this.phaseStrategy.hideShipTooltip === 'function') {
            this.phaseStrategy.hideShipTooltip(this.phaseStrategy.shipTooltip);
        }
    };

    HexPicker.prototype.cancelLongPress = function () {
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
        this.longPressOrigin = null;
    };

    // =========================================================================
    //  Keyboard
    // =========================================================================

    //Everything arrow-navigable, in DOM order. The per-row details buttons are
    //deliberately NOT here — stepping through them would double the length of the list
    //for no gain — but they stay Tab-reachable via focusables() below. No :visible
    //filter is needed or wanted: a folded group emits no rows in the first place.
    HexPicker.prototype.navigables = function () {
        return this.element
            .find('.fv-hexpicker__action, .fv-hexpicker__grouphead, .fv-hexpicker__row')
            .toArray();
    };

    HexPicker.prototype.focusables = function () {
        return this.element
            .find('.fv-hexpicker__action, .fv-hexpicker__grouphead, .fv-hexpicker__row, .fv-hexpicker__details, .fv-hexpicker__close')
            .toArray();
    };

    function moveFocus(list, delta) {
        if (!list.length) return;
        var index = list.indexOf(document.activeElement);
        if (index === -1) {
            index = delta > 0 ? -1 : 0;
        }
        var next = list[(index + delta + list.length) % list.length];
        if (next) {
            try { next.focus({ preventScroll: false }); } catch (e) { next.focus(); }
        }
    }

    function onKeyDown(event) {
        if (this.destroyed) return;

        var key = event.key;

        if (key === 'Escape' || key === 'Esc') {
            event.stopPropagation();
            event.preventDefault();
            this.close();
            return;
        }

        //Only claim keys while focus is actually inside the card, so a player who has
        //clicked back onto the map keeps every normal shortcut.
        if (!jQuery.contains(this.element[0], document.activeElement)
            && document.activeElement !== this.element[0]) {
            return;
        }

        if (key === 'ArrowDown' || key === 'ArrowUp') {
            event.stopPropagation();
            event.preventDefault();
            moveFocus(this.navigables(), key === 'ArrowDown' ? 1 : -1);
            return;
        }

        if (key === 'Home' || key === 'End') {
            var navigables = this.navigables();
            if (!navigables.length) return;
            event.stopPropagation();
            event.preventDefault();
            var target = key === 'Home' ? navigables[0] : navigables[navigables.length - 1];
            try { target.focus({ preventScroll: false }); } catch (e) { target.focus(); }
            return;
        }

        if (key === 'Tab') {
            //Focus is trapped while the card is open, and restored by destroy().
            event.stopPropagation();
            event.preventDefault();
            moveFocus(this.focusables(), event.shiftKey ? -1 : 1);
            return;
        }

        if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
            if (document.activeElement === this.element[0]) return;
            //Space is bound to TogglePlayPause on window; stopPropagation here is what
            //keeps activating a row from also toggling replay playback.
            event.stopPropagation();
            event.preventDefault();
            jQuery(document.activeElement).trigger('click');
        }
    }

    // =========================================================================
    //  Colour
    // =========================================================================

    function getAllyClass(ship) {
        return gamedata.isTerrain(ship.shipSizeClass, ship.userid) ? 'terrain' : (gamedata.isMyShip(ship) ? 'mine' : (gamedata.isMyorMyTeamShip(ship) ? 'ally' : 'enemy'))
    }

    // Inline allegiance colour, mirroring the unified team-colour rule (see
    // gamedata.getFleetHeaderColorRGB / ShipTooltip.getNameStyle):
    //   - Terrain: neutral — keep the CSS 'terrain' class, no override.
    //   - 2-team participant: relative mine/ally/enemy — keep the CSS class from
    //     getAllyClass, so your fleet reads green.
    //   - Observer OR 3+-team participant: absolute per-team palette inline, so
    //     each team is identifiable rather than collapsing to ally/enemy.
    //
    // The gate is unchanged; only the delivery and the tone-map are new. Instead of
    // writing `color:` on the name, this writes the row's TWO channels: the raw team
    // colour on the 3px bar, and a tone-mapped twin on the name, so an arbitrary team
    // colour lands in the same brightness band as the four fixed allegiance tints.
    // Inline custom properties beat the class, and both follow.
    function getTeamColorVars(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return '';
        if (gamedata.isPlayerInGame() && gamedata.getDistinctTeamCount() === 2) return '';

        var raw = gamedata.getTeamColorRGB(ship.team);
        var toned = (typeof gamedata.getMidTeamColorRGB === 'function')
            ? gamedata.getMidTeamColorRGB(ship.team)
            : raw;

        return '--row-bar:rgb(' + Math.round(raw[0]) + ',' + Math.round(raw[1]) + ',' + Math.round(raw[2]) + ');'
            + '--row-name:rgb(' + Math.round(toned[0]) + ',' + Math.round(toned[1]) + ',' + Math.round(toned[2]) + ');';
    }

    return HexPicker;
}();
