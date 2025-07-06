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

	doLinesIntersect: function doLinesIntersect(p1, p2, p3, p4) {
		function crossProduct(a, b) {
			return a.x * b.y - a.y * b.x;
		}

		const d1 = { x: p2.x - p1.x, y: p2.y - p1.y };
		const d2 = { x: p4.x - p3.x, y: p4.y - p3.y };
		const denom = crossProduct(d1, d2);

		if (denom === 0) return false; // Lines are parallel

		const t = crossProduct({ x: p3.x - p1.x, y: p3.y - p1.y }, d2) / denom;
		const u = crossProduct({ x: p3.x - p1.x, y: p3.y - p1.y }, d1) / denom;

		// If t and u are between 0 and 1, the line segments intersect
		return t >= 0 && t <= 1 && u >= 0 && u <= 1;
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
					return true; // Line of sight is blocked
				}
			}
		}

		return false; // Line of sight is clear
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
