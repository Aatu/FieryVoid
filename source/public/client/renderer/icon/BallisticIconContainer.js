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
		// Removed reliance on checking existing icons' visibility:
		// const isFriendlyLinesVisible = oldIcons.some(icon => icon.lineSprite?.isVisible && icon.isFriendly);
		// const isEnemyLinesVisible = oldIcons.some(icon => icon.lineSprite?.isVisible && !icon.isFriendly);

		this.ballisticLineIcons = oldIcons.filter(icon => {
			if (!icon.used) {
				if (icon.lineSprite) this.scene.remove(icon.lineSprite.mesh);
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

	function generateTerrainHexes(gamedata) {
		if (gamedata.gamephase === -1) return; //Don't bother during Deployment phase.

		gamedata.ships.filter(ship => ship.Enormous && ship.shipSizeClass == 5 && !shipManager.isDestroyed(ship)).forEach(ship => {
			//gamedata.ships.filter(ship => ship.Huge > 0).forEach(ship => {
			const position = shipManager.getShipPosition(ship);
			/*const perimeterHexes = (ship.Huge === 2)
				? mathlib.getPerimeterHexes(position, ship.Huge)
				: mathlib.getNeighbouringHexes(position, ship.Huge);
			*/
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

		let hideTargetAlways = false;

		if (replay) {
			if (ballistic.damageclass === 'PersistentEffectPlasma' && ballistic.targetid === -1 && ballistic.notes !== 'PlasmaCloud') return;
			if (!shooter.flight && weapon.alwaysHideFireOrders && gamedata.getPlayerTeam() !== shooter.team) {
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

		if (ballistic.targetid === -1 && ballistic.x !== "null" && ballistic.y !== "null" && !hideTargetAlways) {
			targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		} else if (ballistic.targetid && ballistic.targetid !== -1 && !hideTargetAlways) {
			targetIcon = iconContainer.getById(ballistic.targetid);
			//targetPosition = { x: 0, y: 0 }; // placeholder — the mesh will handle it
		}

		if (!shooter.flight && weapon?.noTargetHexIcon) {
			targetPosition = launchPosition;
		}

		// Mode-specific icon logic
		if (modeName) {
			const modeMap = {
				'Z - Antimine': { type: 'hexRed', text: 'Antimine', color: '#e6140a' },
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
				'Warp Jump': { type: 'hexBlue', text: 'Warp Jump', color: '#787800' },
				'Standard - GN': { type: 'hexGreen', text: 'Gravity Net Standard', color: '#008000' },
				'Priorty - GN': { type: 'hexGreen', text: 'Gravity Net PRIORITY', color: '#787800' },
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
				if (['Z - Antimine', 'Shredder', 'Energy Mine', 'Ion Storm', 'Jammer', '1-Blanket Shield', '3-Blanket Shade'].includes(modeName)) {
					if ((gamedata.thisplayer === shooter.userid || replay) && targetPosition) {
						let sizes = [];

						switch (modeName) {
							case 'Z - Antimine':
								sizes = [3];
								break;
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
						const isFriendly = gamedata.isMyOrTeamOneShip(shooter);
						var modeText = isFriendly ? modeName : weapon.getModeNameForEnemy();

						targetType = 'hexRed';
						text = modeText;
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
		if (!getByTargetIdOrTargetPosition(targetPosition, ballistic.targetid, this.ballisticIcons)) {
			if (targetPosition || targetIcon) {
				targetSprite = new BallisticSprite(targetPosition || { x: 0, y: 0 }, targetType, text, textColour, iconImage);
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
			splash: splash
		});
	}

	const getByLaunchPosition = (position, icons) => icons.find(icon => icon.launchPosition && icon.launchPosition.x === position.x && icon.launchPosition.y === position.y)

	const getByTargetIdOrTargetPosition = (position, targetId, icons) => icons.find(icon => (targetId !== -1 && icon.targetId === targetId) || (position && icon.position && icon.position.x === position.x && icon.position.y === position.y))


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
		if (replay) {
			if (!shooter.flight && weapon.alwaysHideFireOrders && gamedata.getPlayerTeam() !== shooter.team) return;
		}
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
			if (ballistic.damageclass === 'Targeter') {
				type = 'yellow';
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


