"use strict";

/* JUMP_POINTS_PLAN.md STAGE 2b - THE VORTEX FACING CONTROL.

   The facing is part of the DECLARATION TRANSACTION: no fire order exists until Ok is clicked.

       select Jump Engine -> click target hex -> preview marker + arrow at facing 0
           -> turn left / turn right step the facing; arrow and buttons redraw
               -> Ok .............. weaponManager builds the FireOrder, control closes
               -> click away ...... discards; no order, nothing to clean up
               -> deselect engine . discards; same as clicking away (closeForWeapon)

   Because the order is only born on Ok there is never a half-declared vortex, nothing to nag
   about before the Initial Orders commit, and no way to alter a facing after the fact - which
   matches RAW ("the vortex facing cannot be altered once the jump point begins to form").
   Re-aiming is the ordinary ballistic idiom: remove the firing order and declare again.

   This is a SIBLING of UI.shipMovement, not an extension of it. drawShipMovementUI is ~400 lines
   keyed to a ship (ship.movement, canTurn(ship), ...); threading vortex-ness through it would be
   the wrong shape. What is reused is the technique, not the code: placeElement is drawUIElement's
   geometry without its bitmap step. NOTHING here is an image - the two turn arrows are drawn as
   arcs (drawCurvedArrow) and Ok is the WORD, both in --fv-warn yellow straight from tokens.css.

   LAYOUT: the whole control is RIGID and swings with the FACING - Ok directly in front of the
   facing arrow, the two turn buttons flanking it at +/-60 degrees on the side each one turns
   towards, and both turn arrows DRAWN at the facing angle so the whole thing looks at every facing
   exactly as it does at facing 0, just turned. So the arrow always points at the button that
   accepts it. The one exception is the Ok label, which stays UPRIGHT. Their radius follows the zoom (see
   buttonDistance) because they are placed around the hex, which scales; #shipMovementUI's fixed
   pixels would put Ok inside the hex when zoomed in.

   FACING CONVENTION (do not re-derive it - see JUMP_POINTS_PLAN.md section 2.2):
   facing 0 points EAST and facing increases CLOCKWISE on screen, so facing F is F*60 degrees
   clockwise from east. That is what mathlib.hexFacingToAngle returns and what
   CubeCoordinate::NEIGHBOURS / Offset.neighbours step through, which is why the arrow is oriented
   with setFacing(-hexFacingToAngle(facing)) - exactly as ShipIcon orients directionOfMovement.png.
   Turn right therefore means facing + 1.

   window.UI is CREATED by shipMovement.js (it assigns the whole object), so this file must load
   after it in game.php or that assignment would wipe this module. The defensive ||= below keeps a
   future reordering from being silently fatal. */

window.UI = window.UI || {};

window.UI.vortexFacing = {

    iniated: false,
    uiElement: null,
    turnLeftElement: null,
    turnRightElement: null,
    confirmElement: null,

    /* The transaction in progress, or null. Held as the payload OBJECT that weaponManager raised,
       so PhaseStrategy can ask "is the control still showing the transaction I registered?"
       by identity - the same token pattern hideShipTooltip uses. */
    pending: null,

    hexSprite: null,
    arrowSprite: null,
    gamePosition: null,
    currentPosition: null,
    lastActionTime: 0,

    BUTTON_SIZE: 40,
    currentDistance: null,

    /* ---------------- THE ON-MAP PREVIEW ARROW ----------------
       Kept identical to the arrow BallisticIconContainer draws over a committed "Jump Point
       Forming" hex and the one ShipIcon draws over the vortex unit once it opens, so the arrow
       never changes appearance across the three stages of a vortex's life. If you retune these,
       retune BallisticIconContainer's VORTEX_ARROW_SCALE / _OPACITY and
       ShipIcon.FACING_ARROW_SCALE / _OPACITY to match. SCALE is a multiple of the HEX HEIGHT. */
    MARKER_ARROW_SCALE: 1.15,
    MARKER_ARROW_OPACITY: 0.85,

    /* ---------------- THE BUTTON RING ----------------
       BUTTON_GAP        clear space between the hex rim and the nearest button edge, in viewport
                         pixels. Bigger = the whole control sits further out.
       TURN_BUTTON_INSET how much closer to the hex the two turn arrows sit than Ok does. Ok is the
                         one on the ring proper; the arrows tuck in behind it.
       MIN_DISTANCE      floor for when the hex is tiny (zoomed right out) - without it the three
                         buttons would overlap each other and the hex.
       VIEWPORT_LIMIT    fraction of the smaller viewport dimension past which the ring stops
                         following the hex. See buttonDistance for why. */
    BUTTON_GAP: 14,
    TURN_BUTTON_INSET: 15,
    MIN_DISTANCE: 58,
    VIEWPORT_LIMIT: 0.42,

    initVortexUI: function initVortexUI() {
        if (UI.vortexFacing.iniated === true) return;

        var ui = $("#vortexFacingUI");
        UI.vortexFacing.uiElement = ui;
        UI.vortexFacing.turnLeftElement = $("#vortexTurnLeft", ui);
        UI.vortexFacing.turnRightElement = $("#vortexTurnRight", ui);
        UI.vortexFacing.confirmElement = $("#vortexConfirm", ui);

        UI.vortexFacing.turnLeftElement.on("click touchstart", UI.vortexFacing.turnLeftCallback);
        UI.vortexFacing.turnRightElement.on("click touchstart", UI.vortexFacing.turnRightCallback);
        UI.vortexFacing.confirmElement.on("click touchstart", UI.vortexFacing.confirmCallback);

        //Same masking #shipMovementUI does: these divs sit above the WebGL canvas, and without it
        //a press on a button also reads as a map click - which would run the click-away discard.
        jQuery('#vortexFacingUI div').on('mousedown touchend touchmove', cancelEvent);
        jQuery('#vortexFacingUI div').on('mouseup touchend touchmove', cancelEvent);
        ui[0].addEventListener('contextmenu', function (e) {
            e.preventDefault();
        }, true);

        function cancelEvent(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        UI.vortexFacing.iniated = true;
    },

    /* Both handlers are bound to "click touchstart", which is what UI.shipMovement does - but a
       facing STEPS, so a touch that fires touchstart and then a synthetic click 300ms later would
       turn twice per tap. The movement UI tolerates that (its callbacks are idempotent-ish and it
       redraws from ship state); this control cannot, so it swallows a second action inside 350ms. */
    swallowDoubleEvent: function swallowDoubleEvent() {
        var now = new Date().getTime();
        if (now - UI.vortexFacing.lastActionTime < 350) return true;
        UI.vortexFacing.lastActionTime = now;
        return false;
    },

    turnLeftCallback: function turnLeftCallback(e) {
        e.preventDefault();
        e.stopPropagation();
        if (UI.vortexFacing.swallowDoubleEvent()) return;
        UI.vortexFacing.turn(-1);
    },

    turnRightCallback: function turnRightCallback(e) {
        e.preventDefault();
        e.stopPropagation();
        if (UI.vortexFacing.swallowDoubleEvent()) return;
        UI.vortexFacing.turn(1);
    },

    confirmCallback: function confirmCallback(e) {
        e.preventDefault();
        e.stopPropagation();
        if (UI.vortexFacing.swallowDoubleEvent()) return;
        UI.vortexFacing.confirm();
    },

    isOpen: function isOpen() {
        return UI.vortexFacing.pending !== null;
    },

    /* Identity, not equality: PhaseStrategy registers a one-shot click-away discard against the
       payload it opened, and by the time that fires the control may have been closed by Ok or
       replaced by a NEWER declaration. Only the transaction that registered it may close it. */
    isOpenFor: function isOpenFor(pending) {
        return UI.vortexFacing.pending !== null && UI.vortexFacing.pending === pending;
    },

    /* pending = { ship, weapon, hexpos, type, facing, onConfirm } - raised by
       weaponManager.queueJumpPointOrder and relayed here by PhaseStrategy.onVortexFacingRequested. */
    open: function open(pending) {
        UI.vortexFacing.initVortexUI();
        UI.vortexFacing.close(); //one transaction at a time; a new declaration replaces the old

        UI.vortexFacing.pending = pending;
        UI.vortexFacing.gamePosition = window.coordinateConverter.fromHexToGame(pending.hexpos);
        UI.vortexFacing.lastActionTime = 0;

        UI.vortexFacing.drawMarker();
        UI.vortexFacing.drawButtons();
        UI.vortexFacing.uiElement.show();
    },

    close: function close() {
        if (!UI.vortexFacing.pending) return;

        UI.vortexFacing.releaseMarker();
        UI.vortexFacing.pending = null;
        UI.vortexFacing.gamePosition = null;
        UI.vortexFacing.currentPosition = null;
        UI.vortexFacing.currentDistance = null;

        if (UI.vortexFacing.uiElement) UI.vortexFacing.uiElement.hide();
    },

    turn: function turn(step) {
        var pending = UI.vortexFacing.pending;
        if (!pending) return;

        pending.facing = (pending.facing + step + 6) % 6;
        UI.vortexFacing.drawMarker();
        UI.vortexFacing.drawButtons(); //the buttons orbit the facing, so they move with it
    },

    /* Deselecting the Jump Engine abandons the transaction, exactly as clicking away on the map
       does - the pending declaration belongs to that engine and nothing else. Hooked into
       weaponManager.unSelectWeapon, which is the choke point every deselect route funnels through:
       the weapon-list icon toggle, selecting another ship, and the phase teardown sweep alike.
       Weapon identity is the whole test; a system object belongs to exactly one ship. */
    closeForWeapon: function closeForWeapon(weapon) {
        if (!UI.vortexFacing.pending) return;
        if (UI.vortexFacing.pending.weapon !== weapon) return;

        UI.vortexFacing.close();
    },

    confirm: function confirm() {
        var pending = UI.vortexFacing.pending;
        if (!pending) return;

        //Close FIRST: onConfirm raises HexTargeted, which rebuilds the ballistic icons - and the
        //committed order draws its own yellow hex on this very spot. Leaving the preview up would
        //stack two sprites on one hex.
        UI.vortexFacing.close();
        pending.onConfirm(pending.facing);
    },

    /* THE PREVIEW MARKER. The vortex UNIT does not exist yet (Stage 3 spawns it at the end of
       InitialOrdersGamePhase::advance) and neither does the fire order, so the marker is drawn
       from the pending declaration and thrown away with it.

       The hex is a BallisticSprite in exactly the livery createBallisticIcon gives a committed
       'jumppoint' order - yellow, labelled with the firing-mode name, which on a Jump Engine IS the
       facing - so the preview and the saved marker look the same and Ok is visually a no-op.
       The arrow is directionOfMovement.png, the same art (and the same orientation maths) ShipIcon
       uses for a ship's heading. */
    drawMarker: function drawMarker() {
        var pending = UI.vortexFacing.pending;
        if (!pending || !window.webglScene || !window.webglScene.scene) return;

        var scene = window.webglScene.scene;
        /* Stage 4: an EXIT does not borrow the engine's mode names. They read "Vortex 0°" -
           correct for an entrance, where the mode IS the facing and the word is the thing being made,
           but on an exit the facing means the doorway OUT and "Vortex" is the wrong noun for a
           doorway units arrive through. The degrees are still shown, because the facing is exactly
           what this control exists to set. */
        var label = UI.vortexFacing.isExit()
            ? ("Entry " + (pending.facing * 60) + "°")
            : ((pending.weapon.firingModes && pending.weapon.firingModes[pending.facing + 1]) || "Jump Point");

        //The hex sprite carries the label, so it is rebuilt per step rather than mutated.
        //BallisticSprite caches its textures globally by (type|text|colour) and these are the very
        //six strings the committed marker uses, so stepping the facing costs nothing after the
        //first pass round the compass.
        if (UI.vortexFacing.hexSprite) {
            scene.remove(UI.vortexFacing.hexSprite.mesh);
            UI.vortexFacing.hexSprite.destroy();
        }
        //Stage 4: the preview wears the same livery the COMMITTED marker will - a blue hex for
        //an exit, a yellow one for an entrance - so confirming is visually a no-op either way.
        var exit = UI.vortexFacing.isExit();
        UI.vortexFacing.hexSprite = new BallisticSprite(UI.vortexFacing.gamePosition,
            exit ? "hexBlue" : "hexYellow", label, UI.vortexFacing.arrowColour());
        scene.add(UI.vortexFacing.hexSprite.mesh);

        if (!UI.vortexFacing.arrowSprite) {
            //z -99: above the ballistic hexes (-100), below terrain (-50) and ships (0).
            //The arrow is drawn ~0.78 of the way out from the centre of a square canvas, so a
            //sprite 1.15 hex-heights across lands the arrowhead on the hex SIDE the vortex faces -
            //which is the doorway the entry rule is about.
            var size = window.HexagonMath.getHexHeight() * UI.vortexFacing.MARKER_ARROW_SCALE;
            //The EXIT asset is the same glyph mirrored in place, so the size is unchanged.
            var asset = exit ? UI.vortexFacing.EXIT_ARROW : UI.vortexFacing.ENTRANCE_ARROW;
            UI.vortexFacing.arrowSprite = new window.webglSprite(asset,
                { width: size, height: size }, -99);
            UI.vortexFacing.arrowSprite.setPosition(UI.vortexFacing.gamePosition);
            UI.vortexFacing.arrowSprite.setOpacity(UI.vortexFacing.MARKER_ARROW_OPACITY);
            scene.add(UI.vortexFacing.arrowSprite.mesh);
        }
        UI.vortexFacing.arrowSprite.setFacing(-mathlib.hexFacingToAngle(pending.facing));

        //Render-loop idle gating: any scene mutation outside the animation list must ask for a
        //frame or the arrow simply will not redraw until the next input event.
        window.webglScene.requestRender();
    },

    releaseMarker: function releaseMarker() {
        var scene = window.webglScene && window.webglScene.scene;

        if (UI.vortexFacing.hexSprite) {
            if (scene) scene.remove(UI.vortexFacing.hexSprite.mesh);
            UI.vortexFacing.hexSprite.destroy();
            UI.vortexFacing.hexSprite = null;
        }

        if (UI.vortexFacing.arrowSprite) {
            if (scene) scene.remove(UI.vortexFacing.arrowSprite.mesh);
            UI.vortexFacing.arrowSprite.destroy();
            UI.vortexFacing.arrowSprite = null;
        }

        if (window.webglScene && window.webglScene.requestRender) window.webglScene.requestRender();
    },

    /* How far out the buttons sit, in viewport pixels from the hex centre.

       Zoom-relative, unlike #shipMovementUI's fixed pixels, because these three are placed AROUND
       THE HEX and Ok has to land in front of the facing arrow - which is a map object and scales.
       Fixed pixels would bury Ok inside the hex when zoomed in and strand it in empty space when
       zoomed out; the zoom range is 0.1 to 7, so that is not a corner case.
       getHexHeightViewport()/2 is the hex's centre-to-vertex radius on screen - height, not width,
       because that is the LARGER of the two on a pointy-top hex and so clears the rim in every
       direction.

       ⚠️ THIS USED TO HAVE AN UPPER CAP OF 130px AND THAT WAS THE BUG (user report 2026-08-21).
       Note that zoom is a DIVISOR - getHexHeightViewport() is hexHeight / zoom - so zoomed IN is a
       SMALL zoom value and a HUGE hex. At zoom 0.3 the hex radius is 167px, so a ring capped at
       130px sat entirely inside the hex. There is no upper cap on the clearance any more: the ring
       always clears the rim by BUTTON_GAP plus half a button plus the turn buttons' inset.

       The one thing that IS capped is how far the hex is allowed to push the ring - VIEWPORT_LIMIT
       of the smaller viewport dimension. Past that the hex is bigger than the screen, so "outside
       the hex" would mean "off the screen"; staying reachable wins, and there is nothing to overlap
       because the hex IS the viewport at that point. On a 1000px-tall window that only starts to
       bite below zoom ~0.12, the very end of the range. */
    buttonDistance: function buttonDistance() {
        var self = UI.vortexFacing;
        var hexRadius = window.coordinateConverter.getHexHeightViewport() / 2;

        var viewportCap = Math.min(window.innerWidth, window.innerHeight) * self.VIEWPORT_LIMIT;
        if (hexRadius > viewportCap) hexRadius = viewportCap;

        //Clearance is measured to the INNER edge of the innermost button, which is a turn arrow.
        var clearance = self.TURN_BUTTON_INSET + self.BUTTON_SIZE * 0.5 + self.BUTTON_GAP;

        return Math.max(self.MIN_DISTANCE, hexRadius + clearance);
    },

    /* Laid out AROUND THE FACING: Ok sits directly in front of the facing arrow and the two turn
       buttons flank it at +/-60 degrees, on the side each one turns towards. So the whole control
       swings with the vortex mouth and the arrow always points at the button that accepts it.

       drawUIElement's angle is measured CLOCKWISE FROM EAST with +y down - the same convention as
       mathlib.hexFacingToAngle - so the facing angle can be passed straight through with no
       conversion. x/y are 0 because only the delta from them is used; the container itself is
       anchored to the hex by reposition().

       The two ICONS are also rotated by the facing, but they are DRAWN at that angle rather than
       rotated as bitmaps (see drawCurvedArrow) - which is the whole reason they are not images.
       Rotating by facingAngle, not by each button's own position angle, keeps the control RIGID:
       it looks at every facing exactly as it does at facing 0, just turned. That is the same thing
       #shipMovementUI achieves by CSS-rotating its entire container to the ship's heading. */
    drawButtons: function drawButtons() {
        var pending = UI.vortexFacing.pending;
        if (!pending) return;

        var s = UI.vortexFacing.BUTTON_SIZE;
        var dis = UI.vortexFacing.buttonDistance();
        var facingAngle = mathlib.hexFacingToAngle(pending.facing);

        UI.vortexFacing.currentDistance = dis;

        var inset = dis - UI.vortexFacing.TURN_BUTTON_INSET;

        UI.vortexFacing.placeElement(UI.vortexFacing.turnLeftElement, inset, facingAngle - 60, s);
        UI.vortexFacing.drawCurvedArrow("vortexTurnLeftCanvas", s, facingAngle, false);

        UI.vortexFacing.placeElement(UI.vortexFacing.turnRightElement, inset, facingAngle + 60, s);
        UI.vortexFacing.drawCurvedArrow("vortexTurnRightCanvas", s, facingAngle, true);

        //Confirm sits on the ring proper, dead ahead of the facing arrow. Its glyph is DRAWN like
        //the other two, but unlike them it is not rotated - a confirm that leans over reads as
        //broken, and it is the one part of the control that should look the same at every facing.
        UI.vortexFacing.placeElement(UI.vortexFacing.confirmElement, dis, facingAngle, s);
        UI.vortexFacing.drawConfirmIcon("vortexConfirmCanvas", s);
    },

    /* THE TURN ARROWS ARE DRAWN, NOT ROTATED.

       They used to be img/vortexleft.png / img/vortexright.png fed through
       UI.shipMovement.drawUIElement, whose drawAndRotate spins the bitmap inside its canvas. That
       works, but you can SEE it: rotating a 40px raster resamples it, so every facing except 0
       came out soft and the curve's edges crawled as it stepped round. An arc re-drawn at the
       target angle is rasterised fresh each time and is crisp at all six, costs no asset, retunes
       from one colour token, and scales to whatever box size it is handed.

       $base is the facing angle; $clockwise picks which way the arrow curls (right = clockwise =
       facing + 1). The glyph is a $SWEEP-degree arc centred on $base with a solid head at the
       leading end, so the pair are mirror images about the facing axis - exactly what the two
       bitmaps were.

       ANGLES are graphics.js's convention throughout: degrees clockwise from east, screen y down.
       That is also what canvas arc() measures, so they pass through with no conversion, and
       getPointInDirection takes the NEGATED angle (compare drawCircleSegment / drawArrow). */
    /* ---------------- TURN-ARROW GLYPH: THE KNOBS ----------------
       All of these are meant to be retuned by eye - nothing else reads them, and nothing about the
       vortex RULES depends on any of them. Fractions are of BUTTON_SIZE, so the glyph keeps its
       proportions if that changes. What each one does:

         ARROW_SWEEP      how much arc the shaft covers, in degrees, head included. THIS IS THE
                          "make the shaft longer/shorter" knob. ~135 is a comma, ~215 is a
                          three-quarter loop. Does not affect how much room the glyph needs.
         ARROW_TILT       extra rotation of each glyph AWAY from the Ok arrow between them - the
                          left one anticlockwise, the right one clockwise. 0 points both straight
                          along the facing. Purely cosmetic; the buttons themselves do not move
                          (that is the +/-60 in drawButtons).
         ARROW_RADIUS     how far the shaft sits from the middle of its button.
         ARROW_THICKNESS  shaft line width.
         ARROW_HEAD_LEN   arrowhead length. Also sets how much arc the head eats: the stroke stops
                          half a head-length short so the head does not sit on a round line cap.
         ARROW_HEAD_HALF  arrowhead half-width, i.e. how chunky the point is.
         ARROW_OPACITY    0-1, how solid the pair are against the map. 1 is fully opaque. Applied
                          as globalAlpha so it fades the shaft, the head AND the drop shadow
                          together - which is what you want, a half-faded glyph with a solid
                          outline looks like a rendering fault.
         ARROW_ROTATION   degrees added to BOTH glyphs in the SAME screen direction, i.e. the
                          "just turn them a bit" knob. Distinct from ARROW_TILT, which is MIRRORED
                          (it splays the pair apart); this one rotates the pair rigidly and so
                          survives being set to anything. Positive is clockwise on screen, the
                          same sense as a facing step.

       ⚠️ THE ONE REAL CONSTRAINT: the head tip is the outermost part of the glyph, and the canvas
       clips at half the box. Tip radius works out at roughly
       ARROW_RADIUS + ARROW_HEAD_LEN * 0.7, which must stay under 0.5 with a little left over for
       the 2px glow. At the values below that is 0.28 + 0.17 = 0.45, so there is room; push
       ARROW_RADIUS or ARROW_HEAD_LEN much past that and the point gets shaved off. Growing
       BUTTON_SIZE is the way to make the whole glyph bigger - the canvas is resized from here, so
       the width/height in game.php's markup do not need to match. */
    ARROW_SWEEP: 200,
    ARROW_TILT: 15,
    ARROW_RADIUS: 0.28,
    ARROW_THICKNESS: 0.13,
    ARROW_HEAD_LEN: 0.3,
    ARROW_HEAD_HALF: 0.22,
    ARROW_OPACITY: 0.7,
    ARROW_ROTATION: 0,

    drawCurvedArrow: function drawCurvedArrow(canvasId, box, base, clockwise) {
        var ctx = window.graphics.getCanvas(canvasId);
        if (!ctx) return;

        /* HiDPI backing store. This is the point of drawing rather than rotating a bitmap, so do
           not drop it and leave the arc soft again; min(dpr, 2) is the same cap webglScene's
           renderer uses. Assigning width RESETS the context, hence the setTransform every pass. */
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var el = ctx.canvas;
        if (el.width !== box * dpr) {
            el.width = box * dpr;
            el.height = box * dpr;
            el.style.width = box + "px";
            el.style.height = box + "px";
        }
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, box, box);

        var cx = box / 2;
        var cy = box / 2;
        var r = box * UI.vortexFacing.ARROW_RADIUS;
        var headLen = box * UI.vortexFacing.ARROW_HEAD_LEN;
        var headHalf = box * UI.vortexFacing.ARROW_HEAD_HALF;
        var half = UI.vortexFacing.ARROW_SWEEP / 2;

        //Splay the pair apart: the left glyph leans anticlockwise, the right one clockwise, away
        //from the confirm button sitting between them. Cosmetic - it does not move either button.
        base += clockwise ? UI.vortexFacing.ARROW_TILT : -UI.vortexFacing.ARROW_TILT;
        //...and then turn the pair RIGIDLY, same direction for both. The manual "nudge the arrows
        //round" knob; ARROW_TILT above cannot do this because it is mirrored.
        base += UI.vortexFacing.ARROW_ROTATION;

        var start = clockwise ? base - half : base + half;
        var end = clockwise ? base + half : base - half;
        var headDir = end + (clockwise ? 90 : -90); //tangent at the leading end

        //Stop the stroke where the head begins, or the head sits on top of a line cap.
        var backoff = mathlib.radianToDegree((headLen * 0.5) / r);
        var strokeEnd = clockwise ? end - backoff : end + backoff;

        var colour = UI.vortexFacing.arrowColour();

        ctx.strokeStyle = colour;
        ctx.fillStyle = colour;
        ctx.lineWidth = Math.max(3, box * UI.vortexFacing.ARROW_THICKNESS);
        ctx.lineCap = "round";
        //One globalAlpha for the whole glyph rather than an rgba() colour: it fades the shaft, the
        //head and the drop shadow together. Fading only the fill leaves a solid black outline
        //around a ghost, which reads as a rendering fault rather than a design.
        ctx.globalAlpha = UI.vortexFacing.ARROW_OPACITY;
        //Lifts the glyph off whatever map is behind it.
        ctx.shadowColor = "rgba(0,0,0,0.9)";
        ctx.shadowBlur = 2;

        ctx.beginPath();
        ctx.arc(cx, cy, r, mathlib.degreeToRadian(start), mathlib.degreeToRadian(strokeEnd), !clockwise);
        ctx.stroke();

        var tip = mathlib.getPointInDirection(r, -end, cx, cy, true);
        tip = mathlib.getPointInDirection(headLen * 0.5, -headDir, tip.x, tip.y, true);
        var back = mathlib.getPointInDirection(headLen, -(headDir + 180), tip.x, tip.y, true);
        var b1 = mathlib.getPointInDirection(headHalf, -(headDir + 90), back.x, back.y, true);
        var b2 = mathlib.getPointInDirection(headHalf, -(headDir - 90), back.x, back.y, true);

        ctx.beginPath();
        ctx.moveTo(tip.x, tip.y);
        ctx.lineTo(b1.x, b1.y);
        ctx.lineTo(b2.x, b2.y);
        ctx.closePath();
        ctx.fill();
    },

    /* ---------------- THE CONFIRM BUTTON: THE KNOBS ----------------
       Replaced the word "Ok" (user request 2026-08-21). Drawn, not an image and not text, for the
       same three reasons the turn arrows are: it stays crisp at any size, it costs no asset, and
       it takes its yellow from the same one token everything else in the vortex livery uses.

       A SOLID DISC with a dark tick punched out of it, rather than a bare yellow tick. A thin
       stroked tick on a transparent ground is what the word replaced in the first place - it
       disappears over bright map terrain and gives the finger nothing to aim at. A filled disc
       reads as a button at a glance, is a real 40px target on touch, and its silhouette is
       distinct from the two curved arrows flanking it, which matters more than the glyph itself
       when all three are the same colour.

         CONFIRM_DISC       disc radius as a fraction of the button box. 0.5 would touch the edges;
                            leave room for the ring and the glow.
         CONFIRM_RING       outline width in px, drawn just inside the disc edge in the same dark
                            ink as the tick. Set to 0 for a flat disc.
         CONFIRM_TICK_LEN   overall tick width as a fraction of the box.
         CONFIRM_TICK_WIDTH tick stroke width as a fraction of the box.
         CONFIRM_INK        the dark colour of the tick and ring. Anything that reads on yellow.
         CONFIRM_OPACITY    0-1, as ARROW_OPACITY - one globalAlpha over disc, tick and shadow. */
    CONFIRM_DISC: 0.40,
    CONFIRM_RING: 2,
    CONFIRM_TICK_LEN: 0.42,
    CONFIRM_TICK_WIDTH: 0.11,
    CONFIRM_INK: "#1a1206",
    CONFIRM_OPACITY: 0.8,

    drawConfirmIcon: function drawConfirmIcon(canvasId, box) {
        var ctx = window.graphics.getCanvas(canvasId);
        if (!ctx) return;

        //Same HiDPI backing store as drawCurvedArrow - assigning width RESETS the context, hence
        //the setTransform on every pass.
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var el = ctx.canvas;
        if (el.width !== box * dpr) {
            el.width = box * dpr;
            el.height = box * dpr;
            el.style.width = box + "px";
            el.style.height = box + "px";
        }
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, box, box);

        var self = UI.vortexFacing;
        var cx = box / 2;
        var cy = box / 2;
        var r = box * self.CONFIRM_DISC;

        ctx.globalAlpha = self.CONFIRM_OPACITY;
        ctx.shadowColor = "rgba(0,0,0,0.9)";
        ctx.shadowBlur = 3;

        ctx.fillStyle = self.arrowColour();
        ctx.beginPath();
        ctx.arc(cx, cy, r, 0, Math.PI * 2);
        ctx.fill();

        //Everything from here is INSIDE the disc, so the shadow would only muddy it.
        ctx.shadowBlur = 0;
        ctx.strokeStyle = self.CONFIRM_INK;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        if (self.CONFIRM_RING > 0) {
            ctx.lineWidth = self.CONFIRM_RING;
            ctx.beginPath();
            ctx.arc(cx, cy, r - self.CONFIRM_RING / 2, 0, Math.PI * 2);
            ctx.stroke();
        }

        /* The tick, as three points across a box CONFIRM_TICK_LEN wide. Proportions are the
           conventional ones - the short arm about 40% of the long one and the elbow sitting below
           centre - which is what stops it reading as a lopsided V. */
        var len = box * self.CONFIRM_TICK_LEN;
        ctx.lineWidth = Math.max(2, box * self.CONFIRM_TICK_WIDTH);

        ctx.beginPath();
        ctx.moveTo(cx - len * 0.46, cy + len * 0.02);
        ctx.lineTo(cx - len * 0.12, cy + len * 0.32);
        ctx.lineTo(cx + len * 0.46, cy - len * 0.34);
        ctx.stroke();
    },

    /* Read from tokens.css rather than hard-coded, so the control's yellow cannot drift from the
       one the vortex marker and its hex use - styles/tokens.css is the single :root block and the
       only place a colour is defined. Cached: the value cannot change without a reload. */
    arrowColour: function arrowColour() {
        //The exit livery is a flat literal rather than a token: --fv-custom IS the entrance's
        //yellow, and tokens.css carries no cyan matching #00b8e6 (the same reason lobby.css
        //writes it as a literal beside its two siblings in tactical.css).
        if (UI.vortexFacing.isExit()) return UI.vortexFacing.EXIT_COLOUR;

        if (!UI.vortexFacing._arrowColour) {
            var token = "";
            try {
                token = getComputedStyle(document.documentElement).getPropertyValue("--fv-custom");
            } catch (e) {
                token = "";
            }
            UI.vortexFacing._arrowColour = (token && token.trim()) || "#cccc00";
        }
        return UI.vortexFacing._arrowColour;
    },

    _arrowColour: null,

    /* ---------------- THE EXIT LIVERY (REINFORCEMENTS_PLAN.md Stage 4) ----------------
       The same control, in the other direction. An EXIT is declared by a reinforcement that
       is still in hyperspace, and its facing is the doorway OUT rather than the mouth units
       cross inbound - so everything that says 'this is a vortex' swaps colour, and the arrow
       swaps asset:

         yellow --fv-warn + directionOfVortex.png       an ENTRANCE     - the mouth crossed inbound
         cyan   #00b8e6   + directionOfVortexEntry.png  an EXIT - the way units come out

       #00b8e6 is FV's established 'not here yet' cyan, shared with the blue Jump Point marker,
       the fleet list's hyperspace rows and the [Deploys on Turn N] header. Do not introduce a
       second blue.

       The two arrow assets are the SAME glyph mirrored inside its own bounding box, so they
       share MARKER_ARROW_SCALE and need no second constant - see SpawnJumpPointExit.php. */
    EXIT_COLOUR: '#00b8e6',
    EXIT_ARROW: './img/directionOfVortexEntry.png',
    ENTRANCE_ARROW: './img/directionOfVortex.png',

    /* Is the transaction in progress an EXIT? ONE place holds the test, because the preview
       hex, the arrow asset and all three button glyphs ask it. Read off the pending payload
       rather than any stored state, so a closed control answers false and the entrance livery is
       always the default. */
    isExit: function isExit() {
        return Boolean(UI.vortexFacing.pending && UI.vortexFacing.pending.exit);
    },

    /* drawUIElement's geometry without its bitmap step - all three buttons are placed with this
       and then painted (or not) separately, because none of them is an image any more. Same angle
       convention: degrees clockwise from east, screen y down, negated into getPointInDirection.
       x/y are implicitly 0 because only the delta from them was ever used; the container itself is
       anchored to the hex by reposition(). */
    placeElement: function placeElement(e, dis, angle, box) {
        var pos = mathlib.getPointInDirection(dis, -angle, 0, 0);
        e.css("top", pos.y - box * 0.5 + "px").css("left", pos.x - box * 0.5 + "px");
        e.css("width", box + "px").css("height", box + "px");
        e.show();
    },

    //Game-space position of the pending vortex hex - what PhaseStrategy.positionVortexFacingUI
    //converts to viewport coordinates on every zoom and scroll.
    getPosition: function getPosition() {
        return UI.vortexFacing.gamePosition;
    },

    reposition: function reposition(position) {
        if (!UI.vortexFacing.uiElement || !UI.vortexFacing.pending) return;

        //The button ring is measured off the hex's on-screen size, so a ZOOM has to re-lay it out
        //and not just move the container. Guarded on the computed distance rather than on the zoom
        //itself so a scroll (which cannot change it) costs one subtraction.
        if (UI.vortexFacing.buttonDistance() !== UI.vortexFacing.currentDistance) {
            UI.vortexFacing.drawButtons();
        }

        var current = UI.vortexFacing.currentPosition;
        if (current && current.x === position.x && current.y === position.y) return;

        UI.vortexFacing.uiElement.css("top", position.y + "px").css("left", position.x + "px");
        UI.vortexFacing.currentPosition = position;
    }

};
