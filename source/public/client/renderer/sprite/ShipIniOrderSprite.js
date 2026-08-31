"use strict";

/* THE MOVEMENT-GROUP BADGE — the number laid over a unit that has not yet moved this turn.

   It is the readable half of the neutral dotted ring the NotMovedSprite already draws over the
   same units (ShipSelectedSprite, type 'neutral'): the ring says "still to move", the number says
   WHEN, using the exact group number the Order of Battle prints down its left edge
   (SimultaneousMovementRule.getMovementGroup -> shipManager.getIniativeOrder). Ships that share an
   initiative total share a group and move together, so the number is a bracket, not a queue
   position - which is why a stacked hex can collapse to one number plus a '+' without losing
   anything (MovementPhaseStrategy.refreshNotMovedMarkers owns that rule).

   TWO deliberate choices about how it is drawn, both from the brief "visible over a standard ship
   sprite, but doesn't obscure; more obvious when the player zooms out":

   1. NO PLATE. The glyph is a light fill inside a thick dark halo, with nothing behind it. A
      filled disc would be far easier to read and would also hide the hull, which is the one thing
      this marker must not do. The halo is what carries it over bright hull art instead.

   2. IT GROWS AS YOU ZOOM OUT. Everything else on the board holds a constant WORLD size, so at
      zoom 7 a hex is ~14 screen px and a hull is a coloured speck - exactly the view where the
      badge matters most and where a world-sized glyph would be unreadable. setZoom scales it by
      sqrt(zoom) and ramps its opacity up over the same range the coloured ship overlays fade in
      (ShipIconContainer.applyZoomToIcon, zoom 2 -> 3). sqrt rather than a full screen-constant
      `zoom` on purpose: full compensation makes the badge 7 hexes wide at maximum zoom-out and it
      starts colliding with the neighbouring hexes' badges. Zooming IN (zoom < 1) deliberately
      gets no compensation at all - the badge stays its world size and so shrinks relative to the
      hull it sits on, which is the "doesn't obscure" half of the brief. */
window.ShipIniOrderSprite = function () {

    var TEXTURE_SIZE = 96;

    /* label -> THREE.CanvasTexture. A game has at most a couple of dozen distinct labels ("1".."n"
       and their stacked "n+" forms) and every icon showing the same label can share one texture,
       so each is built at most once for the life of the page. */
    var TEXTURES = {};

    // Matches the amber of the neutral "not moved" dotted ring (ShipSelectedSprite's
    // getColorByType fallback, rgba(255,194,102)), lifted slightly because a thin glyph
    // reads darker than a filled arc. Keep the two in step - they mark the same state.
    var GLYPH_COLOR = "rgba(255,214,150,1)";
    var HALO_COLOR = "rgba(0,0,0,0.85)";

    var MIN_OPACITY = 0.45;   // zoom <= 1 (default / zoomed in): present, but the hull wins
    var MAX_OPACITY = 0.7;   // zoomed out past FULL_STRENGTH_ZOOM
    var FULL_STRENGTH_ZOOM = 3;

    function ShipIniOrderSprite(size, z) {
        webglSprite.call(this, null, size, z);

        this.label = null;
        this.setOpacity(MIN_OPACITY);
        this.hide();
    }

    ShipIniOrderSprite.prototype = Object.create(webglSprite.prototype);

    /* Pass a label ("3", "4+") to show it, or a falsy value to take the badge off this icon.
       Cheap to call every poll: an unchanged label doesn't touch the texture. */
    ShipIniOrderSprite.prototype.setLabel = function (label) {
        if (!label) {
            this.label = null;
            this.hide();
            return this;
        }

        if (label !== this.label) {
            this.label = label;
            this.uniforms.spriteTexture.value = getTexture(label);
        }

        this.show();
        return this;
    };

    ShipIniOrderSprite.prototype.setZoom = function (zoom) {
        var grow = Math.sqrt(zoom > 1 ? zoom : 1);
        this.setScale(grow, grow);

        var ramp = (zoom - 1) / (FULL_STRENGTH_ZOOM - 1);
        if (ramp < 0) ramp = 0;
        if (ramp > 1) ramp = 1;

        this.setOpacity(MIN_OPACITY + (MAX_OPACITY - MIN_OPACITY) * ramp);
    };

    function getTexture(label) {
        if (!TEXTURES[label]) {
            TEXTURES[label] = createTexture(label);
        }

        return TEXTURES[label];
    }

    function createTexture(label) {
        var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
        var context = canvas.getContext("2d");

        context.textAlign = "center";

        // Round joins, or the halo grows spikes off the corners of a '4' at this stroke width.
        context.lineJoin = "round";
        context.miterLimit = 2;

        /* MEASURE, don't guess. A one-character label wants every pixel of the texture, but a
           three-character one ("10+", once a big enough game runs past ten initiative groups)
           would silently run off the edge at that size and be drawn with its ends clipped. The
           halo is stroked OUTSIDE the glyph, so half the line width has to come out of the budget
           at both ends too - measuring only the fill would clip the halo instead. Arial Black may
           also not be installed, in which case the fallback metrics differ and a fixed size would
           be wrong anyway. */
        var fontSize = Math.round(TEXTURE_SIZE * 0.5);
        var budget = TEXTURE_SIZE * 0.92;
        var lineWidth;

        while (true) {
            context.font = "bold " + fontSize + "px 'Arial Black', Arial, sans-serif";
            lineWidth = Math.max(2, Math.round(fontSize * 0.22));

            if (fontSize <= 16 || context.measureText(label).width + lineWidth <= budget) {
                break;
            }

            fontSize -= 2;
        }

        /* CENTRE THE INK, NOT THE EM BOX. textBaseline "middle" centres the FONT's em square,
           which reserves descender room below the baseline that digits never use - so a numeral
           drawn that way lands visibly ABOVE the middle of the hex. Measuring the glyph's own
           bounding box and centring that is the only way to get it optically centred, and it also
           self-corrects when Arial Black is missing and a fallback face with different metrics is
           substituted. The halo is stroked symmetrically around the ink, so centring the fill
           centres it too.

           actualBoundingBox* has been in every browser this game supports for years, but it is a
           measurement, not a guarantee: if it ever comes back missing, fall back to the em-box
           centring rather than drawing at a NaN offset (which paints nothing at all). */
        var metrics = context.measureText(label);
        var baselineY;

        if (typeof metrics.actualBoundingBoxAscent === 'number' && typeof metrics.actualBoundingBoxDescent === 'number') {
            context.textBaseline = "alphabetic";
            baselineY = TEXTURE_SIZE / 2 + (metrics.actualBoundingBoxAscent - metrics.actualBoundingBoxDescent) / 2;
        } else {
            context.textBaseline = "middle";
            baselineY = TEXTURE_SIZE / 2;
        }

        context.strokeStyle = HALO_COLOR;
        context.lineWidth = lineWidth;
        context.strokeText(label, TEXTURE_SIZE / 2, baselineY);

        context.fillStyle = GLYPH_COLOR;
        context.fillText(label, TEXTURE_SIZE / 2, baselineY);

        var tex = new THREE.CanvasTexture(canvas);
        tex.colorSpace = THREE.SRGBColorSpace;
        // Mipmaps matter here: the badge is routinely minified hard when zoomed out, and the
        // default linear-only filtering makes a thin glyph shimmer as the zoom animates.
        tex.generateMipmaps = true;
        tex.minFilter = THREE.LinearMipmapLinearFilter;
        tex.magFilter = THREE.LinearFilter;
        tex.needsUpdate = true;

        return tex;
    }

    return ShipIniOrderSprite;
}();
