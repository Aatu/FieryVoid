'use strict';

window.ShipTooltipBallisticsMenu = function () {

    // Row markup. The only addition to the original two spans is `.ballexpand`, the disclosure
    // toggle that opens a grouped row into one sub-row per shot.
    //
    // There is deliberately NO intercept button: interception is declared by clicking the hit
    // chance itself (MANUAL_INTERCEPTION_PLAN.md §4.1, revised 2026-08-19 after testing). A button
    // on every row squashed the layout, and the hit chance is the thing the player is already
    // reading - it drops as interception is committed, and its hover tooltip carries the
    // breakdown. Withdrawing is done from the ship window, like any other fire order.
    // A shooter's heading. Its shots are listed beneath it, so the ship name is written ONCE per
    // ship instead of repeated on every row - which is what let the rows fit on one line.
    var shipTemplate = '<div class="ballship"><span class="shipname"></span></div>';

    // One row per weapon-group under that heading: caret, "Nx Weapon (Mode)", hit chance.
    var template = '<div class="ballrow">'
        + '<span class="ballexpand"></span>'
        + '<span class="weapon"></span>'
        + '<span class="hitchange"></span>'
        + '</div>';

    function ShipTooltipBallisticsMenu(shipIconContainer, turn, allowIntercept, selectedShip) {
        this.shipIconContainer = shipIconContainer;
        this.turn = turn;
        // Honoured again (it was hard-wired to false while manual interception was dormant). The
        // phase gate in canOfferIntercept is what actually shuts it off in Pre-Firing, so the two
        // strategies can keep passing `true` from every call site and a future phase renumber
        // cannot silently re-enable it.
        this.allowIntercept = allowIntercept === true;
        this.selectedShip = selectedShip;
        // Disclosure state, keyed by group key. Lives on the menu instance, which outlives the
        // in-place re-render after a click but not a re-selection of the ship - the phase strategy
        // builds a new menu per selectShip. That is deliberate and cheaper than a global.
        this.expandedGroups = {};
    }

    function getBallisticEntry(ball) {
        return {
            weaponid: ball.weapon.id,
            targetid: ball.fireOrder.targetid,
            shooterid: ball.shooter.id,
            fireOrderId: ball.fireOrder.id,
            position: this.shipIconContainer.getByShip(ball.shooter).getFirstMovementOnTurn(this.turn).position
        };
    }

    // Groups collapse identical shots into one row. They now KEEP their members: the group key
    // already pins shooter, weapon, firing mode and base hit chance, so members are interchangeable
    // except for their fire order ids - and those ids are exactly what a manual intercept order has
    // to name. Without them one click on "3x Missile" could not say which missile it meant.
    function groupByOriginAndHitChange(ballistics) {
        let listObject = {};

        ballistics.forEach(ballistic => {
            //const key = ballistic.shooter.id + '-' +  ballistic.weapon.displayName + '-' + weaponManager.calculataBallisticHitChange(getBallisticEntry.call(this, ballistic));
			//let's differentiate by mode as well!
			const key = ballistic.shooter.id + '-' +  ballistic.weapon.displayName + '-' +  ballistic.fireOrder.firingMode +'-' + weaponManager.calculataBallisticHitChange(getBallisticEntry.call(this, ballistic));

            if (listObject[key]) {
                listObject[key].members.push(ballistic);
            } else {
                listObject[key] = {
                    ballistic,
                    members: [ballistic]
                }
            }
        }, this)

        Object.keys(listObject).forEach(function (key) {
            listObject[key].members.sort(function (a, b) {
                return String(a.fireOrder.id).localeCompare(String(b.fireOrder.id), undefined, { numeric: true });
            });
            listObject[key].amount = listObject[key].members.length;
        });

        return listObject;
    }

    // May this menu offer interception at all? Independent of the call site: PreFiring passes
    // allowIntercept true like everything else, and this is what refuses there.
    ShipTooltipBallisticsMenu.prototype.canOfferIntercept = function () {
        if (!this.allowIntercept) return false;
        if (gamedata.gamephase !== 3) return false; //R1 - declared in the Firing phase only
        if (!this.selectedShip) return false;
        if (!gamedata.isMyShip(this.selectedShip)) return false;
        if (shipManager.isDestroyed(this.selectedShip)) return false;
        return true;
    };

    // Rebuild the INCOMING list in place after a click. The menu owns the element, so it clears
    // .incoming and re-runs itself rather than going through PhaseStrategy.onSystemDataChanged -
    // that guard is deliberately narrow so unrelated callers cannot rebuild a tooltip out from
    // under a click.
    ShipTooltipBallisticsMenu.prototype.refresh = function () {
        if (!this.renderElement || !this.renderShip) return;
        // The floating hit-chance tooltip is anchored to a span this is about to destroy, and its
        // text is stale the moment interception is declared. Nothing will fire mouseleave on a
        // removed element, so hide it here or it hangs over the map showing the old number.
        jQuery('#custom-hit-chance-tooltip').hide();
        jQuery(".incoming", this.renderElement).html("");
        this.renderTo(this.renderShip, this.renderElement);
    };

    ShipTooltipBallisticsMenu.prototype.renderTo = function (ship, element) {
        this.renderShip = ship;
        this.renderElement = element;

        var ballistics = weaponManager.getAllBallisticsAgainst(ship, this.hexagon);

        if (ballistics.length > 0) {
            $(".ballistics", element).show();
        } else {
            $(".ballistics", element).hide();
            return;
        }

        const grouped = groupByOriginAndHitChange.call(this, ballistics)

        // Delegate hit-chance tooltip events on .incoming. We bind locally rather
        // than on document because the parent ShipTooltip element calls
        // stopPropagation() on mouseover/out and would otherwise eat the event.
        // Safe to re-run on every refresh: it .off('.hitchance')s first.
        weaponManager.attachHitChanceTooltipDelegation(jQuery(".incoming", element));

        // Collect the weapon-groups under their shooter, preserving the order they came out of
        // getAllBallisticsAgainst so the list is stable between renders.
        var byShooter = [];
        Object.keys(grouped).forEach(function (key) {
            var shooter = grouped[key].ballistic.shooter;
            var entry = null;
            for (var i = 0; i < byShooter.length; i++) {
                if (byShooter[i].shooter.id === shooter.id) { entry = byShooter[i]; break; }
            }
            if (!entry) {
                entry = { shooter: shooter, keys: [] };
                byShooter.push(entry);
            }
            entry.keys.push(key);
        });

        byShooter.forEach(function (shooterEntry) {
            var shipElement = jQuery(shipTemplate);
            jQuery(".shipname", shipElement)
                .html(shooterEntry.shooter.name)
                .attr('title', shooterEntry.shooter.name); //the full name, when the column ellipsises it
            jQuery(".incoming", element).append(shipElement);

            shooterEntry.keys.forEach(function (key) {
                var ball = grouped[key].ballistic;
                const amount = grouped[key].amount;
                const members = grouped[key].members;
                var ballElement = jQuery(template);

                var ballisticEntry = getBallisticEntry.call(this, ball);

                // The launch hex, which every interception predicate needs (arc, freeintercept
                // geometry). getBallisticEntry already resolved it from the shooter's icon.
                members.forEach(function (member) { member.position = ballisticEntry.position; });

                // Set correct firing mode
                var modeIteration = ball.fireOrder.firingMode;
                if (modeIteration != ball.weapon.firingMode && !ball.weapon.multiModeSplit) {
                    while (modeIteration != ball.weapon.firingMode) {
                        ball.weapon.changeFiringMode();
                    }
                }

                // Set display text. The shooter is named by the heading above, so the row carries only
                // the shot: how many, of what, in which mode, and - for a Shadow split weapon - what
                // the shot is made of. The count is always written - including "1x" - so the weapon
                // names line up down the column.
                var textToDisplay = amount + 'x ' + ball.weapon.displayName
                    + ' (' + ball.weapon.firingModes[ball.fireOrder.firingMode] + ')'
                    + diceSuffix(ball.weapon, members);
                jQuery(".weapon", ballElement).html(textToDisplay).attr('title', textToDisplay);

    			var hitchance = weaponManager.calculataBallisticHitChange(ballisticEntry);
                var hitchanceNormalMode = ball.fireOrder.chance ?? ball.fireOrder.needed;

                // Live re-derived breakdown for the hover tooltip (geometry is locked
                // at start of turn via getFiringHex, so the breakdown remains representative
                // of how the chance was derived at launch).
                var ballTarget = gamedata.getShip(ball.fireOrder.targetid);
                var hitChanceResult = weaponManager.calculateHitChange(ball.shooter, ballTarget, ball.weapon, undefined);

                // Build hitchance list manually, based on number of ballistics.
                /*let hitchanceList = [];
                for (let i = 0; i < ballistics.length; i++) {
                    if(ball.weapon.id == ballistics[i].fireOrder.weaponid && ball.shooter.id == ballistics[i].fireOrder.shooterid){
                        let hc = ballistics[i].fireOrder.chance ?? ballistics[i].fireOrder.needed;
                        hitchanceList.push(hc);
                    }
                }
                */
                let hitchanceLists = {};   // { firingMode: [hc, hc, ...] }
                for (let i = 0; i < ballistics.length; i++) {
                    const b = ballistics[i];

                    if (ball.weapon.id === b.fireOrder.weaponid &&
                        ball.shooter.id === b.fireOrder.shooterid) {

                        const mode = b.fireOrder.firingMode;
                        const hc = b.fireOrder.chance ?? b.fireOrder.needed;

                        // Create the array if it doesn't exist yet
                        if (!hitchanceLists[mode]) {
                            hitchanceLists[mode] = [];
                        }
                        // Push the hit chance into the array for this firing mode
                        hitchanceLists[mode].push(hc);
                    }
                }

                // Get the list for this ballistic’s firing mode
                const mode = ball.fireOrder.firingMode;
                const list = hitchanceLists[mode] ?? [];   // fallback empty array

                // Compute min/max safely
                const minHitchance = list.length > 0 ? Math.min(...list) : null;
                const maxHitchance = list.length > 0 ? Math.max(...list) : null;
                //const minHitchance = Math.min(...hitchanceList);
                //const maxHitchance = Math.max(...hitchanceList);

                // Direct-fire split weapons (Molecular / Shadow Slicer) add a cumulative -5% per
                // shot after the first. The live breakdown bakes in the penalty for the NEXT
                // (uncommitted) shot, over-stating what the locked shots actually took. Re-anchor
                // it to the locked shots: the header uses the best (first) shot's stored chance
                // and the split penalty is spelled out as the range incurred - 0% for the first
                // shot down to the worst locked shot (minHitchance). Geometry/EW are locked this
                // turn, so only the split penalty differs between shots; everything else is constant.
                if (ball.fireOrder.type === 'normal' && ball.weapon.specialHitChanceCalculation
                        && hitChanceResult && hitChanceResult.modifiers) {
                    // The split penalty is bundled into 'Other'; peel the live (next-shot)
                    // contribution out so 'Other' keeps only the constant, non-split components.
                    var liveSplitD20 = 0;
                    if (hitChanceResult._otherDetail) {
                        for (var d = 0; d < hitChanceResult._otherDetail.length; d++) {
                            if (hitChanceResult._otherDetail[d].label === 'Weapon Special') {
                                liveSplitD20 = hitChanceResult._otherDetail[d].value;
                                break;
                            }
                        }
                    }
                    var otherMod = null;
                    for (var mI = 0; mI < hitChanceResult.modifiers.length; mI++) {
                        if (hitChanceResult.modifiers[mI].key === 'other') { otherMod = hitChanceResult.modifiers[mI]; break; }
                    }
                    var nonSplitD20 = (otherMod ? otherMod.value : 0) - liveSplitD20;

                    // worstSplit is <= 0 (best shot took none); single-shot rows collapse it to 0.
                    var worstSplitD20 = (minHitchance != null) ? (minHitchance - hitchanceNormalMode) / 5 : 0;
                    var otherHigh = nonSplitD20;                // best/first locked shot: no split penalty
                    var otherLow = nonSplitD20 + worstSplitD20; // worst locked shot

                    hitChanceResult.hitChance = hitchanceNormalMode; // header matches the row's headline (best shot)

                    if (otherHigh === 0 && otherLow === 0) {
                        // nothing but the removed next-shot penalty was in 'Other' - drop the line
                        if (otherMod) hitChanceResult.modifiers.splice(hitChanceResult.modifiers.indexOf(otherMod), 1);
                    } else {
                        if (!otherMod) {
                            otherMod = { key: 'other', label: 'Other' };
                            hitChanceResult.modifiers.push(otherMod);
                        }
                        otherMod.value = otherHigh;
                        otherMod.valueHigh = otherHigh;
                        otherMod.valueLow = otherLow;
                    }
                }

                // ── Declared interception (MANUAL_INTERCEPTION_PLAN.md §4.6) ───────────────────
                // Per member, because members of a group differ ONLY by how much interception has been
                // committed to each. Applied AFTER the split-penalty re-anchoring above, which has to
                // work on the pre-interception numbers.
                //
                // The base comes from getIncomingShotHitChance, NOT `chance ?? needed`: a ballistic in
                // flight has no stored hit chance at all (tac_fireorder has no `chance` column and its
                // `needed` stays 0 until the shot resolves), so the raw expression reads 0% for every
                // missile on the board.
                var interceptD20 = members.map(function (member) {
                    return weaponManager.getDeclaredInterception(member.fireOrder.id, member.weapon);
                });
                var memberBase = members.map(function (member) {
                    return weaponManager.getIncomingShotHitChance(member);
                });
                var committedD20 = interceptD20.reduce(function (sum, v) { return sum + v; }, 0);
                // NOT floored at 0 (user direction, 2026-08-20) - see getRemainingHitChance. An
                // over-intercepted shot reads "-25%", which is the only way the column can say
                // "you have already spent more than this shot was worth".
                var remaining = members.map(function (member, i) {
                    return memberBase[i] - interceptD20[i] * 5;
                });

                var displayMin = minHitchance;
                var displayMax = hitchanceNormalMode;

                if (committedD20 > 0) {
                    // Fold it into the headline numbers, so a partly-suppressed group reads as the
                    // range its shots now span and a fully-suppressed one reads as a single 0%.
                    displayMin = Math.min(...remaining);
                    displayMax = Math.max(...remaining);

                    if (hitChanceResult && hitChanceResult.modifiers) {
                        // Rendered as a range when members differ, using the same valueHigh/valueLow
                        // support the split penalty above uses. Values are d20 points; the tooltip
                        // builder multiplies by 5.
                        var least = -Math.min(...interceptD20);
                        var most = -Math.max(...interceptD20);
                        hitChanceResult.modifiers.push({
                            key: 'intercept',
                            label: 'Declared interception',
                            value: least,
                            valueHigh: least,
                            valueLow: most
                        });
                        hitChanceResult.hitChance = displayMax;
                    }
                }

                // Can the current selection commit to this row? undefined = interception is not on
                // offer here at all (wrong phase, not my ship); null = yes; a string = why not. The
                // answer is the same for every member of a group - they share shooter, weapon, mode and
                // geometry - so it is asked once and reused by the sub-rows.
                var offer = this.canOfferIntercept()
                    ? weaponManager.getInterceptDisabledReason(this.selectedShip, members[0])
                    : undefined;

                if (offer !== undefined) {
                    // Carried in the tooltip's free-text footer rather than as visible row chrome:
                    // a refusal reason has to be READABLE without adding a permanent widget to a list
                    // that is mostly read at a glance.
                    var note = (offer === null)
                        ? 'Click this hit chance to intercept with the selected weapon(s).'
                        : 'Cannot intercept: ' + offer;
                    hitChanceResult.note = (hitChanceResult.note ? hitChanceResult.note + '\n' : '') + note;
                }

                var tooltipText = weaponManager.buildHitChanceTooltipText(hitChanceResult);

                // "Approx:" and "Between:" are gone - every number in this column is a hit chance, and
                // a range says "between" by being a range. The words cost a third of the row's width.
                // The Shadow "(N dice)" suffix has gone too: the allocation now rides on the weapon
                // label as "(3d + 12)", where it reads as part of the shot rather than as part of the
                // percentage. With it went the branch that carried it - a Slicer order is
                // type "normal" (Sweeping), so it lands on the branch below and is now formatted like
                // every other split weapon. That branch does NOT print a range for a single-shot row,
                // which is what stopped one Slicer shot reading "93-93%".
                var chanceText;
                if (ball.fireOrder.type == "normal") {
                    chanceText = (amount > 1 && displayMin !== displayMax)
                        ? joinRange(displayMin, displayMax)
                        : displayMax + '%';
                } else if (committedD20 > 0) {
                    chanceText = (amount > 1 && displayMin !== displayMax)
                        ? joinRange(displayMin, displayMax)
                        : displayMax + '%';
                } else {
                    chanceText = hitchance + '%';
                }
                setChanceText(jQuery(".hitchange", ballElement), chanceText);

                // Attach hover-tooltip with the per-modifier breakdown.
                if (tooltipText) {
                    jQuery(".hitchange", ballElement)
                        .addClass('hit-chance-tooltip')
                        .attr('data-tooltip', tooltipText);
                }

                if (committedD20 > 0) jQuery(".hitchange", ballElement).addClass('intercepted');

                // The hit chance IS the intercept control. A collapsed group greedy-fills across its
                // members; a single-shot row commits to that one shot.
                if (offer !== undefined) {
                    makeInterceptable.call(this, jQuery(".hitchange", ballElement), offer === null, members[0], function () {
                        if (members.length === 1) {
                            weaponManager.targetBallistic(this.selectedShip, members[0]);
                        } else {
                            weaponManager.allocateIntercept(this.selectedShip, members);
                        }
                    });
                }

                renderDisclosure.call(this, ballElement, key, amount);

                jQuery(".incoming", element).append(ballElement);

                // Expanded: one sub-row per shot, for exact control over which missile of a group a
                // weapon is put on. Sub-rows sit in the same .incoming container, so the hit-chance
                // tooltip delegation attached above covers them too. Each names the weapon rather
                // than "Shot 1 / Shot 2" - which shot it is only matters inside its own tooltip.
                if (amount > 1 && this.expandedGroups[key]) {
                    members.forEach(function (member, index) {
                        var subElement = jQuery(template);
                        subElement.addClass('ballsub');

                        var subText = ball.weapon.displayName
                            + ' (' + ball.weapon.firingModes[ball.fireOrder.firingMode] + ')'
                            + diceSuffix(ball.weapon, [member]);
                        jQuery(".weapon", subElement).html(subText).attr('title', subText);

                        var subHit = jQuery(".hitchange", subElement);
                        setChanceText(subHit, remaining[index] + '%');

                        var subTip = 'Shot ' + (index + 1) + ' of ' + amount
                            + '\nHit chance: ' + memberBase[index] + '%';
                        if (interceptD20[index] > 0) {
                            subTip += '\n• Declared interception: -' + (interceptD20[index] * 5) + '%';
                        }
                        if (offer !== undefined) {
                            subTip += '\n' + ((offer === null)
                                ? 'Click this hit chance to intercept THIS shot with the selected weapon(s).'
                                : 'Cannot intercept: ' + offer);
                        }
                        subHit.addClass('hit-chance-tooltip').attr('data-tooltip', subTip);

                        if (interceptD20[index] > 0) subHit.addClass('intercepted');

                        // A sub-row is an explicit choice, so it commits everything eligible to THAT
                        // shot - no greedy fill.
                        if (offer !== undefined) {
                            makeInterceptable.call(this, subHit, offer === null, member, function () {
                                weaponManager.targetBallistic(this.selectedShip, member);
                            });
                        }

                        jQuery(".incoming", element).append(subElement);
                    }, this);
                }
            }, this);
        }, this);
    };

    /* Write the number into a row's hit-chance cell.

       The text goes in its OWN inline span rather than straight into .hitchange, and the dotted
       underline is drawn on THAT (shipTooltip.css). The underline is a `border-bottom` inherited
       from `.hit-chance-tooltip` in tactical.css, and .hitchange is a blockified, fixed-width,
       right-aligned flex item - so on .hitchange that border ran the whole width of the column, in
       front of the number. An inline span is exactly as wide as its text, so the dots sit under
       the "45%" and nowhere else. .hitchange keeps the classes, the tooltip and the click handler,
       so the whole column stays clickable and the delegated hover still fires. */
    function setChanceText($hitchance, text) {
        $hitchance.empty().append(jQuery('<span class="hitvalue"></span>').text(text));
    }

    /* Join the two ends of a hit-chance range.

       "30-45%" normally - but declared interception is no longer floored at 0 (user direction,
       2026-08-20), so a low end can be negative and a bare hyphen would produce "-30--10%". A range
       that starts below zero repeats the % sign instead - "-30%--10%" - which separates the two
       numbers without costing the width that spelling out "to" did.

       Equal ends collapse to a single number rather than reading "93-93%". Every caller today also
       guards on lo !== hi itself, so this is belt and braces - but a range of one is never the right
       thing to print, and the caller that did print it is how this was found. */
    function joinRange(lo, hi) {
        if (lo === null || lo === undefined || lo === hi) return hi + '%';
        return (lo < 0) ? lo + '%-' + hi + '%' : lo + '-' + hi + '%';
    }

    /* "(3d + 12)" - the dice and set damage a Shadow split weapon has committed to a row's shots.

       Only Molecular Slicers reach this: "Offensive Dice" is written by
       MolecularSlicerBeamL.initializationUpdate, in the Firing phase only. Everything else gets an
       empty string, so ordinary ballistic rows are untouched.

       Summed over the ROW's own shots, because that is what the row describes - a collapsed group
       reads as the whole group's allocation, and each expanded sub-row as its own. (The "(N dice)"
       this replaces summed every shot the weapon had made against this target regardless of which
       group it fell into, so a Slicer whose shots landed in two groups over-counted in both. It
       also never mentioned the set-damage half of the allocation at all.) */
    function diceSuffix(weapon, members) {
        if (!weapon || !weapon.data || !weapon.data["Offensive Dice"]) return '';

        var dice = 0;
        var setDam = 0;
        members.forEach(function (member) {
            dice += member.fireOrder.shots || 0;
            setDam += getOrderSetDamage(weapon, member.fireOrder);
        });

        return ' (' + dice + 'd + ' + setDam + ')';
    }

    /* Set damage on one order. The weapon owns this: a freshly declared order carries ->setDam,
       but one that has round-tripped through the server has only the encoded "MSB|d:x|s:y" token
       left in ->notes, and MolecularSlicerBeamL.getOrderSetDamage reads both. The fallbacks are
       for anything that reaches here without that prototype. */
    function getOrderSetDamage(weapon, fireOrder) {
        if (typeof weapon.getOrderSetDamage === 'function') return weapon.getOrderSetDamage(fireOrder) || 0;
        if (typeof fireOrder.setDam === 'number') return fireOrder.setDam;
        return 0;
    }

    /* Turn a hit-chance span into the row's intercept control.

       ready is the answer as of RENDER time and only drives the affordance. The handler asks
       again at CLICK time and declares on that: the row is re-rendered whenever the weapon
       selection changes (PhaseStrategy.onSystemDataChanged), but a row that has gone stale for any
       other reason must still do the right thing rather than fire on an answer minutes old. */
    function makeInterceptable($hitchance, ready, member, declare) {
        if (ready) $hitchance.addClass('intercept-ready');

        $hitchance.on('click', function (e) {
            e.stopPropagation();
            if (weaponManager.getInterceptDisabledReason(this.selectedShip, member) === null) {
                declare.call(this);
            }
            //Refresh either way: on success to show the new hit chance, on refusal so the row's
            //tooltip catches up to the reason it is refusing for.
            this.refresh();
        }.bind(this));
    }

    // The ▶ / ▼ toggle. Only a real group has anything to open; a single-shot row keeps an empty
    // span so its text still lines up with the grouped rows around it.
    function renderDisclosure(rowElement, groupKey, amount) {
        var expand = jQuery(".ballexpand", rowElement);

        if (amount <= 1) {
            expand.html('');
            return;
        }

        expand.html(this.expandedGroups[groupKey] ? '&#9660;' : '&#9654;')
            .addClass('ballexpand-active')
            .attr('title', this.expandedGroups[groupKey] ? 'Collapse' : 'Show individual shots')
            .on('click', function (e) {
                e.stopPropagation();
                this.expandedGroups[groupKey] = !this.expandedGroups[groupKey];
                this.refresh();
            }.bind(this));
    }

    return ShipTooltipBallisticsMenu;
}();
