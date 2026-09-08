'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

window.mathlib = {

	distance: function distance(x1, y1, x2, y2) {
		if ((typeof x1 === 'undefined' ? 'undefined' : _typeof(x1)) == 'object' && (typeof y1 === 'undefined' ? 'undefined' : _typeof(y1)) == 'object') {
			x2 = y1.x;
			y2 = y1.y;
			y1 = x1.y;
			x1 = x1.x;
		};

		var a = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
		return a;
	},

	getSeededRandomGenerator: function (seed) {
		function lcg(a) { return a * 48271 % 2147483647 }
		seed = seed ? lcg(seed) : lcg(Math.random());
		return function () { return (seed = lcg(seed)) / 2147483648 }
	},

	arrayIsEmpty: function arrayIsEmpty(array) {
		for (var i in array) {
			return false;
		}

		return true;
	},

	addToDirection: function addToDirection(current, add) {
		add = add % 360;

		var ret = 0;
		if (current + add > 360) {
			ret = add - (360 - current);
		} else if (current + add < 0) {
			ret = 360 + (current + add);
		} else {
			ret = current + add;
		}

		return ret;
	},

	getPointBetween: function getPointBetween(start, end, percentage, noRound) {
		var x = start.x + percentage * (end.x - start.x);
		var y = start.y + percentage * (end.y - start.y);

		if (noRound) {
			return { x: x, y: y };
		}

		return { x: Math.floor(x), y: Math.floor(y) };
	},

	getDistanceBetweenShipsInHex: function getDistanceBetweenShipsInHex(s1, s2) {
		var start = shipManager.getShipPosition(s1);
		var end = shipManager.getShipPosition(s2);
		return start.distanceTo(end);
	},

	getAngleBetween: function getAngleBetween(angle1, angle2, right) {
		//console.log(angle1  + " " + angle2);
		var total;
		var difference;
		if (right) {
			if (angle1 > angle2) {
				difference = 360 - angle1 + angle2;
			} else {
				difference = angle2 - angle1;
			}
		} else {
			if (angle1 < angle2) {
				difference = (angle1 + (360 - angle2)) * -1;
			} else {
				difference = angle2 - angle1;
			}
		}

		return difference;
	},

	addToHexFacing: function addToHexFacing(facing, add) {

		if (facing + add > 5) {
			return mathlib.addToHexFacing(0, facing + add - 6);
		}

		if (facing + add < 0) {
			return mathlib.addToHexFacing(6, facing + add);
		}

		return facing + add;
	},

	getPointInDirection: function getPointInDirection(r, a, cx, cy, noRound) {

		a = -a;

		var x = cx + r * Math.cos(a * Math.PI / 180);
		var y = cy + r * Math.sin(a * Math.PI / 180);

		if (noRound) {
			return { x: x, y: y };
		}
		return { x: Math.round(x), y: Math.round(y) };
	},

	getArcLength: function getArcLength(start, end) {
		var a = 0;
		if (start > end) {
			a = 360 - start + end;
		} else {
			a = end - start;
		}

		return a;
	},

	/* Bearing at the middle of an arc. start === end means a full circle, whose "centre" is
	   simply opposite the start - which is what makes a catch-all 360 arc lose to any narrower
	   one under getAngleDistance. Mirror of mathlib::getArcCentre (source/server/lib/mathlib.php);
	   used by weaponManager.getAttachLocation to pick the section an arc is CENTRED on rather
	   than merely contains. */
	getArcCentre: function getArcCentre(start, end) {
		start = start % 360;
		end = end % 360;
		var width = (end - start + 360) % 360;
		if (width === 0) width = 360;

		return (start + width / 2) % 360;
	},

	//Shortest angular separation between two bearings, 0-180. Mirror of mathlib::getAngleDistance.
	getAngleDistance: function getAngleDistance(a, b) {
		var d = Math.abs(a - b) % 360;

		return (d > 180) ? 360 - d : d;
	},

	isInArc: function isInArc(direction, start, end) {
		//direction: 300 start: 360 end: 240
		direction = Math.round(direction);

		if (start == end) return true;

		if (direction == 0 && start == 360 || direction == 0 && end == 360) return true;

		if (start > end) {

			return direction >= start || direction <= end;
		} else if (direction >= start && direction <= end) {
			return true;
		}

		return false;
	},

	radianToDegree: function radianToDegree(angle) {
		return angle * (180.0 / Math.PI);
	},

	degreeToRadian: function degreeToRadian(angle) {
		//radian * (180.0 / Math.PI) = degree
		return angle / (180.0 / Math.PI);
	},

	getCompassHeadingOfShip: function getCompassHeadingOfShip(observer, target) {

		var oPos = shipManager.getShipPosition(observer);
		var tPos = shipManager.getShipPosition(target);

		if (oPos.equals(tPos)) {
			// Attached pair share a hex AND share movement history, so the
			// motion-direction fallback below collapses to host's direction
			// of travel. The boarder's facing already encodes the answer:
			// it's maintained as host_facing + attachOffset, and the offset
			// was set so the boarder's nose points at the host's center.
			if (observer.attached && observer.attached[target.id] !== undefined) {
				return shipManager.getShipHeadingAngle(observer);
			}
			if (target.attached && target.attached[observer.id] !== undefined) {
				return mathlib.addToDirection(shipManager.getShipHeadingAngle(target), 180);
			}

			//if Target has speed 0, consider Observer to have better Init! that would be better for firing arcs...
			//if Observer has speed 0 consider Target to have better Ini!
			if ((shipManager.hasBetterInitive(observer, target) && (shipManager.movement.getSpeed(observer) != 0)) || (shipManager.movement.getSpeed(target) == 0)) {
				oPos = shipManager.movement.getPreviousLocation(observer);
			} else {
				tPos = shipManager.movement.getPreviousLocation(target);
			}
		}

		oPos = window.coordinateConverter.fromHexToGame(oPos);
		tPos = window.coordinateConverter.fromHexToGame(tPos);

		return mathlib.getCompassHeadingOfPoint(oPos, tPos);
	},

	getCompassHeadingOfPoint: function getCompassHeadingOfPoint(observer, target) {

		if (observer instanceof hexagon.Offset) {
			observer = coordinateConverter.fromHexToGame(observer);
		}

		if (target instanceof hexagon.Offset) {
			target = coordinateConverter.fromHexToGame(target);
		}

		var heading = mathlib.radianToDegree(Math.atan2(target.y - observer.y, target.x - observer.x));

		if (heading > 0) {
			heading = 360 - heading;
		} else {
			heading = Math.abs(heading);
		}

		return heading;
	},


	hexFacingToAngle: function hexFacingToAngle(d) {
		switch (d) {
			case 0:
				return 0;
			case 1:
				return 60;
			case 2:
				return 120;
			case 3:
				return 180;
			case 4:
				return 240;
			default:
				return 300;
		}
	},


	doLinesIntersect: function (p1, p2, p3, p4) {
		const EPSILON = 1e-10;

		function crossProduct(a, b) {
			return a.x * b.y - a.y * b.x;
		}

		function subtract(a, b) {
			return { x: a.x - b.x, y: a.y - b.y };
		}

		function isBetween(a, b, c) {
			return (
				Math.min(a.x, c.x) - EPSILON <= b.x && b.x <= Math.max(a.x, c.x) + EPSILON &&
				Math.min(a.y, c.y) - EPSILON <= b.y && b.y <= Math.max(a.y, c.y) + EPSILON
			);
		}

		const d1 = subtract(p2, p1);
		const d2 = subtract(p4, p3);
		const denom = crossProduct(d1, d2);
		const diff = subtract(p3, p1);

		// Check for colinear
		if (Math.abs(denom) < EPSILON) {
			if (Math.abs(crossProduct(diff, d1)) > EPSILON) return false;
			// Colinear — check overlap
			return (
				isBetween(p1, p3, p2) ||
				isBetween(p1, p4, p2) ||
				isBetween(p3, p1, p4) ||
				isBetween(p3, p2, p4)
			);
		}

		const t = crossProduct(diff, d2) / denom;
		const u = crossProduct(diff, d1) / denom;

		return t >= -EPSILON && t <= 1 + EPSILON && u >= -EPSILON && u <= 1 + EPSILON;
	},


	getHexCorners: function getHexCorners(hex) {
		const hexSize = window.Config.HEX_SIZE;
		const shrinkFactor = 1; // Set to <1 if you want shrunken hexes for LoS testing

		const center = coordinateConverter.fromHexToGame(hex);
		const { x: cx, y: cy } = center;

		// Angles for pointy-topped hexes, in radians
		const angles = [30, 90, 150, 210, 270, 330].map(deg => deg * Math.PI / 180);

		// Return the 6 corners around the hex center
		return angles.map(angle => ({
			x: cx + shrinkFactor * hexSize * Math.cos(angle),
			y: cy + shrinkFactor * hexSize * Math.sin(angle)
		}));
	},


	isLoSBlocked: function isLoSBlocked(start, end, blockedHexes) {
		const startPixel = coordinateConverter.fromHexToGame(start);
		const endPixel = coordinateConverter.fromHexToGame(end);

		// Normalize all blocked hexes to plain {q, r} objects
		const normalizedBlockedHexes = blockedHexes.map(hex => ({ q: hex.q, r: hex.r }));

		// Filter out the start and end positions
		const filteredBlockedHexes = normalizedBlockedHexes.filter(
			hex => !(hex.q === start.q && hex.r === start.r) && !(hex.q === end.q && hex.r === end.r)
		);

		const lineMinQ = Math.min(start.q, end.q);
		const lineMaxQ = Math.max(start.q, end.q);
		const lineMinR = Math.min(start.r, end.r);
		const lineMaxR = Math.max(start.r, end.r);

		//Debugging variables
		//var startPixel = coordinateConverter.fromHexToGame({ q: 8, r: 2 });
		//var endPixel = coordinateConverter.fromHexToGame({ q: 7, r: 1 });
		//var blockedCorners = mathlib.getHexCorners({ q: 8, r: 1 });

		for (let hex of filteredBlockedHexes) {
			// Optional: guard against malformed data
			if (typeof hex.q !== 'number' || typeof hex.r !== 'number') continue;

			// Filter out obviously non-intersecting hexes (based on hex grid, not pixels!)
			if (
				hex.q < lineMinQ - 3 || hex.q > lineMaxQ + 3 ||
				hex.r < lineMinR - 3 || hex.r > lineMaxR + 3
			) {
				continue;
			}

			const corners = this.getHexCorners(hex);
			for (let i = 0; i < corners.length; i++) {
				const p1 = corners[i];
				const p2 = corners[(i + 1) % corners.length];

				if (this.doLinesIntersect(startPixel, endPixel, p1, p2)) {
					//if(gamedata.showLoS) mathlib.drawLine(startPixel, endPixel, 0xff00ff); // Magenta line if blocked for debugging
					//if(gamedata.showLoS) mathlib.drawHex(corners, 0xff00ff); // Magenta hex border for debugging					
					return true; // Line of sight is blocked
				}
			}
		}

		//if(gamedata.showLoS) mathlib.drawLine(startPixel, endPixel, 0x87ceeb); // Blue line for debugging
		return false; // Line of sight is clear
	},

	/* ------------------------------------------------------------------------------------
	   hexLine - the CLIENT MIRROR of HexZone::line() (server/lib/HexZone.php).

	   Walkers of Sigma-957 (WALKERS_OF_SIGMA_PLAN.md 2.1, Stage 4) needs the exact set of hexes
	   a shot crosses so the EDF targeting penalty previewed here is the one the server rolls.
	   ⚠️ It deliberately does NOT reuse mathlib.isLoSBlocked's geometry: that one asks a
	   different question (does the segment CLIP any part of a blocked hex, tested in pixels)
	   and would count a hex the server's line never enters. Line of sight and field crossing
	   are two different rules and must stay two different functions.

	   ⚠️⚠️ PHP/JS ROUNDING PARITY IS THE WHOLE POINT OF phpRound(). PHP's round() is
	   "half away from zero" (round(-2.5) === -3) while JS's Math.round() is "half up"
	   (Math.round(-2.5) === -2). Cube coordinates go negative all over a Fiery Void map, and a
	   line that passes exactly through a hex corner lands on a .5 - so plain Math.round() here
	   would silently disagree with the server on precisely the awkward shots, which is the
	   hardest kind of mismatch to notice. Any future port of a PHP geometry routine needs this.

	   Takes and returns plain {q, r}. Endpoints INCLUDED, both of them, exactly as the server's
	   loop does (i = 0 .. steps). The 50-step cap is the server's too - keep them equal.
	   ------------------------------------------------------------------------------------ */
	hexLine: function hexLine(start, end) {
		/* ⚠️⚠️ THIS IS NOT Math.round, AND IT IS NOT JUST A SIGN FIX EITHER. PHP's round() differs
		   from JS's in TWO ways, and both of them bite here:

		     1. HALF AWAY FROM ZERO. PHP round(-2.5) === -3; JS Math.round(-2.5) === -2. Cube
		        coordinates go negative all over a Fiery Void map.
		     2. PRE-ROUNDING. PHP first rounds the value to (14 - floor(log10|v|)) decimal places,
		        so a value that floating-point error has left at -20.49999999999999644 is treated
		        as -20.5 and lands on -21. Plain rounding gives -20.

		   Measured against the real HexZone::line over a 4,000-line corpus: fixing only (1) still
		   left 13 lines disagreeing with the server, all of them from (2). Fixing only (2) leaves
		   843. Both are load-bearing; the throwaway test asserts each of them separately.

		   Mirrors _php_math_round(value, 0) in PHP's ext/standard/math.c. ⚠️ If FV's PHP ever
		   moves to a version that changes round()'s edge-case behaviour (the "saner round()" work
		   lands after 8.3 - this was written against 8.2), re-run the differential test before
		   assuming this still matches. */
		function halfAwayFromZero(x) { return x < 0 ? -Math.round(-x) : Math.round(x); }
		function phpRound(v) {
			if (!isFinite(v) || v === 0) return v;
			var precisionPlaces = 14 - Math.floor(Math.log10(Math.abs(v)));
			//PHP's guard, with places = 0: pre-round only when it is both useful and safe
			if (precisionPlaces > 0 && precisionPlaces < 15) {
				var f = Math.pow(10, precisionPlaces);
				v = halfAwayFromZero(v * f) / f;
			}
			return halfAwayFromZero(v);
		}

		function offsetToCube(hex) {
			var x = hex.q - (hex.r + (hex.r & 1)) / 2;
			var z = hex.r;
			var y = -x - z;
			return [x, y, z];
		}
		function cubeRound(x, y, z) {
			var rx = phpRound(x), ry = phpRound(y), rz = phpRound(z);
			var dx = Math.abs(rx - x), dy = Math.abs(ry - y), dz = Math.abs(rz - z);
			if (dx > dy && dx > dz) { rx = -ry - rz; }
			else if (dy > dz)       { ry = -rx - rz; }
			else                    { rz = -rx - ry; }
			return [rx, ry, rz];
		}
		function cubeToOffset(cube) {
			//Math.trunc mirrors PHP's (int) cast in OffsetCoordinate's constructor. After
			//cubeRound the division is always exact, so this never actually truncates - it is
			//here so the two implementations stay line-for-line comparable.
			var q = cube[0] + (cube[2] + (cube[2] & 1)) / 2;
			return { q: Math.trunc(q), r: Math.trunc(cube[2]) };
		}

		var startCube = offsetToCube(start);
		var endCube   = offsetToCube(end);

		var dx = endCube[0] - startCube[0];
		var dy = endCube[1] - startCube[1];
		var dz = endCube[2] - startCube[2];

		var steps = Math.max(Math.abs(dx), Math.abs(dy), Math.abs(dz));
		if (steps > 50) steps = 50;

		var hexes = [];
		for (var i = 0; i <= steps; i++) {
			var t = (steps === 0) ? 0 : i / steps;
			hexes.push(cubeToOffset(cubeRound(
				startCube[0] + dx * t,
				startCube[1] + dy * t,
				startCube[2] + dz * t
			)));
		}
		return hexes;
	},


	/* //Draws a simple line between two positions, replace by drawRuler() below but maybe useful for something else - DK
	drawLine: function drawLine(p1, p2, color = 0x00ffff) {

		// Run once, at init or before drawing loop
		if (!window.LosSprite) {
		window.LosSprite = new THREE.Group();
		window.webglScene.scene.add(window.LosSprite);
		}

		mathlib.clearLosSprite();

		const material = new THREE.LineBasicMaterial({ color });
		const points = [
			new THREE.Vector3(p1.x, p1.y, 10),
			new THREE.Vector3(p2.x, p2.y, 10)
		];
		const geometry = new THREE.BufferGeometry().setFromPoints(points);
		const line = new THREE.Line(geometry, material);
		window.LosSprite.add(line);

		return line;
	},
	*/

	//Called in Phase Strategy to show Ruler if LoS is toggled on.
	showLoS: function showLoS(start, targetHex) {

		if (start == null) {
			//var firstShip = gamedata.getFirstFriendlyShip();
			//start = shipManager.getShipPosition(firstShip);
			return; //No start selectd yet, or tool just activated.			
		}
		//var blockedHexes = weaponManager.getBlockedHexes();
		var blockedHexes = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.   	

		mathlib.checkLineOfSightSprite(start, targetHex, blockedHexes);
	},

	clearLosSprite: function clearLosSprite() {
		const LosSprite = window.LosSprite;
		if (!LosSprite || !LosSprite.children || !Array.isArray(LosSprite.children)) {
			return;
		}

		while (LosSprite.children.length > 0) {
			const child = LosSprite.children[0];
			LosSprite.remove(child);
			if (child.geometry) child.geometry.dispose();
			if (child.material) child.material.dispose();
		}
	},

	//Alternative version of LoS check function, that calls Ruler and LoS visuals.  Separated to avoid having to show this is other places that call checkLineofSight() e.g. weapons.
	checkLineOfSightSprite: function checkLineOfSightSprite(start, end, blockedHexes) {
		const startPixel = coordinateConverter.fromHexToGame(start);
		const endPixel = coordinateConverter.fromHexToGame(end);

		// Normalize all blocked hexes to plain {q, r} objects
		const normalizedBlockedHexes = blockedHexes.map(hex => ({ q: hex.q, r: hex.r }));

		// Filter out the start and end positions
		const filteredBlockedHexes = normalizedBlockedHexes.filter(
			hex => !(hex.q === start.q && hex.r === start.r) && !(hex.q === end.q && hex.r === end.r)
		);

		const lineMinQ = Math.min(start.q, end.q);
		const lineMaxQ = Math.max(start.q, end.q);
		const lineMinR = Math.min(start.r, end.r);
		const lineMaxR = Math.max(start.r, end.r);

		for (let hex of filteredBlockedHexes) {
			// Optional: guard against malformed data
			if (typeof hex.q !== 'number' || typeof hex.r !== 'number') continue;

			// Filter out obviously non-intersecting hexes (based on hex grid, not pixels!)
			if (
				hex.q < lineMinQ - 3 || hex.q > lineMaxQ + 3 ||
				hex.r < lineMinR - 3 || hex.r > lineMaxR + 3
			) {
				continue;
			}

			const corners = this.getHexCorners(hex);
			for (let i = 0; i < corners.length; i++) {
				const p1 = corners[i];
				const p2 = corners[(i + 1) % corners.length];

				if (this.doLinesIntersect(startPixel, endPixel, p1, p2)) {
					mathlib.drawRuler(startPixel, endPixel, 0xdc143c); // Crimson line if blocked
					mathlib.drawHex(corners, 0xdc143c, 0xdc143c); // Crimson hex border
					//var loSBlockedSprite = new BallisticSprite(hex, 'hexRed', "", "", './img/cancel.png', 100);
					//window.LosSprite.add(loSBlockedSprite);										
					return true; // Line of sight is blocked
				}
			}
		}

		mathlib.drawRuler(startPixel, endPixel, 0x00ffff); // Blue line for clear LoS
		return false; // Line of sight is clear
	},

	//Called by checkLineOfSightSprite() above
	drawHex: function drawHex(corners, color = 0xff0000, fillColor = null) {
		if (!corners || corners.length < 6) return;

		// === Create the shape ===
		const shape = new THREE.Shape();
		shape.moveTo(corners[0].x, corners[0].y);
		for (let i = 1; i < corners.length; i++) {
			shape.lineTo(corners[i].x, corners[i].y);
		}
		shape.lineTo(corners[0].x, corners[0].y); // Close the shape

		const z = 500;

		// === Add fill if specified ===
		if (fillColor !== null) {
			const fillGeometry = new THREE.ShapeGeometry(shape);
			const fillMaterial = new THREE.MeshBasicMaterial({
				color: fillColor,
				transparent: true,
				opacity: 0.3, // Adjust fill visibility
				side: THREE.DoubleSide
			});
			const fillMesh = new THREE.Mesh(fillGeometry, fillMaterial);
			fillMesh.position.z = z; // Place it below the outline
			window.LosSprite.add(fillMesh);
		}

		// === Add outline as thick Line or Mesh ===
		const outlinePoints = corners.map(c => new THREE.Vector2(c.x, c.y));
		const outlineGeometry = new THREE.BufferGeometry().setFromPoints([
			...outlinePoints.map(p => new THREE.Vector3(p.x, p.y, 500)),
			new THREE.Vector3(corners[0].x, corners[0].y, 500) // Close loop
		]);

		const outlineMaterial = new THREE.LineBasicMaterial({ color });
		const outline = new THREE.Line(outlineGeometry, outlineMaterial);
		window.LosSprite.add(outline);

		return outline;
	},


	drawRuler: function drawRuler(p1, p2, color = 0x00ffff) {
		if (!window.LosSprite) {
			window.LosSprite = new THREE.Group();
			window.webglScene.scene.add(window.LosSprite);
		}

		mathlib.clearLosSprite();

		// Create and add the line
		const material = new THREE.LineBasicMaterial({
			color: color,
			transparent: true,
			opacity: 0.8
		});
		const points = [
			new THREE.Vector3(p1.x, p1.y, 501),
			new THREE.Vector3(p2.x, p2.y, 501)
		];
		const geometry = new THREE.BufferGeometry().setFromPoints(points);
		const line = new THREE.Line(geometry, material);
		window.LosSprite.add(line);

		// Calculate midpoint and distance
		const midX = (p1.x + p2.x) / 2;
		const midY = (p1.y + p2.y) / 2;
		const distance = coordinateConverter.fromGameToHex(p1).distanceTo(
			coordinateConverter.fromGameToHex(p2)
		);

		// === Create distance label ===
		const canvas = document.createElement('canvas');
		const TEXTURE_SIZE = 128;
		canvas.width = TEXTURE_SIZE;
		canvas.height = TEXTURE_SIZE;
		const ctx = canvas.getContext('2d');

		let fontSize = 110;
		ctx.fillStyle = 'white';
		ctx.textAlign = "center";
		ctx.textBaseline = "middle";
		const distanceText = distance.toFixed(0);
		ctx.font = `bold ${fontSize}px Arial`;
		ctx.fillText(distanceText, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2);

		const texture = new THREE.CanvasTexture(canvas);
        texture.colorSpace = THREE.SRGBColorSpace;
		texture.needsUpdate = true;
		const spriteMaterial = new THREE.SpriteMaterial({ map: texture, transparent: true });
		const sprite = new THREE.Sprite(spriteMaterial);
		sprite.position.set(midX, midY, 501);
		// Sprites scale with the orthographic frustum, so a fixed world-space
		// scale shrinks on screen as you zoom out. Multiply by the current zoom
		// (and clamp) so the number stays a readable size at any zoom level.
		const zoom = (window.webglScene && window.webglScene.zoom) || 1;
		const labelScale = 28 * Math.max(zoom, 1);
		sprite.scale.set(labelScale, labelScale, 1);
		window.LosSprite.add(sprite);

		// === Helper for circular markers ===
		function createMarker(x, y) {
			function hexToRgba(hex, alpha = 1) {
				const r = (hex >> 16) & 255;
				const g = (hex >> 8) & 255;
				const b = hex & 255;
				return `rgba(${r},${g},${b},${alpha})`;
			}

			const markerCanvas = document.createElement('canvas');
			markerCanvas.width = 64;
			markerCanvas.height = 64;
			const markerCtx = markerCanvas.getContext('2d');

			markerCtx.beginPath();
			markerCtx.arc(32, 32, 18, 0, 2 * Math.PI);
			markerCtx.fillStyle = hexToRgba(color);
			markerCtx.fill();

			const markerTexture = new THREE.CanvasTexture(markerCanvas);
        markerTexture.colorSpace = THREE.SRGBColorSpace;
			markerTexture.needsUpdate = true;

			const markerMaterial = new THREE.SpriteMaterial({ map: markerTexture, transparent: true });
			const markerSprite = new THREE.Sprite(markerMaterial);
			markerSprite.position.set(x, y, 501);
			markerSprite.scale.set(18, 18, 1);

			window.LosSprite.add(markerSprite);
		}

		// Add marker at both ends
		createMarker(p1.x, p1.y);
		createMarker(p2.x, p2.y);

		return line;
	},


	/* ------------------------------------------------------------------------------------
	   moveInDirection / getHexDirection / getHexTurnCost - the CLIENT MIRROR of the three
	   statics of the same names in server/lib/mathlib.php.

	   Walkers of Sigma-957 (WALKERS_OF_SIGMA_PLAN.md 3.9, Stage 9): a Sensor Charge flies in
	   straight legs along the six hex axes and pays manoeuvres to change axis, and the client
	   has to draw exactly the courses the server will accept.

	   ⚠️ THE COMPASS IS hexagon.Offset's, NOT mathlib.offsetNeighbors underneath this.
	   Offset.prototype.neighbours is byte-for-byte the PHP mathlib::$neighbours table, and the
	   ORDER of that table is the whole meaning of a direction index; offsetNeighbors lists the
	   same six hexes in a different order, so reusing it would give the client a different
	   compass from the server's and every leg but due east would disagree.
	   Bearings run 0 = east, then clockwise in 60 degree steps.
	   ------------------------------------------------------------------------------------ */
	moveInDirection: function moveInDirection(pos, bearing, distance) {
		var index = ((((parseInt(bearing, 10) % 360) + 360) % 360) / 60) | 0;
		var current = new hexagon.Offset(parseInt(pos.q, 10), parseInt(pos.r, 10));

		for (var i = 0; i < distance; i++) {
			current = current.getNeighbourAtDirection(index);
		}

		return current;
	},

	/* The bearing of the straight run from `from` to `to`, or null when the two hexes are not on
	   a common hex axis. 0 when they are the same hex - callers must read that as "no leg".
	   Walked rather than derived from a pixel heading, for the reason in the header above. */
	getHexDirection: function getHexDirection(from, to) {
		var start = new hexagon.Offset(parseInt(from.q, 10), parseInt(from.r, 10));
		var end = new hexagon.Offset(parseInt(to.q, 10), parseInt(to.r, 10));
		var distance = start.distanceTo(end);
		if (distance <= 0) return 0;

		var bearings = [0, 60, 120, 180, 240, 300];
		for (var i = 0; i < bearings.length; i++) {
			if (mathlib.moveInDirection(start, bearings[i], distance).equals(end)) return bearings[i];
		}

		return null;
	},

	//Manoeuvres spent turning from one hex bearing to another: 1, 2 or 3, whichever way round the
	//compass is shorter, and 0 for no change.
	getHexTurnCost: function getHexTurnCost(fromBearing, toBearing) {
		var steps = Math.abs((parseInt(fromBearing, 10) - parseInt(toBearing, 10)) / 60) % 6;
		return Math.min(steps, 6 - steps);
	},

	// parity-aware 6 neighbours for your odd-row offset system
	offsetNeighbors: function offsetNeighbors(pos) {
		const q = pos.q, r = pos.r;
		const isOdd = (r % 2) !== 0;
		if (isOdd) {
			return [
				{ q: q + 1, r: r }, // right
				{ q: q - 1, r: r }, // left
				{ q: q - 1, r: r + 1 }, // upper-left (odd row)
				{ q: q - 1, r: r - 1 }, // lower-left (odd row)
				{ q: q, r: r + 1 }, // upper-right (shifted)
				{ q: q, r: r - 1 }  // lower-right (shifted)
			];
		} else {
			return [
				{ q: q + 1, r: r }, // right
				{ q: q - 1, r: r }, // left
				{ q: q + 1, r: r + 1 }, // upper-right (even row)
				{ q: q + 1, r: r - 1 }, // lower-right (even row)
				{ q: q, r: r + 1 }, // upper-left (shifted)
				{ q: q, r: r - 1 }  // lower-left (shifted)
			];
		}
	},

	// Returns all hexes with distance <= radius (excluding center)
	getNeighbouringHexes: function getNeighbouringHexes(position, radius = 0) {
		if (radius <= 0) return [];

		const seen = new Set();
		const key = p => `${p.q},${p.r}`;

		// mark center visited
		seen.add(key(position));

		// frontier starts at center
		let frontier = [{ q: position.q, r: position.r }];
		const results = [];

		// expand ring by ring
		for (let d = 1; d <= radius; d++) {
			const next = [];
			for (const node of frontier) {
				const neighs = mathlib.offsetNeighbors(node);
				for (const n of neighs) {
					const k = key(n);
					if (!seen.has(k)) {
						seen.add(k);
						next.push(n);
						results.push(n); // accumulate all nodes within <= radius
					}
				}
			}
			frontier = next;
		}

		return results; // array of {q,r} (same set your hardcoded radius=1/2 returned)
	},

	// Returns only the perimeter hexes at exactly distance == radius
	getPerimeterHexes: function getPerimeterHexes(position, radius = 0, hexOffsets = null, facing = 0) {
		if (hexOffsets && Array.isArray(hexOffsets) && hexOffsets.length > 0) {
			var hexes = [];
			//hexes.push({ q: position.q, r: position.r });
			hexOffsets.forEach(function (offset) {
				var newHex = mathlib.getRotatedHex(position, offset, facing);
				hexes.push(newHex);
			});
			return hexes;
		}

		if (radius <= 0) return [];

		const seen = new Set();
		const key = p => `${p.q},${p.r}`;
		seen.add(key(position));

		let frontier = [{ q: position.q, r: position.r }];

		for (let d = 1; d <= radius; d++) {
			const next = [];
			for (const node of frontier) {
				const neighs = mathlib.offsetNeighbors(node);
				for (const n of neighs) {
					const k = key(n);
					if (!seen.has(k)) {
						seen.add(k);
						next.push(n);
					}
				}
			}
			frontier = next;
		}

		return frontier; // only the outer ring (distance == radius)
	},

	rotateHex: function rotateHex(q, r, facing) {
		if (facing === 0) return { q: q, r: r };

		// Convert Offset (Odd-R) to Cube
		// Derived from OffsetCoordinate::toCube
		var x = q - (r + (r & 1)) / 2;
		var z = r;
		var y = -x - z;

		// Rotate Cube 'facing' times (Clockwise)
		// (x, y, z) -> (-z, -x, -y)
		for (var i = 0; i < facing; i++) {
			var newX = -z;
			var newY = -x;
			var newZ = -y;
			x = newX;
			y = newY;
			z = newZ;
		}

		// Convert Cube back to Offset (Odd-R)
		// Derived from CubeCoordinate::toOffset
		var newQ = x + (z + (z & 1)) / 2;
		var newR = z;

		return { q: Math.round(newQ), r: Math.round(newR) };
	},

	// New accurate function for getting a hex position based on a center, an offset (definition), and a facing.
	// Uses pixel conversion to ensure grid consistency.
	// Uses pixel conversion to ensure grid consistency.
	getRotatedHex: function (center, offset, facing) {
		// 1. Get pixel coordinates of the center
		var centerPx = coordinateConverter.fromHexToGame(center);

		// 2. Get pixel coordinates of the offset (relative to 0,0)
		// We use hexCoToPixel on the offset directly, then rotate the resulting vector.
		var zero = { q: 0, r: 0 };
		var zeroPx = coordinateConverter.fromHexToGame(zero);
		var offsetPx = coordinateConverter.fromHexToGame(offset);

		var vecX = offsetPx.x - zeroPx.x;
		var vecY = offsetPx.y - zeroPx.y;

		// 3. Rotate Vector by facing * 60 degrees (Clockwise)
		// Game Space is Y-Up (Cartesian). Standard Matrix is CCW.
		// To rotate CW in Y-Up, we use a negative angle.
		var angle = -facing * 60 * (Math.PI / 180);
		var rotX = vecX * Math.cos(angle) - vecY * Math.sin(angle);
		var rotY = vecX * Math.sin(angle) + vecY * Math.cos(angle);

		// 4. Add rotated vector to center pixel
		var targetPxX = centerPx.x + rotX;
		var targetPxY = centerPx.y + rotY;

		// 5. Convert back to Hex
		return coordinateConverter.fromGameToHex({ x: targetPxX, y: targetPxY });
	}


};
