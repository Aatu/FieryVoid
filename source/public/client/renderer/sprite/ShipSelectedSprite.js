"use strict";

window.ShipSelectedSprite = function () {

    var TEXTURE_SIZE = 256;
    var TEXTURE_MINE = null;
    var TEXTURE_ALLY = null;
    var TEXTURE_ENEMY = null;
    var TEXTURE_NEUTRAL = null;
    var TEXTURE_MINE_SELECTED = null;
    var TEXTURE_ALLY_SELECTED = null;
    var TEXTURE_ENEMY_SELECTED = null;
    var TEXTURE_TERRAIN = null;
    var TEXTURE_TERRAIN_SELECTED = null;

    // Per-team circle textures, keyed by team number ('team3' etc). Used whenever
    // the caller works in the per-team palette rather than the relative
    // mine/ally/enemy one (observers, and participants in a 3+-team game).
    //
    // BOTH the filled side circle AND the dotted selection ring need a team
    // variant. An earlier version only built the filled one on the assumption
    // that the dotted rings "never apply" without a selectable ship — but
    // MovementPhaseStrategy.highlightUnmovedShips calls setSelected() on every
    // ship active in the current simultaneous-movement bracket regardless of
    // ownership, so the ring very much applies. Without a team variant the
    // 'teamN' type fell off the end of chooseTexture() and every ship in the game
    // got TEXTURE_NEUTRAL — the same orange ring the NotMoved sprite uses, which
    // made "moving this bracket", "already moved" and team identity all identical.
    var TEXTURE_TEAM = {};
    var TEXTURE_TEAM_SELECTED = {};

    function ShipSelectedSprite(size, z, type, selected, teamColor) {

        this.DEW = 0;
        this.CCEW = 0;
        webglSprite.call(this, null, size, z);

        if (!TEXTURE_MINE) {
            createTextures();
        }

        // teamColor is an sRGB [r,g,b]; supplied when each team needs its own
        // circle instead of the friend/foe colours.
        if (teamColor) {
            this.uniforms.spriteTexture.value = getTeamTexture(type, teamColor, selected);
        } else {
            this.uniforms.spriteTexture.value = chooseTexture(type, selected);
        }
    }

    function getTeamTexture(team, teamColor, selected) {
        var cache = selected ? TEXTURE_TEAM_SELECTED : TEXTURE_TEAM;

        if (!cache[team]) {
            cache[team] = createTeamTexture(teamColor, selected);
        }
        return cache[team];
    }

    function createTeamTexture(teamColor, selected) {
        var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
        var context = canvas.getContext("2d");

        var rgb = Math.round(teamColor[0]) + "," + Math.round(teamColor[1]) + "," + Math.round(teamColor[2]);

        if (selected) {
            // Match the alpha treatment of the selected friend/foe dotted rings.
            // The team colour already carries the identity, so a single segment
            // count is enough - 12 keeps it clearly apart from the 4-segment
            // neutral ring that marks a ship as not yet moved.
            context.strokeStyle = "rgba(" + rgb + ",0.50)";
            context.fillStyle = "rgba(" + rgb + ",0.30)";
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.23, TEXTURE_SIZE * 0.30, 12, 0.25);
        } else {
            // Match the alpha treatment of the non-selected friend/foe filled circle.
            context.strokeStyle = "rgba(" + rgb + ",0.40)";
            context.fillStyle = "rgba(" + rgb + ",0.20)";
            window.graphics.drawCircleAndFill(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.30, 4);
        }

        var tex = new THREE.CanvasTexture(canvas);
        tex.colorSpace = THREE.SRGBColorSpace;
        tex.needsUpdate = true;

        return tex;
    }

    function chooseTexture(type, selected) {
        if (type == "mine" && selected) return TEXTURE_MINE_SELECTED;
        if (type == "mine") return TEXTURE_MINE;
        if (type == "ally" && selected) return TEXTURE_ALLY_SELECTED;
        if (type == "ally") return TEXTURE_ALLY;
        if (type == "enemy" && selected) return TEXTURE_ENEMY_SELECTED;
        if (type == "enemy") return TEXTURE_ENEMY;
        if (type == "terrain" && selected) return TEXTURE_TERRAIN_SELECTED;
        if (type == "terrain") return TEXTURE_TERRAIN;
        return TEXTURE_NEUTRAL;
    }

    function createTextures() {
        TEXTURE_MINE = createTexture('mine', false);
        TEXTURE_MINE_SELECTED = createTexture('mine', true);
        TEXTURE_ALLY = createTexture('ally', false);
        TEXTURE_ALLY_SELECTED = createTexture('ally', true);
        TEXTURE_ENEMY = createTexture('enemy', false);
        TEXTURE_ENEMY_SELECTED = createTexture('enemy', true); //Actually use this somehow for enemies moving this turn.
        TEXTURE_NEUTRAL = createTexture('neutral', true);
        TEXTURE_TERRAIN = createTexture('terrain', false);
        TEXTURE_TERRAIN_SELECTED = createTexture('terrain', true);
    }

    function createTexture(type, selected) {
        var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
        var context = canvas.getContext("2d");
        getColorByType(context, type, selected);

        //Separate these so we can give different type of dotted circles different number of segments - DK 10/24
        if (selected && type == 'mine') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.23, TEXTURE_SIZE * 0.30, 12, 0.25);
        } else if (selected && type == 'ally') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.23, TEXTURE_SIZE * 0.30, 16, 0.3);
        } else if (selected && type == 'enemy') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.23, TEXTURE_SIZE * 0.30, 10, 0.20);
        } else if (selected && type == 'neutral') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.22, TEXTURE_SIZE * 0.26, 4, 0.15);
        } else {
            window.graphics.drawCircleAndFill(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.30, 4);
        }

        var tex = new THREE.CanvasTexture(canvas);
        tex.colorSpace = THREE.SRGBColorSpace;
        tex.needsUpdate = true;

        return tex;
    }

    function getColorByType(context, type, selected) {
        var a = -0.1;

        if (type == "mine" && selected) {
            context.strokeStyle = "rgba(78,220,25," + (0.60 + a) + ")";
            context.fillStyle = "rgba(78,220,25," + (0.40 + a) + ")";
        } else if (type == "mine") {
            context.strokeStyle = "rgba(78,220,25," + (0.50 + a) + ")";
            context.fillStyle = "rgba(78,220,25," + (0.30 + a) + ")";
        } else if (type == "ally" && selected) {
            context.strokeStyle = "rgba(51,173,255," + (0.60 + a) + ")";
            context.fillStyle = "rgba(51,173,255," + (0.40 + a) + ")";
        } else if (type == "ally") {
            context.strokeStyle = "rgba(51,173,255," + (0.50 + a) + ")";
            context.fillStyle = "rgba(51,173,255," + (0.30 + a) + ")";
        } else if (type == "enemy" && selected) {
            context.strokeStyle = "rgba(229,87,38," + (0.80 + a) + ")";
            context.fillStyle = "rgba(229,87,38," + (0.50 + a) + ")";
        } else if (type == "enemy") {
            context.strokeStyle = "rgba(229,87,38," + (0.70 + a) + ")";
            context.fillStyle = "rgba(229,87,38," + (0.30 + a) + ")";
        } else if (type == "terrain" && selected) {
            context.strokeStyle = "rgba(255,255,255,0.4)";
            context.fillStyle = "rgba(255,255,255,0.2)";
        } else if (type == "terrain") {
            context.strokeStyle = "rgba(255,255,255,0.3)";
            context.fillStyle = "rgba(255,255,255,0.2)";
        } else {
            context.strokeStyle = "rgba(255,194,102,0.45)";
            context.fillStyle = "rgba(255,194,102,0.25)";
        }
    }

    ShipSelectedSprite.prototype = Object.create(webglSprite.prototype);

    return ShipSelectedSprite;
}();