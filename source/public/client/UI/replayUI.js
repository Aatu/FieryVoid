'use strict';

window.ReplayUI = function () {

    var template =
        '<div id="replayUI">\n'
        + '    <div class="replay-inactive">\n'
        + '        <button id="activateReplay">Replay</button>\n'
        + '    </div>\n'
        + '    <div class="replay-active">\n'
        + '        <div class="replay-container">\n'
        + '            <button id="deactivateReplay">Resume game</button>\n'
        + '        </div>\n'
        + '        <div class="replay-container">\n'
        + '            <div class="selected-replay"></div>\n'
        + '            <button id="toMovement">Movement</button>\n'
        + '            <button id="toFiring">Firing</button>\n'
        + '        </div>\n'
        + '        <div class="replay-container replay-buttons">\n'
        + '            <button id="turnBack" title="Previous turn">❚◀</button>\n'
        + '            <button id="back" title="Rewind">◀</button>\n'
        + '            <button id="speed" title="Change speed"><span class="rate">1×</span></button>\n'          
        + '            <button id="pause" title="Pause">❚❚</button>\n'
        + '            <button id="play" title="Play">▶</button>\n'
        + '            <button id="turnForward" title="Next turn">▶❚</button>\n'
        + '        </div>\n'
        + '        <div class="replay-container loading-indicator">\n'
        + '            Loading replay...\n'
        + '        </div>\n'
        + '    </div>\n'
        + '</div>';

    function ReplayUI(replayActive, callbacks) {
        this.replayActive = replayActive || false;
        this.element = null;
        if (!callbacks) {
            callbacks = {};
        }
        this.play = callbacks.play || function () { };
        this.back = callbacks.back || function () { };
        this.pause = callbacks.pause || function () { };
        this.changeSpeed = callbacks.changeSpeed || function () { };
        this.turnForward = callbacks.turnForward || function () { };
        this.turnBack = callbacks.turnBack || function () { };
        this.endReplay = callbacks.endReplay || function () { };
        this.toFiringPhase = callbacks.toFiringPhase || function () { };
        this.toMovementPhase = callbacks.toMovementPhase || function () { };
    }

    ReplayUI.prototype.activate = function () {

        this.element = jQuery('#topcontainer').append(template);

        if (this.replayActive) {
            $("#replayUI").addClass('active').removeClass('inactive');
        } else {
            $("#replayUI").addClass('inactive').removeClass('active');
        }

        $("#replayUI #activateReplay").on("click", this.startReplay.bind(this));
        $("#replayUI #deactivateReplay").on("click", this.endReplay);

        jQuery("#replayUI #play").on("click", this.play);
        jQuery("#replayUI #pause").on("click", this.pause);
        jQuery("#replayUI #back").on("click", this.back);
        jQuery("#replayUI #speed").on("click", this.changeSpeed);
        jQuery("#replayUI #turnForward").on("click", this.turnForward);
        jQuery("#replayUI #turnBack").on("click", this.turnBack);
        jQuery("#replayUI #toMovement").on("click", this.toMovementPhase);
        jQuery("#replayUI #toFiring").on("click", this.toFiringPhase);

        return this;
    };

    ReplayUI.prototype.deactivate = function () {

        jQuery("#replayUI", this.element).remove();

        return this;
    };

    ReplayUI.prototype.startReplay = function () {
        gamedata.replay = true;
        webglScene.receiveGamedata(gamedata);
    };

    ReplayUI.prototype.startLoading = function () {
        jQuery("#replayUI", this.element).addClass("loading");
    };

    ReplayUI.prototype.stopLoading = function () {
        jQuery("#replayUI", this.element).removeClass("loading");
    };

    ReplayUI.prototype.setTurn = function (turn) {
        jQuery("#replayUI .selected-replay", this.element).html('Turn ' + turn);
    };

    ReplayUI.prototype.activateButton = function (name) {
        // Highlight only the transport buttons (play/pause/rewind). The speed
        // button is a persistent setting, not a transport state, so it keeps
        // its own active styling and is excluded from this reset.
        jQuery(".replay-buttons button").not("#speed").removeClass("active");
        jQuery(name).addClass("active");
    };

    // Update the speed button's label to the current playback multiplier.
    // Applies to whichever direction (play/rewind) is currently active.
    ReplayUI.prototype.setSpeed = function (multiplier) {
        jQuery("#replayUI #speed .rate", this.element).text(multiplier + "×");
        // Subtle highlight while running faster than normal so it's clear the
        // setting is non-default.
        jQuery("#replayUI #speed", this.element).toggleClass("active", multiplier !== 1);
    };

    return ReplayUI;
}();