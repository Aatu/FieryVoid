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
	}

	BallisticIconContainer.prototype.consumeGamedata = function (gamedata, iconContainer, replayData = null) {
		this.ballisticIcons.forEach(icon => {
			if (gamedata.gamephase !== 1) icon.launchPosition = [];
			icon.used = false;
		});

		this.ballisticLineIcons.forEach(icon => icon.used = false);

		const ballistics = replayData ?? weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');

		ballistics.forEach(ballistic => {
			if (ballistic.turn === gamedata.turn || !replayData) {
				createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn, !!replayData);
				createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn, !!replayData);
			}
		});

		this.ballisticIcons = this.ballisticIcons.filter(icon => {
			if (!icon.used) {
				if (icon.launchSprite) this.scene.remove(icon.launchSprite.mesh);
				if (icon.targetSprite) {
					if (icon.targetId !== -1) {
						iconContainer.getById(icon.targetId).mesh.remove(icon.targetSprite.mesh);
					} else {
						this.scene.remove(icon.targetSprite.mesh);
					}
				}
				return false;
			}
			return true;
		});

		generateBallisticLines.call(this);
		generateTerrainHexes.call(this, gamedata);
		generateReinforcementHexes.call(this, gamedata);
	};

	function generateBallisticLines() {
		const oldIcons = this.ballisticLineIcons;
		const isFriendlyLinesVisible = oldIcons.some(icon => icon.lineSprite?.isVisible && icon.isFriendly);
		const isEnemyLinesVisible = oldIcons.some(icon => icon.lineSprite?.isVisible && !icon.isFriendly);

		this.ballisticLineIcons = oldIcons.filter(icon => {
			if (!icon.used) {
				if (icon.lineSprite) this.scene.remove(icon.lineSprite.mesh);
				return false;
			}

			if (icon.lineSprite) {
				const shouldBeVisible = icon.isFriendly ? isFriendlyLinesVisible : isEnemyLinesVisible;
				icon.lineSprite[shouldBeVisible ? 'show' : 'hide']();
				icon.lineSprite.isVisible = shouldBeVisible;
			}

			return true;
		});
	}

	function generateTerrainHexes(gamedata) {
		if (gamedata.gamephase === -1) return; //Don't bother during Deployment phase.

		gamedata.ships.filter(ship => ship.Huge > 0).forEach(ship => {
			const position = shipManager.getShipPosition(ship);
			/*const perimeterHexes = (ship.Huge === 2)
				? mathlib.getPerimeterHexes(position, ship.Huge)
				: mathlib.getNeighbouringHexes(position, ship.Huge);
			*/
			const perimeterHexes = mathlib.getPerimeterHexes(position, ship.Huge); //Position + radius passed.

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

	function generateReinforcementHexes(gamedata) {
		if (gamedata.gamephase == -1) return;

		gamedata.ships
			.filter(ship => shipManager.getTurnDeployed(ship) == gamedata.turn && gamedata.turn > 1 && !shipManager.shouldBeHidden(ship))
			.forEach(ship => {
				const pos = shipManager.movement.getPositionAtStartOfTurn(ship, gamedata.turn);

				const posGame = this.coordinateConverter.fromHexToGame(pos);
				const sprite = new BallisticSprite(posGame, "hexBlue", `Reinforcement`);
				this.scene.add(sprite.mesh);

				this.ballisticIcons.push({
					id: -6,
					shooterId: ship.id,
					targetId: ship.id,
					launchPosition: pos,
					position: new hexagon.Offset(pos.x, pos.y),
					launchSprite: sprite,
					targetSprite: sprite,
					used: true
				});
			});
	}


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
		if (replay) {
			if (ballistic.damageclass === 'PersistentEffectPlasma' && ballistic.targetid === -1 && ballistic.notes !== 'PlasmaCloud') return;
		}

		if (ballistic.damageclass === 'Sweeping') return;

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
		if (ballistic.type === 'normal' || ballistic.type === 'prefiring') {
			launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
		} else {
			launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
		}

		let weapon = null;
		let modeName = null;

		if (!shooter.flight && ballistic.weaponid in shooter.systems) {
			weapon = shooter.systems[ballistic.weaponid];
			modeName = weapon?.firingModes?.[ballistic.firingMode] || null;
		}

		let targetPosition = null;
		let targetIcon = null;
		let splash = false;

		if (ballistic.targetid === -1 && ballistic.x !== "null" && ballistic.y !== "null") {
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		} else if (ballistic.targetid && ballistic.targetid !== -1) {
			targetIcon = iconContainer.getById(ballistic.targetid);
			targetPosition = { x: 0, y: 0 }; // placeholder — the mesh will handle it
		}

		if (!shooter.flight && weapon?.noTargetHexIcon) {
			targetPosition = launchPosition;
		}

		// Mode-specific icon logic
		if (modeName) {
			const modeMap = {
				'Shredder': { type: 'hexBlue', text: 'Shredder', color: '#00b8e6' },
				'Defensive Plasma Web': { type: 'hexGreen', color: '', color: '#787800' },
				'Anti-Fighter Plasma Web': { type: 'hexGreen', text: 'Plasma', color: '#787800' },
				'Psychic Field': { type: 'hexRed', text: 'Psychic', color: '#e6140a' },
				'Second Sight': { type: 'hexPurple', text: 'Second Sight', color: '#7f00ff' },
				'Energy Mine': { type: 'hexRed', text: 'Energy Mine', color: '#e6140a' },
				'Ion Storm': { type: 'hexPurple', text: 'Ion Field', color: '#7f00ff' },
				'Jammer': { type: 'hexPurple', text: 'Jammer', color: '#7f00ff' },
				'Proximity Launcher': { type: 'hexRed', text: 'Proximity Laser', color: '#e6140a' },
				'Proximity Laser': { type: 'hexRed', text: 'Proximity Laser', color: '#e6140a' },
				'Thought Wave': { type: 'hexPurple', text: 'Thought Wave', color: '#bc3782' },
				'1-Blanket Shield': { type: 'hexGreen', text: 'Shade Modulator', color: '#008000' },
				'3-Blanket Shade': { type: 'hexYellow', text: 'Shade Modulator', color: '#787800' },
				'Transverse Jump': { type: 'hexBlue', text: 'Transverse Jump', color: '#787800' },
				'Standard - GN': { type: 'hexGreen', text: 'Gravity Net Standard', color: '#008000' },
				'Priorty - GN': { type: 'hexGreen', text: 'Gravity Net PRIORITY', color: '#787800' },
			};

			if (modeName == 'Transverse Jump' && !shipManager.isDetectedTorvalus(shooter, 20) && !gamedata.isMyorMyTeamShip(shooter)) return;

			const match = modeMap[modeName];
			if (match) {
				targetType = match.type;
				text = match.text || text;
				textColour = match.color || textColour;

				// Call splash hex generation for cases where weapon affects more than one hex
				if (['Shredder', 'Energy Mine', 'Ion Storm', 'Jammer', '1-Blanket Shield', '3-Blanket Shade'].includes(modeName)) {
					if (gamedata.thisplayer === shooter.userid || replay) {
						let sizes = [];

						switch (modeName) {
							case 'Ion Storm':
								sizes = [1, 2];
								break;
							case 'Jammer':
								sizes = [5];
								break;
							case '1-Blanket Shield':
								sizes = [3];
								break;
							case '3-Blanket Shade':
								sizes = [5];
								break;
							default: // Shredder / Energy Mine
								sizes = [1];
						}

						sizes.forEach(size => {
							generateSplashHexes.call(
								this,
								ballistic.id,
								targetPosition,
								ballistic.shooterid,
								ballistic.targetid,
								size,
								match.type
							);
						});

						splash = true;
					}
				}
			}

			// Damage class-based override logic
			if (ballistic.damageclass && modeName) {
				switch (ballistic.damageclass) {
					case 'MultiModeHex':
						targetType = 'hexRed';
						text = modeName;
						textColour = '#e6140a';
						break;
					case 'support':
						targetType = 'hexGreen';
						iconImage = './img/allySupport.png';
						break;
				}
			}
		}
		// LAUNCH SPRITE
		let launchSprite = null;
		if (
			!getByLaunchPosition(launchPosition, this.ballisticIcons) &&
			ballistic.notes !== 'PersistentEffect' &&
			ballistic.type !== 'normal' &&
			ballistic.damageclass !== 'support'
		) {
			const launchType = gamedata.isMyOrTeamOneShip(shooter) ? 'hexYellow' : 'hexOrange';
			launchSprite = new BallisticSprite(launchPosition, launchType);
			scene.add(launchSprite.mesh);
		}

		// TARGET SPRITE
		let targetSprite = null;
		if (!getByTargetIdOrTargetPosition(targetPosition, ballistic.targetId, this.ballisticIcons)) {
			if (targetPosition) {
				targetSprite = new BallisticSprite(targetPosition, targetType, text, textColour, iconImage);
				if (targetIcon) {
					targetIcon.mesh.add(targetSprite.mesh);
				} else {
					scene.add(targetSprite.mesh);
				}
			}
		}

		this.ballisticIcons.push({
			id: ballistic.id,
			shooterId: ballistic.shooterid,
			targetId: ballistic.targetid,
			launchPosition,
			position: new hexagon.Offset(ballistic.x, ballistic.y),
			launchSprite,
			targetSprite,
			used: true,
			splash: true
		});
	}

	const getByLaunchPosition = (position, icons) => icons.find(icon => icon.launchPosition.x === position.x && icon.launchPosition.y === position.y)

	const getByTargetIdOrTargetPosition = (position, targetId, icons) => icons.find(icon => position && ((icon.position.x === position.x && icon.position.y === position.y) || (targetId !== -1 && icon.targetId === targetId)))


	function updateBallisticIcon(icon) {
		icon.used = true;
	}

	BallisticIconContainer.prototype.hide = function () {
		this.ballisticIcons.forEach(icon => {
			icon.launchSprite?.hide();
			icon.targetSprite?.hide();
		});
		return this;
	};

	BallisticIconContainer.prototype.show = function () {
		this.ballisticIcons.forEach(icon => {
			icon.launchSprite?.show();
			icon.targetSprite?.show();
		});
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
		} else if (ballistic.notes !== 'PersistentEffect') {
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
		let weapon = !shooter.flight ? shooter.systems[ballistic.weaponid] : null;
		let modeName = weapon?.firingModes?.[ballistic.firingMode] ?? null;

		// Get launch position (may be overwritten later)
		let launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn)?.position);
		let targetPosition;

		// Determine target position
		if (replay && targetIcon) {
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
			const launcherHex = weaponManager.getFiringHex(shooter, weapon);
			launchPosition = this.coordinateConverter.fromHexToGame(launcherHex);
			type = 'red';
			if (ballistic.damageclass == 'Targeter') type = 'yellow';
		}

		// Handle specific modeName cases
		if (ballistic.type === 'normal' || ballistic.type === 'prefiring') {
			launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn)?.position);

			const modeColorMap = {
				'Shredder': 'blue',
				'Defensive Plasma Web': 'green',
				'Anti-Fighter Plasma Web': 'green',
				'Transverse Jump': 'blue'
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
			-3,
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

		// Control line visibility based on team and current toggle state
		const isFriendlyLinesVisible = this.ballisticLineIcons.some(
			icon => icon.lineSprite?.isVisible && icon.isFriendly
		);
		const isEnemyLinesVisible = this.ballisticLineIcons.some(
			icon => icon.lineSprite?.isVisible && !icon.isFriendly
		);

		const currentIcon = this.ballisticLineIcons.find(icon => icon.id === ballistic.id);
		if (currentIcon) {
			currentIcon.lineSprite.isVisible =
				(isFriendly && isFriendlyLinesVisible) ||
				(!isFriendly && isEnemyLinesVisible);
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
		this.ballisticLineIcons.forEach(icon => {
			if (shipIds.includes(icon.shooterId) && icon.lineSprite) {
				const visible = icon.lineSprite.isVisible;
				icon.lineSprite[visible ? 'hide' : 'show']();
				icon.lineSprite.isVisible = !visible;
			}
		});
		return this;
	};

	BallisticIconContainer.prototype.hideLines = function (ships) {
		const shipIds = ships.map(s => s.id);
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
			return "rgba(0, 128, 0)";
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
			// Toggle visibility
			this.hexNumberMesh.visible = !this.hexNumberMesh.visible;
			return;
		}

		// Define grid dimensions based on hex count
		const gridWidth = 72; // Adjust based on your hex layout
		const gridHeight = 48;
		const hexSize = 50;

		// Create single large texture with all hex numbers
		const largeTexture = createLargeHexNumberTexture(gridWidth, gridHeight, hexSize);

		// Hexagon grid dimensions (corrected aspect ratio)
		const totalWidth = gridWidth * hexSize * 2;
		const totalHeight = gridHeight * hexSize * Math.sqrt(4);

		// Create a plane to apply the texture (or adjust for hexagonal shape)
		const geometry = new THREE.PlaneGeometry(totalWidth, totalHeight);
		const material = new THREE.MeshBasicMaterial({
			map: largeTexture,
			transparent: true
		});

		this.hexNumberMesh = new THREE.Mesh(geometry, material);
		this.hexNumberMesh.position.set(502.5, -651, -1); // Adjust as needed	
		scene.add(this.hexNumberMesh);
	};

	function createLargeHexNumberTexture(gridWidth, gridHeight, hexSize, textColour = "#ffffff") {
		const HEX_WIDTH = Math.sqrt(3) * hexSize;  // Corrected for side-standing hexagons
		const HEX_HEIGHT = 2 * hexSize; // Corrected for vertical stacking
		const SCALE_FACTOR = 2;  // Increase resolution for sharp text
		const TEXTURE_WIDTH = gridWidth * HEX_WIDTH * SCALE_FACTOR;
		const TEXTURE_HEIGHT = gridHeight * HEX_HEIGHT * SCALE_FACTOR;

		// Create canvas with refined dimensions
		const canvas = document.createElement("canvas");
		canvas.width = TEXTURE_WIDTH;
		canvas.height = TEXTURE_HEIGHT;
		const ctx = canvas.getContext("2d");

		// Clear the canvas
		ctx.clearRect(0, 0, TEXTURE_WIDTH, TEXTURE_HEIGHT);

		// Set text properties
		const fontSize = Math.floor(hexSize * 0.2 * SCALE_FACTOR);  // Smaller text but sharp
		ctx.globalAlpha = 0.85;
		ctx.font = `bold ${fontSize}px Arial`;
		ctx.fillStyle = textColour;
		ctx.textAlign = "center";
		ctx.textBaseline = "middle";
		ctx.globalAlpha = 0.6; // 50% transparency

		let number = 1;

		for (let r = 0; r < gridHeight; r++) {
			for (let q = 0; q < gridWidth; q++) {
				let x = q * HEX_WIDTH * 1.7315 + HEX_WIDTH / 2; // REDUCED COLUMN SPACING			
				let y = r * HEX_HEIGHT * 1.5 + HEX_HEIGHT / 2; // INCREASED ROW SPACING

				// Offset odd rows for staggered hex layout
				if (r % 2 !== 0) x += HEX_WIDTH * 0.855;

				ctx.fillText(String(number).padStart(4, '0'), x, y);
				number++;
			}
		}

		// Convert canvas to a texture
		const texture = new THREE.Texture(canvas);
		texture.needsUpdate = true;
		return texture;
	}

	return BallisticIconContainer;

}();

/*//All the old unoptimised code, keep here until I'm sure the new stuff above works! - DK June 25
window.BallisticIconContainer = function () {

	function BallisticIconContainer(coordinateConverter, scene) {
		this.ballisticIcons = [];
		this.ballisticLineIcons = []
		this.coordinateConverter = coordinateConverter;
		this.scene = scene;
		this.zoomScale = 1;
		this.hexNumberIcons = [];
		this.hexNumbersGenerated = false;
	}

	BallisticIconContainer.prototype.consumeGamedata = function (gamedata, iconContainer, replayData = null) {
		this.ballisticIcons.forEach(function (ballisticIcon) {
			if(gamedata.gamephase !== 1) ballisticIcon.launchPosition = []; //If not cleared, doesn't always display launch hex between Initial and Movement/Firing without browser refresh - DK 12.24
			ballisticIcon.used = false;
		});

		this.ballisticLineIcons.forEach(function (lineIcon) {
			lineIcon.used = false;
		});
			   
		if(replayData){ //True marker is passed when Replay
			var allBallistics = replayData;
			
			allBallistics.forEach(function (ballistic) {
				if(ballistic.turn === gamedata.turn){ 
					createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn, true); 
					createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn, true);
				}	            
			}, this);
					
		} else {
			var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');			
			allBallistics.forEach(function (ballistic) {
				createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn);
				createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn);
			}, this);
		}
		
		this.ballisticIcons = this.ballisticIcons.filter(function (icon) {
			if (!icon.used) {
			    
				if (icon.launchSprite) {
					this.scene.remove(icon.launchSprite.mesh);
				}

				if (icon.targetSprite) {
					if (icon.targetId !== -1) {
						iconContainer.getById(icon.targetId).mesh.remove(icon.targetSprite.mesh);
					} else {
						this.scene.remove(icon.targetSprite.mesh);
					}
				}
				return false;
			}

			return true;
		}, this);

		//Now create ballistic lines
		generateBallisticLines.call(this);
		//Now create hex icons to illustrate large terrain.
		generateTerrainHexes.call(this, gamedata);
		//Create blue hex icons for where ships are deploying later.
		generateReinforcementHexes.call(this, gamedata);

	};


	function generateBallisticLines() {
		//Remove old line sprites when they are no longer required or being recreated.
		this.ballisticLineIcons = this.ballisticLineIcons.filter(function (lineIcon) {
			if (!lineIcon.used) {
				if (lineIcon.lineSprite) {
					this.scene.remove(lineIcon.lineSprite.mesh);           	
				}
			return false;	
			}	

		//This section looks to see if Ballistic Lines are showing at moment consumeGamedata() is called.
		var isFriendlyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === true
		);

		var isEnemyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === false
		); 
			    
		this.ballisticLineIcons.forEach(lineIcon => {
		  if (lineIcon.lineSprite) {
			if (lineIcon.isFriendly) {
			  // Handle friendly lines
			  if (isFriendlyLinesVisible) {
				lineIcon.lineSprite.show();
				lineIcon.lineSprite.isVisible = true;
			  } else {
				lineIcon.lineSprite.hide();
				lineIcon.lineSprite.isVisible = false;
			  }
			} else {
			  // Handle enemy lines
			  if (isEnemyLinesVisible) {
				lineIcon.lineSprite.show();
				lineIcon.lineSprite.isVisible = true;
			  } else {
				lineIcon.lineSprite.hide();
				lineIcon.lineSprite.isVisible = false;
			  }
			}
		  }
		});
		
			return true;
		}, this);
	}


	function generateTerrainHexes(gamedata) {
		// Filter for ships with Huge value
		gamedata.ships
		.filter(ship => ship.Huge > 0) // Find Huge Terrain
		.forEach(ship => {
			if(gamedata.gamephase !== -1){ //Don't generate sprites until Terrain is in place!
				const position = shipManager.getShipPosition(ship); // Get ship's position
				let perimeterHexes = [];
				// Get neighboring hexes based on the ship's size (Huge)
				if(ship.Huge == 2){
					perimeterHexes = mathlib.getPerimeterHexes(position, ship.Huge);
				}else{
					perimeterHexes = mathlib.getNeighbouringHexes(position, ship.Huge);
				}	

				// Create sprites for neighboring hexes
				perimeterHexes.forEach(neighbour => {
					var neighbourPosGame = this.coordinateConverter.fromHexToGame(neighbour);
					const neighbourSprite = new BallisticSprite(neighbourPosGame, "hexWhite");
					this.scene.add(neighbourSprite.mesh);

					this.ballisticIcons.push({
						id: -5,
						shooterId: ship.id,
						targetId: ship.id,
						launchPosition: neighbour,
						position: new hexagon.Offset(neighbourPosGame.x, neighbourPosGame.y),
						launchSprite: neighbourSprite,
						targetSprite: neighbourSprite,
						used: true
					});
				
				});
			}	

		});
	}


	function generateReinforcementHexes(gamedata) {
		// === 2. Handle Not Deployed Ships (Blue Hexes) ===
		gamedata.ships
			.filter(ship => shipManager.getTurnDeployed(ship) > gamedata.turn) // Only undeployed ships
			.filter(ship => gamedata.isMyorMyTeamShip(ship)) // Only own Team ships.		
			.forEach(ship => {
				const turnDeploys = shipManager.getTurnDeployed(ship);
				const position = shipManager.getShipPosition(ship);
				const posGame = this.coordinateConverter.fromHexToGame(position);
					const reinforceSprite = new BallisticSprite(posGame, "hexBlue", "Deploys on Turn " + turnDeploys + "");
					this.scene.add(reinforceSprite.mesh);

					this.ballisticIcons.push({
						id: -6,
						shooterId: ship.id,
						targetId: ship.id,
						launchPosition: position,
						position: new hexagon.Offset(position.x, position.y),
						launchSprite: reinforceSprite,
						targetSprite: reinforceSprite,
						used: true
					});

			});
	}



	function createOrUpdateBallistic(ballistic, iconContainer, turn, replay = false) {
		var icon = getBallisticIcon.call(this, ballistic.id);

		if (icon && ballistic.notes != 'PersistentEffect' && ballistic.notes != 'Split') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
			updateBallisticIcon.call(this, icon, ballistic, iconContainer, turn);
		} else {   	
			createBallisticIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
		}
		
	}		

	function updateBallisticIcon(icon, ballistic, iconContainer, turn) {
		icon.used = true;
	}

	const getByLaunchPosition = (position, icons) => icons.find(icon => icon.launchPosition.x === position.x && icon.launchPosition.y === position.y)

	const getByTargetIdOrTargetPosition = (position, targetId, icons) => icons.find(icon => position && ((icon.position.x === position.x && icon.position.y === position.y) || (targetId !== -1 && icon.targetId === targetId )) )

	BallisticIconContainer.prototype.hide = function () {
		this.ballisticIcons.forEach(function (icon) {
			if (icon.launchSprite) {
				icon.launchSprite.hide();
			}
			if (icon.targetSprite) {
				icon.targetSprite.hide();
			}
		});

		return this;
	};

	BallisticIconContainer.prototype.show = function () {
		this.ballisticIcons.forEach(function (icon) {
			if (icon.launchSprite) {
				icon.launchSprite.show();
			}
			if (icon.targetSprite) {
				icon.targetSprite.show();
			}
		});

		return this;
	};	

	BallisticIconContainer.prototype.onEvent = function (name, payload) {
		var target = this['on' + name];
		if (target && typeof target == 'function') {
			target.call(this, payload);
		}
	};

	BallisticIconContainer.prototype.onZoomEvent = function (payload) {
		var zoom = payload.zoom;
		if (zoom <= 0.5) {
			this.zoomScale = 2 * zoom;
			this.ballisticLineIcons.forEach(function (lineIcon) {
				lineIcon.lineSprite.setLineWidth(this.zoomScale * 2);
			}, this);
		} else {
			this.zoomScale = 1;
		} 
	};

	function getBallisticIcon(id) {
		return this.ballisticIcons.filter(function (icon) {
			return icon.id === id;
		}).pop();
	}


	function createOrUpdateBallisticLines(ballistic, iconContainer, turn, replay = false) {
		var lineIcon = getBallisticLineIcon.call(this, ballistic.id);

		if (lineIcon && ballistic.notes != 'PersistentEffect' && ballistic.notes != 'Split') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
			if(replay){
				createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);	        	   	
			}else{	
				updateBallisticLineIcon.call(this, lineIcon, ballistic, iconContainer, turn);
			}	
		} else {   	
			if(ballistic.notes != 'PersistentEffect') createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
		}
		
	}	


	function updateBallisticLineIcon(lineIcon, ballistic, iconContainer, turn) {
		lineIcon.used = true;

		if(ballistic.targetid === -1) return; //No need to update further for AoE ballistics (they don't move) 

		var wasVisible = false; //Variable to track if destroyed lines were visible. If one was, they all were.

		// Destroy lines where the ship is either the target.
		if (lineIcon.lineSprite.isVisible) wasVisible = true;
		lineIcon.lineSprite.destroy();
		this.scene.remove(lineIcon.lineSprite.mesh);

		//Now recreate them.
		if(ballistic.notes != 'PersistentEffect') createBallisticLineIcon.call(this, ballistic, iconContainer, gamedata.turn, this.scene);

		//Check if lines were visible and if so continue to show.
		if(!wasVisible){
			lineIcon.lineSprite.hide();
			lineIcon.lineSprite.isVisible = false;	 	            
		}else{
			lineIcon.lineSprite.show();
			lineIcon.lineSprite.isVisible = true;	            		
		}
	}	

	//New method that toggles Ballistic Lines on and off.
	BallisticIconContainer.prototype.toggleBallisticLines = function (ships) {
		if (this.ballisticLineIcons) {
			this.ballisticLineIcons.forEach(function (lineIcon) {
				if (lineIcon.lineSprite) {
					if (ships.some(ship => ship.id === lineIcon.shooterId)) {
						if (!lineIcon.lineSprite.isVisible){		                	
							lineIcon.lineSprite.show();
							lineIcon.lineSprite.isVisible = true;	                    
						}else{
							lineIcon.lineSprite.hide();
							lineIcon.lineSprite.isVisible = false;	 		                    		             	
						}
					}    
				}	            
			});
		}
		return this;
	};

	BallisticIconContainer.prototype.hideLines = function (ships) {
		this.ballisticLineIcons.forEach(function (lineIcon) {
			if (lineIcon.lineSprite) {
				if (ships.some(ship => ship.id === lineIcon.shooterId)) {
					lineIcon.lineSprite.hide();
					lineIcon.lineSprite.isVisible = false;
				}
			}			 	
		});

		return this;
	};

	BallisticIconContainer.prototype.showLines = function (ships) {
		this.ballisticLineIcons.forEach(function (lineIcon) {
			if (lineIcon.lineSprite) {
				if (ships.some(ship => ship.id === lineIcon.shooterId)) {        	
					lineIcon.lineSprite.show();
					lineIcon.lineSprite.isVisible = true;
				}
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
			}else{		    
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
				if(!wasVisibleTarget){
					lineIcon.lineSprite.hide();
					lineIcon.lineSprite.isVisible = false;	 	            
				}else{
					lineIcon.lineSprite.show();
					lineIcon.lineSprite.isVisible = true;	            		
				}
			}
		});        
	};
*/
/*	
	function createBallisticIcon(ballistic, iconContainer, turn, scene, replay = false) {

			if(replay){
				if(ballistic.damageclass == 'PersistentEffectPlasma' && ballistic.targetid == -1 && ballistic.notes != 'PlasmaCloud') return;
			}
			if(ballistic.damageclass == 'Sweeping')	return;	//For Shadow Slicers, Gravs Beams etc. Let's just rely on lines and targeting tooltip and not clutter with Hex colours.	

			var shooterIcon = iconContainer.getById(ballistic.shooterid);	
			var targetType = 'hexRed'; //Default red hex if none of the later conditions are true.
			var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
			var text = ""; //Additional variable that can pass text to new BallisticSprite()
			var textColour = ""; //Additional variable that chooses base text colour for new BallisticSprite()
			var iconImage = null; //Additional variable that can pass images to new BallisticSprite()!
			
			//New variables to enhance Ballistic Icons further! - DK 10.24
			var shooter = shooterIcon.ship; //Get shooter info.
			var modeName = null;
			
			if(!shooter.flight){ //Fighters don't currently have hex target weapons (plus, would need to find weaponid differently)
				var weapon = shooter.systems[ballistic.weaponid]; //Find weapon			
				var modeName = weapon.firingModes[ballistic.firingMode]; //Get actual Firing Mode name, so we can be more specific below!
			}

			if (ballistic.type == 'normal') { //it's direct fire after all!
				launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
				if(modeName){
					switch (modeName) {			
					case 'Shredder': //Vree Anti-Matter Shredder
							targetType = 'hexBlue';
							text = "Shredder";
							textColour = "#00b8e6";				        
					break;
					case 'Defensive Plasma Web': //Pak'ma'ra Plasma Web Defensive
							targetType = 'hexGreen';
							textColour = "#787800";				        	
					break;									
					case 'Anti-Fighter Plasma Web': //Pak'ma'ra Plasma Web Offensive
							targetType = 'hexGreen';
							text = 'Plasma';
							textColour = "#787800";				        	
					break;
					case 'Psychic Field': //Thirdspace Psychic Field
							targetType = 'hexRed';
							text = "Psychic";
							textColour = "#e6140a";		        
					break;						
					case 'Second Sight': //Mindrider Second Sight
							targetType = 'hexPurple';
							text = "Second Sight";
							textColour = "#7f00ff";			        
					break;								
					default:
							targetType = 'hexRed';
					break;													        
					}				
				}

			}else if (ballistic.targetid == -1){ //Maybe its nice to have other colours for certain types of hex targetted weapons?
				
				if(modeName){
					switch (modeName) {			
					case 'Energy Mine': //Narn Energy Mine
							targetType = 'hexRed';
							text = "Energy Mine";
							textColour = "#e6140a";				        
					break;				
					case 'Ion Storm': //Cascor Ion Field
							targetType = 'hexPurple';
							text = "Ion Field";
							textColour = "#7f00ff";				        
					break;
					case 'Jammer': //Jammer Missile
							targetType = 'hexPurple';
							text = "Jammer";
							textColour = "#7f00ff";		        
						break;					
					case 'Anti-Fighter Plasma Web': //Pak'ma'ra Plasma Web Persistent Effect
							targetType = 'hexGreen';
							text = 'Plasma';
							textColour = "#787800";				        	
					break;
					case 'Proximity Launcher': //KL Proximity Laser
							targetType = 'hexRed';
							text = "Proximity Laser";
							textColour = "#e6140a";		        
					break;
					case 'Thought Wave': //Mindrider Thoughwave
							targetType = 'hexPurple';
							text = "Thought Wave";
							textColour = "#bc3782"; //Actually a pink-purple, as it blends with luanch hex Orange!	 		        
					break;													        
					}				
				}
			} 
			 
		//Ballistic Mines / Support etc need to be handled separately using damageClass/initializationUpdate method! - DK 10.24
		if(ballistic.damageclass){
			if(modeName){
				switch (ballistic.damageclass) {	
				case 'MultiModeHex': //Hex-Weapons with multiple modes.
					targetType = 'hexRed';
					text = modeName;					
					textColour = "#e6140a";		        
				break;											
				case 'support': //Generic Support icon for these type of weapons. 06.24 - DK	
					targetType = 'hexGreen';
					iconImage = "./img/allySupport.png"; 		        
				break;
				}
			}		 
		} 
					
			var targetPosition = null;
			var targetIcon = null;

			if (ballistic.targetid === -1 && ballistic.x !== "null" && ballistic.y !== "null") {
				targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
			} else if (ballistic.targetid && ballistic.targetid !== -1) {
				targetIcon = iconContainer.getById(ballistic.targetid);
				targetPosition = { x: 0, y: 0 };
			}

			if(!shooter.flight){ //Don't create target hex for certain ship weapons.
				if(weapon.noTargetHexIcon) targetPosition = launchPosition;
			}	

			//Create orange launch icon on firing ship.
			var launchSprite = null;
		    
			//Don't create launch sprite for duplicates, persistent effects or Direct Fire/Support Weapon 
			if ((!getByLaunchPosition(launchPosition, this.ballisticIcons)) && ballistic.notes != 'PersistentEffect' && ballistic.type !== 'normal' && ballistic.damageclass !== 'support') {        	
					if(gamedata.isMyOrTeamOneShip(shooter)){
						launchSprite = new BallisticSprite(launchPosition, 'hexYellow');       
						scene.add(launchSprite.mesh);	        		
						  }else{
						launchSprite = new BallisticSprite(launchPosition, 'hexOrange');       
						scene.add(launchSprite.mesh);
					}
			}

			var targetSprite = null;

			if (!getByTargetIdOrTargetPosition(targetPosition, ballistic.targetId, this.ballisticIcons)) {	
				if (targetIcon && targetPosition) {
					targetSprite =  new BallisticSprite(targetPosition, targetType, text, textColour, iconImage);//'hex');
					targetIcon.mesh.add(targetSprite.mesh);
				} else if (targetPosition) {
					targetSprite =  new BallisticSprite(targetPosition, targetType, text, textColour, iconImage);//'hex');
					scene.add(targetSprite.mesh);
				}
			}


			this.ballisticIcons.push({
				id: ballistic.id,
				shooterId: ballistic.shooterid,
				targetId: ballistic.targetid,
				launchPosition: launchPosition,
				position: new hexagon.Offset(ballistic.x, ballistic.y),
				launchSprite: launchSprite,
				targetSprite: targetSprite,
				used: true
			});
    
		
	}//endof createBallisticIcon()
*/

/*
	function createBallisticLineIcon(ballistic, iconContainer, turn, scene, replay = false) {

		if (ballistic.targetid === -1 && ballistic.x == "null" && ballistic.y == "null") return; // Skip creation of enemy hidden weapons, can cause visual bugs.

		var shooterIcon = null;		
		shooterIcon = iconContainer.getById(ballistic.shooterid);        
		var targetIcon = null;
		targetIcon = iconContainer.getById(ballistic.targetid);

		//Get Launch Position and Target Position
		var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);

		var targetPosition = null;

		if(targetIcon && ballistic.targetid !== -1){
			targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovement(turn).position);
		}else{
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		}

		//Slightly different method for Replays.  Could I make a shipManager.movement.getLastCommitedMoveONTURN(targetIcon.ship) function???	
		if(replay && targetIcon) targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovementOnTurn(turn).position);	

		//Get shooter and modeName from it.
		var shooter = shooterIcon.ship; //Get shooter info.
		var modeName = null;
		var weapon = null;	
		if(!shooter.flight){ //Fighters would need to find weaponid differently
			weapon = shooter.systems[ballistic.weaponid]; //Find weapon			
			modeName = weapon.firingModes[ballistic.firingMode]; //Get actual Firing Mode name
		}	

		if(!shooter.flight){ //Don't create target hex for certain ship weapons.
			if(weapon.noTargetHexIcon) targetPosition = launchPosition;
		}

		//Don't create a line if ballistic is in same hex (or positions are null/undefined.
		if (launchPosition == null || targetPosition == null || 
			(launchPosition.x === targetPosition.x && 
			 launchPosition.y === targetPosition.y && 
			 launchPosition.z === targetPosition.z)) {
			return;
		} 
				
		var type = 'white'; //Default white line if none of the later conditions are true.
		if(gamedata.isMyOrTeamOneShip(shooterIcon.ship)){
			type = 'yellow';				
		}else{
			type = 'orange';				
		}

		//Create line for Proximity Laser from launcher targeted hex.
		if (weapon && weapon.hasSpecialLaunchHexCalculation) {
			var launcherHex = weaponManager.getFiringHex(shooter, weapon);
			launchPosition = this.coordinateConverter.fromHexToGame(launcherHex);
			type = 'red';
		}

		if(ballistic.type == 'normal'){
				launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn).position);			
				if(modeName){
					switch (modeName) {			
						case 'Shredder': //Vree Anti-Matter Shredder
							type = 'blue';				        
						break;
						case 'Defensive Plasma Web': //Pak'ma'ra Plasma Web Defensive
							type = 'green';				        	
						break;									
						case 'Anti-Fighter Plasma Web': //Pak'ma'ra Plasma Web Offensive
							type = 'green';				        	
						break;									
						default:
							type = 'white';
						break;													        
					}				
				}	 
		}
		
		if(ballistic.damageclass){
			switch (ballistic.damageclass) {	
				case 'support': //Generic Support icon for these type of weapons. 06.24 - DK	
					type = 'green';
					launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn).position); //More important to show where ship support weapon originates, not hex.								        
				break;
				case 'Sweeping': //Shadow Slicer
					type = 'purple'; //Default for slicers
					if(weapon.weaponClass == "Gravitic"){
						type = 'green'; //But now other weapon types use sweeping.
					}else if(weapon.weaponClass == "Psychic"){ //Thirdspace Psionic Concentrator
						type = 'red';
					}else if(weapon.weaponClass == "Molecular" && !(weapon instanceof MolecularSlicerBeamL)){ //Shadow Multiphased Cutters,, leave slicers as purple.
						type = 'blue';
					}else if(weapon.weaponClass == "Particle"){ //Mindrider Telekinetic Cutter
						type = 'orange';
					}else if(weapon.weaponClass == "Electromagnetic"){ //Vorlon Discharge Gunsb
						type = 'yellow';
					}														        
				break;					
			}		 
		}
			
			var lineSprite = null;	        
			var isFriendly = gamedata.isMyOrTeamOneShip(shooter);

			this.ballisticLineIcons.push({
				id: ballistic.id,
				shooterId: ballistic.shooterid,
				targetId: ballistic.targetid,
				lineSprite: lineSprite =  new BallisticLineSprite(launchPosition, targetPosition, 3 * this.zoomScale, -3, getLineColorByType(type), 0.5),
				used: true,
				isFriendly: isFriendly
			});

			scene.add(lineSprite.mesh);

		//Need some checks here to handle when lines are toggled on and new ballistic fireORder/line is added

		var isFriendlyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === true
		);

		var isEnemyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === false
		);  

		var ballisticIdToFind = ballistic.id;
		var currentIcon = this.ballisticLineIcons.find(lineIcon => lineIcon.id === ballisticIdToFind );

		if(isFriendly && isFriendlyLinesVisible){
			currentIcon.lineSprite.isVisible = true;		
		}else if(!isFriendly && isEnemyLinesVisible){
			currentIcon.lineSprite.isVisible = true;						
		}else{
			currentIcon.lineSprite.isVisible = false;			
		}
		
	}//endof createBallisticLineIcon()


	function getBallisticLineIcon(id) {
		return this.ballisticLineIcons.filter(function (lineIcon) {
			return lineIcon.id === id;
		}).pop();
	}
*/


/* //Future functions that I might need/use - DK 12.24
	BallisticIconContainer.prototype.showForShip = function (ship) {
		this.ballisticLineIcons.filter(function (lineIcon) {
			return lineIcon.shooterId === ship.id || icon.targetId === ship.id;
		}).forEach(function (lineIcon) {
			lineIcon.lineSprite.setStartAndEnd(lineIcon.shooterIcon.getPosition(), lineIcon.targetIcon.getPosition());
			lineIcon.lineSprite.show();
		}, this);
	};

	BallisticIconContainer.prototype.showByShip = function (ship) {
		this.ballisticLineIcons.filter(function (lineIcon) {
			return lineIcon.shooterId === ship.id;
		}).forEach(function (lineIcon) {
			lineIcon.lineSprite.setStartAndEnd(lineIcon.shipIcon.getPosition(), lineIcon.targetIcon.getPosition());
			lineIcon.lineSprite.show();
			lineIcon.lineSprite.isVisible = true;            
		}, this);
	};
*/
