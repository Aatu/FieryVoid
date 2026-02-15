'use strict';

window.lobbyEnhancements = {

	setEnhancementsShip: function setEnhancementsShip(ship) {

		// Ammo magazine is necessary for some options
		let ammoMagazine = null;
		for (let magazine of ship.systems) {
			if (magazine.name === 'ammoMagazine') {
				ammoMagazine = magazine;
				break;
			}
		}
		var totalRounds = 0; //Counter to check and update total round used in magazine (where it matters).	

		for (let entry of ship.enhancementOptions) {
			// ID, readableName, numberTaken, limit, price, priceStep
			let enhID = entry[0];
			let enhCount = entry[2];
			let enhDescription = entry[1];
			if (enhCount > 0) {
				if (ship.enhancementTooltip !== "") this.enhancementTooltip += "<br>";
				ship.enhancementTooltip += enhDescription;
				if (enhCount > 1) ship.enhancementTooltip += ` (x${enhCount})`;

				switch (enhID) {
					/*case 'DEPLOY': // Delayed
						if(!ship.deployEnh && enhCount > 1){
								ship.notes += "<br>Deploys on Turn " + enhCount + "";
							}
							ship.deployEnh = true;
					break;		
					*/
					case 'ELITE_CREW':
						if (!ship.eliteEnh) {
							ship.forwardDefense -= enhCount;
							ship.sideDefense -= enhCount;
							ship.iniativebonus += enhCount * 5;
							ship.critRollMod -= enhCount * 2;
							ship.toHitBonus += enhCount;

							// System mods: Scanner
							let strongestEScan = null;
							let strongestEScanValue = -1;
							for (let system of ship.systems) {
								if (system.name == "scanner" || system.name == "elintScanner") {
									if (system.output > strongestEScanValue) {
										strongestEScanValue = system.output;
										strongestEScan = system;
									}
								}
							}
							if (strongestEScanValue > 0) {
								strongestEScan.output += enhCount;
							}

							// System mods: Engine
							let strongestEEngine = null;
							let strongestEEngVal = -1;
							for (let system of ship.systems) {
								if (system.name == "engine") {
									if (system.output > strongestEEngVal) {
										strongestEEngVal = system.output;
										strongestEEngine = system;
									}
								}
							}
							if (strongestEEngVal > 0) {
								strongestEEngine.output += enhCount * 2;
								strongestEEngine.data["Own thrust"] += enhCount * 2;
							}

							// System mods: Reactor
							let strongestEReact = null;
							let strongestEReactVal = -1000;
							for (let system of ship.systems) {
								if (system.name == "reactor") {
									if (system.maxhealth > strongestEReactVal) {
										strongestEReactVal = system.maxhealth;
										strongestEReact = system;
									}
								}
							}
							if (strongestEReact != null) {
								strongestEReact.output += enhCount * 2;
							}

							// Modify thruster ratings as well
							for (let system of ship.systems) {
								if (system.name == "thruster") {
									system.output += enhCount;
								}
							}
							ship.notes += "<br>Elite Crew (" + enhCount + ")";
						}
						ship.eliteEnh = true;
						break;

					case 'ELT_MRN':
						if (!ship.elMrnEnhShip) {
							for (let system of ship.systems) {
								if (system.name == "GrapplingClaw") {
									system.eliteMarines = enhCount;
									system.data["Elite"] = "Yes";
								}
							}
						}
						ship.elMrnEnhShip = true;
						break;

					case 'EXT_MRN':
						if (!ship.exMrnEnhShip) {
							for (let system of ship.systems) {
								if (system.name == "GrapplingClaw") {
									system.ammunition += enhCount;
									system.data["Ammunition"] += enhCount;
								}
							}
						}
						ship.exMrnEnhShip = true;
						break;

					case 'GUNSIGHT'://Gunsights: allows Particle Repeaters to split their shots.  
						if (!ship.gunsightEnh) {
							ship.notes += "<br>Repeater Gunsights";
						}
						ship.gunsightEnh = true;
						break;

					case 'HANG_F'://Hangar Conversion to Fighter slot, no actual need to change anything here.  
						if (!ship.hangFEnh) {
							ship.notes += "<br>Fighter Conversion (" + enhCount + ")";
						}
						ship.hangFEnh = true;
						break;

					case 'HANG_AS'://Hangar Conversion to Assault Shuttle slot, no actual need to change anything here.  
						if (!ship.hangASEnh) {
							ship.notes += "<br>Shuttle Conversion (" + enhCount + ")";
						}
						ship.hangASEnh = true;
						break;


					case 'IFF_SYS':
						if (!ship.iffEnh) {
							ship.IFFSystem = true;
							ship.notes += "<br>IFF System";
						}
						ship.iffEnh = true;
						break;

					case 'IMPR_ENG':
						if (!ship.engEnh) {
							let strongestEng = null;
							let strongestEngVal = -1;
							for (let system of ship.systems) {
								if (system.name == "engine") {
									if (system.output > strongestEngVal) {
										strongestEngVal = system.output;
										strongestEng = system;
									}
								}
							}
							if (strongestEngVal > 0) {
								strongestEng.output += enhCount;
								strongestEng.data["Own thrust"] += enhCount;
							}
						}
						ship.engEnh = true;
						break;

					case 'IMPR_PSY':
						if (!ship.psyEnh) {
							for (let system of ship.systems) {
								if (system.name == "PsychicField") {
									system.data["Range"] += enhCount;
									system.range += enhCount;
								}
							}
						}
						ship.psyEnh = true;
						break;

					case 'IMPR_REA':
						if (!ship.reaEnh) {
							let strongestReact = null;
							let strongestReactValue = -1;
							for (let system of ship.systems) {
								if (system.name == "reactor") {
									if (system.maxhealth > strongestReactValue) {
										strongestReactValue = system.maxhealth;
										strongestReact = system;
									}
								}
							}
							if (strongestReactValue > 0) {
								let addedPower = 0;
								if (ship.Enormous === true) {
									addedPower = 4;
								} else {
									addedPower = ship.shipSizeClass;
								}
								strongestReact.output += enhCount * addedPower;
							}
						}
						ship.reaEnh = true;
						break;

					case 'IMPR_SENS':
						if (!ship.sensEnh) {
							let strongestScan = null;
							let strongestScanValue = -1;
							for (let system of ship.systems) {
								if (system.name == "scanner" || system.name == "elintScanner") {
									if (system.output > strongestScanValue) {
										strongestScanValue = system.output;
										strongestScan = system;
									}
								}
							}
							if (strongestScanValue > 0) {
								strongestScan.output += enhCount;
							}
						}
						ship.sensEnh = true;
						break;

					case 'IMPR_SR':
						if (!ship.srEnh) {
							for (let system of ship.systems) {
								if (system.name == "SelfRepair") {
									system.output += enhCount;
								}
							}
						}
						ship.srEnh = true;
						break;

					case 'IMPR_THSD':
						if (!ship.thsdEnh) {
							for (let system of ship.systems) {
								if (system.name == "ThirdspaceShield") {
									system.output += enhCount;
								}
								if (system.name == "ThirdspaceShieldGenerator") {
									system.output += enhCount;
								}
							}
						}
						ship.thsdEnh = true;
						break;

					case 'IMPR_TS':
						if (!ship.tsEnh) {
							for (let system of ship.systems) {
								if (system.name == "ThoughtShield") {
									system.output += enhCount;
								}
								if (system.name == "ThoughtShieldGenerator") {
									system.output += enhCount;
								}
							}
						}
						ship.tsEnh = true;
						break;

					case 'IPSH_EETH':
						if (!ship.eethEnh) {
							ship.iniativebonus -= enhCount * 5;
							ship.turndelaycost += 0.1;
							for (let system of ship.systems) {
								if (system.name == "reactor") {
									system.output = Math.round(system.output * 1.25);
									system.critRollMod += 4;
								} else if (system.name == "engine") {
									system.output += 2;
									system.data["Own thrust"] += 1;
									system.critRollMod += 4;
								}
							}
							ship.notes += "<br>Eethan Refit";
						}
						ship.eethEnh = true;
						break;

					case 'IPSH_ESSAN':
						if (!ship.essanEnh) {
							for (let system of ship.systems) {
								if (system.name == "scanner") {
									system.output -= 1;
									system.maxhealth -= 2;
								} else if (system.name == "engine") {
									system.output += 1;
									system.data["Own thrust"] += 1;
									system.maxhealth += 2;
								} else if (system.name == "structure") {
									if (system.armour < 5) system.armour += 1;
								}
							}
							ship.notes += "<br>Essan Refit";
						}
						ship.essanEnh = true;
						break;

					case 'MARK_FERV':
						if (!ship.fervEnh) {
							//ship.toHitBonus += enhCount;
							ship.iniativebonus += enhCount * 10;
							ship.forwardDefense += enhCount * 2;
							ship.sideDefense += enhCount * 2;
							ship.fervEnh = true;
							ship.notes += "<br>Markab Fervor";
						}
						break;

					case 'POOR_CREW':
						if (!ship.poorEnh) {
							ship.forwardDefense += enhCount;
							ship.sideDefense += enhCount;
							ship.iniativebonus -= enhCount * 5;
							ship.critRollMod += enhCount * 2;
							ship.toHitBonus -= enhCount;

							// System mods: Scanner
							let strongestPScan = null;
							let strongestPScanValue = -1;
							for (let system of ship.systems) {
								if (system.name == "scanner" || system.name == "elintScanner") {
									if (system.output > strongestPScanValue) {
										strongestPScanValue = system.output;
										strongestPScan = system;
									}
								}
							}
							if (strongestPScanValue > 0) {
								strongestPScan.output -= enhCount;
							}

							// System mods: Engine
							let strongestPEng = null;
							let strongestPEngValue = -1;
							for (let system of ship.systems) {
								if (system.name == "engine") {
									if (system.output > strongestPEngValue) {
										strongestPEngValue = system.output;
										strongestPEng = system;
									}
								}
							}
							if (strongestPEngValue > 0) {
								strongestPEng.output -= enhCount * 2;
								strongestPEng.data["Own thrust"] -= enhCount * 2;
							}

							// System mods: Reactor
							let strongestPReact = null;
							let strongestPReactValue = -1000;
							for (let system of ship.systems) {
								if (system.name == "reactor") {
									if (system.maxhealth > strongestPReactValue) {
										strongestPReactValue = system.maxhealth;
										strongestPReact = system;
									}
								}
							}
							if (strongestPReact != null) {
								strongestPReact.output -= enhCount;
							}
							ship.notes += "<br>Poor Crew (" + enhCount + ")";
						}
						ship.poorEnh = true;
						break;

					case 'SHAD_DIFF':
						if (!ship.diffEnh) {
							for (let system of ship.systems) {
								if (system.name == "EnergyDiffuser") {
									system.output += enhCount;
								}
							}
						}
						ship.diffEnh = true;
						break;

					case 'SHAD_FTRL':
						if (!ship.ftrlEnh) {
							let struct = shipManager.systems.getStructureSystem(ship, 0);
							if (struct) {
								struct.maxhealth -= enhCount;
								ship.notes += "<br>" + enhCount + " fighter(s) spawned";
							}
						}
						ship.ftrlEnh = true;
						break;

					case 'SPARK_CURT':
						if (!ship.sparkEnh) {
							for (let system of ship.systems) {
								if (system.name === "SparkField") {
									system.output = system.baseOutput;
									system.data["Spark Curtain"] = "Yes";
								}
							}
							ship.notes += "<br>Spark Curtain";
						}
						ship.sparkEnh = true;
						break;

					case 'SLUGGISH':
						if (!ship.slugEnh) {
							ship.iniativebonus -= enhCount * 5;
							ship.notes += "<br>Sluggish (" + enhCount + ")";
						}
						ship.slugEnh = true;
						break;

					case 'VOR_AMETHS': // Vorlon Amethyst Skin (for ship)
						if (!ship.amethsEnh) {
							let AActrl = null;
							for (let system of ship.systems) {
								if (system.name === 'adaptiveArmorController') {
									AActrl = system;
									break;
								}
							}

							if (AActrl) {
								AActrl.AAtotal += enhCount;
								AActrl.output = AActrl.AAtotal;
								AActrl.data["Adaptive Armor"] = 0 + "/" + AActrl.AAtotal;
								AActrl.data[" - per weapon type"] = Math.floor(AActrl.AAtotal / 2);
								AActrl.data[" - preassigned"] = 0 + "/" + Math.floor(AActrl.AAtotal / 2);
							}
							ship.notes += "<br>Amethyst skin";
						}
						ship.amethsEnh = true;
						break;

					case 'VOR_AZURS': // Vorlon Azure Skin (for ship) - +1 Shield rating
						if (!ship.azursEnh) {
							for (let system of ship.systems) {
								if (system.name === "eMShield") {
									system.output += enhCount;
								}
							}
							ship.notes += "<br>Azure skin";
						}
						ship.azursEnh = true;
						break;

					case 'VOR_CRIMS': // Vorlon Crimson Skin (for ship) - Power Capacitor gains +2 storage points and +1 recharge point
						if (!ship.crimsEnh) {
							let capacitor = null;
							for (let system of ship.systems) {
								if (system.name === 'powerCapacitor') {
									capacitor = system;
									break;
								}
							}

							if (capacitor) {
								capacitor.output += enhCount;
								capacitor.data["Power stored/max"] = capacitor.powerMax + (2 * enhCount);
							}
							ship.notes += "<br>Crimson skin";
						}
						ship.crimsEnh = true;
						break;

					case 'VULN_CRIT': // Vulnerable to Criticals: +1 Crit roll mod
						if (!ship.vulnEnh) {
							ship.critRollMod += enhCount;
							ship.notes += "<br>Vulnerable to Criticals (" + enhCount + ")";
						}
						ship.vulnEnh = true;
						break;
					// Add more cases as necessary

				}//end of non-ammo enhancements list

				//If there's a magazine, let's check if any missiles were bought!
				if (ammoMagazine != null) {

					switch (enhID) {

						case 'AMMO_B': //Basic Missile - Shouldn't ever be purchasable an an enhancement, but here we are.					
							if (!ship.ammoBEnh) {
								//May need a different method as Basic entry already exists in Special data.
								let special = ammoMagazine.data["Special"];
								if (special.includes("- Basic Missile: ")) {
									special = special.replace(/(- Basic Missile: )\d+/, `$1${enhCount}`);
									ammoMagazine.data["Special"] = special;
								} else {
									ammoMagazine.data["Special"] += "<br>- Basic Missile: " + enhCount;
								}
								totalRounds += enhCount;
							}
							ship.ammoBEnh = true;
							break;

						case 'AMMO_L': //Long Range Missile
							if (!ship.ammoLEnh) {
								ammoMagazine.data["Special"] += "<br>- Long Range Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoLEnh = true;
							break;

						case 'AMMO_H': //Heavy Missile
							if (!ship.ammoHEnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoHEnh = true;
							break;

						case 'AMMO_F': //Flash Missile
							if (!ship.ammoFEnh) {
								ammoMagazine.data["Special"] += "<br>- Flash Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoFEnh = true;
							break;

						case 'AMMO_A': //Antifighter Missile
							if (!ship.ammoAEnh) {
								ammoMagazine.data["Special"] += "<br>- Antifighter Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoAEnh = true;
							break;

						case 'AMMO_P': //Piercing Missile
							if (!ship.ammoPEnh) {
								ammoMagazine.data["Special"] += "<br>- Piercing Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoPEnh = true;
							break;

						case 'AMMO_D': //Light Missile						
							if (!ship.ammoDEnh) {
								ammoMagazine.data["Special"] += "<br>- Light Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoDEnh = true;
							break;

						case 'AMMO_I': //Interceptor Missile 						
							if (!ship.ammoIEnh) {
								ammoMagazine.data["Special"] += "<br>- Interceptor Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoIEnh = true;
							break;

						case 'AMMO_C': //Chaff Missile						
							if (!ship.ammoCEnh) {
								ammoMagazine.data["Special"] += "<br>- Chaff Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoCEnh = true;
							break;

						case 'AMMO_J': //Jammer Missile						
							if (!ship.ammoJEnh) {
								ammoMagazine.data["Special"] += "<br>- Jammer Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoJEnh = true;
							break;

						case 'AMMO_K': //Starburst Missile						
							if (!ship.ammoKEnh) {
								ammoMagazine.data["Special"] += "<br>- Starburst Missile: " + enhCount;
								totalRounds += enhCount * 2; //Each missile takes up 2 slots
							}
							ship.ammoKEnh = true;
							break;

						case 'AMMO_M': //Multiwarhead Missile						
							if (!ship.ammoMEnh) {
								ammoMagazine.data["Special"] += "<br>- Multiwarhead Missile: " + enhCount;
								totalRounds += enhCount * 2; //Each missile takes up 2 slots
							}
							ship.ammoMEnh = true;
							break;

						case 'AMMO_KK': //Kinetic Missile						
							if (!ship.ammoKKEnh) {
								ammoMagazine.data["Special"] += "<br>- Kinetic Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoKKEnh = true;
							break;

						case 'AMMO_S': //Stealth Missile						
							if (!ship.ammoSEnh) {
								ammoMagazine.data["Special"] += "<br>- Stealth Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoSEnh = true;
							break;

						case 'AMMO_X': //HARM Missile						
							if (!ship.ammoXEnh) {
								ammoMagazine.data["Special"] += "<br>- HARM Missile: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoXEnh = true;
							break;

						case 'MINE_BLB': //Ballistic Launcher Basic Mine						
							if (!ship.ammoBLBEnh) {
								//May need a different method as Basic entry already exists in Special data.
								let special = ammoMagazine.data["Special"];
								if (special.includes("- Basic Mine: ")) {
									special = special.replace(/(- Basic Mine: )\d+/, `$1${enhCount}`);
									ammoMagazine.data["Special"] = special;
								} else {
									ammoMagazine.data["Special"] += "<br>- Basic Mine: " + enhCount;
								}
								totalRounds += enhCount;
							}
							ship.ammoBLBEnh = true;
							break;

						case 'MINE_BLH': //Ballistic Launcher Heavy Mine						
							if (!ship.ammoBLHEnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Mine: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoBLHEnh = true;
							break;

						case 'MINE_BLW': //Ballistic Launcher Wide-Range Mine						
							if (!ship.ammoBLWEnh) {
								ammoMagazine.data["Special"] += "<br>- Wide-range Mine: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoBLWEnh = true;
							break;

						case 'MINE_MLB': //Abbai Mine Launcher Basic Mine													
							if (!ship.ammoMLBEnh) {
								//May need a different method as Basic entry already exists in Special data.
								let special = ammoMagazine.data["Special"];
								if (special.includes("- Basic Mine: ")) {
									special = special.replace(/(- Basic Mine: )\d+/, `$1${enhCount}`);
									ammoMagazine.data["Special"] = special;
								} else {
									ammoMagazine.data["Special"] += "<br>- Basic Mine: " + enhCount;
								}
								totalRounds += enhCount;
							}
							ship.ammoMLBEnh = true;
							break;

						case 'MINE_MLW': //Abbai Mine Launcher Wide-Ranged Mine						
							if (!ship.ammoMLWEnh) {
								ammoMagazine.data["Special"] += "<br>- Wide-range Mine: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoMLWEnh = true;
							break;

						//AMMO TYPES FOR DIRECT FIRE WEAPONS					
						case 'SHELL_HBSC': //Standard Ammo for Heavy Railgun						
							if (!ship.shellHBEnh) {
								//May need a different method as Basic entry already exists in Special data.
								let special = ammoMagazine.data["Special"];
								if (special.includes("- Basic Heavy Shell: ")) {
									special = special.replace(/(- Basic Heavy Shell: )\d+/, `$1${enhCount}`);
									ammoMagazine.data["Special"] = special;
								} else {
									ammoMagazine.data["Special"] += "<br>- Basic Heavy Shell: " + enhCount;
								}
								totalRounds += enhCount;
							}
							ship.shellHBEnh = true;
							break;

						case 'SHELL_MBSC': //Standard Ammo for Medium Railgun						
							if (!ship.shellMBEnh) {
								//May need a different method as Basic entry already exists in Special data.
								let special = ammoMagazine.data["Special"];
								if (special.includes("- Basic Medium Shell: ")) {
									special = special.replace(/(- Basic Medium Shell: )\d+/, `$1${enhCount}`);
									ammoMagazine.data["Special"] = special;
								} else {
									ammoMagazine.data["Special"] += "<br>- Basic Medium Shell: " + enhCount;
								}
								totalRounds += enhCount;
							}
							ship.shellMBEnh = true;
							break;

						case 'SHELL_LBSC': //Standard Ammo for Light Railgun						
							if (!ship.shellLBEnh) {
								//May need a different method as Basic entry already exists in Special data.
								let special = ammoMagazine.data["Special"];
								if (special.includes("- Basic Light Shell: ")) {
									special = special.replace(/(- Basic Light Shell: )\d+/, `$1${enhCount}`);
									ammoMagazine.data["Special"] = special;
								} else {
									ammoMagazine.data["Special"] += "<br>- Basic Light Shell: " + enhCount;
								}
								totalRounds += enhCount;
							}
							ship.shellLBEnh = true;
							break;

						case 'SHELL_HFLH': //Flash Ammo for Heavy Railgun						
							if (!ship.ammoHFEnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Flash Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoHFEnh = true;
							break;

						case 'SHELL_MFLH': //Flash Ammo for Medium Railgun						
							if (!ship.ammoMFEnh) {
								ammoMagazine.data["Special"] += "<br>- Medium Flash Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoMFEnh = true;
							break;

						case 'SHELL_LFLH': //Flash Ammo for Light Railgun						
							if (!ship.ammoLFEnh) {
								ammoMagazine.data["Special"] += "<br>- Light Flash Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoLFEnh = true;
							break;

						case 'SHELL_HSCT': //Scatter Ammo for Heavy Railgun						
							if (!ship.ammoHSEnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Scatter Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoHSEnh = true;
							break;

						case 'SHELL_MSCT': //Scatter Ammo for Medium Railgun						
							if (!ship.ammoMSEnh) {
								ammoMagazine.data["Special"] += "<br>- Medium Scatter Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoMSEnh = true;
							break;

						case 'SHELL_LSCT': //Scatter Ammo for Light Railgun						
							if (!ship.ammoLSEnh) {
								ammoMagazine.data["Special"] += "<br>- Light Scatter Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoLSEnh = true;
							break;

						case 'SHELL_HHVY': //Heavy Ammo for Heavy Railgun						
							if (!ship.ammoHHEnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Heavy Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoHHEnh = true;
							break;

						case 'SHELL_MHVY': //Heavy Ammo for Medium Railgun						
							if (!ship.ammoMHEnh) {
								ammoMagazine.data["Special"] += "<br>- Medium Heavy Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoMHEnh = true;
							break;

						case 'SHELL_LHVY': //Heavy Ammo for Light Railgun						
							if (!ship.ammoLHEnh) {
								ammoMagazine.data["Special"] += "<br>- Light Heavy Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoLHEnh = true;
							break;

						case 'SHELL_HLR': //Long Range Ammo for Heavy Railgun						
							if (!ship.ammoHLREnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Long Range Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoHLREnh = true;
							break;

						case 'SHELL_MLR': //Long Range Ammo for Medium Railgun						
							if (!ship.ammoMLREnh) {
								ammoMagazine.data["Special"] += "<br>- Medium Long Range Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoMLREnh = true;
							break;

						case 'SHELL_HULR': //Ultra Long Range Ammo for Heavy Railgun						
							if (!ship.ammoHULREnh) {
								ammoMagazine.data["Special"] += "<br>- Heavy Ultra Long Range Shell: " + enhCount;
								totalRounds += enhCount;
							}
							ship.ammoHULREnh = true;
							break;
					}//endof ammo listings.

				}//endof if(ammoMagazine) check.		


			}//end of enhCount > 0 check

		}//end of loop through enhancements.

		// Insert update to Total Rounds here.
		if (ammoMagazine != null) {
			var specialText = ammoMagazine.data["Special"];
			if (specialText.includes("Total rounds: ")) { // There will be a number immediately after this text e.g. Total rounds: 180/220        
				// Extract both the numbers before and after the oblique (`/`)
				var match = specialText.match(/Total rounds: (\d+)\/(\d+)/);
				if (match) {
					var extractedValueBefore = parseInt(match[1], 10); // Number before `/`
					var extractedValueAfter = parseInt(match[2], 10); // Number after `/`

					// Update the number before the `/` (e.g., add new ammo)
					if ((extractedValueBefore + totalRounds) < extractedValueAfter) extractedValueBefore += totalRounds;

					// Replace the old value with the new total
					specialText = specialText.replace(/(Total rounds: )\d+/, `$1${extractedValueBefore}`);
					ammoMagazine.data["Special"] = specialText;
					ammoMagazine.output = extractedValueBefore;
				}
			}
		}
	},

	setEnhancementsFighter: function setEnhancementsFighter(flight) {

		var totalRounds = 0; //Initialise

		for (let entry of flight.enhancementOptions) {
			// ID, readableName, numberTaken, limit, price, priceStep
			let enhID = entry[0];
			let enhCount = entry[2];
			let enhDescription = entry[1];

			if (enhCount > 0) {

				if (flight.enhancementTooltip !== "") this.enhancementTooltip += "<br>";
				flight.enhancementTooltip += enhDescription;
				if (enhCount > 1) flight.enhancementTooltip += ` (x${enhCount})`;

				switch (enhID) {
					/*
					case 'DEPLOY': // Expert Motivator
						if(!flight.deployEnh && enhCount > 1){
							flight.notes += "<br>Deploys on Turn " + enhCount + "";
						}
						flight.deployEnh = true;
					break;
					*/
					case 'ELT_MAR': // Elite marines, mark every Marines system as Elite.
						if (!flight.elMarEnhFlight) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "Marines") {
										sys.data["Elite"] = "Yes";
									}
								});
							});
						}
						flight.elMarEnhFlight = true;
						break;

					case 'ELITE_SW': // Elite Pilot (SW)
						if (!flight.swEnh) {
							flight.pivotcost = 1;
							flight.offensivebonus += enhCount;
							flight.iniativebonus += enhCount * 5;
							flight.forwardDefense -= enhCount;
							flight.sideDefense -= enhCount;
							flight.notes += "<br>Elite Pilot";
						}
						flight.swEnh = true;
						break;

					case 'EXP_MOTIV': // Expert Motivator
						if (!flight.motivEnh) {
							flight.notes += "<br>Expert Motivator";
						}
						flight.motivEnh = true;
						break;

					case 'EXT_AMMO': // Extra ammo
						if (!flight.exAmmoEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "PairedGatlingGun" ||
										sys.name == "MatterGun" ||
										sys.name == "SlugCannon" ||
										sys.name == "GatlingGunFtr" ||
										sys.name == "NexusAutogun" ||
										sys.name == "NexusMinigunFtr" ||
										sys.name == "NexusShatterGunFtr" ||
										sys.name == "NexusLightDefenseGun") {
										sys.data["Ammunition"] += enhCount;
									}
								});
							});
						}
						flight.exAmmoEnh = true;
						break;

					case 'EXT_HAMMO': // Extra heavy ammo
						if (!flight.exHAmmoEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "NexusAutocannonFtr" ||
										sys.name == "NexusLightGasGunFtr") {
										sys.data["Ammunition"] += enhCount;
									}
								});
							});
						}
						flight.exHAmmoEnh = true;
						break;

					case 'EXT_MAR': // Extra marines
						if (!flight.exMarEnhFlight) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "Marines") {
										sys.data["Ammunition"] += enhCount;
									}
								});
							});
						}
						flight.exMarEnhFlight = true;
						break;

					case 'FTR_FERV': // Markab Religious Fervor
						if (!flight.fervEnhFlight) {
							flight.offensivebonus += enhCount;
							flight.iniativebonus += enhCount * 10;
							flight.forwardDefense += enhCount * 2;
							flight.sideDefense += enhCount * 2;
							flight.critRollMod -= enhCount * 3;
							flight.notes += "<br>Markab Fervor";
						}
						flight.fervEnhFlight = true;
						break;

					case 'IMPR_OB': // Improved Targeting Computer
						if (!flight.obEnh) {
							flight.offensivebonus += enhCount;
							flight.notes += "<br>Improved Targeting Computer";
						}
						flight.obEnh = true;
						break;

					case 'IMPR_THR': // Improved Thrust
						if (!flight.thrEnh) {
							flight.freethrust += enhCount;
						}
						flight.thrEnh = true;
						break;

					case 'NAVIGATOR': // Navigator
						if (!flight.navEnh) {
							flight.notes += "<br>Navigator";
						}
						flight.navEnh = true;
						break;

					case 'POOR_TRAIN': // Poor Training
						if (!flight.poorTrEnh) {
							flight.critRollMod += enhCount * 2;
							flight.offensivebonus -= enhCount;
							flight.freethrust -= enhCount;
							flight.iniativebonus -= enhCount * 5;
							flight.forwardDefense += enhCount;
							flight.sideDefense += enhCount;
							flight.notes += "<br>Poor Training";
						}
						flight.poorTrEnh = true;
						break;

					case 'SHAD_CTRL': // Shadow fighter deployed without carrier control
						if (!flight.shadCtrlEnh) {
							flight.offensivebonus -= enhCount * 2;
							flight.iniativebonus -= enhCount * 3 * 5;
							flight.notes += "<br>Uncontrolled";
						}
						flight.shadCtrlEnh = true;
						break;

					case 'VOR_AZURF': // Vorlon Azure Skin Coloring
						if (!flight.azurfEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "FtrShield") {
										sys.data["Basic Strength"] += enhCount;
									}
								});
							});
							flight.notes += "<br>Azure Skin Coloring";
						}
						flight.azurfEnh = true;
						break;

					//consumable ammunition - add to ALL missile magazines on flight!
					case 'AMMO_FB': //Basic Fighter Missile
						if (!flight.fbEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "ammoMagazine") {
										let special = sys.data["Special"];
										if (special.includes("- Basic Missile: ")) {
											special = special.replace(/(- Basic Missile: )\d+/, `$1${enhCount}`);
											sys.data["Special"] = special;
										} else {
											sys.data["Special"] += "<br>- Basic Missile: " + enhCount;
										}
										totalRounds += enhCount;
									}
								});
							});
						}
						flight.fbEnh = true;
						break;

					case 'AMMO_FH': //Heavy Fighter Missile
						if (!flight.fhEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "ammoMagazine") {
										sys.data["Special"] += "<br>- Heavy Missile: " + enhCount;
										totalRounds += enhCount;
									}
								});
							});
						}
						flight.fhEnh = true;
						break;

					case 'AMMO_FL': //Long Range Fighter Missile
						if (!flight.flEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "ammoMagazine") {
										sys.data["Special"] += "<br>- Long Range Missile: " + enhCount;
										totalRounds += enhCount;
									}
								});
							});
						}
						flight.flEnh = true;
						break;

					case 'AMMO_FY': //Dogfight Fighter Missile
						if (!flight.fyEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "ammoMagazine") {
										let special = sys.data["Special"];
										if (special.includes("- Dogfight Missile: ")) { //For some fighter Dogfight are the default
											special = special.replace(/(- Dogfight Missile: )\d+/, `$1${enhCount}`);
											sys.data["Special"] = special;
										} else {
											sys.data["Special"] += "<br>- Dogfight Missile: " + enhCount;
										}
										totalRounds += enhCount;
									}
								});
							});
						}
						flight.fyEnh = true;
						break;

					case 'AMMO_FD': //Dropout Fighter Missile
						if (!flight.fdEnh) {
							flight.systems.forEach(ftr => {
								ftr.systems.forEach(sys => {
									if (sys.name == "ammoMagazine") {
										sys.data["Special"] += "<br>- Dropout Missile: " + enhCount;
										totalRounds += enhCount;
									}
								});
							});
						}
						flight.fdEnh = true;
						break;

				}//end of swtich function
			}
		}//end of loop through fighter enhancement options.

		// Insert update to Total Rounds here.
		flight.systems.forEach(ftr => {
			ftr.systems.forEach(sys => {
				if (sys.name == "ammoMagazine") {
					var specialText = sys.data["Special"];
					if (specialText.includes("Total rounds: ")) { // There will be a number immediately after this text e.g. Total rounds: 180/220        
						// Extract both the numbers before and after the oblique (`/`)
						var match = specialText.match(/Total rounds: (\d+)\/(\d+)/);
						if (match) {
							var extractedValueBefore = parseInt(match[1], 10); // Number before `/`
							var extractedValueAfter = parseInt(match[2], 10); // Number after `/`

							// Update the number before the `/` (e.g., add new ammo)
							if ((extractedValueBefore + totalRounds) < extractedValueAfter) extractedValueBefore += totalRounds;

							// Replace the old value with the new total
							specialText = specialText.replace(/(Total rounds: )\d+/, `$1${extractedValueBefore}`);
							sys.data["Special"] = specialText;
							sys.output = extractedValueBefore;
						}
					}
				}
			});
		});

	},//end of setEnhancementsFighter()	


	resetEnhancementMarkersShip: function resetEnhancementMarkersShip(ship) {

		for (let entry of ship.enhancementOptions) {
			let enhID = entry[0];
			//We're just finding the relevant enh and reseting update marker, as during Edit process all systems stats will be reset to defaults.
			switch (enhID) {
				case 'ELITE_CREW':
					ship.eliteEnh = false;
					break;

				case 'ELT_MRN':
					ship.elMrnEnhShip = false;
					break;

				case 'EXT_MRN':
					ship.exMrnEnhShip = false;
					break;

				case 'HANG_F'://Hangar Conversion to Fighter slot, no actual need to change anything here.  
					ship.hangFEnh = false;
					break;

				case 'HANG_AS'://Hangar Conversion to Assault Shuttle slot, no actual need to change anything here.  
					ship.hangASEnh = false;
					break;

				case 'IFF_SYS':
					ship.iffEnh = false;
					break;

				case 'IMPR_ENG':
					ship.engEnh = false;
					break;

				case 'IMPR_PSY':
					ship.psyEnh = false;
					break;

				case 'IMPR_REA':
					ship.reaEnh = false;
					break;

				case 'IMPR_SENS':
					ship.sensEnh = false;
					break;

				case 'IMPR_SR':
					ship.srEnh = false;
					break;

				case 'IMPR_THSD':
					ship.thsdEnh = false;
					break;

				case 'IMPR_TS':
					ship.tsEnh = false;
					break;

				case 'IPSH_EETH':
					ship.eethEnh = false;
					break;

				case 'IPSH_ESSAN':
					ship.essanEnh = false;
					break;

				case 'MARK_FERV':
					ship.fervEnh = false;
					break;

				case 'POOR_CREW':
					ship.poorEnh = false;
					break;

				case 'SHAD_DIFF':
					ship.diffEnh = false;
					break;

				case 'SHAD_FTRL':
					ship.ftrlEnh = false;
					break;

				case 'SPARK_CURT':
					ship.sparkEnh = false;
					break;

				case 'SLUGGISH':
					ship.slugEnh = false;
					break;

				case 'VOR_AMETHS': // Vorlon Amethyst Skin (for ship)
					ship.amethsEnh = false;
					break;

				case 'VOR_AZURS': // Vorlon Azure Skin (for ship) - +1 Shield rating
					ship.azursEnh = false;
					break;

				case 'VOR_CRIMS': // Vorlon Crimson Skin (for ship) - Power Capacitor gains +2 storage points and +1 recharge point
					ship.crimsEnh = false;
					break;

				case 'VULN_CRIT': // Vulnerable to Criticals: +1 Crit roll mod
					ship.vulnEnh = false;
					break;
				// Add more cases as necessary				
			}

			// Ammo magazine is necessary for some options
			let ammoMagazine = null;
			for (let magazine of ship.systems) {
				if (magazine.name === 'ammoMagazine') {
					ammoMagazine = magazine;
					break;
				}
			}
			//If there's a magazine, let's check if any missiles were bought!
			if (ammoMagazine != null) {

				switch (enhID) {

					case 'AMMO_B': //Basic Missile - Shouldn't ever be purchasable an an enhancement, but here we are.					
						ship.ammoBEnh = false;
						break;

					case 'AMMO_L': //Long Range Missile
						ship.ammoLEnh = false;
						break;

					case 'AMMO_H': //Heavy Missile
						ship.ammoHEnh = false;
						break;

					case 'AMMO_F': //Flash Missile
						ship.ammoFEnh = false;
						break;

					case 'AMMO_A': //Antifighter Missile
						ship.ammoAEnh = false;
						break;

					case 'AMMO_P': //Piercing Missile
						ship.ammoPEnh = false;
						break;

					case 'AMMO_D': //Light Missile						
						ship.ammoDEnh = false;
						break;

					case 'AMMO_I': //Interceptor Missile 						
						ship.ammoIEnh = false;
						break;

					case 'AMMO_C': //Chaff Missile						
						ship.ammoCEnh = false;
						break;

					case 'AMMO_J': //Jammer Missile						
						ship.ammoJEnh = false;
						break;

					case 'AMMO_K': //Starburst Missile						
						ship.ammoKEnh = false;
						break;

					case 'AMMO_M': //Multiwarhead Missile						
						ship.ammoMEnh = false;
						break;

					case 'AMMO_KK': //Kinetic Missile						
						ship.ammoKKEnh = false;
						break;

					case 'AMMO_S': //Stealth Missile						
						ship.ammoSEnh = false;
						break;

					case 'AMMO_X': //HARM Missile						
						ship.ammoXEnh = false;
						break;

					case 'MINE_BLB': //Ballistic Launcher Basic Mine										
						ship.ammoBLBEnh = false;
						break;

					case 'MINE_BLH': //Ballistic Launcher Heavy Mine						
						ship.ammoBLHEnh = false;
						break;

					case 'MINE_BLW': //Ballistic Launcher Wide-Range Mine						
						ship.ammoBLWEnh = false;
						break;

					case 'MINE_MLB': //Abbai Mine Launcher Basic Mine													
						ship.ammoMLBEnh = false;
						break;

					case 'MINE_MLW': //Abbai Mine Launcher Wide-Ranged Mine						
						ship.ammoMLWEnh = false;
						break;

					//AMMO TYPES FOR DIRECT FIRE WEAPONS					
					case 'SHELL_HBSC': //Standard Ammo for Heavy Railgun						
						ship.shellHBEnh = false;
						break;

					case 'SHELL_MBSC': //Standard Ammo for Medium Railgun						
						ship.shellMBEnh = false;
						break;

					case 'SHELL_LBSC': //Standard Ammo for Light Railgun						
						ship.shellLBEnh = false;
						break;

					case 'SHELL_HFLH': //Flash Ammo for Heavy Railgun						
						ship.ammoHFEnh = false;
						break;

					case 'SHELL_MFLH': //Flash Ammo for Medium Railgun						
						ship.ammoMFEnh = false;
						break;

					case 'SHELL_LFLH': //Flash Ammo for Light Railgun						
						ship.ammoLFEnh = false;
						break;

					case 'SHELL_HSCT': //Scatter Ammo for Heavy Railgun						
						ship.ammoHSEnh = false;
						break;

					case 'SHELL_MSCT': //Scatter Ammo for Medium Railgun						
						ship.ammoMSEnh = false;
						break;

					case 'SHELL_LSCT': //Scatter Ammo for Light Railgun						
						ship.ammoLSEnh = false;
						break;

					case 'SHELL_HHVY': //Heavy Ammo for Heavy Railgun						
						ship.ammoHHEnh = false;
						break;

					case 'SHELL_MHVY': //Heavy Ammo for Medium Railgun						
						ship.ammoMHEnh = false;
						break;

					case 'SHELL_LHVY': //Heavy Ammo for Light Railgun						
						ship.ammoLHEnh = false;
						break;

					case 'SHELL_HLR': //Long Range Ammo for Heavy Railgun						
						ship.ammoHLREnh = false;
						break;

					case 'SHELL_MLR': //Long Range Ammo for Medium Railgun						
						ship.ammoMLREnh = false;
						break;

					case 'SHELL_HULR': //Ultra Long Range Ammo for Heavy Railgun						
						ship.ammoHULREnh = false;
						break;
				}//endof ammo listings.

			}//endof if(ammoMagazine) check.	

		}
	},

	resetEnhancementMarkersFighter: function resetEnhancementMarkersFighter(flight) {

		for (let entry of flight.enhancementOptions) {
			// ID, readableName, numberTaken, limit, price, priceStep
			let enhID = entry[0];

			switch (enhID) {
				/*
				case 'DEPLOY': // Expert Motivator
					if(!flight.deployEnh && enhCount > 1){
						flight.notes += "<br>Deploys on Turn " + enhCount + "";
					}
					flight.deployEnh = true;
				break;
				*/
				case 'ELT_MAR': // Elite marines, mark every Marines system as Elite.
					flight.elMarEnhFlight = false;
					break;

				case 'ELITE_SW': // Elite Pilot (SW)
					flight.swEnh = false;
					break;

				case 'EXP_MOTIV': // Expert Motivator
					flight.motivEnh = false;
					break;

				case 'EXT_AMMO': // Extra ammo
					flight.exAmmoEnh = false;
					break;

				case 'EXT_HAMMO': // Extra heavy ammo
					flight.exHAmmoEnh = false;
					break;

				case 'EXT_MAR': // Extra marines	
					flight.exMarEnhFlight = false;
					break;

				case 'FTR_FERV': // Markab Religious Fervor
					flight.fervEnhFlight = false;
					break;

				case 'IMPR_OB': // Improved Targeting Computer
					flight.obEnh = false;
					break;

				case 'IMPR_THR': // Improved Thrust
					flight.thrEnh = false;
					break;

				case 'NAVIGATOR': // Navigator
					flight.navEnh = false;
					break;

				case 'POOR_TRAIN': // Poor Training
					flight.poorTrEnh = false;
					break;

				case 'SHAD_CTRL': // Shadow fighter deployed without carrier control
					flight.shadCtrlEnh = false;
					break;

				case 'VOR_AZURF': // Vorlon Azure Skin Coloring
					flight.azurfEnh = false;
					break;

				//consumable ammunition - add to ALL missile magazines on flight!
				case 'AMMO_FB': //Basic Fighter Missile
					flight.fbEnh = false;
					break;

				case 'AMMO_FH': //Heavy Fighter Missile
					flight.fhEnh = false;
					break;

				case 'AMMO_FL': //Long Range Fighter Missile
					flight.flEnh = false;
					break;

				case 'AMMO_FY': //Dogfight Fighter Missile
					flight.fyEnh = false;
					break;

				case 'AMMO_FD': //Dropout Fighter Missile
					flight.fdEnh = false;
					break;

			}//end of swtich function
		}

	}
};

