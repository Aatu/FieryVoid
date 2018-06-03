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
    + '            <button id="turnBack">❚◀</button>\n' 
    + '            <button id="back">◀</button>\n' 
    + '            <button id="pause">❚❚</button>\n' 
    + '            <button id="play">▶</button>\n' 
    + '            <button id="turnForward">▶❚</button>\n' 
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
        this.play = callbacks.play || function () {};
        this.back = callbacks.back || function () {};
        this.pause = callbacks.pause || function () {};
        this.turnForward = callbacks.turnForward || function () {};
        this.turnBack = callbacks.turnBack || function () {};
        this.endReplay = callbacks.endReplay || function () {};
        this.toFiringPhase = callbacks.toFiringPhase || function () {};
        this.toMovementPhase = callbacks.toMovementPhase ||function () {};
    }

    ReplayUI.prototype.activate = function () {

        this.element = jQuery('body').prepend(template);

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
        jQuery(".replay-buttons button").removeClass("active");
        jQuery(name).addClass("active");
    };

    return ReplayUI;
}();