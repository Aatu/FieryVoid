'use strict';

window.BallisticIconContainer = function () {

	function BallisticIconContainer(coordinateConverter, scene) {
		this.coordinateConverter = coordinateConverter;
		this.scene = scene;
		this.zoomScale = 1;
		this.ballisticIcons = [];
		this.ballisticLineIcons = [];
		this.hexNumberIcons = [];
		this.hexNumbersGenerated = false;

		/* Scene objects that depict something STABLE - a terrain unit's footprint, a splash area, a
		   reinforcement marker - keyed so they are rebuilt only when what they depict changes.
		   consumeGamedata runs on every server poll AND on every hex targeted, ship targeted and
		   ballistic-line toggle; without this, clicking a target hex tore down and rebuilt every
		   terrain overlay on the map. See syncSceneObject. */
		this.sceneObjects = new Map();
		this.iconsVisible = true;

		// Track lines visibility state explicitly rather than inferring from existing sprites
		this.friendlyLinesVisible = false;
		this.enemyLinesVisible = false;
	}

	BallisticIconContainer.prototype.consumeGamedata = function (gamedata, iconContainer, replayData = null) {
		this.ballisticIcons.forEach(icon => {
			if (gamedata.gamephase !== 1) icon.launchPosition = [];
			icon.used = false;
		});

		this.ballisticLineIcons.forEach(icon => icon.used = false);
		this.sceneObjects.forEach(entry => entry.used = false);

//		const ballistics = replayData ?? weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');
// GTS_Change
		const ballistics = replayData ?? weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');

		//Collected in the same pass, and under exactly the same turn/phase/masking filter the
		//markers themselves get, so the facing arrows can never outlive or precede their hex.
		const jumpPointOrders = [];
		/* REINFORCEMENTS_PLAN.md Stage 4 - EXIT declarations, collected separately because
		   almost nothing about them shares the entrance path: they are drawn BLUE, their arrow uses the
		   outward asset, and - the reason they cannot go through createBallisticIcon at all - their
		   SHOOTER IS IN HYPERSPACE. That unit has no icon (shouldBeHidden keeps it off the board), so
		   createBallisticIcon's `if (!shooterIcon) return;` would drop the marker outright; and if it
		   ever DID have one, the launch sprite and the ballistic line would both be drawn from its
		   'start' movement row at its slot's deployment-box centre - a bright line, on the map, from
		   a ship that is not there, to the hex it is about to arrive in. */
		const exitOrders = [];

		ballistics.forEach(ballistic => {
			if (ballistic.turn === gamedata.turn || !replayData) {
				//Suppress Gravitic Mine icons/lines in live phase 3: the mine has already detonated in
				//phase 5 PreFire, so it should not render as a pending ballistic. We must do this at the
				//loop level (not just in createBallisticIcon) so existing icons left over from a Replay
				//session aren't kept alive by updateBallisticIcon.
				if (gamedata.gamephase === 3 && !replayData) {
					const shooterIcon = iconContainer.getById(ballistic.shooterid);
					if (shooterIcon) {
						const weapon = shipManager.systems.getSystem(shooterIcon.ship, ballistic.weaponid);
						const modeName = weapon?.firingModes?.[ballistic.firingMode] || null;
						if (modeName === 'Gravitic Mine' || modeName === 'Standard - GN' || modeName === 'Priority - GN') return;
					}
				}
				/* ⭐ WALKERS_OF_SIGMA_PLAN.md 3.9 - A SENSOR CHARGE WAYPOINT IS NOT A SHOT, and the
				   generic hex-ballistic treatment insists that it is: getAllFireOrdersForAllShipsForTurn
				   admits ANY normal order with targetid -1, so every waypoint drew a red "incoming fire"
				   hex and a white arrow back to the shooter - a dozen of them fanning out of one hull,
				   describing a straight line to each corner of a course that is not straight and does not
				   start there. generateSensorChargeCourses below draws the real thing: the legs, the
				   direction arrows, the green markers and the reachable fan (user request 2026-09-06).

				   ⚠️ SUPPRESSED AT THE LOOP LEVEL, not inside createBallisticIcon, for the same reason
				   the Gravitic Mine above is: createOrUpdateBallistic would otherwise find an existing
				   icon and keep it alive through updateBallisticIcon, so icons left over from a Replay
				   session would never be swept. */
				if (ballistic.damageclass === 'sensorcharge') return;

				if (ballistic.damageclass === 'jumppoint' && ballistic.x !== "null" && ballistic.y !== "null") {
					/* ⚠️ NOT FOR A FIXED JUMP GATE (JUMP_GATES_PLAN.md Stage 3). generateJumpPointArrows
					   draws an arrow at facing (firingMode - 1), and on a gate the firing mode is the
					   programmed OPEN DURATION in turns - so "Open 3 turns" would be rendered as
					   facing 2, i.e. the duration drawn as a direction, and usually the wrong one.
					   A GATE SHOWS NO MOUTH ARROW AT ALL until the vortex actually opens - the gate
					   blueprint dropped $facingArrow (user ruling 2026-08-24, see JumpgateCapital.php),
					   so the arrow now belongs to the SpawnJumpPoint alone. Suppressing it here is
					   therefore not just "redundant", it is the only thing keeping a duration off the
					   map as a direction. */
					const gateIcon = iconContainer.getById(ballistic.shooterid);
					if (!gateIcon || !gamedata.isJumpGate(gateIcon.ship)) {
						jumpPointOrders.push(ballistic);
					}
				}

				/* An exit draws itself, below, and takes NO part in the icon or line pipeline - see
				   the note on exitOrders above. Returned from the forEach rather than filtered inside
				   createBallisticIcon, so no half-built icon record is ever created for one. */
				if (ballistic.damageclass === 'jumpexit') {
					if (ballistic.x !== "null" && ballistic.y !== "null") exitOrders.push(ballistic);
					return;
				}

				createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn, !!replayData);
				createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn, !!replayData);
			}
		});

		this.ballisticIcons = this.ballisticIcons.filter(icon => {
			if (!icon.used) {
				if (icon.launchSprite) this.scene.remove(icon.launchSprite.mesh);
				if (icon.targetSprite) {
					// getById is a bare lookup on ShipIconContainer.iconsAsObject and returns undefined
					// for an id it does not hold, so this used to be a crash waiting for a target ship
					// to leave the board between polls. Fall back to the scene, which is where a sprite
					// with no parent icon was added.
					const targetIcon = icon.targetId !== -1 ? iconContainer.getById(icon.targetId) : null;

					if (targetIcon) targetIcon.mesh.remove(icon.targetSprite.mesh);
					else this.scene.remove(icon.targetSprite.mesh);
				}

				// scene.remove() does not free anything - THREE frees a material on dispose(). Every
				// sprite carries its OWN cloned ShaderMaterial (webglSprite.create), and these are
				// rebuilt on every poll, so skipping this leaked one material per hex per refresh.
				// Textures are shared statics (see BallisticSprite's caches) and must NOT be disposed.
				releaseSprite(icon.launchSprite);
				if (icon.targetSprite !== icon.launchSprite) releaseSprite(icon.targetSprite);

				return false;
			}
			return true;
		});

		// Phase 3: rebuild Flare hex grid from mode2FiredThisTurn since ballistic order is gone GTS_Change
		if (gamedata.gamephase === 3 && !replayData) {
			gamedata.ships.forEach(ship => {
				ship.systems.forEach(system => {
					if (system.name !== 'FlareGenerator') return;
					if (system.mode2FiredThisTurn !== gamedata.turn) return;

					const shooterIcon = iconContainer.getById(ship.id);
					if (!shooterIcon) return;

					const targetPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
					if (!targetPosition) return;

					const fakeBallistic = {
						id: 'flare_' + ship.id + '_' + system.id,
						shooterid: ship.id,
						targetid: ship.id,
						weaponid: system.id,
						firingMode: 2,
						turn: gamedata.turn,
						type: 'ballistic',
						damageclass: 'electromagnetic'
					};

					createOrUpdateBallistic.call(this, fakeBallistic, iconContainer, gamedata.turn, false);
				});
			});
		}

		generateBallisticLines.call(this);

		generateBallisticLines.call(this);
		generateTerrainHexes.call(this, gamedata);
		generateEdfNetHexes.call(this, gamedata);
		generateSensorChargeCourses.call(this, gamedata);
		generateReinforcementHexes.call(this, gamedata);
		generateJumpPointArrows.call(this, jumpPointOrders);
		generateExitHexes.call(this, gamedata, exitOrders);
		pruneSceneObjects.call(this);
	};

	//Material only: geometry is shared per size (webglSprite's cache) and textures are shared statics.
	function releaseSprite(sprite) {
		if (sprite) sprite.destroy();
	}

	/* A scene object rebuilt only when its `signature` changes. `build` returns
	   { object, release } or null for "nothing to draw"; `release` frees whatever the object owns. */
	function syncSceneObject(key, signature, build) {
		const existing = this.sceneObjects.get(key);

		if (existing && existing.signature === signature) {
			existing.used = true;
			return existing.object;
		}

		if (existing) releaseSceneObject.call(this, existing);

		const built = build();

		if (!built) {
			this.sceneObjects.delete(key);
			return null;
		}

		built.object.visible = this.iconsVisible;
		this.scene.add(built.object);
		this.sceneObjects.set(key, { object: built.object, release: built.release, signature: signature, used: true });

		return built.object;
	}

	function releaseSceneObject(entry) {
		this.scene.remove(entry.object);
		entry.release(entry.object);
	}

	//Whatever nothing claimed this pass has gone from the board.
	function pruneSceneObjects() {
		this.sceneObjects.forEach((entry, key) => {
			if (entry.used) return;

			releaseSceneObject.call(this, entry);
			this.sceneObjects.delete(key);
		});
	}

	function generateBallisticLines() {
		const oldIcons = this.ballisticLineIcons;
		// Removed reliance on checking existing icons' visibility:
		// const isFriendlyLinesVisible = oldIcons.some(icon => icon.lineSprite?.isVisible && icon.isFriendly);
		// const isEnemyLinesVisible = oldIcons.some(icon => icon.lineSprite?.isVisible && !icon.isFriendly);

		this.ballisticLineIcons = oldIcons.filter(icon => {
			if (!icon.used) {
				if (icon.lineSprite) {
					this.scene.remove(icon.lineSprite.mesh);
					icon.lineSprite.destroy(); //same as the hex sprites: remove() frees nothing
				}
				return false;
			}

			if (icon.lineSprite) {
				const shouldBeVisible = icon.isFriendly ? this.friendlyLinesVisible : this.enemyLinesVisible;
				icon.lineSprite[shouldBeVisible ? 'show' : 'hide']();
				icon.lineSprite.isVisible = shouldBeVisible;
			}

			return true;
		});
	}

	/* Fill and border for a hex region, matched to the per-hex sprite textures they replace: the same
	   colours at the same alphas BallisticSprite.getFillColorByType / getStrokeColorByType bake into
	   the 512px hex textures, so a blanket reads as the tint the individual hexes used to.

	   Tune HERE, not in the colours: a THREE colour carries no alpha, and setStyle parses a fourth
	   component in "rgb(r,g,b,a)" and then silently DISCARDS it, so softening an edge means the
	   opacity constant, never the colour string. */
	const HEX_REGION_COLOURS = {
		hexOrange: 0xfa6e05,
		hexRed: 0xe6140a,
		hexBlue: 0x00b8e6,
		hexGreen: 0x00cc00,
		hexYellow: 0xffff00,
		hexPurple: 0x7f00ff,
		hexWhite: 0xffffff,
		//⚠️ MIRROR of the `rgb(20,80,128)` ShipIcon.showWeaponArc uses for an ordinary firing arc -
		//EDIT BOTH. It is here so a hex blanket can make the same statement an arc does.
		hexArcBlue: 0x145080
	};
	const HEX_REGION_FILL_OPACITY = 0.10;   //the 0.10 fill baked into the hex textures
	const HEX_REGION_BORDER_OPACITY = 0.40; //lifted by hand; 0.40 is what the textures stroke at
	const SPLASH_REGION_DIM = 0.7;          //splash hexes were drawn at sprite opacity 0.7 - "a bit less bright"
	const HEX_REGION_Z = -101;              //just behind the hex sprites at -100, well clear of the grid at -500

	/* A region's rim has to be the same THICKNESS as the rim of the hex sprite sitting in the middle
	   of it, and a sprite's rim is baked into its texture - so it is a fixed number of game units and
	   it scales with the map. A one-device-pixel line (the ship arcs' convention, and what these
	   regions used at first) is 3.4x too thin at zoom 1 and drifts further at every other zoom, which
	   reads as two different kinds of edge rather than one shape.

	   Derived rather than eyeballed: BallisticSprite hands HexagonTexture.renderHexGrid a lineWidth
	   of 10 on a 512px canvas whose hexagon has a circumradius of 512/4/cos(30) = 147.8px, and that
	   texture is mapped onto a hexagon of circumradius Config.HEX_SIZE. So the stroke is
	   10 x HEX_SIZE / 147.8 game units - 3.383 at the standard HEX_SIZE of 50 - and this follows
	   HEX_SIZE if it ever changes. Read lazily: Config comes from an inline script in game.php that
	   has not necessarily run when this file is evaluated. */
	const SPRITE_TEXTURE_SIZE = 512;  //BallisticSprite.TEXTURE_SIZE
	const SPRITE_STROKE_PIXELS = 10;  //the lineWidth it passes to renderHexGrid

	function getHexSpriteStrokeWidth() {
		const textureHexRadius = SPRITE_TEXTURE_SIZE / 4 / Math.cos(30 * Math.PI / 180);

		return SPRITE_STROKE_PIXELS * (window.Config.HEX_SIZE / textureHexRadius);
	}

	/* A patch of grid hexes as ONE blanket polygon with a hex-true outline, instead of a textured
	   quad per hex. Hexes in a radius-N patch grow with N SQUARED while the boundary grows with N:
	   a radius-5 splash is 91 hexes but 66 boundary points, and it draws in two calls rather than 91.

	   The region's own coordinates come out as game-space deltas from the centre hex (see
	   HexRegion.buildRegionFromHexes), so placing it is just the centre hex's game position. No
	   rotation, so none of ShipIcon's facing-rounding hazards apply; no grid-lock either, since these
	   hang off the scene rather than off an icon that rescales with zoom. */
	function buildHexRegionOverlay(centreHex, hexes, type, dim) {
		const colour = HEX_REGION_COLOURS[type];
		if (colour === undefined) return null; //hexClear and the 'ship' fallback have no region form

		const loops = window.HexRegion.buildRegionFromHexes(centreHex, hexes, this.coordinateConverter.getHexDistance());
		if (!loops.length) return null;

		const overlay = window.HexRegion.buildOverlay(
			loops,
			colour, HEX_REGION_FILL_OPACITY * dim,
			colour, HEX_REGION_BORDER_OPACITY * dim, getHexSpriteStrokeWidth()
		);
		const centre = this.coordinateConverter.fromHexToGame(centreHex);

		overlay.position.set(centre.x, centre.y, HEX_REGION_Z);

		return overlay;
	}

	/* Every hex a Terrain unit occupies, mirroring the server's authoritative
	   SpecialWeapons::getTerrainOccupiedHexes (specialWeapons.php): the centre hex, plus either an
	   irregular hexOffsets shape rotated to facing, or the FULL DISC of radius Huge.

	   The disc is the correctness half of this change. The old per-sprite version called
	   mathlib.getPerimeterHexes, which returns only the ring at exactly distance == Huge, so a
	   radius-3 moon drew its centre hex and its rim with two unmarked rings in between - while the
	   server counted all 37 hexes as occupied. */
	function getTerrainOccupiedHexes(ship, position, facing) {
		const hexes = [{ q: position.q, r: position.r }];

		if (ship.hexOffsets && ship.hexOffsets.length) {
			ship.hexOffsets.forEach(offset => hexes.push(mathlib.getRotatedHex(position, offset, facing)));
		} else if (ship.Huge > 0) {
			mathlib.getNeighbouringHexes(position, ship.Huge).forEach(hex => hexes.push(hex));
		}

		return hexes;
	}

	function generateTerrainHexes(gamedata) {
		if (gamedata.gamephase === -1) return; //Don't bother during Deployment phase.

		gamedata.ships.filter(ship => ship.Enormous && ship.shipSizeClass == 5 && !shipManager.isDestroyed(ship)).forEach(ship => {
			const position = shipManager.getShipPosition(ship);
			const move = shipManager.movement.getLastCommitedMove(ship);
			const facing = move ? move.facing : 0;
			const hexes = getTerrainOccupiedHexes(ship, position, facing);

			//position + facing fix the footprint, so an unmoved moon is never rebuilt
			syncSceneObject.call(this, 'terrain:' + ship.id, `${position.q},${position.r}|${facing}|${hexes.length}`, () => {
				const overlay = buildHexRegionOverlay.call(this, position, hexes, 'hexWhite', 1);

				return overlay && { object: overlay, release: window.HexRegion.dispose };
			});
		});
	}

	/* WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.7, Stage 7) - the Energy Draining NET field.

	   ⭐ WHY THIS IS NOT ShipIcon.showEdfField. Every other Energy Draining Field is a disc round
	   the unit projecting it, so it is drawn as an overlay parented to that unit's icon - which is
	   what keeps two overlapping fields legible as two shapes rather than one blob. A Net's field
	   has no unit at its centre: it is its own hex, the CORRIDOR between two Nets three hexes
	   apart, and the area three or more of them enclose. There is no icon to hang it on, so the
	   server publishes the hexes themselves (gamedata.edfNetHexes) and they are drawn here, in the
	   one container that already knows how to lay a blanket over an arbitrary patch of grid.

	   Purple, matching the EDF disc and the Energy Draining Mine marker: on the board they are the
	   same phenomenon and the rules treat them as one field.

	   ⚠️ gamedata.edfNetHexes is the cheap gate - the server publishes NULL when no Net is on the
	   board, so an ordinary game does one property read. And the hex LIST is the signature, so a
	   formation that has not moved is never rebuilt: syncSceneObject would otherwise re-sweep the
	   region and mint fresh geometry on every poll of every turn.

	   ⚠️⚠️ ONE OVERLAY PER CONNECTED CLUSTER, NOT ONE FOR THE WHOLE SET, and that is a performance
	   requirement rather than a visual one. HexRegion.buildRegionFromHexes sizes its sweep from the
	   FARTHEST hex from the anchor and then tests every hex in that square - so a single overlay
	   covering two Walkers 60 hexes apart would sweep 121 x 121 = 14,641 hexes to draw a handful.
	   Two lone unlinked Nets at opposite corners of the board is an ordinary thing to happen, so
	   this is not a corner case. Splitting on adjacency bounds each sweep by the cluster's own
	   span, which the link rules cap at LINK_RANGE per step.

	   ⚠️ Teams are not distinguished here (the shape is the information, and each cluster belongs
	   to one fleet anyway); anything that needs to know WHOSE field a hex is reads
	   gamedata.edfHexes, which is team-tagged and is what every actual rule uses. */
	function generateEdfNetHexes(gamedata) {
		//⭐ THE LIVE PREVIEW WINS WHEN THERE IS ONE. PhaseStrategy.syncEdfNetPreview recomputes the
		//field from PLOTTED positions while the player is moving, so the corridors and the filled
		//area form as the ship is dragged rather than a commit later (user request, 2026-09-05).
		//It is advisory only - see model/EdfNetLinks.js - and falls back to the server's answer the
		//moment the preview is cleared, which is what every other phase gets.
		const hexes = gamedata.edfNetPreview || gamedata.edfNetHexes;
		if (!hexes || !hexes.length) return;

		edfNetClusters(hexes).forEach(cluster => {
			//The cluster's own first hex anchors it, and the cluster list is built in a stable
			//order - so both the key and the signature stay put while nothing moves.
			const centre = { q: cluster[0].q, r: cluster[0].r };
			const signature = cluster.map(hex => `${hex.q},${hex.r}`).join('|');

			syncSceneObject.call(this, `edfnet:${centre.q},${centre.r}`, signature, () => {
				const overlay = buildHexRegionOverlay.call(this, centre, cluster, 'hexPurple', 1);

				return overlay && { object: overlay, release: window.HexRegion.dispose };
			});
		});
	}

	/* Redraw the Net field ALONE, outside the consumeGamedata pass - which is what the movement
	   preview needs, because plotting a move fires no poll.

	   ⚠️⚠️ IT HAS TO PRUNE ITS OWN STALE CLUSTERS. consumeGamedata clears `used` on everything and
	   calls pruneSceneObjects at the end, so an overlay nothing claimed that pass is reclaimed for
	   free. Nothing does that here, so a cluster that MOVED - which is exactly what happens on
	   every step of a plotted move - would leave its previous shape on the board and the field
	   would smear across the whole plotted path. Marking only this prefix and sweeping only this
	   prefix keeps that fix from touching ballistics, terrain or jump points.

	   Returns whether anything actually changed, so the caller can decide whether to wake the
	   idle-gated render loop (trap 10) rather than requesting a frame on every mouse move. */
	BallisticIconContainer.prototype.refreshEdfNetHexes = function (gamedata) {
		const before = new Map();

		//Clear the claim on this prefix only, and remember what each cluster looked like.
		this.sceneObjects.forEach((entry, key) => {
			if (!key.startsWith('edfnet:')) return;

			before.set(key, entry.signature);
			entry.used = false;
		});

		generateEdfNetHexes.call(this, gamedata);

		let changed = false;

		this.sceneObjects.forEach((entry, key) => {
			if (!key.startsWith('edfnet:')) return;

			if (!entry.used) {
				//Nothing claimed it this pass, so this cluster has gone or moved.
				releaseSceneObject.call(this, entry);
				this.sceneObjects.delete(key);
				changed = true;

				return;
			}

			//A key it did claim: new, or the same anchor with a different shape.
			if (before.get(key) !== entry.signature) changed = true;
		});

		return changed;
	};

	/* The published Net hexes split into groups of touching hexes. A Net field is corridors and
	   filled areas, so each group is compact; what it separates is two FORMATIONS, which can be
	   anywhere relative to each other.

	   Flood fill over mathlib.getNeighbouringHexes, which is the same odd-row offset frame the
	   server's Mathlib walks - so a corridor the server built as contiguous reads as contiguous
	   here. O(hexes), and the whole thing only runs when the board actually has a Net on it. */
	function edfNetClusters(hexes) {
		const remaining = new Map();

		hexes.forEach(hex => remaining.set(`${hex.q},${hex.r}`, hex));

		const clusters = [];

		hexes.forEach(hex => {
			const key = `${hex.q},${hex.r}`;
			if (!remaining.has(key)) return; //already swallowed by an earlier cluster

			const cluster = [];
			const stack = [remaining.get(key)];

			remaining.delete(key);

			while (stack.length) {
				const current = stack.pop();

				cluster.push(current);

				mathlib.getNeighbouringHexes(current, 1).forEach(neighbour => {
					const neighbourKey = `${neighbour.q},${neighbour.r}`;

					if (!remaining.has(neighbourKey)) return;
					stack.push(remaining.get(neighbourKey));
					remaining.delete(neighbourKey);
				});
			}

			clusters.push(cluster);
		});

		return clusters;
	}

	/* ================================================================================
	   SENSOR CHARGE COURSES - WALKERS_OF_SIGMA_PLAN.md 3.9 (Stage 9).

	   A Sensor Charge Transceiver's declaration is a COURSE, not a target, so there is nothing
	   for the ballistic icon layer to draw: no launch hex, no target hex, no unit at the end.
	   Four overlays instead, ALL of them built from the weapon's own getCoursePlan() so that what
	   the player sees and what doMultipleHexFireOrders will accept can never disagree:

	     - the COMMITTED course, one line per leg, with chevrons along it saying which way the
	       charge flies. GREEN once the last waypoint stands on a friendly ship with a transceiver
	       (the charge would be received); YELLOW until then, because a course that ends nowhere
	       does no damage at all - that colour change is the whole rule, shown rather than explained.
	     - the REACHABLE FAN: every hex a further leg could still legally end on, as a blue patch
	       of grid, drawn only while the transceiver is SELECTED.
	     - the MARKERS: a green hex wherever the course did something - manoeuvred, ended so far,
	       or had a unit named on it. See SensorChargeTransceiver.getCourseMarkers for which and why.
	     - the BUDGET, two rows of figures at the head of the course, also SELECTED-only.

	   ⚠️ THE FAN WAS SIX LINES UNTIL 2026-09-06 and is now hexes, at the user's request: a line
	   asks the player to judge whether a hex centre sits on it, and the answer to "can I click
	   there?" is a hex. The cost that argued for lines is real but small - ~96 hexes over a
	   41x41 sweep, and only when the weapon is selected AND the course changed (syncSceneObject's
	   signature covers both), which is a handful of rebuilds per turn rather than one per poll.
	   ================================================================================ */
	const SCT_COURSE_COLOUR_OPEN = 0xffff00;      //no receiver at the end yet - the charge is lost
	const SCT_COURSE_COLOUR_RECEIVED = 0x00cc00;  //ends on a friendly transceiver
	const SCT_COURSE_OPACITY = 0.85;
	const SCT_COURSE_Z = -4;                      //just above the ballistic lines at -5

	/* ⭐⭐ THE SIX NUMBERS THAT SIZE THIS OVERLAY ARE ALL IN THIS BLOCK, and they are meant to be
	   edited by hand - every one was set by eye and then cut on the first play test (2026-09-06).
	   Nothing else changes when you move one: they feed the builders below directly, and each is
	   expressed in HEXES or in canvas pixels, so it holds at every zoom level.

	     SCT_ARROW_SIZE     how long a chevron's barbs are, in hexes. ⭐ THE arrow knob.
	     SCT_ARROW_EVERY    hexes between chevrons - raise it for fewer, sparser arrows.
	     SCT_ARROW_ANGLE    how wide the chevron opens. Below ~20 it reads as a line, above ~45 as a T.
	     SCT_LABEL_SCALE    how big the whole budget read-out is, in hexes. ⭐ THE font knob.
	     SCT_LABEL_FONT_PX  glyph size WITHIN the canvas, i.e. text against padding. Leave this alone
	                        unless the rows crowd each other - SCT_LABEL_SCALE is the one to reach for.
	     SCT_FAN_DIM        how strongly the reachable fan tints its hexes (1 = the standard hex
	                        blanket, as the terrain and Net overlays use).                            */

	/* THE DIRECTION ARROWS. A course crosses itself, doubles back and ends nowhere in particular,
	   so which way round it is flown is not something the geometry says on its own - and it decides
	   which end has to hold the receiver. One chevron every SCT_ARROW_EVERY hexes, measured BACK
	   from each waypoint so every leg finishes with one pointing into its corner. */
	const SCT_ARROW_EVERY = 2;                    //hexes between chevrons
	const SCT_ARROW_SIZE = 0.12;                  //barb length, as a fraction of one hex
	const SCT_ARROW_ANGLE = 32;                   //degrees each barb sweeps off the reverse heading

	/* THE BUDGET READ-OUT, in the style of the LoS ruler's distance figure (user request): bold
	   white on a shadow, sized in world units so it scales with the map like everything else on it.
	   ⚠️ z > 100 IS THE REQUIREMENT, not z > 0 - the head of a course is exactly the sort of hex
	   that holds a stack of ships, and a selected or active-mover hull sits at +100 for a whole
	   phase (arch_map_z_planes). 130 clears that and the ini badge at +120, and still loses to the
	   +499 hover lift, which is a bring-to-front the player asked for. */
	const SCT_LABEL_Z = 130;
	const SCT_LABEL_TEXTURE = 128;                //canvas side in px - the RESOLUTION, not the size
	const SCT_LABEL_SCALE = 0.7;                  //sprite side, in hexes
	const SCT_LABEL_FONT_PX = 38;                 //glyph size within that canvas

	/* THE REACHABLE FAN reads in the ordinary WEAPON ARC BLUE (user request 2026-09-06) - the
	   `rgb(20,80,128)` ShipIcon.showWeaponArc paints every firing arc with. It is the same statement
	   those arcs make ("this is where this weapon can reach"), so it should not have a colour of its
	   own; the cyan it launched with belongs to "not here yet" markers instead.
	   ⚠️ Arc blue is much darker than that cyan, so it needs more than the standard hex-blanket
	   alpha to read at all over the map - hence SCT_FAN_DIM. */
	const SCT_FAN_DIM = 2.2;

	//A marker's word sits ON its green hex, so it is drawn in the same green - which is how every
	//other coloured hex marker on this map pairs its tint with its label.
	const SCT_MARKER_TEXT_COLOUR = '#00cc00';

	/* EVERY scene-object key prefix this feature owns. refreshSensorChargeCourses marks and sweeps
	   BY PREFIX, because it runs outside the consumeGamedata pass and that pass is the only thing
	   that clears `used` on everything - so a prefix left out of this list is an overlay that never
	   goes away. ('sct:' does not shadow the rest: 'sctray:' does not start with 'sct:'.) */
	const SCT_KEY_PREFIXES = ['sct:', 'sctray:', 'sctmark:', 'sctfig:'];

	/* A chain of LineSprites as one scene object. `segments` is [{from, to}] in HEX coordinates;
	   the conversion to game space happens here so callers only ever speak hexes.
	   ⚠️ The release function disposes geometry AS WELL AS material: LineSprite.destroy() frees
	   only the material, and each sprite builds its own PlaneGeometry. */
	function buildLineChain(segments, colour, opacity, z, arrows) {
		if (!segments.length) return null;

		const group = new THREE.Group();
		const width = getHexSpriteStrokeWidth();
		const sprites = [];

		const addSprite = (from, to) => {
			const sprite = new window.LineSprite(from, to, width, z, colour, opacity);

			sprites.push(sprite);
			group.add(sprite.mesh);
		};

		segments.forEach(segment => {
			const from = this.coordinateConverter.fromHexToGame(segment.from);
			const to = this.coordinateConverter.fromHexToGame(segment.to);

			addSprite(from, to);
			if (arrows) addDirectionArrows(addSprite, from, to, segment.length);
		});

		return {
			object: group,
			release: () => sprites.forEach(sprite => {
				sprite.destroy();
				if (sprite.mesh && sprite.mesh.geometry) sprite.mesh.geometry.dispose();
			})
		};
	}

	/* Chevrons along one leg, pointing the way the charge flies. Two short lines per arrow, in the
	   leg's own colour and width, so an arrow is visibly part of the line rather than an ornament
	   laid over it.

	   `hexes` is the leg's length in HEXES, which is what makes the spacing exact without going
	   near the coordinate converter: the leg is straight, so one hex of it is gameLength/hexes
	   whatever the zoom or the bearing. Walked BACKWARDS from the waypoint so that every leg -
	   including a one-hex slide - ends with an arrow pointing into its corner. */
	function addDirectionArrows(addSprite, from, to, hexes) {
		const span = mathlib.distance(from, to);
		if (!span || !hexes) return;

		const unit = { x: (to.x - from.x) / span, y: (to.y - from.y) / span };
		const perHex = span / hexes;
		const size = perHex * SCT_ARROW_SIZE;
		const step = perHex * SCT_ARROW_EVERY;

		//The two barbs are the REVERSE heading rotated each way by SCT_ARROW_ANGLE.
		const angle = SCT_ARROW_ANGLE * Math.PI / 180;
		const cos = Math.cos(angle);
		const sin = Math.sin(angle);
		const back = { x: -unit.x, y: -unit.y };
		const barbs = [
			{ x: back.x * cos - back.y * sin, y: back.x * sin + back.y * cos },
			{ x: back.x * cos + back.y * sin, y: back.y * cos - back.x * sin }
		];

		//The epsilon keeps floating-point dust from minting a zero-length arrow on the launch hex.
		for (let along = span; along > 0.001; along -= step) {
			const tip = { x: from.x + unit.x * along, y: from.y + unit.y * along };

			barbs.forEach(barb => addSprite(tip, { x: tip.x + barb.x * size, y: tip.y + barb.y * size }));
		}
	}

	/* The green hexes the course leaves behind it (user ruling 2026-09-06). BallisticSprites rather
	   than one HexRegion blanket, for two reasons: they are scattered rather than contiguous, so a
	   region would be one loop per hex anyway, and a sprite can carry TEXT - which is how a hex where
	   the player named a unit says whose name it is.
	   ⚠️ destroy() frees the MATERIAL only. BallisticSprite's textures are shared statics cached by
	   content (its TEXTURE_CACHE), so disposing one here would blank every other sprite using it. */
	function buildCourseMarkers(markers) {
		if (!markers.length) return null;

		const group = new THREE.Group();
		const sprites = [];

		markers.forEach(marker => {
			const position = this.coordinateConverter.fromHexToGame(new hexagon.Offset(marker.q, marker.r));
			const sprite = new window.BallisticSprite(position, 'hexGreen', marker.text, SCT_MARKER_TEXT_COLOUR);

			sprites.push(sprite);
			group.add(sprite.mesh);
		});

		return { object: group, release: () => sprites.forEach(sprite => sprite.destroy()) };
	}

	/* Two rows of figures over one hex, in the LoS ruler's idiom (mathlib.drawRuler). A THREE.Sprite
	   rather than a TextSprite plane so it never inherits a rotation from anything, and so it can sit
	   at a fixed world z without a parent to compensate for.
	   ⚠️ THIS TEXTURE IS NOT SHARED and MUST be disposed - unlike every other sprite in this file,
	   whose textures are cached statics. It is minted per set of figures, and the figures change on
	   every click. */
	function buildFigureLabel(hex, rows) {
		const canvas = window.AbstractCanvas.create(SCT_LABEL_TEXTURE, SCT_LABEL_TEXTURE);
		const context = canvas.getContext('2d');
		const middle = SCT_LABEL_TEXTURE / 2;
		//Any number of rows, always centred on the hex: the third one only appears when the weapon
		//is boosted, and the block must not shift up the map when it does.
		const spacing = SCT_LABEL_FONT_PX * 1.1;
		const first = middle - (rows.length - 1) * spacing / 2;

		context.fillStyle = '#ffffff';
		context.textAlign = 'center';
		context.textBaseline = 'middle';
		context.shadowColor = 'rgba(0,0,0,1)';
		context.shadowBlur = 10;
		context.font = 'bold ' + SCT_LABEL_FONT_PX + 'px Arial';
		rows.forEach((row, index) => context.fillText(row, middle, first + index * spacing));

		const texture = new THREE.CanvasTexture(canvas);
		texture.colorSpace = THREE.SRGBColorSpace;
		texture.needsUpdate = true;

		const sprite = new THREE.Sprite(new THREE.SpriteMaterial({ map: texture, transparent: true }));
		const position = this.coordinateConverter.fromHexToGame(new hexagon.Offset(hex.q, hex.r));
		const side = this.coordinateConverter.getHexDistance() * SCT_LABEL_SCALE;

		sprite.position.set(position.x, position.y, SCT_LABEL_Z);
		sprite.scale.set(side, side, 1);

		return {
			object: sprite,
			release: object => {
				if (object.material.map) object.material.map.dispose();
				object.material.dispose();
			}
		};
	}

	/* Every transceiver on the board with a course declared this turn, plus the reachable fan and
	   the budget figures of whichever one is selected.

	   ⚠️ NO EXPLICIT OWN-TEAM FILTER, and none is needed: SensorChargeTransceiver is a hidetarget
	   weapon, so TacGamedata::hideSystemFireOrders blanks x and y on every waypoint order in an
	   enemy's payload for the turn it was declared on, and getCoursePlan drops a waypoint whose
	   hex will not parse. An enemy course therefore resolves to an empty plan by construction
	   rather than by a rule this file has to remember to apply. */
	function generateSensorChargeCourses(gamedata) {
		if (!window.SensorChargeTransceiver) return; //no such weapon loaded in this build

		gamedata.ships.forEach(ship => {
			if (!ship.systems || shipManager.isDestroyed(ship)) return;

			ship.systems.forEach(system => {
				if (!system || system.name !== 'SensorChargeTransceiver') return;
				if (typeof system.getCoursePlan !== 'function') return;

				const plan = system.getCoursePlan(ship);
				if (!plan) return;

				const key = `sct:${ship.id}:${system.id}`;

				if (plan.legs.length) {
					const colour = plan.receiver ? SCT_COURSE_COLOUR_RECEIVED : SCT_COURSE_COLOUR_OPEN;
					//The signature has to carry the colour as well as the geometry: a ship moving
					//onto the last waypoint turns the same course green without changing a leg.
					const signature = colour + '|' + plan.legs
						.map(leg => `${leg.from.q},${leg.from.r}>${leg.to.q},${leg.to.r}`).join('|');

					syncSceneObject.call(this, key, signature,
						() => buildLineChain.call(this, plan.legs, colour, SCT_COURSE_OPACITY, SCT_COURSE_Z, true));
				}

				//The green markers belong to the COURSE, not to the selection: they are what the
				//player has committed, and they have to stay readable while another ship is being
				//given its orders.
				if (plan.markers.length) {
					//The text is part of the signature: naming a unit on a hex already marked changes
					//the sprite without moving it.
					const markerSignature = plan.markers
						.map(marker => `${marker.q},${marker.r}:${marker.text}`).join('|');

					syncSceneObject.call(this, `sctmark:${ship.id}:${system.id}`, markerSignature,
						() => buildCourseMarkers.call(this, plan.markers));
				}

				//The fan and the figures are a targeting aid, so they belong to the SELECTED weapon
				//only - otherwise every transceiver in the fleet would paint the map.
				const selected = gamedata.selectedSystems && gamedata.selectedSystems.indexOf(system) !== -1;
				if (!selected) return;

				/* WHAT IS STILL AFFORDABLE, as spent-of-total on each axis. The totals are derived
				   rather than the class constants: boost levels are bought in Initial Orders and can
				   go to either axis, so "16" is only right until the player buys one. plan.hexesLeft
				   and plan.manoeuvresLeft already price that the way the player reads it - the most
				   this axis could still take - so used + left IS the axis total.

				   ⚠️⚠️ AND THAT IS WHY A BOOSTED CHARGE NEEDS THE THIRD ROW (user report 2026-09-06:
				   "seems to run out of hexes after it goes above the normal 4 manoeuvres"). The two
				   totals are each "the most THIS axis could take if nothing further goes to the other",
				   and the boost levels are a SHARED pool - so they cannot both be reached, and the hex
				   total visibly SHRINKS (19 -> 18 -> 17) as manoeuvres eat levels off it. The maths is
				   right and the server agrees with it exactly; what was missing was any sign of the pool
				   the two rows are competing for. `+N` is that pool, shown only when there is one. */
				const rows = [
					`${plan.hexesUsed}/${plan.hexesUsed + plan.hexesLeft}`,
					`${plan.manoeuvresUsed}/${plan.manoeuvresUsed + plan.manoeuvresLeft}`
				];

				if (plan.boost > 0) rows.push(`+${plan.boostLeft}`);

				syncSceneObject.call(this, `sctfig:${ship.id}:${system.id}`,
					`${plan.head.q},${plan.head.r}|${rows.join('|')}`,
					() => buildFigureLabel.call(this, plan.head, rows));

				const reachable = system.getReachableHexes(plan);
				if (!reachable.length) return;

				//Anchored on the head, which is where buildRegionFromHexes measures its sweep from -
				//and the fan is symmetric about it, so that is also the smallest sweep available.
				const reachSignature = `${plan.head.q},${plan.head.r}|` + reachable
					.map(hex => `${hex.q},${hex.r}`).join('|');

				syncSceneObject.call(this, `sctray:${ship.id}:${system.id}`, reachSignature, () => {
					const overlay = buildHexRegionOverlay.call(this, plan.head, reachable, 'hexArcBlue', SCT_FAN_DIM);

					return overlay && { object: overlay, release: window.HexRegion.dispose };
				});
			});
		});
	}

	/* Redraw the courses ALONE, outside the consumeGamedata pass - which is what withdrawing a
	   waypoint and selecting or unselecting the weapon need, since neither fires a poll.

	   ⚠️⚠️ IT HAS TO PRUNE ITS OWN STALE KEYS, for the same reason refreshEdfNetHexes does:
	   consumeGamedata clears `used` on everything and sweeps at the end, and nothing does that
	   here - so a course that shrank by a leg, or a reachable fan whose weapon has just been
	   unselected, would stay on the board. Marking and sweeping only SCT_KEY_PREFIXES keeps it
	   away from ballistics, terrain and jump points.

	   The caller does not have to request a frame: every path that reaches this one goes through
	   webglScene.customEvent, which calls requestRender() itself. */
	BallisticIconContainer.prototype.refreshSensorChargeCourses = function (gamedata) {
		//⚠️ EVERY PREFIX THIS FEATURE OWNS, or the one left out is the one that smears: a marker
		//whose waypoint has just been withdrawn, or a figure label for a course that shrank.
		const isCourseKey = key => SCT_KEY_PREFIXES.some(prefix => key.startsWith(prefix));

		this.sceneObjects.forEach((entry, key) => {
			if (isCourseKey(key)) entry.used = false;
		});

		generateSensorChargeCourses.call(this, gamedata);

		this.sceneObjects.forEach((entry, key) => {
			if (!isCourseKey(key) || entry.used) return;

			releaseSceneObject.call(this, entry);
			this.sceneObjects.delete(key);
		});
	};

	/* SUPERSEDED - kept for reference while the blanket version beds in. One BallisticSprite (one
	   draw call, one cloned ShaderMaterial) per hex, rebuilt in full on every consumeGamedata, and
	   drawing only the perimeter RING rather than the occupied disc.

	function generateTerrainHexes(gamedata) {
		if (gamedata.gamephase === -1) return; //Don't bother during Deployment phase.

		gamedata.ships.filter(ship => ship.Enormous && ship.shipSizeClass == 5 && !shipManager.isDestroyed(ship)).forEach(ship => {
			//gamedata.ships.filter(ship => ship.Huge > 0).forEach(ship => {
			const position = shipManager.getShipPosition(ship);
			//const perimeterHexes = (ship.Huge === 2)
			//	? mathlib.getPerimeterHexes(position, ship.Huge)
			//	: mathlib.getNeighbouringHexes(position, ship.Huge);
			const facing = shipManager.movement.getLastCommitedMove(ship).facing;
			const perimeterHexes = mathlib.getPerimeterHexes(position, ship.Huge, ship.hexOffsets, facing); //Position + radius passed.

			perimeterHexes.push(position); //Let's see what performance is like if we do add hexes for single hex Terrain. Remove if it causes rendering issues e.g. on Mobile - DK 8.1.26

			perimeterHexes.forEach(neighbour => {
				const pos = this.coordinateConverter.fromHexToGame(neighbour);
				const sprite = new BallisticSprite(pos, "hexWhite");
				this.scene.add(sprite.mesh);

				this.ballisticIcons.push({
					id: -5,
					shooterId: ship.id,
					targetId: ship.id,
					launchPosition: neighbour,
					position: new hexagon.Offset(pos.x, pos.y),
					launchSprite: sprite,
					targetSprite: sprite,
					used: true
				});
			});
		});
	}
	*/

	/* ONE marker per HEX, not per ship. A whole reinforcement wave shares a deployment box and
	   several units routinely land on the same hex (fighters and mines stack freely), which
	   previously minted one sprite per ship all at the same coordinates - a stack of identical
	   overlapping labels that read as a single smeared, over-bright hex.

	   Jump Points are drawn FIRST so that when a pending arrival and an arriving unit claim the
	   same hex, the Jump Point wins: the arriving unit is already visible as itself, while the
	   pending one has nothing else on the map to represent it. */
	function generateReinforcementHexes(gamedata) {
		if (gamedata.gamephase == -1) return;

		const claimed = new Set();

		generateJumpPointHexes.call(this, gamedata, claimed);

		/* The ARRIVAL marker: the hex a unit that deploys THIS turn came in at. Also covers
		   spawned units (mid-game mines), which have no deploy move of their own - hence the
		   position coming from getPositionAtStartOfTurn rather than the movement row. */
		gamedata.ships
			.filter(ship => shipManager.getTurnDeployed(ship) == gamedata.turn && gamedata.turn > 1 && !shipManager.shouldBeHidden(ship))
			.forEach(ship => {
				const pos = shipManager.movement.getPositionAtStartOfTurn(ship, gamedata.turn);

				markReinforcementHex.call(this, pos, 'Reinforcement', claimed);
			});
	}

	/* The WARNING half of the reinforcement marker: a late slot now picks its entry hexes during
	   the Deployment phase of the turn BEFORE it arrives (shipManager.getTurnPlaced), so from that
	   turn's Initial Orders onward everyone can see where the jump points will open. Previously the
	   entry hex, the marker and the ships themselves all appeared together on the arrival turn and
	   an opponent got no warning at all.

	   Deliberately NOT filtered by shouldBeHidden - it returns true for every one of these ships
	   (they are not on the board yet), which would suppress the whole feature. Stealth units get a
	   marker too, by design ruling: the jump point is public even though the ship that comes
	   through it arrives cloaked.

	   No committed deploy move means no marker, which is exactly what keeps unplaced units and
	   flights queued for a hangar deploy-start dock (they go into a bay, not onto the board) off
	   the map. */
	function generateJumpPointHexes(gamedata, claimed) {
		gamedata.ships
			.filter(ship => shipManager.getTurnDeployed(ship) == gamedata.turn + 1)
			.filter(ship => !shipManager.isDestroyed(ship) && !ship.removed)
			.filter(ship => !ship.pendingDeployDock && !ship.pendingLcvDeployDock)
			.filter(ship => ship.spawned === undefined || ship.spawned === -1) //spawns never pre-place
			.forEach(ship => {
				const move = getCommittedDeployMove(ship);
				if (!move) return;

				markReinforcementHex.call(this, new hexagon.Offset(move.position), 'Jump Point', claimed);
			});
	}

	function getCommittedDeployMove(ship) {
		for (const key in ship.movement) {
			const move = ship.movement[key];
			if (move && move.type === 'deploy' && move.commit) return move;
		}
		return null;
	}

	/* One sprite per hex, keyed BY HEX so it survives a poll untouched. Keying by ship id (as this
	   did) is what allowed a stack: N ships on one hex meant N distinct keys resolving to N sprites
	   at identical coordinates. $claimed is the per-pass guard - first caller wins the hex, later
	   ones are dropped.

	   The LABEL is part of the signature so a hex that flips from "Jump Point" to "Reinforcement"
	   when the wave arrives rebuilds its sprite rather than keeping the stale text. */
	function markReinforcementHex(pos, label, claimed) {
		const hexKey = `${pos.q},${pos.r}`;
		if (claimed.has(hexKey)) return;
		claimed.add(hexKey);

		syncSceneObject.call(this, 'reinforcement:' + hexKey, label, () => {
			const sprite = new BallisticSprite(this.coordinateConverter.fromHexToGame(pos), "hexBlue", label);

			return { object: sprite.mesh, release: () => releaseSprite(sprite) };
		});
	}


	/* THE FACING ARROW OVER A FORMING JUMP POINT (JUMP_POINTS_PLAN.md).

	   A vortex is not on the board on the turn it is declared — the "Jump Point Forming" hex above
	   IS the vortex for that turn — so the facing, which is the rule that decides who can use it,
	   has to be shown on the marker. It is drawn as the SAME asset, at the same size and opacity,
	   that UI.vortexFacing puts over the pending hex and that ShipIcon puts over the vortex unit
	   once it opens, so the arrow never changes appearance across the three stages of its life.
	   Keep the three constants in step (see ShipIcon.FACING_ARROW_SCALE).

	   ONE ARROW PER HEX, keyed by hex and signed by facing, because syncSceneObject rebuilds only
	   on a signature change: a poll that leaves the declaration alone costs nothing, a re-declared
	   facing rebuilds, and a withdrawn order is released by pruneSceneObjects.

	   ⚠️ It has to be its own sweep rather than a line inside createBallisticIcon: an existing
	   ballistic icon is UPDATED, not rebuilt, on later polls (see createOrUpdateBallistic), so a
	   syncSceneObject call in there would run once and then let prune reclaim the arrow on the very
	   next poll.

	   firingMode is the storage for the facing on a Jump Engine — mode = facing + 1. */
	const VORTEX_ARROW_SCALE = 1.15;    //multiple of hex HEIGHT; arrowhead lands on the hex side
	const VORTEX_ARROW_OPACITY = 0.85;
	const VORTEX_ARROW_Z = -99;         //above the ballistic hexes (-100), below terrain (-50)

	function generateJumpPointArrows(orders) {
		orders.forEach(order => {
			//Stage 5: mode 7 is MAINTAIN, not a facing. The vortex is already on the board by then
			//and carries its own permanent facing arrow (ShipIcon.$facingArrow), so drawing a second
			//one over it - at facing (7-1)%6 = 0, which would usually be the WRONG way round - would
			//be both redundant and a lie.
			if (parseInt(order.firingMode, 10) === 7) return;

			const facing = (((parseInt(order.firingMode, 10) || 1) - 1) % 6 + 6) % 6;
			const hex = new hexagon.Offset(order.x, order.y);

			syncSceneObject.call(this, `jumppointArrow:${hex.q},${hex.r}`, String(facing), () => {
				const size = window.HexagonMath.getHexHeight() * VORTEX_ARROW_SCALE;
				const sprite = new window.webglSprite('./img/directionOfVortex.png', { width: size, height: size }, VORTEX_ARROW_Z);

				sprite.setPosition(this.coordinateConverter.fromHexToGame(hex));
				sprite.setFacing(-mathlib.hexFacingToAngle(facing));
				sprite.setOpacity(VORTEX_ARROW_OPACITY);

				return { object: sprite.mesh, release: () => releaseSprite(sprite) };
			});
		});
	}


	/* REINFORCEMENTS_PLAN.md Stage 4 / section 2.3 - A JUMP POINT EXIT FORMING.

	   On the turn it is declared THIS MARKER IS THE EXIT: the vortex unit is not created until
	   the end of that turn, and deliberately so - the deviation is rolled then, so until it has
	   been rolled there is no true hex for anyone's payload to leak (section 2.3's concealment
	   rule). What everyone sees for the whole of turn N is a blue hex at the DECLARED hex and an
	   arrow showing which way units will come out of it.

	   BLUE, not yellow: #00b8e6 is FV's 'not here yet' cyan and it is what tells an exit from
	   an entrance at a glance. The arrow is the mirrored asset, pointing OUTWARD, because an
	   exit's facing is the doorway out rather than the mouth an entrance is entered through.

	   TWO SOURCES, ONE DRAWING. The OWNER sees their own declaration as a fire order and it
	   arrives here. An ENEMY never does - TacGamedata::hideHyperspaceReinforcements deletes the
	   whole opening ship from their payload, orders and all - so the server republishes just the
	   hex and the facing on the PlayerSlot, and they are folded in here so both viewers get the
	   identical marker from the identical code (section 3.6).

	   ⭐ ONE OF THEM CAN BE HIGHLIGHTED (user request 2026-08-28). Three drives put three identical
	   blue hexes on the map and the Manage Reinforcements menu could not say which row owned which
	   one; selecting a declared row there names its marker here - the opener's NAME in the hex
	   instead of the generic label, in white, with the arrow at full opacity.
	     - It rides the ORDER's shooterid, so it can only ever apply to the owner's own half. The
	       republished enemy entries carry no shooter and are never highlighted, which is correct:
	       an opponent's menu is not open and the units are hidden from them anyway.
	     - The label goes into the sprite's SIGNATURE, so syncSceneObject rebuilds exactly the two
	       hexes whose state changed and leaves every other marker's texture alone.
	     - ⚠️ The name is read from gamedata at draw time rather than passed in: the highlight is an
	       ID for the reason ReinforcementEntry documents (every poll replaces every ship object).

	   ⚠️ Its own sweep rather than a line inside createBallisticIcon, for the reason
	   generateJumpPointArrows gives: an existing ballistic icon is UPDATED, not rebuilt, on later
	   polls, so a syncSceneObject call in there would run once and then let prune reclaim it. */
	function generateExitHexes(gamedata, orders) {
		/* ⭐ STAGE 9 EFFICIENCY GATE (user request 2026-08-29). This runs on EVERY ballistic redraw,
		   and its second half walks gamedata.slots looking for republished hexes that can only exist
		   in a game with the rule. `orders` is already empty without it (no unit can be in
		   hyperspace to declare one), so this is purely about the slot pass - but "already empty"
		   is a property of today's masking, and a gate that says so is what stops the next change
		   quietly reintroducing the cost. Cheap enough to be unconditional; it is one property read. */
		if (!gamedata.reinforcementsAllowed()) return;

		const claimed = new Set();

		const draw = (q, r, facing, label, phasing) => {
			const hex = new hexagon.Offset(q, r);
			const hexKey = `${hex.q},${hex.r}`;
			if (claimed.has(hexKey)) return;
			claimed.add(hexKey);

			/* ⭐ REINFORCEMENTS_PLAN.md STAGE 9 - A PHASING HULL'S HEX SAYS "REINFORCEMENTS", because
			   for it nothing is forming: no vortex is torn open and no terrain will appear here
			   (user ruling 2026-08-29). The marker itself is unchanged - same blue, same arrow, same
			   public-from-phase-2 rule - so the warning an opponent gets is exactly the warning
			   §2.3 already trades for; only the noun is honest about what will arrive.
			   The word is deliberately GENERIC. Naming the faction, the hull or the count here would
			   give away more than an ordinary declaration does, and §3.6 says none of those is ever
			   disclosed. */
			const defaultLabel = phasing ? 'Reinforcements' : 'Jump Point Forming';
			const signature = `${facing}|${label || ''}|${phasing ? 'p' : ''}`;

			syncSceneObject.call(this, 'jumpexit:' + hexKey, signature, () => {
				const sprite = new BallisticSprite(this.coordinateConverter.fromHexToGame(hex),
					'hexBlue', label || defaultLabel, label ? '#ffffff' : '#00b8e6');

				return { object: sprite.mesh, release: () => releaseSprite(sprite) };
			});

			syncSceneObject.call(this, 'jumpexitArrow:' + hexKey, signature, () => {
				const size = window.HexagonMath.getHexHeight() * VORTEX_ARROW_SCALE;
				const sprite = new window.webglSprite('./img/directionOfVortexEntry.png',
					{ width: size, height: size }, VORTEX_ARROW_Z);

				sprite.setPosition(this.coordinateConverter.fromHexToGame(hex));
				sprite.setFacing(-mathlib.hexFacingToAngle(facing));
				sprite.setOpacity(label ? 1 : VORTEX_ARROW_OPACITY);

				return { object: sprite.mesh, release: () => releaseSprite(sprite) };
			});
		};

		//Which opener the Manage Reinforcements menu is pointing at, if it is open at all. Guarded
		//because this container is also driven by the replay, where the module may not be loaded.
		const highlightId = (window.ReinforcementEntry && ReinforcementEntry.getHighlightedOpener)
			? ReinforcementEntry.getHighlightedOpener() : null;

		//The owner's own orders. firingMode is the storage for the facing - mode = facing + 1 - the
		//same convention an entrance uses, so the arrow maths is shared verbatim.
		orders.forEach(order => {
			const facing = (((parseInt(order.firingMode, 10) || 1) - 1) % 6 + 6) % 6;

			//Resolved once. ⚠️ Through gamedata rather than held, for the reason ReinforcementEntry
			//documents at length: every poll replaces every ship object (plan trap 17).
			const opener = gamedata.getShip(order.shooterid);

			let label = null;
			if (highlightId !== null && order.shooterid == highlightId && opener) label = opener.name;

			//STAGE 9 - the owner's own half can ask the drive directly: the declaring ship is in
			//their payload (it is theirs), so the engine that carries the order is reachable.
			const engine = opener ? shipManager.systems.getSystem(opener, order.weaponid) : null;

			draw(order.x, order.y, facing, label, shipManager.movement.isLegacyJumpEngine(engine));
		});

		//The republished half, for a viewer whose payload has no opening ship to carry an order.
		//Empty on the owner's own copy and on their team's, so the two can never double-draw - and
		//`claimed` would drop the second one anyway if they ever did.
		//STAGE 9 - `phase` rides the republished entry because this viewer has no opening ship to
		//ask (hideHyperspaceReinforcements deleted it, engine and all). It is not a disclosure: the
		//two labels differ only in whether terrain is coming, which the opponent finds out next
		//turn regardless by looking at the hex.
		for (const key in gamedata.slots) {
			const entries = gamedata.slots[key] && gamedata.slots[key].formingExits;
			if (!Array.isArray(entries)) continue;

			entries.forEach(entry => draw(entry.x, entry.y,
				(((parseInt(entry.facing, 10) || 0) % 6) + 6) % 6, null, !!entry.phase));
		}
	}


	/* The whole affected area as one blanket, centre hex included. `size` is the radius in hexes and
	   the area is the DISC of that radius - which is what the rules mean (IonFieldGenerator, for one,
	   is documented as "affects all units within 2 hexes" and resolves with getShipsInDistance($target,
	   2)) and what the old ring-of-sprites could not draw without one call per ring.

	   The centre hex keeps its own target sprite, with the weapon's name on it, drawn on top at
	   z = -100. */
	function generateSplashHexes(id, position, shooterid, targetid, size, type) {
		const centreHex = this.coordinateConverter.fromGameToHex(position);
		const hexes = [{ q: centreHex.q, r: centreHex.r }].concat(mathlib.getNeighbouringHexes(centreHex, size));

		syncSceneObject.call(this, 'splash:' + id, `${centreHex.q},${centreHex.r}|${size}|${type}`, () => {
			const overlay = buildHexRegionOverlay.call(this, centreHex, hexes, type, SPLASH_REGION_DIM);			
		/*// GTS_Triad
		syncSceneObject.call(this, 'splash:' + id, `${centreHex.q},${centreHex.r}|${size}|${type}|${gamedata.gamephase}`, () => {			const overlay = buildHexRegionOverlay.call(this, centreHex, hexes, type, SPLASH_REGION_DIM);
		*/ //Restoring old version above for now, which you'd accidentally deleted part of btw - DK
			return overlay && { object: overlay, release: window.HexRegion.dispose };
		});
	}

	/* SUPERSEDED - kept for reference while the blanket version beds in. One BallisticSprite per hex
	   of the PERIMETER ring only (mathlib.getPerimeterHexes returns distance == radius, not <=), which
	   is why callers passed a list of sizes - [1, 2] painted two rings to fill a radius-2 disc, while
	   a lone [5] left the interior blank.

	function generateSplashHexes(id, position, shooterid, targetid, size, type) {

		let targetHex = this.coordinateConverter.fromGameToHex(position);
		const perimeterHexes = mathlib.getPerimeterHexes(targetHex, size); //Position + radius passed.

		perimeterHexes.forEach(neighbour => {
			const pos = this.coordinateConverter.fromHexToGame(neighbour);
			const sprite = new BallisticSprite(pos, type);
			sprite.uniforms.opacity.value = 0.7; //Make them a bit less bright than main hex sprites.

			this.scene.add(sprite.mesh);

			this.ballisticIcons.push({
				id: -4,
				shooterId: shooterid,
				targetId: targetid,
				launchPosition: neighbour,
				position: new hexagon.Offset(pos.x, pos.y),
				launchSprite: sprite,
				targetSprite: sprite,
				used: true,
				splash: true
			});
		});
	}
	*/


	function createOrUpdateBallistic(ballistic, iconContainer, turn, replay = false) {
		const icon = getBallisticIcon.call(this, ballistic.id);

		//Sometimes need to force creation of hex sprites, e.g. for persistent effects or splash damage.
		if (icon && !['PersistentEffect', 'Split'].includes(ballistic.notes) && !icon.splash) {
			updateBallisticIcon.call(this, icon, ballistic, iconContainer, turn);
		} else {
			createBallisticIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
		}
	}


	//To create coloured hexes signifying ballistic launches and other effects.
	function createBallisticIcon(ballistic, iconContainer, turn, scene, replay = false) {

//		if (ballistic.damageclass === 'Sweeping') return;
if (ballistic.damageclass === 'Sweeping' || ballistic.damageclass === 'HPC-subordinate') return;

		const shooterIcon = iconContainer.getById(ballistic.shooterid);
		if (!shooterIcon) return;

		const shooter = shooterIcon.ship;
		let targetType = 'hexRed';
		let text = "";
		let textColour = "";
		let iconImage = null;

		let launchPosition = null;
		/*let launchPosition = this.coordinateConverter.fromHexToGame(
			ballistic.type === 'normal'
				? shooterIcon.getLastMovement().position
				: shooterIcon.getFirstMovementOnTurn(turn).position
		);
		*/
		if (ballistic.damageclass === 'HomingMissile') {
			/* HOMING MISSILE (HOMING_MISSILE_PLAN.md): a re-attack launches from the hex its TARGET
			   was standing on when the previous pass missed, not from the shooter. That hex rides on
			   the ORDER (x/y), which is why this cannot go through weapon.hasSpecialLaunchHexCalculation
			   - that flag is per-WEAPON, and it also switches off the launcher's line-of-sight
			   fire-control penalty, which must keep applying. */
			launchPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(Number(ballistic.x), Number(ballistic.y)));
		} else if (ballistic.type === 'normal' || ballistic.type === 'prefiring') {
			launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
		} else {
			launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
		}

		let weapon = null;
		let modeName = null;

		weapon = shipManager.systems.getSystem(shooter, ballistic.weaponid);
		if (weapon) {
			modeName = weapon?.firingModes?.[ballistic.firingMode] || null;
		}

		let hideTargetAlways = false;

		if (replay) {
			//if (ballistic.damageclass === 'PersistentEffectPlasma' && ballistic.targetid === -1) return;
			if (weapon?.alwaysHideFireOrders && gamedata.getPlayerTeam() !== shooter.team) {
				for (var i in weapon.fireOrders) {
					var otherBall = weapon.fireOrders[i];
					if (otherBall.damageclass == "SecondAttack") {
						hideTargetAlways = false; //stays false effecitvely
						break;
					} else {
						hideTargetAlways = true; //No second attack after hex shot, don't show e.g. Ballistic Mine Launchers.	
					}
				}
			}
		}

		let targetPosition = null;
		let targetIcon = null;
		let splash = false;

		/* ⭐⭐ A VORTEX DECLARATION IS ALWAYS DRAWN ON ITS HEX, WHATEVER ITS targetid SAYS
		   (JUMP_GATES_PLAN.md sections 2.1 and 3.3).

		   Every other ballistic order means "targetid names a unit, so hang the marker on that
		   unit's icon". A FIXED JUMP GATE's signal order breaks that rule: its targetid carries the
		   claiming PLAYER, recorded as their nearest qualifying unit, because tac_fireorder has no
		   player column and the gate belongs to nobody in particular. The hex is, and always is,
		   the gate's own.

		   Left alone the branch below would do two wrong things at once: draw the "Jump Gate
		   Signalled" marker over the SIGNALLING SHIP instead of over the gate, and - because the
		   line drawer takes the same branch - run a bright line from the gate to it. The second is
		   an information leak on the map of the very fact section 2.1 says is never revealed, and
		   it would fire on the claimant's own screen the moment the order is created, before the
		   server has masked anything.

		   So a 'jumppoint' order is treated as targetid -1 THROUGHOUT this function - here, in the
		   duplicate-icon lookup, and in the record pushed at the end - which is exactly what a
		   ship's own vortex declaration already sends. */
		/* ⚠️⚠️ 'gateexit' MUST BE IN THIS TEST (REINFORCEMENTS_PLAN.md Stage 8), and leaving it out
		   is the information leak the whole paragraph above describes, not a cosmetic miss. An
		   ARRIVAL claim is the same order shape with the same targetid - the claiming player's
		   nearest qualifying unit - so without this line the marker would be hung on that SHIP and a
		   bright line drawn to it from the gate, on the claimant's own screen, the instant the order
		   is built and before the server has masked anything. Which unit signalled is never revealed
		   (JUMP_GATES_PLAN.md section 2.1); this is what keeps that true for the second flavour. */
		const isVortexDeclaration = ballistic.damageclass === 'jumppoint'
			|| ballistic.damageclass === 'gateexit';
		const iconTargetId = isVortexDeclaration ? -1 : ballistic.targetid;
		//A FIXED GATE signals ITSELF: launch hex and target hex are the same hex, which is what the
		//label and the launch-sprite suppression below both key off.
		const isGateSignal = isVortexDeclaration && gamedata.isJumpGate(shooter);

		if (iconTargetId === -1 && ballistic.x !== "null" && ballistic.y !== "null" && !hideTargetAlways) {
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		} else if (iconTargetId && iconTargetId !== -1 && !hideTargetAlways) {
			targetIcon = iconContainer.getById(iconTargetId);
			//targetPosition = { x: 0, y: 0 }; // placeholder — the mesh will handle it
		}

		//GTS Need this to get the hex grid to appear on the Flare generating ship and follow its movement
		if (modeName === 'Flare' && targetIcon) {
			targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovement().position);
		}
		
		if (weapon?.noTargetHexIcon) {
			targetPosition = launchPosition;
		}

		// Mode-specific icon logic
		if (modeName) {
			const modeMap = {
				'1-Blanket Shield': { type: 'hexGreen', text: 'Shade Modulator', color: '#008000' },
				'3-Blanket Shade': { type: 'hexYellow', text: 'Shade Modulator', color: '#787800' },
				'Anti-Clockwise': { type: 'hexPurple', text: 'Singularity Mine', color: '#7f00ff' }, // GTS for Singularity Mine
				'Anti-Fighter Plasma Web': { type: 'hexGreen', text: 'Plasma', color: '#787800' },
				'Anti-Fighter Sand Caster': { type: 'hexYellow', text: 'Sand', color: '#787800' },
				'Asteroid Salvo': { type: 'hexWhite', text: 'Asteroid Salvo', color: '#ffffff' },  // GTS for Asteroid Salvo
				'Basic Mine': { type: 'hexRed', text: 'Basic', color: '#e6140a' },				
				'Clockwise': { type: 'hexPurple', text: 'Singularity Mine', color: '#7f00ff' }, // GTS for Singularity Mine
				'Defensive Plasma Web': { type: 'hexGreen', color: '', color: '#787800' },								
				'Defensive Sand Caster': { type: 'hexYellow', color: '', color: '#787800' },
				'Energy Mine': { type: 'hexRed', text: 'Energy Mine', color: '#e6140a' },
				//WALKERS OF SIGMA-957 (Stage 6). Purple, not red: the probe deals no damage, and a
				//red hex on this map means incoming fire. The key is the FIRING MODE NAME as the
				//server declares it (EnergyDrainingMine::$firingModes) - the label is shorter on
				//purpose, so it fits the hex.
				'Energy Draining Mine': { type: 'hexPurple', text: 'Energy Drain Mine', color: '#7f00ff' },
				'Fighter Bomb': { type: 'hexBlue', text: 'Fighter Bomb', color: '#00b8e6' },
				'Flare': { type: 'hexWhite', text: 'Flare', color: '#ffffff' },  // GTS for Flare Generator
				'Gravitic Mine': { type: 'hexGreen', text: 'Gravitic Mine', color: '#008000' },											
				'Ion Storm': { type: 'hexPurple', text: 'Ion Field', color: '#7f00ff' },
				'Jammer': { type: 'hexPurple', text: 'Jammer', color: '#7f00ff' },
				'Priority - GN': { type: 'hexGreen', text: 'Gravity Net PRIORITY', color: '#787800' },
				'Proximity Laser': { type: 'hexRed', text: 'Proximity Laser', color: '#e6140a' },
				'Proximity Launcher': { type: 'hexRed', text: 'Proximity Laser', color: '#e6140a' },					
				'Psychic Field': { type: 'hexRed', text: 'Psychic', color: '#e6140a' },
				'Second Sight': { type: 'hexPurple', text: 'Second Sight', color: '#7f00ff' },							
				'Shredder': { type: 'hexBlue', text: 'Shredder', color: '#00b8e6' },	
				'Standard - GN': { type: 'hexGreen', text: 'Gravity Net Standard', color: '#008000' },							
				'Thought Wave': { type: 'hexPurple', text: 'Thought Wave', color: '#bc3782' },				
				'Transverse Jump': { type: 'hexBlue', text: 'Transverse Jump', color: '#787800' },
				'Warp Jump': { type: 'hexBlue', text: 'Warp Jump', color: '#787800' },	
				'Z - Antimine': { type: 'hexRed', text: 'Antimine', color: '#e6140a' },						
			};

			if (modeName == 'Transverse Jump' && !gamedata.isMyorMyTeamShip(shooter)) {
				var shadingField = shipManager.systems.getSystemByName(shooter, "ShadingField");
				if (!shadingField.isDetectedTorvalus(shooter, 20)) return;
			}

			const match = modeMap[modeName];
			if (match) {
				targetType = match.type;
				text = match.text || text;
				textColour = match.color || textColour;

				// Call splash hex generation for cases where weapon affects more than one hex.
				// Guard with targetPosition: mine-targeting fire orders (targetid !== -1) have a targetIcon
				// but no targetPosition, which would make generateSplashHexes place hexes at 0,0 in Replay.
				if (['Z - Antimine', 'Shredder', 'Energy Mine', 'Energy Draining Mine', 'Ion Storm', 'Jammer', '1-Blanket Shield', '3-Blanket Shade', 'Flare', 'Asteroid Salvo', 'Clockwise', 'Anti-Clockwise'].includes(modeName)) {  //GTS Added Flare, Asteroid Salvo, and spins for Singularity Mine
					if ((gamedata.isMyOrTeamOneShip(shooter) || replay) && targetPosition) {
						//A single RADIUS now, not a list of ring sizes: generateSplashHexes fills the whole
						//disc in one region, so Ion Storm's old [1, 2] - ring 1 plus ring 2, the only way
						//to cover a radius-2 area a ring at a time - is simply 2.
						//Energy Draining Mine takes this default deliberately: radius 1 is the seven
						//hexes its field covers, i.e. EnergyDrainingMine::FIELD_RADIUS. Move both together.
						let size = 1; // Shredder / Energy Mine / Energy Draining Mine

						switch (modeName) {
							case 'Z - Antimine':
								size = 3;
								break;
							case 'Ion Storm':
								size = 2;
								break;
							case 'Jammer':
								size = 5;
								break;
							case '1-Blanket Shield':
								size = 3;
								break;
							case '3-Blanket Shade':
								size = 5;
								break;
							// GTS_Triad
							case 'Asteroid Salvo':
								size = 2;
								break;
							case 'Clockwise':
							case 'Anti-Clockwise':
								size = 10;
								break;
						}

						generateSplashHexes.call(
							this,
							ballistic.id,
							targetPosition,
							ballistic.shooterid,
							ballistic.targetid,
							size,
							match.type
						);

						splash = true;
					}
					
					if (modeName === 'Flare' && targetPosition) { 
						[1, 2].forEach(size => {
							generateSplashHexes.call(
								this,
								ballistic.id,
								targetPosition,
								ballistic.shooterid,
								ballistic.targetid,
								size,
								'hexWhite'
							);
						});
						splash = true;
					}					
					
				}
			}
			
			// Damage class-based override logic.
			// A few hex-targeted launchers (mines) rely on their fire order carrying
			// damageclass 'MultiModeHex' so the icon prints the firing-mode (mine type) name.
			// That flag is stamped lazily by the weapon's initializationUpdate (via
			// initializeSystem), which for a freshly-declared targeting order hasn't run yet -
			// and even then only ever tags fireOrders[0]. Honour a stable weapon-level flag
			// instead, so the name shows the moment the hex is targeted and on every mine order.
			const effectiveDamageClass = (weapon && weapon.multiModeHexIcon && modeName)
				? 'MultiModeHex'
				: ballistic.damageclass;

			if (effectiveDamageClass && modeName) {
				switch (effectiveDamageClass) {
					case 'MultiModeHex':
						const isFriendly = gamedata.isMyOrTeamOneShip(shooter);
						var modeText = isFriendly ? modeName : weapon.getModeNameForEnemy();

						targetType = 'hexRed';
						text = modeText;
						textColour = '#e6140a';
						break;
					case 'support':
						targetType = 'hexGreen';
						//iconImage = './img/allySupport.png';
						break;
					/* A vortex declaration (JUMP_POINTS_PLAN.md) is not an attack, and the default
					   red hex reads as incoming fire. Yellow, in the same --fv-warn the Stage 2b
					   facing control and the vortex unit use.

					   THIS MARKER IS THE VORTEX for the whole of the turn it was declared on: the
					   unit itself is deliberately not on the board until the turn it OPENS (user
					   ruling 2026-08-21, plan section 2.3 — the vortex spawns with
					   spawned = declaration turn + 1, so shouldBeHidden keeps it off). So the label
					   says what is actually true — Forming, not yet enterable — and the FACING,
					   which the firing mode used to carry as text, is shown instead by the arrow
					   generateJumpPointArrows draws over this hex. The marker retires by itself at
					   turn advance: onConstructed only builds ballistics for the current turn. */
					case 'jumppoint':
						targetType = 'hexYellow';
						/* Stage 5: firing mode 7 is not a facing, it is the MAINTAIN declaration -
						   the same gesture aimed at a vortex that already exists. Its hex already
						   holds the vortex unit and the vortex unit's own arrow, so this marker
						   only has to say that the player has spent this turn's declaration on
						   keeping it open.

						   JUMP GATES (PHASE 2): on a FIXED GATE the mode is neither a facing nor
						   Maintain - it is the programmed open duration in turns, and the gate is
						   already sitting on this hex with its own permanent facing arrow. The
						   marker says the gate has been SIGNALLED and nothing else; who signalled
						   it is never shown (plan section 2.1), and how long for is not public
						   either, so the duration stays off the label. */
						text = isGateSignal
							? 'Jump Gate Signalled'
							: ((parseInt(ballistic.firingMode, 10) === 7)
								? 'Maintaining Jump Point'
								: 'Jump Point Forming');
						textColour = '#e1b000';
						break;

					/* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE ARRIVAL CLAIM, IN BLUE. Yellow =
					   leaving, blue = arriving is the pairing the whole reinforcements feature is
					   built on (plan section 3.7), and #00b8e6 is FV's one "not here yet" cyan -
					   the same value the Forming marker, the exit vortex and the fleet list's
					   hyperspace row use. Without a case of its own this order would fall to the
					   default RED hex, which reads as incoming fire at a gate nobody is shooting.

					   NO DURATION AND NO CLAIMANT ON THE LABEL, exactly as the yellow twin above:
					   who signalled is never shown and how long for is not public either. And no
					   facing, because a gate's is fixed when it is placed and the arrow that says so
					   belongs to the vortex this claim will spawn.

					   ⚠️ IN PRACTICE ONLY ITS AUTHOR EVER SEES THIS. TacGamedata::hideSystemFireOrders
					   strips every phase-1 ballistic order from every payload, its author's included,
					   so the marker exists for the length of one client's Initial Orders and then the
					   blue vortex unit takes over. It is local feedback, not a public announcement -
					   which is why saying "Arrival" here leaks nothing. */
					case 'gateexit':
						targetType = 'hexBlue';
						text = 'Arrival Gate Signalled';
						textColour = '#00b8e6';
						break;
				}
			}
		}
		// LAUNCH SPRITE
		let launchSprite = null;
		if (
			!getByLaunchPosition(launchPosition, this.ballisticIcons) &&
			ballistic.damageclass !== 'PersistentEffectPlasma' &&
			ballistic.type !== 'normal' &&
			ballistic.damageclass !== 'support' &&
			//A FIXED GATE signals itself, so its launch hex IS its target hex: the blank launch
			//sprite would sit exactly under the "Jump Gate Signalled" marker and read as a doubled,
			//mis-drawn outline. A SHIP declaring a vortex still gets one - there the launch hex says
			//which ship declared it, and the two hexes are genuinely different places.
			!isGateSignal
		) {
			const launchType = gamedata.isMyOrTeamOneShip(shooter) ? 'hexYellow' : 'hexOrange';
			launchSprite = new BallisticSprite(launchPosition, launchType);
			scene.add(launchSprite.mesh);
		}

		// TARGET SPRITE
		let targetSprite = null;
		//iconTargetId, not ballistic.targetid - see the note above: a gate signal's targetid names
		//the claiming player's unit, and matching on it would let an unrelated order aimed at that
		//same unit suppress the gate's own marker.
		if (!getByTargetIdOrTargetPosition(targetPosition, iconTargetId, this.ballisticIcons)) {
			if (targetPosition || targetIcon) {

				targetSprite = new BallisticSprite(targetPosition || { x: 0, y: 0 }, targetType, text, textColour, iconImage);
				if (targetIcon && modeName !== 'Flare') {
					targetIcon.mesh.add(targetSprite.mesh);
				} else if (targetIcon && modeName === 'Flare') {
					targetIcon.mesh.add(targetSprite.mesh);
				targetSprite.mesh.position.set(0, 0, -100);

//				targetSprite = new BallisticSprite(targetPosition || { x: 0, y: 0 }, targetType, text, textColour, iconImage);
//				if (targetIcon) {
//					targetIcon.mesh.add(targetSprite.mesh);
				} else {
					scene.add(targetSprite.mesh);
				}
			}
		}

		this.ballisticIcons.push({
			id: ballistic.id,
			shooterId: ballistic.shooterid,
			targetId: iconTargetId,   //see the note above - a vortex declaration is hex-keyed, never unit-keyed
			launchPosition,
			/* `position` means THE HEX THIS SHOT IS AIMED AT - it is only ever read by
			   getByTargetIdOrTargetPosition, to stop two orders drawing two markers on one hex. On an
			   ordinary unit-targeted ballistic x/y are the string "null" and it is harmless nonsense.
			   A HOMING re-attack is the exception: its x/y hold a REAL hex, but it is the LAUNCH hex
			   (its target's previous position), not a target - so left as-is it would suppress an
			   unrelated hex-targeted marker that happened to land where the missile came from.
			   HOMING_MISSILE_PLAN.md. */
			position: (ballistic.damageclass === 'HomingMissile' && iconTargetId !== -1)
				? null
				: new hexagon.Offset(ballistic.x, ballistic.y),
			launchSprite,
			targetSprite,
			used: true,
			splash: splash
		});
	}

	const getByLaunchPosition = (position, icons) => icons.find(icon => icon.used && icon.launchPosition && icon.launchPosition.x === position.x && icon.launchPosition.y === position.y)

	const getByTargetIdOrTargetPosition = (position, targetId, icons) => icons.find(icon => icon.used && ((targetId !== -1 && icon.targetId === targetId) || (position && icon.position && icon.position.x === position.x && icon.position.y === position.y)))


	function updateBallisticIcon(icon) {
		icon.used = true;
	}

	BallisticIconContainer.prototype.hide = function () {
		this.ballisticIcons.forEach(icon => {
			icon.launchSprite?.hide();
			icon.targetSprite?.hide();
		});
		//Remembered so a region built while hidden (consumeGamedata runs in both states) starts hidden.
		this.iconsVisible = false;
		this.sceneObjects.forEach(entry => entry.object.visible = false);
		return this;
	};

	BallisticIconContainer.prototype.show = function () {
		this.ballisticIcons.forEach(icon => {
			icon.launchSprite?.show();
			icon.targetSprite?.show();
		});
		this.iconsVisible = true;
		this.sceneObjects.forEach(entry => entry.object.visible = true);
		return this;
	};

	BallisticIconContainer.prototype.onEvent = function (name, payload) {
		const handler = this['on' + name];
		if (typeof handler === 'function') handler.call(this, payload);
	};

	BallisticIconContainer.prototype.onZoomEvent = function ({ zoom }) {
		this.zoomScale = zoom <= 0.5 ? 2 * zoom : 1;
		if (zoom <= 0.5) {
			this.ballisticLineIcons.forEach(icon => {
				icon.lineSprite.setLineWidth(this.zoomScale * 2);
			});
		}
	};

	function getBallisticIcon(id) {
		return this.ballisticIcons.filter(function (icon) {
			return icon.id === id;
		}).pop();
	}


	//BALLISTIC LINE FUNCTION BELOW
	function createOrUpdateBallisticLines(ballistic, iconContainer, turn, replay = false) {
		const icon = getBallisticLineIcon.call(this, ballistic.id);

		if (icon && !['PersistentEffect', 'Split'].includes(ballistic.notes)) {
			if (replay) {
				createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, true);
			} else {
				updateBallisticLineIcon.call(this, icon, ballistic, iconContainer, turn);
			}
		} else if (ballistic.notes !== 'PersistentEffect' && ballistic.damageclass !== 'PersistentEffectPlasma') {
			createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
		}
	}


	//To create ballistic lines between launches and targets.
	function createBallisticLineIcon(ballistic, iconContainer, turn, scene, replay = false) {
		//if(ballistic.damageclass == 'Targeter') return;		
		if (ballistic.targetid === -1 && ballistic.x == "null" && ballistic.y == "null") return; // Skip creation of enemy hidden weapons, can cause visual bugs.	

		const shooterIcon = iconContainer.getById(ballistic.shooterid);
		const targetIcon = iconContainer.getById(ballistic.targetid);
		if (!shooterIcon) return;

		let shooter = shooterIcon.ship;
		let weapon = shipManager.systems.getSystem(shooter, ballistic.weaponid);
		let modeName = weapon?.firingModes?.[ballistic.firingMode] ?? null; 
		
		// If this is an Antimine hex shot, skip if there's a follow-on mine shot
		if (modeName === 'Z - Antimine' && ballistic.targetid === -1) {
			if (weapon && weapon.fireOrders && weapon.fireOrders.some(order => order.targetid !== -1 && order.turn === turn)) {
				return;
			}
		}

		if (replay && weapon) {
			if (weapon.alwaysHideFireOrders && gamedata.getPlayerTeam() !== shooter.team) return;
		}
		// Get launch position (may be overwritten later)
		let launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn)?.position);
		// HOMING MISSILE: a re-attack's launch hex is its target's PREVIOUS hex, carried on the
		// order itself - see the matching branch in createBallisticIcon.
		if (ballistic.damageclass === 'HomingMissile') {
			launchPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(Number(ballistic.x), Number(ballistic.y)));
		}
		let targetPosition;

		// Determine target position
		if (ballistic.damageclass === 'PersistentEffectPlasma') {
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		} else if (ballistic.damageclass === 'jumppoint') {
			/* ⭐ A VORTEX DECLARATION IS HEX-KEYED, NEVER UNIT-KEYED - see the long note in
			   createBallisticIcon. A FIXED JUMP GATE's signal order carries the claiming player's
			   nearest unit in targetid, so the targetIcon branch below would draw a bright line
			   from the gate to the SIGNALLER: wrong, and the map half of an information leak the
			   whole feature is built to avoid (JUMP_GATES_PLAN.md section 2.1).
			   Forced to the hex, launch and target are the gate's own position, and the
			   same-position guard further down skips the line entirely - which is right: a gate
			   signals itself and there is nothing to draw a line to. */
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		} else if (replay && targetIcon) {
			targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovementOnTurn(turn)?.position);
		} else if (targetIcon && ballistic.targetid !== -1) {
			targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovement(turn)?.position);
		} else {
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		}

		// Handle special case where target hex is not used
		if (weapon?.noTargetHexIcon) {
			targetPosition = launchPosition;
		}

		// If either position is invalid or same, skip drawing
		if (!launchPosition || !targetPosition || (
			launchPosition.x === targetPosition.x &&
			launchPosition.y === targetPosition.y &&
			launchPosition.z === targetPosition.z
		)) {
			return;
		}

		// Determine line color type
		let type = gamedata.isMyOrTeamOneShip(shooter) ? 'yellow' : 'orange';

		// Override for special launcher hex logic
		if (weapon?.hasSpecialLaunchHexCalculation) {
			if (ballistic.damageclass === 'Targeter') {
				// Keep the team-based colour (yellow for own team, orange for enemy)
				// already set above rather than forcing yellow for everyone.
				type = gamedata.isMyOrTeamOneShip(shooter) ? 'yellow' : 'orange';
			} else {
				const launcherHex = weaponManager.getFiringHex(shooter, weapon);
				launchPosition = this.coordinateConverter.fromHexToGame(launcherHex);
				type = 'red';
			}
		}

		// Handle specific modeName cases
		if (ballistic.type === 'normal' || ballistic.type === 'prefiring') {
			launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn)?.position);

			const modeColorMap = {
				'Shredder': 'blue',
				'Fighter Bomb': 'blue',
				'Defensive Plasma Web': 'green',
				'Anti-Fighter Plasma Web': 'green',
				'Transverse Jump': 'blue',
				'Gravitic Mine': 'green'
			};

			if (modeColorMap[modeName]) {
				type = modeColorMap[modeName];
			} else {
				type = 'white';
			}
		}

		// Handle damage class overrides
		if (ballistic.damageclass) {
			switch (ballistic.damageclass) {
				case 'support':
					type = 'green';
					launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn)?.position);
					break;

				case 'gravNetMoveHex': {
					// Line should start at the captured target ship, not the GravityNet's own ship.
					type = 'green';
					const gravNetTargetIcon = ballistic.gravNetTargetId != null && ballistic.gravNetTargetId !== -1
						? iconContainer.getById(ballistic.gravNetTargetId)
						: null;
					if (gravNetTargetIcon) {
						launchPosition = this.coordinateConverter.fromHexToGame(
							replay
								? gravNetTargetIcon.getLastMovementOnTurn(turn)?.position
								: gravNetTargetIcon.getLastMovement(turn)?.position
						);
					}
					break;
				}

				case 'Sweeping':
					type = 'purple';
					if (weapon?.weaponClass === 'Particle') type = 'orange';
					else if (weapon?.weaponClass === 'Molecular' && !(weapon instanceof MolecularSlicerBeamL)) type = 'blue';
					else if (weapon?.weaponClass === 'Gravitic') type = 'green';
					else if (weapon?.weaponClass === 'Psychic') type = 'red';
					else if (weapon?.weaponClass === 'Support') type = 'green';
					else if (weapon?.weaponClass === 'Electromagnetic') type = 'yellow';
					break;
			}
		}

		const lineSprite = new BallisticLineSprite(
			launchPosition,
			targetPosition,
			3 * this.zoomScale,
			-5, // Render above terrain but below ships
			getLineColorByType(type),
			0.5
		);

		const isFriendly = gamedata.isMyOrTeamOneShip(shooter);

		this.ballisticLineIcons.push({
			id: ballistic.id,
			shooterId: ballistic.shooterid,
			targetId: ballistic.targetid,
			lineSprite: lineSprite,
			used: true,
			isFriendly: isFriendly
		});

		scene.add(lineSprite.mesh);

		// Control line visibility based on explicit toggle state
		const currentIcon = this.ballisticLineIcons.find(icon => icon.id === ballistic.id);
		if (currentIcon) {
			const shouldBeVisible = isFriendly ? this.friendlyLinesVisible : this.enemyLinesVisible;
			if (shouldBeVisible) {
				currentIcon.lineSprite.show();
			} else {
				currentIcon.lineSprite.hide();
			}
			currentIcon.lineSprite.isVisible = shouldBeVisible;
		}
	}


	function updateBallisticLineIcon(lineIcon, ballistic, iconContainer, turn) {
		lineIcon.used = true;
		if (ballistic.targetid === -1) return;

		const wasVisible = lineIcon.lineSprite.isVisible;
		lineIcon.lineSprite.destroy();
		this.scene.remove(lineIcon.lineSprite.mesh);

		createBallisticLineIcon.call(this, ballistic, iconContainer, gamedata.turn, this.scene);

		lineIcon.lineSprite[wasVisible ? 'show' : 'hide']();
		lineIcon.lineSprite.isVisible = wasVisible;
	}


	BallisticIconContainer.prototype.toggleBallisticLines = function (ships) {
		const shipIds = ships.map(s => s.id);

		if (ships.length > 0) {
			if (gamedata.isMyOrTeamOneShip(ships[0])) {
				this.friendlyLinesVisible = !this.friendlyLinesVisible;
			} else {
				this.enemyLinesVisible = !this.enemyLinesVisible;
			}
		}

		this.ballisticLineIcons.forEach(icon => {
			if (shipIds.includes(icon.shooterId) && icon.lineSprite) {
				const visible = icon.isFriendly ? this.friendlyLinesVisible : this.enemyLinesVisible;
				icon.lineSprite[visible ? 'show' : 'hide']();
				icon.lineSprite.isVisible = visible;
			}
		});
		return this;
	};

	BallisticIconContainer.prototype.hideLines = function (ships) {
		const shipIds = ships.map(s => s.id);

		if (ships.length > 0) {
			if (gamedata.isMyOrTeamOneShip(ships[0])) {
				this.friendlyLinesVisible = false;
			} else {
				this.enemyLinesVisible = false;
			}
		}

		this.ballisticLineIcons.forEach(icon => {
			if (shipIds.includes(icon.shooterId) && icon.lineSprite) {
				icon.lineSprite.hide();
				icon.lineSprite.isVisible = false;
			}
		});
		return this;
	};

	BallisticIconContainer.prototype.showLines = function (ships) {
		const shipIds = ships.map(s => s.id);

		if (ships.length > 0) {
			if (gamedata.isMyOrTeamOneShip(ships[0])) {
				this.friendlyLinesVisible = true;
			} else {
				this.enemyLinesVisible = true;
			}
		}

		this.ballisticLineIcons.forEach(icon => {
			if (shipIds.includes(icon.shooterId) && icon.lineSprite) {
				icon.lineSprite.show();
				icon.lineSprite.isVisible = true;
			}
		});
		return this;
	};

	//Called during movement phase to recreate lines after a target ship moves.
	BallisticIconContainer.prototype.updateLinesForShip = function (ship, iconContainer) {

		var wasVisibleTarget = false; //Variable to track if destroyed lines were visible. If one was, they all were.

		this.ballisticLineIcons = this.ballisticLineIcons.filter((lineIcon) => {
			// Destroy lines where the ship is the target.
			if (lineIcon.targetId === ship.id) {
				if (lineIcon.lineSprite.isVisible) wasVisibleTarget = true;
				this.scene.remove(lineIcon.lineSprite.mesh);
				lineIcon.lineSprite.destroy();
				return false;
			} else {
				return true;
			}
		});

		//Now recreate line using usual method.
		var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');
		allBallistics.forEach(function (ballistic) {
			if (ship.id === ballistic.targetid) {
				createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn);
			}
		}, this);

		//Check if lines were visible and if so continue to show.
		this.ballisticLineIcons.forEach(function (lineIcon) {
			if (lineIcon.targetId === ship.id) {
				if (!wasVisibleTarget) {
					lineIcon.lineSprite.hide();
					lineIcon.lineSprite.isVisible = false;
				} else {
					lineIcon.lineSprite.show();
					lineIcon.lineSprite.isVisible = true;
				}
			}
		});
	};

	function getBallisticLineIcon(id) {
		return this.ballisticLineIcons.find(icon => icon.id === id);
	}

	function getLineColorByType(type) {
		if (type == "orange") {
			return "rgba(250,153,53)";
		} else if (type == "yellow") {
			return "rgba(255, 255, 0)";
		} else if (type == "red") {
			return "rgba(230,20,10)";
		} else if (type == "blue") {
			return "rgba(0,184,230)";
		} else if (type == "green") {
			return "rgba(0, 179, 0)";
		} else if (type == "purple") {
			return "rgba(204, 51, 255)";
		} else if (type == "white") {
			return "rgba(255, 255, 255)";
		} else {
			return "rgba(144,185,208)";
		}
	}

	BallisticIconContainer.prototype.createHexNumbers = function (scene) {
		if (this.hexNumberMesh) {
			this.hexNumberMesh.visible = !this.hexNumberMesh.visible;
			return;
		}

		const gridWidth = 72;
		const gridHeight = 48;
		const hexSize = 50;

		const largeTexture = createLargeHexNumberTexture(gridWidth, gridHeight, hexSize);

		const totalWidth = gridWidth * hexSize * 2;
		const totalHeight = gridHeight * hexSize * 2;

		const geometry = new THREE.PlaneGeometry(totalWidth, totalHeight);
		const material = new THREE.MeshBasicMaterial({
			map: largeTexture,
			transparent: true,
			depthWrite: false
		});

		this.hexNumberMesh = new THREE.Mesh(geometry, material);
		this.hexNumberMesh.position.set(502.5, -651, -1);
		scene.add(this.hexNumberMesh);
	};

	function createLargeHexNumberTexture(gridWidth, gridHeight, hexSize, textColour) {
		textColour = textColour || "#ffffff";
		const HEX_WIDTH = Math.sqrt(3) * hexSize;
		const HEX_HEIGHT = 2 * hexSize;

		// Use half-size canvas (30MP vs original 120MP = 4x less RAM / generation time).
		// ctx.scale(0.5, 0.5) maps all drawing coordinates to this smaller canvas
		// while keeping the same relative positions in the texture UV space.
		const DRAW_SCALE = 2;  // Drawing coordinate scale (original positions)
		const CANVAS_SCALE = 0.5; // Canvas is half the original size
		const TEXTURE_WIDTH = Math.ceil(gridWidth * HEX_WIDTH * DRAW_SCALE * CANVAS_SCALE);
		const TEXTURE_HEIGHT = Math.ceil(gridHeight * HEX_HEIGHT * DRAW_SCALE * CANVAS_SCALE);

		const canvas = document.createElement("canvas");
		canvas.width = TEXTURE_WIDTH;
		canvas.height = TEXTURE_HEIGHT;
		const ctx = canvas.getContext("2d");

		// Scale the context down so that original DRAW_SCALE=2 positions fit in half the canvas
		ctx.scale(CANVAS_SCALE, CANVAS_SCALE);

		ctx.clearRect(0, 0, TEXTURE_WIDTH / CANVAS_SCALE, TEXTURE_HEIGHT / CANVAS_SCALE);

		const fontSize = Math.floor(hexSize * 0.2 * DRAW_SCALE);
		ctx.font = "bold " + fontSize + "px Arial";
		ctx.fillStyle = textColour;
		ctx.textAlign = "center";
		ctx.textBaseline = "middle";
		ctx.globalAlpha = 0.5;

		let number = 1;

		for (let r = 0; r < gridHeight; r++) {
			for (let q = 0; q < gridWidth; q++) {
				// Exact original spacing constants from SCALE_FACTOR=2 version
				let x = q * HEX_WIDTH * 1.7315 + HEX_WIDTH / 2;
				let y = r * HEX_HEIGHT * 1.5 + HEX_HEIGHT / 2;

				if (r % 2 !== 0) x += HEX_WIDTH * 0.855;

				// Snap to even pixels so ctx.scale(0.5) always hits a whole canvas pixel
				const px = Math.round(x / 2) * 2;
				const py = Math.round(y / 2) * 2;
				ctx.fillText(String(number).padStart(4, '0'), px, py);
				number++;
			}
		}

		const texture = new THREE.CanvasTexture(canvas);
		texture.colorSpace = THREE.SRGBColorSpace;
		texture.generateMipmaps = false;
		texture.minFilter = THREE.LinearFilter;
		texture.magFilter = THREE.LinearFilter;
		texture.needsUpdate = true;
		return texture;
	}

	return BallisticIconContainer;

}();


