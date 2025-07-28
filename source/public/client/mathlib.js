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
		function lcg(a) {return a * 48271 % 2147483647}
		seed = seed ? lcg(seed) : lcg(Math.random());
		return function() {return (seed = lcg(seed)) / 2147483648}
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
			//if Target has speed 0, consider Observer to have better Init! that would be better for firing arcs...
			//if Observer has speed 0 consider Target to have better Ini!
			if ( (shipManager.hasBetterInitive(observer, target) && (shipManager.movement.getSpeed(observer)!=0) ) || (shipManager.movement.getSpeed(target)==0) ) {
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


	doLinesIntersect: function(p1, p2, p3, p4) {
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
		

	checkLineOfSight: function checkLineOfSight(start, end, blockedHexes) {
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
					//if(gamedata.showLoS) mathlib.drawLine(startPixel, endPixel, 0xff00ff); // Magenta line if blocked
					//if(gamedata.showLoS) mathlib.drawHex(corners, 0xff00ff); // Magenta hex border					
					return true; // Line of sight is blocked
				}
			}
		}

		//if(gamedata.showLoS) mathlib.drawLine(startPixel, endPixel, 0x87ceeb); // Blue line
		return false; // Line of sight is clear
	},


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


	//Called in Phase Strategy to show LoS lines if LoS is toggled on.
	showLoS: function showLoS(shooter, target){
		var start = shipManager.getShipPosition(shooter);
		var end = shipManager.getShipPosition(target);
		var blockedHexes = weaponManager.getBlockedHexes();
		
		mathlib.checkLineOfSightSprite(start, end, blockedHexes);
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

	/* //Draws a simple line between two positions, replace by drawRuler() below.
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

	drawHex: function drawHex(corners, color = 0xff0000, fillColor = null) {
		if (!corners || corners.length < 6) return;

		// === Create the shape ===
		const shape = new THREE.Shape();
		shape.moveTo(corners[0].x, corners[0].y);
		for (let i = 1; i < corners.length; i++) {
			shape.lineTo(corners[i].x, corners[i].y);
		}
		shape.lineTo(corners[0].x, corners[0].y); // Close the shape

		const z = 100; // Ensure it's rendered behind other overlays

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
			...outlinePoints.map(p => new THREE.Vector3(p.x, p.y, 10)),
			new THREE.Vector3(corners[0].x, corners[0].y, 10) // Close loop
		]);

		const outlineMaterial = new THREE.LineBasicMaterial({ color });
		const outline = new THREE.Line(outlineGeometry, outlineMaterial);
		window.LosSprite.add(outline);

		return outline;
	},


	//Uses game/pixel coordinates not hex!
	drawRuler: function drawRuler(p1, p2, color = 0x00ffff) {
		if (!window.LosSprite) {
			window.LosSprite = new THREE.Group();
			window.webglScene.scene.add(window.LosSprite);
		}

		mathlib.clearLosSprite();

		// Create and add the line
		const material = new THREE.LineBasicMaterial({ color });
		const points = [
			new THREE.Vector3(p1.x, p1.y, 10),
			new THREE.Vector3(p2.x, p2.y, 10)
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

		// === Create distance label using same approach as your hex text ===
		const canvas = document.createElement('canvas');
		const TEXTURE_SIZE = 128;
		canvas.width = TEXTURE_SIZE;
		canvas.height = TEXTURE_SIZE;
		const ctx = canvas.getContext('2d');

		// Style similar to your existing system
		let fontSize = 110;
		ctx.fillStyle = 'white'; // Or any color you prefer
		ctx.textAlign = "center";
		ctx.textBaseline = "middle";

		const distanceText = distance.toFixed(0);
		ctx.font = `bold ${fontSize}px Arial`;
		ctx.fillText(distanceText, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2);

		// Create sprite from texture
		const texture = new THREE.Texture(canvas);
		texture.needsUpdate = true;

		const spriteMaterial = new THREE.SpriteMaterial({ map: texture, transparent: true });
		const sprite = new THREE.Sprite(spriteMaterial);

		// Position the label at the midpoint, above the line
		sprite.position.set(midX, midY, 500); // z = 12 so it sits above line
		sprite.scale.set(30, 30, 1); // Tune this size as needed

		window.LosSprite.add(sprite);

		return line;
	},


	//Returns 19 hexes around central position e.g. radius of 1
	getNeighbouringHexes: function getNeighbouringHexes(position, radius = 1) {
		if(radius == 1){
			let isOddRow = position.r % 2 !== 0; //Test hexes ODD (-1,-11) EVEN (-2,-12)
			let neighborOffsets = isOddRow 
				? [ 
					[+1,  0], // Right {q: 0, r: -11}
					[-1,  0], // Left {q: -2, r: -11}
					[ -1, +1], // Upper left {q: -2, r: -10}
					[ -1, -1], // Lower Left {q: -2, r: -12}
					[0, +1], // Upper Right (shifted) {q: -1, r: -10}
					[0, -1]  // Lower Right (shifted) {q: -1, r: -12}
				]
				: [
					[+1,  0], // Right {q: -1, r: -12}
					[-1,  0], // Left {q: -3, r: -12}
					[+1, +1], // Upper right {q: -1, r: -11}
					[+1, -1], // Down right {q: -1, r: -13}
					[0, +1], // Upper Left (shifted) {q: -2, r: -11}
					[0, -1]  // Lower Left (shifted) {q: -2, r: -13}
				];

			// Generate neighboring hexes
			return neighborOffsets.map(offset => ({
				q: position.q + offset[0],
				r: position.r + offset[1]
			}));
		}else if(radius == 2){
			//Radius 2.
			let isOddRow = position.r % 2 !== 0;
			let neighborOffsets = isOddRow 
				? [[+1, 0], [-1, 0], [-1, +1], [-1, -1], [0, +1], [0, -1],
				[+2, 0], [+1, -1], [+1, -2], [0, -2], [-1, -2], [-2, -1], 
				[-2, 0], [-2, +1], [-1, +2], [0, +2], [+1, +2], [+1, +1]]

				: [[+1, 0], [-1, 0], [+1, +1], [+1, -1], [0, +1], [0, -1], 
				[+2, 0], [+2, -1], [+1, -2], [0, -2], [-1, -2], [-1, -1], 
				[-2, 0], [-1, +1], [-1, +2], [0, +2], [+1, +2], [+2, +1]];

			return neighborOffsets.map(offset => ({
				q: position.q + offset[0],
				r: position.r + offset[1]
			}));
		}

    },

	//Returns exterior 12 hexes around central position e.g. radius of 2
	getPerimeterHexes: function getPerimeterHexes(position, radius = 2) {
			let isOddRow = position.r % 2 !== 0;
			let neighborOffsets = isOddRow 
				? [[+2, 0], [+1, -1], [+1, -2], [0, -2], [-1, -2], [-2, -1], 
				[-2, 0], [-2, +1], [-1, +2], [0, +2], [+1, +2], [+1, +1]]

				: [[+2, 0], [+2, -1], [+1, -2], [0, -2], [-1, -2], [-1, -1], 
				[-2, 0], [-1, +1], [-1, +2], [0, +2], [+1, +2], [+2, +1]];
	
			return neighborOffsets.map(offset => ({
				q: position.q + offset[0],
				r: position.r + offset[1]
			}));
	}

};
