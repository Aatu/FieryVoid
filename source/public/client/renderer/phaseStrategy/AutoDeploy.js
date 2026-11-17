'use strict';

/**
 * AutoDeploy - the "Auto-Deploy" button of the Deployment phase (TACTICAL_AI_PLAN.md section 5.2).
 *
 * The button sits at the bottom of #iniGui beside Deploy Minefield (gamedata.drawIniGUI draws it
 * while isOffered() says so). It asks the server where its FleetDeployer would stand the player's
 * fleet (autoDeploy.php - the same service the CPU opponent deploys with) and puts the units
 * there THROUGH THE SAME FUNCTIONS A MANUAL PLACEMENT USES, so each one looks and behaves exactly
 * as if the player had clicked it into place.
 *
 * ⭐ IT NEVER COMMITS THE PHASE. Nothing is written on the server; the player can move any unit,
 * change facing and speed, press Auto-Deploy again, and commits by hand as always.
 *
 * Not placed by it (FleetDeployer has the same list): mines - they have Deploy Minefield - bases,
 * OSATs, terrain, reinforcements arriving from hyperspace, and craft queued to start docked.
 *
 * The feature flag reaches the page as window.fvFeatures.autoDeploy (game.php prints it only when
 * the flag is on for this player); the server checks it again on every request.
 */
window.AutoDeploy = (function () {

    var _busy = false;

    /* Shuffle: Deploy always gives the standard layout (seed 0), so pressing it twice changes
       nothing. Shuffle asks for the next variant instead - the server mirrors the wings, changes
       which battlegroup stands where and re-deals the places among hulls of one size class - and
       lays the WHOLE fleet out again, since a variant of only the units not yet placed would not
       be one. Counts up, so each press differs from the one before. */
    var _shuffleSeed = 0;

    function _strategy() {
        return window.webglScene && window.webglScene.phaseDirector && window.webglScene.phaseDirector.phaseStrategy;
    }

    //The player's units Auto-Deploy may place this phase, placed already or not.
    function _units() {
        return gamedata.ships.filter(function (ship) {
            if (!gamedata.isMyShip(ship) || shipManager.isDestroyed(ship)) return false;
            if (ship.mine || ship.base || ship.smallBase || ship.osat) return false;
            if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
            if (shipManager.getTurnPlaced(ship) != gamedata.turn) return false;
            if (shipManager.isArrivingReinforcement(ship)) return false;
            if (ship.pendingDeployDock || ship.pendingLcvDeployDock || ship.removed) return false;
            return !!(ship.movement && ship.movement.length);
        });
    }

    function isOffered() {
        if (!window.fvFeatures || !window.fvFeatures.autoDeploy) return false;
        if (gamedata.gamephase !== -1 || gamedata.waiting || gamedata.replay) return false;
        if (gamedata.status && gamedata.status !== "ACTIVE") return false;
        return _units().length > 0;
    }

    /* How many battlegroups the fleet can form - the server's rule (FleetDeployer anchors): one
       per Capital or Enormous hull, or with none, per hull of the largest size present. Only a
       hint for the panel: the server clamps whatever is asked, slot by slot. */
    function _maxGroups(units) {
        var most = 1;
        var bySlot = {};
        units.forEach(function (ship) {
            if (ship.flight) return;
            (bySlot[ship.slot] = bySlot[ship.slot] || []).push(ship.shipSizeClass);
        });
        Object.keys(bySlot).forEach(function (slot) {
            var sizes = bySlot[slot];
            var anchor = Math.min(3, Math.max.apply(null, sizes));
            most = Math.max(most, sizes.filter(function (size) { return size >= anchor; }).length);
        });
        return most;
    }

    function onButtonClicked() {
        if (_busy || !isOffered()) return;

        var units = _units();
        var maxGroups = _maxGroups(units);
        var anyPlaced = units.some(function (ship) { return !!ship.deploymove; });

        //Nothing to choose: one possible battlegroup and nothing placed yet.
        if (maxGroups <= 1 && !anyPlaced) {
            _request(1, false, 0);
            return;
        }

        _showPanel(maxGroups, anyPlaced);
    }

    function _closePanel() {
        var panel = document.getElementById('autoDeployDialog');
        if (panel) panel.parentNode.removeChild(panel);
    }

    function _showPanel(maxGroups, anyPlaced) {
        _closePanel();

        var panel = document.createElement('div');
        panel.id = 'autoDeployDialog';

        var html = '<div class="mine-deploy-dialog-inner">'
            + '<p class="mine-deploy-title">AUTO-DEPLOY</p>'
            + '<p class="mine-deploy-sub">Places your fleet in formation. You can adjust it afterwards, and you commit as usual. Shuffle lays the whole fleet out again in a different arrangement.</p>';

        if (maxGroups > 1) {
            html += '<div class="mine-deploy-row">'
                + '<div class="mine-deploy-name">Battlegroups</div>'
                + '<div class="mine-deploy-controls-sm">'
                + '<button type="button" class="mine-deploy-btn-sm" data-step="-1">&#8722;</button>'
                + '<input type="text" class="mine-deploy-count" id="autoDeployGroups" value="1" readonly aria-label="Battlegroups">'
                + '<button type="button" class="mine-deploy-btn-sm" data-step="1">&#43;</button>'
                + '<button type="button" class="mine-deploy-btn-sm btn-max" data-step="max">Max (' + maxGroups + ')</button>'
                + '</div></div>';
        }

        if (anyPlaced) {
            html += '<label class="auto-deploy-check"><input type="checkbox" id="autoDeployReplace"> Replace units I have already placed</label>';
        }

        html += '<div class="mine-deploy-actions">'
            + '<button type="button" id="autoDeployConfirm" class="mine-deploy-btn">Deploy</button>'
            + '<button type="button" id="autoDeployShuffle" class="mine-deploy-btn" title="Lay the whole fleet out again in a different arrangement. Press again for another.">Shuffle</button>'
            + '<button type="button" id="autoDeployCancel" class="mine-deploy-btn mine-deploy-btn-cancel">Cancel</button>'
            + '</div></div>';

        panel.innerHTML = html;
        document.body.appendChild(panel);

        var groupsInput = document.getElementById('autoDeployGroups');
        Array.prototype.forEach.call(panel.querySelectorAll('[data-step]'), function (button) {
            button.addEventListener('click', function () {
                var step = button.getAttribute('data-step');
                var value = parseInt(groupsInput.value, 10) || 1;
                value = (step === 'max') ? maxGroups : value + parseInt(step, 10);
                groupsInput.value = Math.max(1, Math.min(maxGroups, value));
            });
        });

        document.getElementById('autoDeployCancel').addEventListener('click', _closePanel);
        document.getElementById('autoDeployConfirm').addEventListener('click', function () {
            var groups = groupsInput ? (parseInt(groupsInput.value, 10) || 1) : 1;
            var replaceBox = document.getElementById('autoDeployReplace');
            var replace = !!(replaceBox && replaceBox.checked);
            _closePanel();
            _request(groups, replace, 0);
        });
        //Shuffle keeps the panel open, so the player can press it until a layout suits them.
        document.getElementById('autoDeployShuffle').addEventListener('click', function () {
            var groups = groupsInput ? (parseInt(groupsInput.value, 10) || 1) : 1;
            _shuffleSeed++;
            _request(groups, true, _shuffleSeed);
        });
    }

    function _request(groups, replace, seed) {
        if (_busy) return; //one plan at a time - Shuffle can be pressed faster than the server answers
        var units = _units();
        var ids = [];
        var occupied = [];

        units.forEach(function (ship) {
            if (ship.deploymove && !replace) return;
            ids.push(ship.id);
        });

        /* What stands where already and is NOT in the database yet: every unit of the player's
           own that has been placed this phase and is staying put - kept units of this list, and
           the mines, bases and OSATs Auto-Deploy never moves. */
        var moving = {};
        ids.forEach(function (id) { moving[id] = true; });
        gamedata.ships.forEach(function (ship) {
            if (!gamedata.isMyShip(ship) || !ship.deploymove || moving[ship.id]) return;
            var hex = new hexagon.Offset(ship.deploymove.position);
            occupied.push({ q: hex.q, r: hex.r, flight: !!(ship.flight || ship.mine) });
        });

        if (ids.length === 0) {
            _toast('Every unit is already placed. Tick "Replace units I have already placed" to lay the fleet out again.');
            return;
        }

        _busy = true;
        $.ajax({
            type: 'POST',
            url: 'autoDeploy.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({ gameid: gamedata.gameid, groups: groups, seed: seed || 0, ships: ids, occupied: occupied }),
            timeout: 20000
        }).done(function (response) {
            _busy = false;
            if (!response || response.error || !response.placements) {
                _toast((response && response.error) ? String(response.error) : 'Auto-Deploy failed.');
                return;
            }
            _apply(response.placements, ids.length, seed > 0);
        }).fail(function () {
            _busy = false;
            _toast('Auto-Deploy could not reach the server.');
        });
    }

    /* Put the units where the plan says - each through shipManager.movement.deploy and
       onShipMovementChanged, the two calls a hex click makes (DeploymentPhaseStrategy.onHexClicked).
       Facing and speed are then written onto the unit's own deploy move, which is what the
       deployment turn / speed arrows edit. */
    function _apply(placements, asked, shuffled) {
        if (gamedata.gamephase !== -1 || gamedata.waiting) return; //the phase moved on while the request was out

        var strategy = _strategy();
        var allowed = {};
        _units().forEach(function (ship) { allowed[ship.id] = ship; });

        var placed = 0;
        placements.forEach(function (place) {
            var ship = allowed[place.shipid];
            if (!ship) return;

            shipManager.movement.deploy(ship, new hexagon.Offset(place.x, place.y));
            if (!ship.deploymove) return;

            ship.deploymove.facing = place.facing;
            ship.deploymove.heading = place.facing;
            ship.deploymove.speed = place.speed;

            if (strategy && strategy.onShipMovementChanged && strategy.shipIconContainer && strategy.shipIconContainer.getByShip(ship)) {
                strategy.onShipMovementChanged({ ship: ship });
            }
            placed++;
        });

        if (strategy) {
            //The selected unit's facing / speed arrows have to follow it to its new hex.
            if (strategy.selectedShip && allowed[strategy.selectedShip.id] && typeof strategy.drawMovementUI === 'function') {
                strategy.drawMovementUI(strategy.selectedShip);
            }

            //The commit button is armed exactly as a manual placement arms it: only once every
            //unit that must be placed this phase validly is.
            if (strategy.deploymentSprites && window.validateAllDeploymentGlobal
                && window.validateAllDeploymentGlobal(gamedata, strategy.deploymentSprites)) {
                gamedata.showCommitButton();
            }
        }

        if (placed === 0) {
            _toast('Auto-Deploy found no room for your units in the deployment zone.');
        } else if (placed < asked) {
            _toast('Fleet auto-deployed, but ' + (asked - placed) + ' unit(s) found no room - place them by hand, then commit.', 6000);
        } else {
            _toast(shuffled ? 'Fleet shuffled - press Shuffle again for another layout.' : 'Fleet auto-deployed - adjust and commit when ready.');
        }
    }

    //A passive notice: fades by itself, takes no clicks (the Deploy Minefield toast's look).
    function _toast(message, duration) {
        var existing = document.getElementById('autoDeployToast');
        if (existing) existing.parentNode.removeChild(existing);

        var toast = document.createElement('div');
        toast.id = 'autoDeployToast';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function () {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, duration || 4000);
    }

    return {
        isOffered: isOffered,
        onButtonClicked: onButtonClicked,
        close: _closePanel
    };
}());
