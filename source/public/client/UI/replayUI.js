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
        + '            <button id="back" title="Rewind"><span class="glyph">◀</span></button>\n'
        + '            <span class="speed-control">\n'
        + '                <button id="slower" title="Slower">−</button>\n'
        + '                <span class="rate">1×</span>\n'
        + '                <button id="faster" title="Faster">+</button>\n'
        + '            </span>\n'
        + '            <button id="playPause" title="Play"><span class="glyph">▶</span></button>\n'
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
        this.playPause = callbacks.playPause || function () { };
        this.back = callbacks.back || function () { };
        this.slower = callbacks.slower || function () { };
        this.faster = callbacks.faster || function () { };
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

        jQuery("#replayUI #playPause").on("click", this.playPause);
        jQuery("#replayUI #back").on("click", this.back);
        jQuery("#replayUI #slower").on("click", this.slower);
        jQuery("#replayUI #faster").on("click", this.faster);
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

    // Pause appears in place of whichever direction is currently running: the
    // active direction button swaps its glyph to ❚❚ (and acts as the pause
    // control), while the other keeps its normal direction glyph. This is the
    // single source of truth for both transport buttons' glyph, tooltip and
    // active highlight; the strategy calls it on every play/pause/seek/done.
    //   state: "forward" (playing forward), "rewind" (playing back), "paused".
    ReplayUI.prototype.setTransportState = function (state) {
        var rewind = jQuery("#replayUI #back", this.element);
        var play = jQuery("#replayUI #playPause", this.element);

        var rewinding = state === "rewind";
        var forward = state === "forward";

        jQuery(".glyph", rewind).text(rewinding ? "❚❚" : "◀");
        rewind.attr("title", rewinding ? "Pause" : "Rewind");
        rewind.toggleClass("active", rewinding);

        jQuery(".glyph", play).text(forward ? "❚❚" : "▶");
        play.attr("title", forward ? "Pause" : "Play");
        play.toggleClass("active", forward);
    };

    // Update the rate readout to the current playback multiplier.
    // Applies to whichever direction (play/rewind) is currently active.
    ReplayUI.prototype.setSpeed = function (multiplier) {
        jQuery("#replayUI .rate", this.element).text(multiplier + "×");
        // Subtle highlight while running off the default rate so it's clear the
        // setting is non-default.
        jQuery("#replayUI .speed-control", this.element).toggleClass("active", multiplier !== 1);
    };

    return ReplayUI;
}();