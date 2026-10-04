'use strict';

window.gamedata = {
	thisplayer: 0,
	slots: null,
	ships: [],
	gameid: 0,
	turn: 0,
	phase: 0,
	activeship: 0,
	waiting: true,
	maxpoints: 0,
	status: "LOBBY",
	selectedSlot: null,
	allShips: null,
	displayedShip: '',
	displayedFaction: '',
	lastShipNumber: 0,
	fleetWindowOpen: false,
	gamespace: '',

	/* Fleet Builder (fleetTest) points cap. The slot itself is ALWAYS unlimited in a
	   builder lobby, so this optional override is what the buy panel, the affordability
	   checks and the Fleet Checker measure against. null = unlimited (the default, and
	   the only possible value in an ordinary lobby, where the markup is not rendered).
	   Read through gamedata.getMaxPoints() and nowhere else. */
	builderMaxPoints: null,

	/* The game's In-Service Date cutoff (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md Stage 6): a year, or
	   null for none. Set once by gamelobby.php (tac_game.in_service_date). The Store's ISD box is
	   locked to it, and doLoadFleet leaves out any saved unit that entered service later. */
	inServiceDate: null,


	getPowerRating: function getPowerRating(factionName) {
		var powerRating = '';
		switch (factionName) {
			case 'Abbai Matriarchate':
				powerRating = 'Tier 2; League Faction';
				break;
			case 'Abbai Matriarchate (WotCR)':
				powerRating = 'Tier 3; League Faction';
				break;
			case 'Alacan Republic':
				powerRating = 'Tier 3; Minor Faction';
				break;
			case 'Balosian Underdwellers':
				powerRating = 'Tier 2; Minor Faction';
				break;
			case 'Barada Imperium':
				powerRating = 'Tier 2; Minor Custom Faction';
				break;
			case 'Belt Alliance':
				powerRating = 'Tier 2; Minor Faction';
				break;
			case 'Brakiri Syndicracy':
				powerRating = 'Tier 2; League Faction';
				break;
			case 'Cascor Commonwealth':
				powerRating = 'Tier 3; League Faction';
				break;
			case 'Centauri Republic':
				powerRating = 'Tier 1; Major Faction';
				break;
			case 'Centauri Republic (WotCR)':
				powerRating = 'Tier 3; Major Faction';
				break;
			case "Ch'Lonas Cooperative":
				powerRating = 'Tier 2; Minor Custom Faction';
				break;
			case 'Civilians':
				powerRating = 'Tier Other';
				break;
			case 'Corillani Theocracy':
				powerRating = 'Tier 2; Minor Faction';
				break;
			case 'Custom Ships':
				powerRating = "Tier Other, Custom";
				break;
			case 'Deneth Tribes':
				powerRating = 'Tier 2; Minor Faction';
				break;
			case 'Descari Committees':
				powerRating = 'Tier 2; Minor Faction';
				break;
			case 'Dilgar Imperium':
				powerRating = 'Tier 1; Major Faction';
				break;
			case 'Drakh':
				powerRating = 'Tier 1, Major Custom faction';
				break;
			case 'Drazi Freehold':
				powerRating = 'Tier 1; League Faction';
				break;
			case 'Drazi Freehold (WotCR)':
				powerRating = 'Tier 2; League Faction';
				break;
			case 'Earth Alliance':
				powerRating = 'Tier 1; Major Faction';
				break;
			/*case 'Earth Alliance (Custom)':
				powerRating = 'Tier 1; Major Custom Faction';
				break;*/
			/*case 'Earth Alliance (defenses)':
			  powerRating = 'Tier 1; Major Faction';
			  break;*/
			case 'Earth Alliance (Early)':
				powerRating = 'Tier 3; Major Faction';
				break;
			case 'Gaim Intelligence':
				powerRating = 'Tier 1; League Faction';
				break;
			case 'Grome Autocracy':
				powerRating = 'Tier 3; League Faction';
				break;
			case 'Hurr Republic':
				powerRating = 'Tier 3; League Faction';
				break;
			case 'Hyach Gerontocracy':
				powerRating = 'Tier 1; League Faction';
				break;
			case 'Ipsha Baronies':
				powerRating = 'Tier 3; League Faction';
				break;
			case 'Kirishiac Lords':
				powerRating = 'Tier Ancients';
				break;
			case 'Kor-Lyan Kingdoms':
				powerRating = 'Tier 1; League Faction';
				break;
			case 'Llort': //actually no full name in the sourcebook (RPP1), it's just Llort!
				powerRating = 'Tier 1; Minor Faction';
				break;
			case 'Markab Theocracy':
				powerRating = 'Tier 3; Minor Faction';
				break;
			case 'Minbari Federation':
				powerRating = 'Tier 1; Major Faction';
				break;
			case 'Minbari Protectorate':
				powerRating = 'Tier 1; Minor Faction';
				break;
			case 'Mindriders':
				powerRating = 'Tier Ancients';
				break;
			case 'Narn Regime':
				powerRating = 'Tier 1; Major Faction';
				break;
			case 'Orieni Imperium':
				powerRating = 'Tier 1; Major Faction';
				break;
			/*case 'Orieni Imperium (defenses)':
			  powerRating = 'Tier 1; Major Faction';
			  break;*/
			case 'Great Crusade Orieni Imperium':
				powerRating = 'Tier 1; Custom Faction; Playtest';
				break;
			case "Pak'ma'ra Confederacy":
				powerRating = 'Tier 2; League Faction';
				break;
			case 'Raiders':
				powerRating = 'Tier 2; Major Faction';
				break;
			case 'Rogolon Dynasty':
				powerRating = 'Tier 3; Minor Faction';
				break;
			case 'Shadow Association':
				powerRating = 'Tier Ancients';
				break;
			case 'Small Races':
				powerRating = 'Tier 3; Minor Faction';
				break;
			case 'Streib':
				powerRating = 'Tier Other';
				break;
			case 'Terrain':
				powerRating = 'Tier Other';
				break;
			case 'The System':
				powerRating = 'Tier Ancients, Custom faction, Playtest';
				break;
			case 'The Triad':
				powerRating = 'Tier Ancients';
				break;
			case 'Thirdspace':
				powerRating = 'Tier Ancients, Custom faction';
				break;
			case 'Torata Regency':
				powerRating = 'Tier 1; League Faction';
				break;
			case 'Torvalus Speculators':
				powerRating = 'Tier Ancients';
				break;
			case 'Usuuth Coalition':
				powerRating = 'Tier 3; Minor Faction';
				break;
			case 'Vorlon Empire':
				powerRating = 'Tier Ancients';
				break;
			case 'Vree Conglomerate':
				powerRating = 'Tier 1; League Faction';
				break;
			case 'Walkers of Sigma-957':
				powerRating = 'Tier Ancients';
				break;
			case 'Yolu Confederation':
				powerRating = 'Tier 1; Minor Faction';
				break;
			case 'Barada Imperium':
				powerRating = 'Tier 3; Minor Custom faction';
				break;
			case 'BSG Colonials':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Escalation Wars Blood Sword Raiders':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Escalation Wars Civilian':
				powerRating = 'Tier N/A, Custom';
				break;
			case 'Escalation Wars Support Units':
				powerRating = 'Tier 3; Designs for scenarios, Custom';
				break;
			case 'Escalation Wars Chouka Raider':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Escalation Wars Chouka Theocracy':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Escalation Wars Circasian Empire':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Escalation Wars Kastan Monarchy':
				powerRating = 'Tier 2; Custom faction';
				break;
			case "Escalation Wars Sshel'ath Alliance":
				powerRating = 'Tier 2; Custom faction';
				break;
			case "House Valheru":
				powerRating = 'Tier 1; Custom Centauri faction';
				break;
			case 'Nexus Brixadii Clans (early)':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Nexus Brixadii Clans':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Nexus Support Units':
				powerRating = 'Tier 3; Designs for scenarios, Custom';
				break;
			case 'Nexus Craytan Union (early)':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Nexus Craytan Union':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Nexus Dalithorn Commonwealth (early)':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Nexus Dalithorn Commonwealth':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Nexus Makar Federation (early)':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Nexus Makar Federation':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Nexus Polaren Confederacy (early)':
				powerRating = 'Tier 3; Custom faction, Playtest';
				break;
			case 'Nexus Sal-bez Coalition (early)':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Nexus Sal-bez Coalition':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'Nexus Velrax Republic (early)':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'Nexus Velrax Republic':
				powerRating = 'Tier 2; Custom faction';
				break;
			case '12 Colonies of Kobol':
				powerRating = 'Tier 1; Custom faction, Playtest';
				break;
			case 'BSG Cylons':
				powerRating = 'Tier 2; Custom faction, Playtest';
				break;
			case 'Star Trek (Kelly)':
				powerRating = 'Tier 1; Custom faction';
				break;				
			case 'StarTrek (TOS) Federation':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'StarTrek (early) Federation':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'StarTrek Klingon':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'StarTrek (early) Suliban':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'StarWars':
				powerRating = 'Tier 2, Custom faction';
				break;
			case 'Star Wars Clone Wars':
				powerRating = 'Tier 2, Custom faction, Playtest';
				break;
			case 'Trek Playtest Other Factions':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'What If':
				powerRating = 'Tier 1; Custom faction';
				break;
			default:
				powerRating = 'NOT ASSIGNED';
		}
		//...disclaimer proved too long to be practical
		//powerRating = 'Estimated CUSTOM combat effectiveness rating: ' + powerRating;
		return powerRating;
	},

	/* ⭐ THE point cap in force for the selected slot, and the ONLY place it is derived.
	   Ordinary lobby: the slot's own points (-1 meaning unlimited).
	   Fleet Builder: the slot is always -1, so an unticked "Unlimited" box substitutes the
	   value typed into the buy panel. Display (calculateFleet), the affordability checks
	   and the Fleet Checker all read it here, so the number the player is shown is exactly
	   the number they are held to. */
	getMaxPoints: function getMaxPoints() {
		var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
		var slotPoints = selectedSlot ? selectedSlot.points : -1;

		if (slotPoints != -1) return slotPoints;
		return gamedata.builderMaxPoints === null ? -1 : gamedata.builderMaxPoints;
	},

	/* Is this fleet-list row a BULK purchase - ONE object standing for N identical units,
	   minted into N ships by BuyingGamePhase? Mines always have been; OSATs joined them
	   (user request 2026-08-10). Everything that prices, lists, names or saves a row asks
	   here rather than testing .mine directly, so the two kinds cannot drift apart.

	   ⚠️ `osat` is NOT only the ship-shaped OSAT hull. MicroSAT extends SuperHeavyFighter
	   extends FighterFlight and sets osat = true, so a MicroSAT is a FLIGHT that lists
	   under Immobile Structures - and with maxFlightSize 3 it needs the ship dialog's
	   flight-size selector, which the bulk dialog has no equivalent for. Flights are
	   therefore excluded here, EXCEPT the flight-shaped mines (MineClass), which have been
	   bought in bulk all along and must keep behaving exactly as they do. */
	isBulkRow: function isBulkRow(ship) {
		if (!ship) return false;
		if (ship.mine) return true;

		return !!ship.osat && !ship.flight;
	},

	/* How many units this one row stands for (always >= 1). */
	bulkCount: function bulkCount(ship) {
		if (!gamedata.isBulkRow(ship)) return 1;
		var count = parseInt(ship.bulkBuy, 10);
		return (isNaN(count) || count < 1) ? 1 : count;
	},

	/* What this ONE row costs the fleet.
	   ⭐ ONE pricing convention across every row: ship.pointCost is the cost of a SINGLE
	   unit with its enhancements already folded in (doBuyShip, doBuyBulk and doLoadFleet
	   all establish that), so a row is simply that times the number of units it stands
	   for. Bulk rows used to keep their enhancements OUT of pointCost and re-add them at
	   each display site, which broke the moment a bulk-bought unit went through the edit
	   dialog (which writes the folded total) - and double-counted them on every loaded
	   mine bulk.
	   The mines-only 100pt premium and per-class surcharge are FLEET-level and live in
	   fleetCost, not here. */
	rowPointCost: function rowPointCost(ship) {
		return ship.pointCost * gamedata.bulkCount(ship);
	},

	/* Total cost of the selected slot's fleet.
	     pendingShip - a unit about to be bought, costed as if it were already in the list.
	     excludeId   - a row to leave out (the ship being edited, whose new cost arrives as
	                   pendingShip instead).
	   ONE implementation for the points display and for every affordability check: the two
	   used to be separate sums, and canAfford's ignored the mine premium entirely, so a
	   fleet could be bought that the panel already showed as over budget. */
	fleetCost: function fleetCost(pendingShip, excludeId) {
		var slotid = gamedata.selectedSlot;
		var points = 0;
		var unitPoints = 0;
		var uniqueUnitClasses = [];

		var tally = function (lship) {
			if (lship.mine) {
				unitPoints += gamedata.rowPointCost(lship);
				if (uniqueUnitClasses.indexOf(lship.mineType) === -1) {
					uniqueUnitClasses.push(lship.mineType);
				}
			} else {
				points += gamedata.rowPointCost(lship);
			}
		};

		for (var i in gamedata.ships) {
			if (gamedata.ships[i].slot != slotid) continue;
			if (excludeId !== undefined && gamedata.ships[i].id == excludeId) continue;
			tally(gamedata.ships[i]);
		}

		if (pendingShip) tally(pendingShip);

		//Mines are priced as a minefield: a flat 100pt to lay one at all, plus 10% for
		//every mine CLASS beyond the first.
		if (unitPoints > 0) {
			var surchargeMultiplier = 1 + ((uniqueUnitClasses.length - 1) * 0.10);
			points += Math.round((100 + unitPoints) * surchargeMultiplier);
		}

		return points;
	},

	canAfford: function canAfford(ship) {
		var maxPoints = gamedata.getMaxPoints();
		if (maxPoints == -1) return true; // Unlimited points

		return gamedata.fleetCost(ship) <= maxPoints;
	},

	canAffordEdit: function canAffordEdit(ship) {
		var maxPoints = gamedata.getMaxPoints();
		if (maxPoints == -1) return true; // Unlimited points

		/* ⚠️ Only the SHIP dialog is read back off the DOM here. The bulk dialog's
		   .totalUnitCostAmount is the whole ROW's total (per-unit cost x quantity), not the
		   single-unit pointCost this convention stores. doEditBulk has already priced the ship
		   through readBulkPurchase by the time it asks, which is the same arithmetic the dialog
		   displays. */
		if (!$(".confirm #bulkQuantity").length && $(".confirm .totalUnitCostAmount").length > 0) {
			ship.pointCost = $(".confirm .totalUnitCostAmount").data("value");
		}

		//the edited ship is costed as `pendingShip`, so its OLD row must not also count
		return gamedata.fleetCost(ship, ship.id) <= maxPoints;
	},

	/* Can this ALREADY-BOUGHT row still fit the budget after a change made outside the buy
	   dialog? Used by the ship window's per-system enhancement menu
	   (WEAPON_ENHANCEMENTS_PLAN.md §5.2), which writes straight onto the ship.

	   Deliberately NOT canAffordEdit: that one reads ship.pointCost back off the confirm
	   dialog's DOM, and there is no dialog open here. Same fleetCost() otherwise, with the
	   ship's own row excluded and itself costed as the pending one, so the refusal and the
	   pts-left figure the player is looking at can never disagree. */
	canAffordRefit: function canAffordRefit(ship) {
		var maxPoints = gamedata.getMaxPoints();
		if (maxPoints == -1) return true; // Unlimited points

		return gamedata.fleetCost(ship, ship.id) <= maxPoints;
	},

	/* ⭐ Has the selected slot's fleet been READIED? `lastphase >= "-2"` is the one test for
	   that, and it is what every buy/edit/remove path already refuses on ("You have already
	   confirmed your fleet"). Stated here so the React damage editors can ask the same
	   question instead of re-deriving it.

	   Pre-battle damage was the one thing still editable after Ready: the fleet has been
	   POSTed by then, so anything authored afterwards is never submitted - the player is
	   editing a fleet that no longer exists, and the ship window happily let them. Now the
	   menus simply do not open.

	   Lobby-only: game.php has no playerManager slots and never reaches this, because every
	   caller tests gamephase === -2 first. Defensive anyway - a missing slot reads as NOT
	   committed, which is the same answer the page starts with. */
	fleetIsCommitted: function fleetIsCommitted() {
		if (typeof playerManager === 'undefined' || !playerManager) return false;

		var slot = playerManager.getSlotById(gamedata.selectedSlot);
		return Boolean(slot) && slot.lastphase >= "-2";
	},

	/* ── REINFORCEMENTS (REINFORCEMENTS_PLAN.md §4 Stage 1) ─────────────────────────────
	   A reinforcement is an ORDINARY purchase with one boolean on it. It costs the same, it
	   comes out of the same point pool (fleetCost walks every row of the slot and does not
	   ask), and it passes the same fleet-composition checks. All this section does is let
	   the player set that boolean, show which rows carry it, and warn at Ready if nothing
	   they own could ever bring them in.

	   ⚠️ A PLAIN PROPERTY, never a class or a marker object. Lobby ship objects are
	   jQuery.extend clones of the static blueprint, so every instanceof fails and there is no
	   window.staticShips here ([[arch_lobby_ship_objects]]).                              */

	/* Was this game created with Allow Reinforcements? Off ⇒ the whole feature is invisible:
	   no toggle, no group header, no row link, and the server drops the flag anyway
	   (BuyingGamePhase::process reads the rule, never the claim alone). */
	reinforcementsAllowed: function reinforcementsAllowed() {
		return Boolean(gamedata.rules && gamedata.rules.allowReinforcements);
	},

	/* Force the buy mode off when the rule is not in play. Called from parseServerData, i.e. on
	   every poll, because gamedata.rules only exists once the first payload has landed. Cheap
	   and idempotent. The checkbox itself is hidden (Stage 5): its control is the fleet list's
	   MAIN FLEET / REINFORCEMENTS headers (setBuyTarget). */
	applyReinforcementRule: function applyReinforcementRule() {
		if (!gamedata.reinforcementsAllowed()) $("#reinforcementModeToggle").prop("checked", false);
	},

	/* Is the buy panel currently minting reinforcements? Read at PURCHASE time only - never
	   stored anywhere but on the bought unit - so flipping it later cannot rewrite history. */
	buyingReinforcement: function buyingReinforcement() {
		if (!gamedata.reinforcementsAllowed()) return false;
		return $("#reinforcementModeToggle").is(":checked");
	},

	/* Is this row a reinforcement? ONE place holds the test, because four sites ask: both row
	   builders, the grouping sweep and the Ready warning. The rule gate is folded in so that
	   a fleet carrying the flag from a game that HAD the rule reads as ordinary in one that
	   does not - which is also what the server will do with it. */
	isReinforcementRow: function isReinforcementRow(ship) {
		return Boolean(ship && ship.reinforcement) && gamedata.reinforcementsAllowed()
			&& gamedata.canBeReinforcement(ship);
	},

	/* ⭐ COULD THIS UNIT EVER WAIT IN HYPERSPACE? Bases, OSATs and Terrain are on the board on
	   turn 1 whatever the slot or the flag says - the mirror of BaseShip::alwaysDeploysTurnOne,
	   which is the first line of getTurnDeployed on BOTH sides.

	   ⚠️ THE FLAG IS NOT MERELY USELESS ON THEM, IT IS HARMFUL (user report 2026-08-29, game
	   4319). isReinforcement() means "flagged AND no arrival turn yet", so a Fixed Jump Gate
	   bought with the REINFORCEMENTS group selected answered TRUE to it while standing in plain
	   sight on the map - and the end-of-turn sweep then stamped the gate itself as arriving,
	   handing its owner an empty PRE-TURN ACTIONS phase every turn its jump point stood.
	   BuyingGamePhase::process refuses the same purchases server-side; this is here so the
	   player never sees a row in a group it cannot belong to. */
	canBeReinforcement: function canBeReinforcement(ship) {
		if (!ship) return false;
		if (ship.base || ship.osat) return false;
		return !gamedata.isTerrain(ship.shipSizeClass, ship.userid);
	},

	/* Does this lobby unit mount a Jump Engine that could bring its group OUT of hyperspace?

	   ⭐⭐ ANY JUMP ENGINE, INCLUDING A LEGACY ONE (Stage 9, user ruling 2026-08-29). This used to
	   be the three-property legacy test - markLegacy() clears ballistic and hextarget and zeroes
	   range, and $legacyJump itself is PROTECTED server-side and never reaches a blueprint - which
	   was right while only a B5 vortex could bring a wave in. It is wrong now: a Shadow hull
	   PHASES in, and the server's arrival rules (Firing::getExitDeclarationBlock) contain no range,
	   line-of-sight, offline or charge test for a legacy engine to fail. Narrowing this again would
	   put the WARNING BELOW on a Shadow reinforcement group that is perfectly able to arrive - a
	   scary, wrong message on a legal fleet, which is worse than no message at all.

	   ⚠️ THE NAME IS THE WHOLE TEST, and it can be, because markLegacy() deliberately keeps $name
	   'jumpEngine' (so SystemFactory reuses the client class) and PhasingDrive inherits it. This
	   also now counts the nine key-less engines in the stale uncompacted "Earth Alliance (Custom)"
	   blueprint, which is correct rather than tolerated: they are jump engines.
	   ⚠️ It also counts a FIXED GATE's engine, whose isGateJump() is invisible to the client. That
	   was true of the old test too and is handled by the caller - see readyReinforcementWarning,
	   where a gate is exactly what makes a jump-drive-less reinforcement group legal anyway. */
	hasArrivalJumpEngine: function hasArrivalJumpEngine(ship) {
		if (!ship || !ship.systems) return false;

		for (var a in ship.systems) {
			var s = ship.systems[a];
			if (s && s.name === 'jumpEngine') return true;
		}

		return false;
	},

	/* Is this unit a fixed jump gate? Mirrors gamedata.isJumpGate in game.php's gamedata.js -
	   JumpgateCapital ONLY. jumpgateNew and the civilian Jumpgate are obsolete, hidden from the
	   store by variantOf, and must never match (JUMP_GATES_PLAN.md trap 12). */
	isJumpGateRow: function isJumpGateRow(ship) {
		return Boolean(ship) && ship.phpclass === 'JumpgateCapital';
	},

	/* Flip one bought row between the main fleet and hyperspace.

	   ⭐ THE PER-ROW CORRECTION, not the primary control. Where a purchase LANDS is chosen
	   before it is bought - by clicking one of the two group headers (setBuyTarget) or the
	   store's "Buy as Reinforcement" tick - and a loaded fleet now comes back with the flags it
	   was saved with (plan §0, reversed 2026-08-28). This link is what changes one row's mind
	   afterwards. Keeping it out of the buy/edit dialogs also keeps it out of
	   confirm.snapshotShip, whose fixed field list would otherwise have to learn about it or
	   silently restore the old value on a cancelled edit. */
	toggleReinforcement: function toggleReinforcement(id) {
		if (!gamedata.reinforcementsAllowed()) return;

		//Same refusal every other row action makes - the fleet has been POSTed by now, so
		//anything authored afterwards is never submitted.
		if (gamedata.fleetIsCommitted()) {
			window.confirm.error("You have already readied your fleet!", function () { });
			return;
		}

		for (var i in gamedata.ships) {
			if (gamedata.ships[i].id != id) continue;
			//A hull that is on the board on turn 1 regardless can never wait in hyperspace -
			//see canBeReinforcement. The link is not offered on such a row, but the row could
			//still be carrying the flag from a fleet saved before that rule existed, in which
			//case clearing it is the only useful direction this toggle has.
			gamedata.ships[i].reinforcement = !gamedata.ships[i].reinforcement
				&& gamedata.canBeReinforcement(gamedata.ships[i]);
			break;
		}

		//A full rebuild rather than a patch: the row's class, its action link and its position
		//in the two groups all change together, and constructFleetList is the one place that
		//knows how to write all three.
		gamedata.constructFleetList();
	},

	/* Sort the fleet list into MAIN FLEET and REINFORCEMENTS, under one header each - and let
	   those two headers double as the BUY TARGET selector (user request 2026-08-28).

	   Called at the end of BOTH row-writing paths, for the same reason damagedShipBadge and
	   enhancementListHtml are shared: constructFleetList throws every row away and rewrites it
	   on every poll, while updateFleet appends one row at the end. Running here means a
	   freshly bought unit lands in its group immediately instead of sitting at the bottom
	   until the next poll.

	   ⚠️ BOTH HEADERS ARE ALWAYS WRITTEN once the game carries the rule - empty group or not,
	   and even on an empty fleet (user request 2026-08-28). They used to be conditional on there
	   BEING a reinforcement, which is no longer possible: an EMPTY group's header is the click
	   target that says "put the next purchase in here", so suppressing it would hide the only
	   control that could ever fill it.

	   ⚠️ IT REMOVES ITS OWN HEADERS FIRST, and that is load-bearing. constructFleetList clears
	   the list with $(".ship.bought").remove() - there is no $("#fleet").empty() anywhere - so
	   a header written by a previous pass would survive every rebuild and accumulate, once per
	   poll, forever.

	   ⚠️ The header must not carry the class `ship`, and must contain no .remove / .showship /
	   .editship / .copyship / .reinforcetoggle element: #fleet's click handlers are delegated
	   by those classes and resolve the row with closest(".ship"), which would come back empty.
	   Its OWN handler is delegated on .fleet-group-header and reads data-buytarget off the
	   header itself, so it never looks for a row either.

	   .appendTo on an existing element MOVES it, preserving document order within the set, so
	   the two groups keep the order the builder wrote them in. */
	applyFleetGrouping: function applyFleetGrouping() {
		$("#fleet .fleet-group-header").remove();
		if (!gamedata.reinforcementsAllowed()) return;

		//Which header is lit is exactly what #reinforcementModeToggle says - ONE source of truth
		//for the buy mode, so the tick in the store's filter strip and the two headers can never
		//disagree about where the next purchase is going to land.
		var toHyperspace = gamedata.buyingReinforcement();
		var marker = '<span class="fleet-group-target">buying here &#9656;</span>';

		var reinforcements = $("#fleet .ship.bought.reinforcement");
		var frontLine = $("#fleet .ship.bought").not(".reinforcement");

		$('<div class="fleet-group-header buy-target' + (toHyperspace ? '' : ' selected') + '"'
			+ ' data-buytarget="main" title="Buy the next unit into the main fleet">'
			+ marker + 'MAIN FLEET</div>').appendTo("#fleet");
		frontLine.appendTo("#fleet");

		$('<div class="fleet-group-header reinforcement buy-target' + (toHyperspace ? ' selected' : '') + '"'
			+ ' data-buytarget="reinforcement" title="Buy the next unit as a reinforcement">'
			+ marker + 'REINFORCEMENTS'
			+ '<span class="fleet-group-note">wait in hyperspace, arrive through a jump point</span>'
			+ '</div>').appendTo("#fleet");
		reinforcements.appendTo("#fleet");
	},

	/* Point the buy panel at one of the two groups, so a purchase lands where the player asked
	   instead of arriving front-line and needing its Reinforce link clicked afterwards (user
	   request 2026-08-28).

	   ⭐ THE HEADERS ARE THE CONTROL, #reinforcementModeToggle IS THE STATE. No second flag is
	   introduced: buyingReinforcement() already reads that checkbox at purchase time, and
	   applyReinforcementRule already forces it off when the game does not carry the rule - so
	   writing it from here is what keeps the header highlight, the lit-up filter-strip label and
	   the flag actually stamped on the bought unit all saying the same thing.

	   .prop() deliberately does not fire `change`, so the repaint is called explicitly. */
	setBuyTarget: function setBuyTarget(target) {
		if (!gamedata.reinforcementsAllowed()) return;
		$("#reinforcementModeToggle").prop("checked", target === 'reinforcement');
		gamedata.applyFleetGrouping();
	},

	/* The Ready-time warning for a reinforcement group that can never reach the battle, as an
	   HTML string, or "" when there is nothing to say (plan §2.1).

	   ⚠️ IT CAN ONLY SEE THIS PLAYER'S OWN PURCHASES, and says so. A lobby client is served NO
	   ships at all - TacGamedata::prepareForPlayer empties the list for a LOBBY game and
	   gamelobby.js discards serverdata.ships anyway - so gamedata.ships holds exactly what this
	   browser has bought. An ally's gate is invisible here, which is why this is a warning the
	   player confirms rather than a refusal. */
	readyReinforcementWarning: function readyReinforcementWarning() {
		if (!gamedata.reinforcementsAllowed()) return "";

		var slotid = gamedata.selectedSlot;
		var reinforcements = 0;
		var opener = false;
		var gate = false;

		for (var i in gamedata.ships) {
			var lship = gamedata.ships[i];
			if (lship.slot != slotid) continue;

			//A gate anywhere in the fleet will do - it does not have to be a reinforcement.
			if (gamedata.isJumpGateRow(lship)) gate = true;
			if (!gamedata.isReinforcementRow(lship)) continue;

			reinforcements++;
			//Only a unit that is ITSELF waiting in hyperspace can open its group's exit.
			if (gamedata.hasArrivalJumpEngine(lship)) opener = true;
		}

		if (reinforcements === 0 || opener || gate) return "";

		return '<span class="prebattle-note">'
			+ '<span class="prebattle-note-label">WARNING:</span> '
			+ 'None of your ' + reinforcements + ' reinforcement' + (reinforcements === 1 ? '' : 's')
			+ ' mounts a usable jump drive, and you have bought no jump gate. Unless an ally '
			+ 'provides one, they will stay in hyperspace for the whole battle and their points '
			+ 'will be wasted.'
			+ '</span>';
	},

	/* Broken-heart badge for a bought unit carrying pre-battle damage or criticals, as an
	   HTML string ready to prepend to a fleet-list row. Empty string when it carries
	   neither.
	   ONE helper for BOTH row builders (updateFleet and constructFleetList): the badge has
	   to be re-derived from the ship every time the fleet list is rebuilt, because the list
	   is thrown away and rewritten from gamedata.ships on every slot select, remove and
	   edit. The payload lives on the ship, so it always survives - it was only the markup
	   that was being lost.
	   Same icon the saved-fleet dropdown uses for a fleet carrying damage. */
	damagedShipBadge: function damagedShipBadge(ship) {
		if (!window.battleDamage || battleDamage.isEmpty(battleDamage.peek(ship))) return '';

		var carried = battleDamage.contents(battleDamage.peek(ship));
		var title = carried.damage && carried.criticals ? 'Carries pre-battle damage and critical effects'
			: (carried.criticals ? 'Carries pre-battle critical effects' : 'Carries pre-battle damage');

		//fa-screwdriver-wrench, not fa-heart-crack (user request 2026-08-08): a wound the
		//unit is carrying INTO the battle reads as "needs repair", not as a death.
		return '<span class="shipDamagedBadge fa-solid fa-screwdriver-wrench" title="' + title + '"></span>';
	},

	/* The bought-enhancement list for a fleet-list row, as an HTML string. Empty when the
	   unit has none.

	   ONE helper for BOTH row builders (updateFleet and constructFleetList) for exactly the
	   reason damagedShipBadge above is: the two used to carry identical copies of this loop,
	   and the pre-battle-damage badge was written into only one of them and vanished on the
	   next rebuild (PREBATTLE_DAMAGE_PLAN.md §6). Per-system refits are summarised as ONE
	   line rather than a dozen - the detail is in each system's own tooltip
	   (WEAPON_ENHANCEMENTS_PLAN.md §6.4). */
	enhancementListHtml: function enhancementListHtml(ship) {
		var listHtml = "";
		var hasEnhancements = false;

		for (var enhId in (ship.enhancementOptions || {})) {
			var name = lobbyEnhancements.describeTaken(ship.enhancementOptions[enhId]); //null when not taken
			if (name === null) continue;
			name = name.replace(/^(\(AMMO\)|\(LIGHT AMMO\)|\(MEDIUM AMMO\)|\(HEAVY AMMO\)|\(Option\))\s*/, '');
			hasEnhancements = true;
			listHtml = '<div class="ship-enhancement-entry">- ' + name + '</div>' + listHtml; // Prepend to reverse order
		}

		if (window.systemEnhancements) {
			var sysEnhLine = systemEnhancements.summaryLine(ship);
			if (sysEnhLine) {
				hasEnhancements = true;
				//Appended, so it reads LAST after the prepend-reversed ship-level lines above.
				listHtml = listHtml + '<div class="ship-enhancement-entry">- ' + sysEnhLine + '</div>';
			}
		}

		return hasEnhancements ? '<div class="ship-enhancements">' + listHtml + '</div>' : '';
	},

	/* Re-derive ONE fleet-list row's mutable content from the ship. Called by the React
	   damage/enhancement menus after every edit, so the row keeps up as the player works
	   rather than waiting for the next full fleet-list rebuild. Lobby-only: game.php's
	   gamedata has no such method and the callers guard on typeof.

	   ⭐ EVERYTHING here is re-derived through the same helpers the two row BUILDERS use
	   (rowDisplay / damagedShipBadge / enhancementListHtml), never patched field by field.
	   That is what stops this drifting away from updateFleet and constructFleetList - the
	   badge did exactly that once already (PREBATTLE_DAMAGE_PLAN.md §6).

	   ⚠️ Three things move when a per-system refit is bought, and until 2026-08-16 only the
	   first was repainted: the badge, the row's POINT COST (calculateFleet updates the
	   points panel, never the row) and the "System Enhancements (n)" line. The cost and the
	   line were both only ever written at row-BUILD time, so a refit read as free and
	   invisible until the player edited the ship or reloaded the fleet - while the panel
	   subheader had already charged for it (user report 2026-08-16). */
	refreshFleetRow: function refreshFleetRow(ship) {
		if (!ship) return;

		var row = $(".ship.bought").filter(function () {
			return $(this).data("shipindex") == ship.id;
		});
		if (!row.length) return;

		row.find(".shipDamagedBadge").remove();
		var badge = gamedata.damagedShipBadge(ship);
		if (badge) row.prepend(badge);

		var display = gamedata.rowDisplay(ship);
		row.find(".shipname").first().text(display.name);
		row.find(".boughtPointCost").first().text(display.cost + 'p');

		/* Re-inserted BEFORE .ship-actions rather than appended: the row is a block stack
		   and the action links are always its last child, so appending would put the
		   enhancement lines underneath them. */
		row.find(".ship-enhancements").remove();
		var enhancementHtml = gamedata.enhancementListHtml(ship);
		if (enhancementHtml) {
			var actions = row.find(".ship-actions").first();
			if (actions.length) $(enhancementHtml).insertBefore(actions);
			else row.append(enhancementHtml);
		}
	},

	/* The action links one fleet-list row offers, as an HTML string.
	   EVERY row gets the full set (user request 2026-08-10). A bulk row is edited and
	   copied as a whole purchase - quantity plus the enhancements carried by every unit in
	   it - through the bulk dialog rather than the ship one; editShip/copyShip pick which
	   on isBulkRow, so nothing here has to know the difference.
	   Copy on a bulk row is not just "raise the quantity": two rows of one class is the
	   only way to hold two differently-enhanced batches of it (five mines with MINE_SIGN
	   and five without). The lobby saves such rows separately - groupSaveableShips only
	   merges mines outside gamephase -2 - and BuyingGamePhase's name counters run on
	   across rows, so the second batch numbers #6, #7... rather than restarting.
	   ONE builder for both row-writing paths (updateFleet and constructFleetList), which
	   previously carried two near-identical copies of this markup. */
	rowActionsHtml: function rowActionsHtml(ship) {
		/* REINFORCEMENTS_PLAN.md §4 Stage 1 - the re-flag link, offered only when the game
		   carries the rule so every other lobby's row is byte-identical to before. It reads as
		   the ACTION, not the state (the state is the group the row sits in and the colour of
		   its name), which is the convention the other four links follow. */
		var reinforce = '';
		//canBeReinforcement: no link at all on a hull that is on the board on turn 1 regardless
		//(base, OSAT, Terrain) - offering it would be offering a state it can never hold.
		if (gamedata.reinforcementsAllowed() && gamedata.canBeReinforcement(ship)) {
			reinforce = gamedata.isReinforcementRow(ship)
				? ' -<span class="reinforcetoggle clickable" title="Move this unit into the main fleet instead">Main Fleet</span> '
				: ' -<span class="reinforcetoggle clickable" title="Hold this unit in hyperspace and bring it in through a jump point">Reinforcement</span> ';
		}

		return '<div class="ship-actions">' +
			' <span class="showship clickable">Details</span> ' +
			' -<span class="editship clickable">Edit</span> ' +
			' -<span class="copyship clickable">Copy</span> ' +
			reinforce +
			' -<span class="remove clickable">Remove</span> </div>';
	},

	/* The name and cost a fleet-list row displays. A bulk row shows the whole purchase:
	   "Gravitic Mine (10)" at the cost of all ten. */
	rowDisplay: function rowDisplay(ship) {
		var count = gamedata.bulkCount(ship);

		return {
			name: count > 1 ? ship.name + ' (' + count + ')' : ship.name,
			cost: Math.ceil(gamedata.rowPointCost(ship))
		};
	},

	updateFleet: function updateFleet(ship) {
		var a = 0;
		for (var i in gamedata.ships) {
			a = i;
		}
		a++;
		ship.id = Date.now() + Math.random().toString(36).substr(2, 5);

		ship.slot = gamedata.selectedSlot;
		gamedata.ships[a] = ship;
		//ONE builder for both fleet-list row paths - see gamedata.enhancementListHtml.
		var enhancementHtml = gamedata.enhancementListHtml(ship);

		var displayType = ship.shipClass;
		var display = gamedata.rowDisplay(ship);

		//Pre-battle damage: broken-heart badge ahead of the name, so a damaged unit reads as
		//damaged without opening its window. Built by the shared helper so this row and the
		//one constructFleetList rebuilds cannot drift - the old ' (damaged)' suffix was
		//written HERE ONLY and vanished at the next fleet-list rebuild.
		var damageBadge = gamedata.damagedShipBadge(ship);

		//REINFORCEMENTS_PLAN.md §4 Stage 1: the row carries its own state as a class, so the
		//grouping sweep and the CSS can both read it off the DOM without walking gamedata.ships.
		var reinforcementClass = gamedata.isReinforcementRow(ship) ? ' reinforcement' : '';

		var h = $('<div class="ship bought' + reinforcementClass + ' slotid_' + ship.slot + ' shipid_' + ship.id + '" data-shipindex="' + ship.id + '">' +
			damageBadge +
			'<span class="shipname name">' + display.name + '</span>' +
			//Class and cost in one box, so a narrow fleet column wraps them onto a line together.
			'<span class="boughtClassCost"><span class="boughtShiptype">' + displayType + '</span>' +
			'<span class="boughtPointCost">' + display.cost + 'p</span></span>' +
			enhancementHtml +
			gamedata.rowActionsHtml(ship) +
			'</div>');

		$(".remove", h).bind("click", function () {
			delete gamedata.ships[a];
			h.remove();
			//REINFORCEMENTS_PLAN.md §4 Stage 1: the removed row leaves a gap in whichever group it
			//sat in, and the two headers have to be re-drawn around what is left. This handler is a
			//direct bind on a row updateFleet built, so - unlike the delegated twin in
			//constructFleetList - it is the only one that runs and it does not rebuild the list.
			gamedata.applyFleetGrouping();
			gamedata.calculateFleet();
			gamedata.populateFleetDropdown();
		});

		$(".showship", h).on("click", function (e) {
			gamedata.onShipContextMenu(ship.phpclass, ship.faction, ship.id, true);
		});

		//No .mine guard needed: editShip/copyShip send a bulk row to the bulk dialog and
		//anything else to the ship one, so this binding is the same for every row.
		$(".editship", h).on("click", function (e) {
			gamedata.editShip(ship);
		});

		$(".copyship", h).on("click", function (e) {
			gamedata.copyShip(ship);
		});

		h.appendTo("#fleet");
		//REINFORCEMENTS_PLAN.md §4 Stage 1: this row was appended at the END of the list, so a
		//freshly bought reinforcement would sit outside its own group until the next poll
		//rebuilt the list. Re-sorting here costs nothing in a game without the rule -
		//applyFleetGrouping returns on its second line.
		gamedata.applyFleetGrouping();
		gamedata.calculateFleet();
	},
	/*
		updateLoadedFleet: function updateLoadedFleet(ships) {
			for(var k in ships){
				var ship = ships[k]	
				var a = 0;
				for (var i in gamedata.ships) {
					a = i;
				}
				a++;
				ship.id = Date.now() + Math.random().toString(36).substr(2, 5);
				
				ship.slot = gamedata.selectedSlot;
				gamedata.ships[a] = ship;
				var h = $('<div class="ship bought slotid_' + ship.slot + ' shipid_' + ship.id + '" data-shipindex="' + ship.id + '">' +
						'<span class="shipname name">' + ship.name + '</span>' +				
						'<span class="shiptype">' + ship.shipClass + '</span>' +
					'<span class="pointcost">' + ship.pointCost + 'p</span>' +
					' <span class="showship clickable">Details</span> ' +
					' -<span class="editship clickable">Edit</span> ' +		
					' -<span class="copyship clickable">Copy</span> ' +							
					' -<span class="remove clickable">Remove</span> ' +
					'</div>');
				
				$(".remove", h).bind("click", function () {
					delete gamedata.ships[a];
					h.remove();
					gamedata.calculateFleet();
					gamedata.populateFleetDropdown();			
				});
	
				$(".showship", h).on("click", function (e) {
					gamedata.onShipContextMenu(ship.phpclass, ship.faction, ship.id, true);
				});
	
				$(".editship", h).on("click", function (e) {
					gamedata.editShip(ship);
				});
	
				$(".copyship", h).on("click", function (e) {
					gamedata.copyShip(ship);
				});
	
				h.appendTo("#fleet");
			}
			gamedata.calculateFleet();
		},
	*/

	/*returns ship variant as a single letter*/
	variantLetter: function (ship) {
		var vLetter = '';
		switch (ship.occurence) {
			case 'unique':
				vLetter = 'Q';
				break;
			case 'rare':
				vLetter = 'R';
				break;
			case 'uncommon':
				vLetter = 'U';
				break;
			case 'common':
				vLetter = 'C';
				break;
			default: //assume something atypical
				vLetter = 'X';
		}
		return (vLetter);
	},

	/*checks fleet composition and displays alert with result*/
	checkChoices: function () {
		/*this is for interaction with $outOfTier array in ship SCS
		indicates PROBLEM => (count->current count; limit->accepted count max; text->warning text if over limit)
		*/
		var outOfTierArray = new Array('WARLOCK', 'EMINE'); //list of allowed entries - must match object below
		var outOfTierList = {
			'WARLOCK': { count: 0, limit: 0, text: 'Warlock is above Tier 1' }, //Warlock: not allowed
			'EMINE': { count: 0, limit: 6, text: 'Massed EMines are above Tier 1 (up to 6 are allowed)' } //EMines: up to 6 EMines allowed
		};

		//Reused result snippets (Item 8). Each constant is the exact string —
		//including the leading space — that previously appeared inline dozens of
		//times. The verdicts are classes, not inline colours: .fc-ok / .fc-bad /
		//.fc-warn in gameLobby.css, where the report window styles them (Stage 5).
		var R_OK = " <span class='fc-ok'>OK</span>";
		var R_TOOMANY = " <span class='fc-bad'>TOO MANY!</span>";
		var R_FAILURE = " <span class='fc-bad'>FAILURE!</span>";
		var R_FAILED = " <span class='fc-bad'>FAILED!</span>";

		var warningText = ""
		var checkResult = "";
		var problemFound = false;
		var warningFound = false;
		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);

		var totalPointsSpent = 0;
		var units10 = 0;
		var units33 = 0;
		var units50 = 0;
		var points10 = 0;
		var points33 = 0;
		var points50 = 0;
		var totalU = 0;
		var totalR = 0;
		var jumpDrivePresent = false;
		var capitalShips = 0;
		var totalShips = 0;
		var customShipPresent = false;
		var enhancementPresent = false;
		var uniqueShipPresent = false;
		var ancientUnitPresent = false;
		var specialVariantPresent = false;
		var staticPresent = false;
		var nonCombatPresent = false;
		var shipTable = [];
		var noSmallFlights = 0;

		var specialFighters = [];
		var specialHangars = [];
		var specialFtrAmt = 0;
		var specialFtrName = '';
		var specialHgrAmt = 0;
		var specialHgrName = '';
		var totalHangarH = 0; //hangarspace for heavy fighters
		var totalHangarM = 0; //hangarspace for medium fighters
		var totalHangarL = 0; //hangarspace for light fighters
		var totalHangarXL = 0; //hangarspace for ultralight fighters
		var totalHangarAS = 0;//total Assault Shuttle/Breaching pod slots
		var totalHangarOther = new Array(); //other hangarspace
		var totalFtrH = 0;//total heavy fighters
		var totalFtrM = 0;//total medium fighters
		var totalFtrL = 0;//total light fighters
		var totalFtrXL = 0;//total ultralight fighters
		var totalFtrAS = 0;//total Assault Shuttle/Breaching pods
		var hangarConversionsF = 0; //How many converted hangar slots TO fighter slots.
		var hangarConversionsAS = 0; //How many converted hangar slots TO Assault Shuttle slots.
		var totalFtrOther = new Array();//total other small craft
		var smallCraftUsed = new Array();//small craft sizes that happen to be present, whether as hangar space or actual craft
		/* Small-craft categories whose craft may be taken WITHOUT hangar space, and whose declared
		   capacity is therefore reported with a 50% MINIMUM and no maximum. Mapmaker Sensor Probes
		   are the only one today.

		   ⚠️ SEEDED FROM A DECLARED LIST, NOT DERIVED FROM THE FLEET ALONE. The half-full rule has to
		   bite on an EMPTY carrier - a Traveler carrying no probes at all is exactly what "at least
		   half full" exists to forbid - and a set built only from the craft actually bought is empty
		   in precisely that case. The flights' own $noHangarRequired flag then ADDS to this below,
		   which covers probes bought with no carrier in the fleet at all; the two must name the same
		   category string, and MapmakerProbes::$hangarRequired is where that string comes from.
		   See the small-craft report loop below, and WALKERS_OF_SIGMA_PLAN.md 3.13. */
		var noHangarMaxCraftTypes = ['Mapmaker Probes'];
		/* WALKERS_OF_SIGMA_PLAN.md 3.14 (Stage 16) - DOCKED SHIPS COUNT TOWARD A DOCKING BAY'S CATEGORY
		   (user, 2026-09-11): "purchasing Waymarker (24), Pathfinder/Guideships (12) and Scribes (4) can
		   help meet Traveler hangar capacity in Fleet Checker, along with Mapmaker fighters as usual.
		   Since 24 of its 36 fighter slots are associated with its Aft Docking Bay system."
		   So every bought ship a Docking Bay in the fleet can take adds its BOX cost to that bay's
		   category ($fleetCheckCategory), CAPPED at the fleet's total Docking Bay boxes - a ship only
		   fills the slots it could physically sit in, so a lone Pathfinder cannot satisfy its own
		   6-probe minimum, and a second Traveler's worth of ships adds nothing without a second
		   Traveler. The Waymarker counts although it cannot dock in play yet (3.14a deferred). */
		var dockingBayPools = {};     //category -> {capacity, classes: {phpclass: true}}
		var fleetShipsForBays = [];   //every non-flight unit's {phpclass, unitSize}
		var dockedShipBoxesByCategory = {};   //category -> boxes the report credits to docked ships
		var totalShuttleCapacity = 0; //sum of default shuttle/flyer pool capacity across the fleet (excludes minesweeping shuttles)
		var defaultShuttleKeyList = []; //distinct lship.fighters keys used by default shuttle pools (e.g. "shuttles", "minbari flyers")

		var totalEnhancementsValue = 0;
		var totalBPSizeCap = 0;     //sum of per-ship size-based BP caps (1/2/4 with x2 for Assault hulls)
		var totalBPDedicated = 0;   //sum of dedicated "Breaching Pods" slots declared in ship.fighters
		var totalBPUsage = 0;
		var shipHangarProfiles = [];
		var breachingPodsList = [];

		for (var i in gamedata.ships) {
			var lship = gamedata.ships[i];
			if (lship.slot != slotid) continue;

			//rowPointCost, not pointCost: a bulk row (mines, OSATs) is N units, and this
			//figure is what stands in for the fleet limit when the fleet has none.
			totalPointsSpent += gamedata.rowPointCost(lship);

			//Docking Bays and the ships that could fill them (see dockingBayPools above) - credited
			//to the bay's category in the small-craft report below.
			if (!lship.flight) {
				fleetShipsForBays.push({ phpclass: String(lship.phpclass), unitSize: lship.unitSize });
				for (var dbk in lship.systems) {
					var dbSys = lship.systems[dbk];
					if (!dbSys || !dbSys.isDockingBay || !dbSys.fleetCheckCategory) continue;
					var dbPool = dockingBayPools[dbSys.fleetCheckCategory]
						|| (dockingBayPools[dbSys.fleetCheckCategory] = { capacity: 0, classes: {} });
					dbPool.capacity += parseInt(dbSys.maxhealth || 0, 10);
					(dbSys.dockableShipClasses || []).forEach(function (cls) { dbPool.classes[cls] = true; });
				}
			}

			// 10%/33% deployment brackets use the BASE ship cost only (no ammo, no
			// enhancements). lship.pointCost is overwritten at purchase to the post-
			// purchase total (base + ammo + enhancements); the canonical base lives on
			// the catalog entry. For flights, catalog cost is for a full 6-craft flight,
			// so scale by actual flightSize/6 to mirror confirm.js getTotalCost.
			var bracketBaseCost = lship.pointCost;
			var catalogShip = gamedata.getShipByType(lship.phpclass);
			if (catalogShip) {
				bracketBaseCost = catalogShip.pointCost;
				if (lship.flight && lship.flightSize) {
					bracketBaseCost = bracketBaseCost * (lship.flightSize / 6);
				}
			}

			if (lship.limited == 10) {
				points10 += bracketBaseCost;
				units10 += 1;
			}
			if (lship.limited == 33) {
				points33 += bracketBaseCost;
				units33 += 1;
			}
			if (lship.limited == 50) {
				points50 += bracketBaseCost;
				units50 += 1;
			}
			totalEnhancementsValue += lship.pointCostEnh;
			var vLetter = gamedata.variantLetter(lship);
			var hull = lship.variantOf;
			if (hull == "") hull = lship.shipClass; //ship is either base itself, or base is indicated in variantOf variable

			// Item 5: find-or-create the hull row up front, then run ONE variant
			// switch against it. Previously this was two near-identical switches
			// (one for an existing shipTable row, one for a freshly-built one). A
			// new row starts Total:1 and the switch bumps one variant counter, so
			// existing-vs-new produce the same per-row tallies. hangarRequired is
			// sticky (any hangar-requiring ship of the hull flips it true and it
			// never resets); isFtr is only meaningful at creation. Behaviour —
			// including the Item 10 fix (special variants increment THIS row's X
			// and set specialVariantPresent) — is unchanged.
			var hullRow = null;
			for (var j in shipTable) {
				if (shipTable[j].name == hull) { hullRow = shipTable[j]; break; }
			}
			if (hullRow === null) {
				hullRow = { name: hull, Total: 0, Q: 0, R: 0, U: 0, C: 0, X: 0, isFtr: lship.flight ? lship.flight : false, hangarRequired: false };
				shipTable.push(hullRow);
			}
			hullRow.Total++;
			if (lship.hangarRequired != '') { //let's require sticking to hull limit if ANY ship of this hull requires it
				hullRow.hangarRequired = true;
			}
			switch (vLetter) {
				case 'Q':
					hullRow.Q++;
					totalR++; //Unique is treated more or less the same as Rare
					uniqueShipPresent = true;
					break;
				case 'R':
					hullRow.R++;
					totalR++;
					break;
				case 'U':
					hullRow.U++;
					totalU++;
					break;
				case 'C':
					hullRow.C++;
					break;
				default:
					//Item 10 fix: special variants increment this hull row's X and
					//flag specialVariantPresent (the old already-seen-hull path wrote
					//the wrong object and skipped the flag).
					hullRow.X++;
					specialVariantPresent = true;
			}
			if (lship.factionAge > 2) {
				ancientUnitPresent = true;
			}



			//potentially out-of-Tier elements
			for (var potProblem in lship.outOfTier) {
				var potProblemCount = lship.outOfTier[potProblem];
				if (potProblemCount > 0) {
					var outOfTierEntry = outOfTierList[potProblem];
					if (outOfTierEntry) outOfTierEntry.count += potProblemCount;
				}
			}


			if (!lship.flight) {
				totalShips++;

				// Apply HANG_BP slot conversion to lship.fighters so every downstream
				// consumer in this loop (BP totals, hangar tallies, getDefaultShuttles)
				// sees the post-conversion shape. Mirrors the server-side mutation in
				// Enhancements::setEnhancementsShip.
				//
				// HANG_MSW is deliberately NOT applied here — minesweeping shuttles
				// still count as default shuttle capacity for fleet-check purposes;
				// only the auto-populated *type* changes at game-load (HangarOps step 3).
				//
				// Snapshot the original on first encounter so subsequent fleet-check
				// passes restore-then-reapply (otherwise enhCount changes would stack).
				if (!lship._originalFighters) {
					lship._originalFighters = JSON.parse(JSON.stringify(lship.fighters || {}));
				} else {
					lship.fighters = JSON.parse(JSON.stringify(lship._originalFighters));
				}
				if (lship.enhancementOptions) {
					for (var preEnh in lship.enhancementOptions) {
						var preEnhID = lship.enhancementOptions[preEnh][0];
						var preConvNum = lship.enhancementOptions[preEnh][2] || 0;
						if (preConvNum <= 0) continue;
						//HANG_BP — convert default shuttle slots into dedicated Breaching
						//Pod slots. Default shuttles auto-fill leftover hangar capacity,
						//so adding to "Breaching Pods" implicitly steals from that pool;
						//no explicit "shuttles" decrement needed. Mirrors the server-side
						//mutation in Enhancements::setEnhancementsShip (HANG_BP case).
						if (preEnhID === "HANG_BP") {
							lship.fighters["Breaching Pods"] = (lship.fighters["Breaching Pods"] || 0) + preConvNum;
						}
					}
				}

				// Calculate Breaching Pod capacity for this ship - only if it has suitable hangar capacity.
				// Dedicated "Breaching Pods" slots in ship.fighters (e.g. Decurion's 4 side-bay pod racks)
				// are guaranteed BP capacity, additive to the size-based limit, and BPs prefer them first.
				var hasBPCompatibleHangar = false;
				var shipBPDedicated = lship.fighters["Breaching Pods"] || 0;
				var shipSlots = {
					"heavy": lship.fighters["heavy"] || lship.fighters["normal"] || 0,
					"medium": lship.fighters["medium"] || 0,
					"assault shuttles": lship.fighters["assault shuttles"] || 0,
					"breaching pods": shipBPDedicated
				};

				if (shipSlots["heavy"] > 0 || shipSlots["medium"] > 0 || shipSlots["assault shuttles"] > 0 || shipSlots["breaching pods"] > 0) {
					hasBPCompatibleHangar = true;
				}

				var shipBPLimit = 0;
				if (hasBPCompatibleHangar) {
					shipBPLimit = 1;
					if (lship.Enormous || lship.base || lship.smallBase) {
						shipBPLimit = 4;
					} else if (lship.shipSizeClass >= 3) { // Capital ships
						shipBPLimit = 2;
					}
					// Double for Assault units (hull type as requested)
					if (lship.shipClass.toLowerCase().indexOf("assault") !== -1) {
						shipBPLimit *= 2;
					}
					// The size-based cap is how many of THIS ship's own AS/Heavy/Medium
					// slots it may dedicate to pods — it can't exceed the slots the ship
					// actually has to host them. Dedicated "Breaching Pods" racks are
					// counted separately (totalBPDedicated) and don't host size-cap pods.
					// Without this clamp a ship that converted its ONLY hangar box into a
					// BP rack (e.g. Urik'hal: capacity 1 → 1 rack, 0 fighter slots) would
					// still contribute its full size cap to the fleet pool, letting those
					// phantom slots be borrowed by another carrier's pods.
					var shipOwnOverflowSlots = shipSlots["heavy"] + shipSlots["medium"] + shipSlots["assault shuttles"];
					shipBPLimit = Math.min(shipBPLimit, shipOwnOverflowSlots);
					totalBPSizeCap += shipBPLimit;
					totalBPDedicated += shipBPDedicated;
				}

				// Record ship profile for per-ship validation
				var shipProfile = {
					id: lship.id,
					name: lship.shipClass,
					bpLimit: shipBPLimit,           //original size-based cap (immutable)
					bpDedicated: shipBPDedicated,   //original dedicated BP slot count (immutable)
					bpLimitRemaining: shipBPLimit,  //decremented as BPs are assigned
					slots: shipSlots
				};
				shipHangarProfiles.push(shipProfile);

				// Check if ship has converted Hangar Space (adjust ship-specific profile too)
				for (var enh in lship.enhancementOptions) {
					if (lship.enhancementOptions[enh][6]) { // Hangar conversion is an option
						var convNum = lship.enhancementOptions[enh][2];
						if (lship.enhancementOptions[enh][0] === "HANG_F") {
							hangarConversionsF += convNum;
							shipProfile.slots["assault shuttles"] -= convNum;
							shipProfile.slots["heavy"] += convNum;
						}
						if (lship.enhancementOptions[enh][0] === "HANG_AS") {
							hangarConversionsAS += convNum;
							// Deduct from heavy then medium
							var toDeduct = convNum;
							var taken = Math.min(toDeduct, shipProfile.slots["heavy"]);
							shipProfile.slots["heavy"] -= taken;
							toDeduct -= taken;
							if (toDeduct > 0) {
								shipProfile.slots["medium"] -= toDeduct;
							}
							shipProfile.slots["assault shuttles"] += convNum;
						}
						//HANG_BP/HANG_MSW have already been baked into lship.fighters
						//up-front (see _originalFighters snapshot block above), so
						//shipBPDedicated / shipSlots / totalBPDedicated already include
						//the conversion. Nothing further to do here.
					}
				}

				//check for custom hangars
				if (lship.customFighter) {
					for (var h in lship.customFighter) {
						specialHgrName = h;
						specialHgrAmt = lship.customFighter[h];
						specialHangars.push([specialHgrName, specialHgrAmt]);
					}
					//console.table(specialHangars);
				}


				//check hangar space available...
				for (var h in lship.fighters) {
					var amount = lship.fighters[h];
					if (h == "normal" || h == "heavy") {
						totalHangarH += amount;
					} else if (h == "medium") {
						totalHangarM += amount;
					} else if (h == "light") {
						totalHangarL += amount;
					} else if (h == "ultralight") {
						totalHangarXL += amount;
					} else if (h == "assault shuttles") {
						totalHangarAS += amount;
					} else if (h == "Breaching Pods") {
						//Dedicated BP slots are folded into totalBPCapacity above
						//(plus per-ship shipSlots["breaching pods"] for assignment).
						//Don't add them to totalHangarOther / smallCraftUsed — that
						//would re-render them as a separate "Breaching Pods: X (allowed up to Y)"
						//small-craft row alongside the main BP report.
					} else { //something other than fighters
						var found = false;
						for (var nh = 0; nh < totalHangarOther.length; nh++) {
							if (totalHangarOther[nh][0] == h) {//this is small craft type we're looking for!
								found = true;
								totalHangarOther[nh][1] += amount;
							}
						}
						if (found != true) { //such craft wasn't encountered yet
							if(h == "minesweeping shuttles" || h == "cargo shuttles") continue; //These are not bought, don't add to checker.
							totalHangarOther.push(new Array(h, amount));
							smallCraftUsed.push(h);
						}
					}
				}

				//Stage S: integrated fighters (SHAD_FTRL) are BOUGHT as an enhancement,
				//not deployed as separate flights — but per the rules they count toward
				//the ship's fighter maximum. Consume one MEDIUM fighter-slot per bought
				//integrated fighter (ShadowMediumFighterFlight is a medium craft) so a
				//player can't buy 6 integrated fighters AND also deploy 6 separate Shadow
				//fighters. The pools are aggregated in the totalFtrPresent vs
				//totalHangarAvailable check below, so charging them to totalFtrM is exact
				//even though the carrier declares its capacity as 'normal'.
				for (var senh in lship.enhancementOptions) {
					if (lship.enhancementOptions[senh][0] === "SHAD_FTRL") {
						var shadFtrBought = lship.enhancementOptions[senh][2] || 0;
						if (shadFtrBought > 0) totalFtrM += shadFtrBought;
						break;
					}
				}

				//Default shuttle slots auto-populate any leftover hangar capacity
				//(see HangarOps::populateInitialHangarUsage step 3 on the server).
				//Surface them as 'shuttles' capacity so armed-shuttle variants
				//(ArmedFlyer for Minbari, future ArmedShuttleEA, etc.) — which set
				//hangarRequired='shuttles' — can be bought against this pool. We
				//deliberately don't push to smallCraftUsed: the report row only
				//appears when the player actually buys armed shuttles, so empty
				//rows don't clutter ships that just have leftover default shuttles.
				var defaultShuttles = shipManager.systems.getDefaultShuttles(lship);
				if (defaultShuttles.count > 0 && defaultShuttles.key !== "minesweeping shuttles") {
					var defaultKey = defaultShuttles.key;
					var foundDefault = false;
					for (var nh = 0; nh < totalHangarOther.length; nh++) {
						if (totalHangarOther[nh][0] == defaultKey) {
							foundDefault = true;
							totalHangarOther[nh][1] += defaultShuttles.count;
						}
					}
					if (!foundDefault) {
						totalHangarOther.push(new Array(defaultKey, defaultShuttles.count));
					}
					//POOR CREW "cannot purchase Armed Shuttles, or accommodate them in Fleet Checker":
					//this hull contributes NO armed-shuttle berths to the fleet. It still RECEIVES its
					//own (unarmed) default shuttles, which is why only totalShuttleCapacity is skipped
					//and the totalHangarOther entry above is left alone - that entry is reported in the
					//Breaching Pods & Shuttles section, not used as armed-shuttle capacity.
					//Server twins: HangarOps::defaultShuttleLeftoverBoxes and
					//suppressDefaultShuttlesForArmed, which make the same exclusion when apportioning
					//bought armed shuttles across carriers - the two MUST agree or the lobby and the
					//game will disagree about how many default shuttles a carrier ends up holding.
					if (!window.HangarShared.crewBlocksArmedShuttles(lship)) {
						totalShuttleCapacity += defaultShuttles.count;
					}
					if (defaultShuttleKeyList.indexOf(defaultKey) === -1) {
						defaultShuttleKeyList.push(defaultKey);
					}
				}

				//ship may actually require hangar, too! but this must be specified directly
				if (lship.hangarRequired != '') { //classify based on explicit info from craft
					if (lship.hangarRequired == 'Breaching Pods') {
						totalBPUsage += 1 / lship.unitSize;
					} else {
						var found = false;
						for (var nh = 0; nh < totalFtrOther.length; nh++) {
							if (totalFtrOther[nh][0] == lship.hangarRequired) {//this is small craft type we're looking for!
								found = true;
								totalFtrOther[nh][1] += 1 / lship.unitSize; //always 1 craft in this case!
							}
						}
						if (found != true) { //such craft wasn't encountered yet
							totalFtrOther.push(new Array(lship.hangarRequired, 1 / lship.unitSize));
							smallCraftUsed.push(lship.hangarRequired);
						}
					}
				}
			} else {//note presence of fighters
				totalShips++; //well, total units anyway... rules say "one other unit present" and indicate that unit may be a fighter flight as well

				//check for presence of small flights: if for something flight size of 6 is allowed, then anything less counts as small flight
				if ((lship.flightSize < 6) && (lship.maxFlightSize >= 6)) noSmallFlights++;

				var smallCraftSize = '';
				if (lship.hangarRequired != 'fighters') { //classify based on explicit info from craft
					smallCraftSize = lship.hangarRequired;
				} else {//classify depending on jinking limit...
					if (lship.jinkinglimit >= 99) { //ultralight jinking limit is unlimited
						smallCraftSize = 'ultralight';
					} else if (lship.jinkinglimit >= 10) {
						smallCraftSize = 'light';
					} else if (lship.jinkinglimit >= 8) {
						smallCraftSize = 'medium';
					} else if (lship.jinkinglimit >= 6) {
						smallCraftSize = 'heavy';
					} else {
						smallCraftSize = 'NOT RECOGNIZED';
					}
				}
				//Stage S: separate Shadow fighter flights are scenario-only after the
				//integrated-fighter patch and do NOT consume the fleet's fighter
				//allowance (the carrier's integrated fighters already account for the
				//hull's fighter maximum via SHAD_FTRL). Skip the hangar-space tally for
				//them entirely; totalShips++ above still counts them as a unit present.
				var isShadowFighterFlight = (lship.faction == "Shadow Association");

				/* ⭐ WALKERS_OF_SIGMA_PLAN.md §3.12 (Stage 13) - "They do not require hangars at all
				   in Fleet Checker (so can be taken even if the fleet does not have enough hangar
				   space)" (user, original rules text). The craft is still a UNIT PRESENT
				   (totalShips++ ran above) and still counts for points, tiers and every other fleet
				   rule.

				   ⭐⭐ IT LIFTS THE MAXIMUM, NOT THE ACCOUNTING (user ruling, 2026-09-10). The craft
				   is still TALLIED against its own category - a Mapmaker sets $hangarRequired to
				   'Mapmaker Probes', which is the same string the Walker hulls declare their capacity
				   in - so that buying probes is what FILLS a Walker's hangar. What the flag removes
				   is the "allowed up to N" ceiling, so a fleet with no carrier at all still passes.
				   ⚠️ Before this the flag skipped the tally ENTIRELY (which is what the Shadow skip
				   above still does), and the two halves of the rule never met: the Walker hulls
				   declared Mapmaker Probes capacity that nothing could ever fill, so the 50%
				   full-hangar rule was silently unenforceable on every one of them.

				   ⚠️ noHangarRequired IS A FLEET-BUILDING FLAG AND NOTHING ELSE. It is declared on
				   the hull (MapmakerProbes) and rides the static blueprint verbatim, exactly as
				   Stage 11's unTargetable does. It does NOT make the craft hangar-less in play: a
				   Mapmaker still fills boxes the moment a Traveler carries one, which is what
				   Stage 15's docking bay depends on, so HangarOps is deliberately not taught about
				   it. */
				var noHangarRequired = Boolean(lship.noHangarRequired);
				if (noHangarRequired && smallCraftSize != '' && noHangarMaxCraftTypes.indexOf(smallCraftSize) === -1) {
					noHangarMaxCraftTypes.push(smallCraftSize);
				}

				//now translate size into hangar space used...
				if (smallCraftSize != '' && !isShadowFighterFlight) {
					if (lship.customFtrName) {
						specialFtrAmt = lship.flightSize / lship.unitSize;
						specialFtrName = lship.customFtrName;
						specialFighters.push([specialFtrName, specialFtrAmt]);
					}

					if (smallCraftSize == "Breaching Pods") {
						var podsInFlight = lship.flightSize / lship.unitSize;
						totalBPUsage += podsInFlight;
						for (var p = 0; p < podsInFlight; p++) {
							breachingPodsList.push({ id: lship.id });
						}
					} else if (smallCraftSize == "heavy") {
						totalFtrH += lship.flightSize / lship.unitSize;		
					} else if (smallCraftSize == "medium") {
						totalFtrM += lship.flightSize / lship.unitSize;
					} else if (smallCraftSize == "light") {
						totalFtrL += lship.flightSize / lship.unitSize;
					} else if (smallCraftSize == "ultralight") {
						//totalFtrXL += lship.flightSize / lship.unitSize;
						totalFtrXL += lship.flightSize; //Ultralight should show 1 usage in their own row.						
					} else if (smallCraftSize == "assault shuttles") {
						totalFtrAS += lship.flightSize / lship.unitSize;
					} else { //something other than standard fighters
						var found = false;
						for (var nh = 0; nh < totalFtrOther.length; nh++) {
							if (totalFtrOther[nh][0] == smallCraftSize) {//this is small craft type we're looking for!
								found = true;
								totalFtrOther[nh][1] += lship.flightSize / lship.unitSize;
							}
						}
						if (found != true) { //such craft wasn't encountered yet
							totalFtrOther.push(new Array(smallCraftSize, lship.flightSize / lship.unitSize));
							smallCraftUsed.push(smallCraftSize);
						}
					}
				}
			}
			if (jumpDrivePresent == false) { //if already found there's no point
				for (var a in lship.systems) {
					var sSystem = lship.systems[a];
					if (sSystem.name == 'jumpEngine') jumpDrivePresent = true;

					/* ⭐ A FLIGHT'S ENGINES ARE ONE LEVEL DOWN (WALKERS_OF_SIGMA_PLAN.md §3.12,
					   Stage 13). lship.systems on a flight is a list of CRAFT and their systems are
					   inside those, so this scan answered "no jump engine" for a fleet of Mapmakers
					   - which each carry one and whose whole point is that they arrive under their
					   own power. The fleet-wide "at least one is required" rule is the only reader,
					   and it should be told the truth. Free for every other flight in the game:
					   they have no jumpEngine to find. */
					if (sSystem.fighter && sSystem.systems) {
						for (var fs in sSystem.systems) {
							if (sSystem.systems[fs].name == 'jumpEngine') jumpDrivePresent = true;
						}
					}
				}
			}
			if (lship.shipSizeClass >= 3) capitalShips++;
			if (lship.unofficial == true) { //as opposed to eg. 'S'
				customShipPresent = true;
				warningFound = true;
			}
			if ((lship.base == true) || (lship.osat == true && !lship.mine)) staticPresent = true;
			if (lship.isCombatUnit != true) nonCombatPresent = true;
			//check for presence of enhancements
			if (!enhancementPresent) { //if already found - no point in checking
				for (var enhNo in lship.enhancementOptions) if (!lship.enhancementOptions[enhNo][6]) { //only if enhancement isn't really an option
					if (lship.enhancementOptions[enhNo][2] > 0) {
						enhancementPresent = true;
					}
				}
			}

		} //end of loop at ships preparing data

		/* Every bracket and per-hull limit below scales off the fleet's POINT LIMIT, and
		   getMaxPoints is the single place that answers what that limit is. In Fleet
		   Builder that is the figure typed beside the "Unlimited" box once the player has
		   unticked it; only a genuinely unlimited fleet still falls back to measuring
		   itself against what it happens to have spent. */
		var calcPoints = gamedata.getMaxPoints();
		if (calcPoints == -1) { //If unlimited points, assess against points spent so far.
			calcPoints = totalPointsSpent;
		}

		checkResult = "Total fleet limit: " + (calcPoints == -1 ? "Unlimited" : calcPoints) + "<br><br>";

		//check: overall fleet traits
		checkResult += "Jump engine: "; //Jump Engine present?
		if (jumpDrivePresent) {
			checkResult += " <span class='fc-ok'>present</span>";
		} else {
			checkResult += " <span class='fc-bad'>NOT present!</span> (at least one is required)";
			problemFound = true;
		}
		checkResult += "<br>";

		checkResult += "Capital ships: " + capitalShips + ": "; //Capital Ship present?
		//var capsRequired = Math.floor(calcPoints/3000);//1 per 3000, round down; so 1 at 3000, 2 at 6000, 3 at 9000, 10 at 30000
		//let's decrease the requirement at larger battles: 1 per 4000, round up, with first 2499 not counted; so 1 at 2500, 2 at 6500, 3 at 10500, 10 at 42500
		var capsRequired = 0;
		if (!ancientUnitPresent) { //regular limit: one per 5000 points, starting at 3000
			if (calcPoints >= 3000) {
				//capsRequired = Math.ceil((calcPoints-2499)/4000); //previous: one per 4000 points above 2499
				capsRequired = Math.ceil(calcPoints / 5000);
			}
		} else { //Ancient-level limit: one per 15000 points, starting at 5000
			if (calcPoints >= 5000) {
				capsRequired = Math.ceil(calcPoints / 15000);
			}
		}

		checkResult += " (min. " + capsRequired + ")";
		if (capitalShips >= capsRequired) { //tournament rules: at least 1; changed for scalability
			checkResult += R_OK;
		} else {
			checkResult += R_FAILED;
			problemFound = true;
		}
		checkResult += "<br>";

		//Ancient units present?
		if (ancientUnitPresent) {
			warningText += "<br> - Ancient unit(s) present! Seek opponent's permission first. Fleet restrictions adjusted to Ancients.";
			warningFound = true;
		}
		//Custom units present?
		if (customShipPresent) {
			warningText += "<br> - Custom unit(s) present! Seek opponent's permission first.";
			warningFound = true;
		}
		//enhanced units present?
		if (enhancementPresent) {
			warningText += "<br> - Enhancement(s) present! Seek opponent's permission first. Total value: " + totalEnhancementsValue;
			warningFound = true;
		}
		//unique units present?
		if (uniqueShipPresent) {
			warningText += "<br> - Unique unit(s) present! Seek opponent's permission first.";
			warningFound = true;
		}
		//unchecked variant present?
		if (specialVariantPresent) {
			warningText += "<br> - Special deployment unit(s) present! See particular unit description.";
			warningFound = true;
		}

		//Static structures present?
		if (staticPresent) {
			checkResult += "<span class='fc-bad'>Static structures present!</span> They're not allowed in pickup battle.<br>";
			problemFound = true;
		}

		//non-combat units present?
		if (nonCombatPresent) {
			checkResult += "<span class='fc-bad'>Non-Combat units present!</span> They're not allowed in pickup battle.<br>";
			problemFound = true;
		}


		//potentially out-of-Tier elements
		for (var outOfTierIndex = 0; outOfTierIndex < outOfTierArray.length; outOfTierIndex++) {
			var problemName = outOfTierArray[outOfTierIndex];

			var potProblemEntry = outOfTierList[problemName];
			if (potProblemEntry && (potProblemEntry.count > potProblemEntry.limit)) {
				checkResult += potProblemEntry.text + " <span class='fc-bad'>NOT OK!</span>" + "<br>";
				problemFound = true;
			}
		}


		checkResult += "<br>";


		var limit10 = Math.floor(calcPoints * 0.1);
		var limit33 = Math.floor(calcPoints * 0.33);
		var limit50 = Math.floor(calcPoints * 0.5);
		/*if (calcPoints == -1) { //If unlimited points, assess against points spent so far.
			limit10 = totalPointsSpent;
			limit33 = totalPointsSpent;
		}*/

		//Rules note: a single over-limit ship in a bracket is tolerated (the
		//"one single ship is allowed to break limit" exception). The old
		//oneOverAllowed flag that gated this was always false (its only writes
		//are commented out, since Restricted/Limited pools are checked
		//separately), so the units10/units33/units50 == 1 test alone is the live rule.
		checkResult += "<br><u><b>Deployment restrictions:</b></u><br><br>";
		checkResult += " - 10% bracket: " + points10 + "/" + limit10 + ": ";
		if (points10 <= limit10) {
			checkResult += R_OK;
		} else {
			if (units10 == 1) { //only 1 unit - allowed to break limit
				checkResult += "<span class='fc-ok'>OK</span> (one single ship is allowed to break limit)";
			} else {
				checkResult += "<span class='fc-bad'>FAILED!</span> (too many points in this deployment bracket)";
				problemFound = true;
			}
		}
		checkResult += "<br>";
		checkResult += " - 33% bracket: " + points33 + "/" + limit33 + ": ";
		if (points33 <= limit33) {
			checkResult += R_OK;
		} else {
			if (units33 == 1) { //only 1 unit - allowed to break limit
				checkResult += "<span class='fc-ok'>OK</span> (one single ship is allowed to break limit)";
			} else {
				checkResult += "<span class='fc-bad'>FAILED!</span> (too many points in this deployment bracket)";
				problemFound = true;
			}
		}
		checkResult += "<br>";
		checkResult += " - 50% bracket: " + points50 + "/" + limit50 + ": ";
		if (points50 <= limit50) {
			checkResult += R_OK;
		} else {
			if (units50 == 1) { //only 1 unit - allowed to break limit
				checkResult += "<span class='fc-ok'>OK</span> (one single ship is allowed to break limit)";
			} else {
				checkResult += "<span class='fc-bad'>FAILED!</span> (too many points in this deployment bracket)";
				problemFound = true;
			}
		}
		//The escort rule is a rule about RESTRICTED (10%) units only - it must not
		//learn about the 33% or 50% brackets.
		if (points10 > 0 && totalShips < 2) {
			checkResult += "<br><span class='fc-bad'>Restricted (10%) ship present without escort!</span> Such a rare ship needs to be accompanied by at least one other unit, unless it's Dargan or a Minbari ship.";
			problemFound = true;
		}
		checkResult += "<br><br>";

		//variant restrictions
		checkResult += "<br><u><b>Variant restrictions:</b></u><br><br>";
		var limitPerHull = Math.floor(calcPoints / 1100); //turnament rules: 3, but it's for 3500 points
		if (ancientUnitPresent) { //Ancients have way fewer total units...
			limitPerHull = Math.floor(calcPoints / 3000);
		}
		limitPerHull = Math.max(limitPerHull, 2); //always allow at least 2!
		var currRlimit = 0;
		var currUlimit = 0;
		var sumVar = 0;
		for (var j in shipTable) {
			var currHull = shipTable[j];
			checkResult += " <b>" + currHull.name + "</b><br>";
			checkResult += " - Total: " + currHull.Total;
			//if ((!currHull.isFtr) && (!currHull.hangarRequired)){ //fighter total is not limited; also, let's not limit units requiring hangar slots! (this isn't in the rules but I think LCV logic demands it)
			if (!currHull.hangarRequired) { //actually there MAY be hangarless fighters - they should be limited per hull (well, per flight) just like ships!
				checkResult += " (allowed " + limitPerHull + ")";
				if (currHull.Total > limitPerHull) {
					checkResult += R_TOOMANY;
					problemFound = true;
				} else {
					checkResult += R_OK;
				}
			}
			checkResult += "<br>";
			currRlimit = Math.ceil(currHull.Total / 9);
			currUlimit = Math.ceil(currHull.Total / 3);
			sumVar = currHull.R + currHull.Q + currHull.U;
			if (sumVar > 0) {
				checkResult += " - Uncommon/Rare/Unique: " + sumVar + " (allowed " + currUlimit + ")";
				if (sumVar > currUlimit) {
					checkResult += R_TOOMANY;
					problemFound = true;
				} else {
					checkResult += R_OK;
				}
				checkResult += "<br>";
			}
			sumVar = currHull.R + currHull.Q;
			if (sumVar > 0) {
				checkResult += " - Rare/Unique: " + sumVar + " (allowed " + currRlimit + ")";
				if (sumVar > currRlimit) {
					checkResult += R_TOOMANY;
					problemFound = true;
				} else {
					checkResult += R_OK;
				}
				checkResult += "<br>";
			}
			sumVar = currHull.X;
			if (sumVar > 0) {
				checkResult += " - Special: " + sumVar;
				checkResult += " <span class='fc-warn'>CORRECTNESS NOT CHECKED!</span>";
				warningFound = true;
				checkResult += "<br>";
			}
			checkResult += "<br>";
		}
		checkResult += "<br>";

		//total Uncommon/Rare units in fleet
		var limitUTotal = 0;
		var limitRTotal = 0;

		if (ancientUnitPresent) { //Ancients have way fewer total units...
			limitUTotal = Math.floor(calcPoints / 4000);
		} else if ((calcPoints - 1500) > 0) {
			limitUTotal = Math.floor((calcPoints - 1500) / 1000); //limit Uncommon units per fleet; turnament rules: 2, but it's for 3500 points
		}

		limitUTotal = Math.max(limitUTotal, 2); //always allow at least 2!
		limitRTotal = Math.floor(limitUTotal / 2); //limit Rare units per fleet; turnament rules: 1, but it's for 3500 points
		var limitUTotalResult = "<span class='fc-ok'>OK</span>";
		var limitRTotalResult = "<span class='fc-ok'>OK</span>";
		if (totalU > limitUTotal) {
			limitUTotalResult = R_TOOMANY;
			//checkResult += "FAILED: You have " + totalU + " Uncommon units, out of " + limitUTotal + " allowed for fleet.<br><br>" ;
			problemFound = true;
		}
		if (totalR > limitRTotal) {
			limitRTotalResult = R_TOOMANY;
			//checkResult += "FAILED: You have " + totalR + " Rare/Unique units, out of " + limitRTotal + " allowed for fleet.<br><br>" ;
			problemFound = true;
		}
		checkResult += 'Total Uncommon units: ' + totalU + ' (allowed ' + limitUTotal + ') ' + limitUTotalResult + '<br>';
		checkResult += 'Total Rare/Unique units: ' + totalR + ' (allowed ' + limitRTotal + ') ' + limitRTotalResult + '<br><br>';


		//fighters!
		//ultralights count as half a fighter when accounting for hangar space used - IF packed into something other than ultralight hangars...

		// Snapshot fleet-wide hangar totals before the BP assignment loop
		// mutates them — needed below to compute the effective BP cap, which
		// must exclude AS/H/M slots already claimed by non-BP small craft.
		var preBPHangarAS = totalHangarAS;
		var preBPHangarH = totalHangarH;
		var preBPHangarM = totalHangarM;

		// Per-Ship Breaching Pod Assignment and Deduction.
		// Pass 1: fill dedicated "Breaching Pods" hangar slots first — these
		// are guaranteed BP capacity and don't consume the ship's size-based
		// BP cap (e.g. Decurion's 4 side-bay pod racks).
		// Pass 2: overflow into AS/Heavy/Medium slots, capped by the ship's
		// size-based bpLimitRemaining (1/2/4 with x2 for Assault hulls).
		// Count of BPs that had to borrow an AS/Heavy/Medium hangar slot in Pass 2
		// (i.e. didn't land in a dedicated "Breaching Pods" rack). This is the true
		// "hangar slots used by BPs" figure — derived from the actual assignment
		// rather than a fleet-wide totalBPUsage - totalBPDedicated subtraction, which
		// can't tell one ship's dedicated racks apart from another's borrowed slots.
		var bpHangarSlotsUsed = 0;
		var unassignedBPs = 0;
		for (var bpIdx = 0; bpIdx < breachingPodsList.length; bpIdx++) {
			var assigned = false;
			for (var shIdx = 0; shIdx < shipHangarProfiles.length; shIdx++) {
				var ship = shipHangarProfiles[shIdx];
				if (ship.slots["breaching pods"] > 0) {
					ship.slots["breaching pods"]--;
					assigned = true;
					break;
				}
			}
			if (!assigned) {
				for (var shIdx = 0; shIdx < shipHangarProfiles.length; shIdx++) {
					var ship = shipHangarProfiles[shIdx];
					if (ship.bpLimitRemaining > 0) {
						// Check for suitable slot: AS > Heavy > Medium
						if (ship.slots["assault shuttles"] > 0) {
							ship.slots["assault shuttles"]--;
							totalHangarAS--;
							assigned = true;
						} else if (ship.slots["heavy"] > 0) {
							ship.slots["heavy"]--;
							totalHangarH--;
							assigned = true;
						} else if (ship.slots["medium"] > 0) {
							ship.slots["medium"]--;
							totalHangarM--;
							assigned = true;
						}

						if (assigned) {
							ship.bpLimitRemaining--;
							bpHangarSlotsUsed++;
							break;
						}
					}
				}
			}
			if (!assigned) unassignedBPs++;
		}

		var hangarConversionNet = hangarConversionsF - hangarConversionsAS; //Positive is more fighter slots, negative if more AS.
		var totalHangarAvailable = totalHangarH + totalHangarM + totalHangarL + (totalHangarXL / 2) + hangarConversionNet;
		var minFtrRequired = Math.ceil(totalHangarAvailable / 2);
		var totalFtrPresent = totalFtrH + totalFtrM + totalFtrL + (totalFtrXL / 2);
		var totalFtrCurr = 0;
		var totalHangarCurr = 0;

		checkResult += "<br><b><u>Fighters:</u></b><br>";
		checkResult += "<br> Total Hangar Usage: " + totalFtrPresent;
		checkResult += " (select between " + minFtrRequired + " and " + totalHangarAvailable + ")";
		if ((totalFtrXL > 0) || (totalHangarXL > 0)) { //add disclaimer because sums will not add up straight
			checkResult += " [Note - Ultralights only use half a hangar slot]";
		}
		if (totalFtrPresent > totalHangarAvailable || totalFtrPresent < minFtrRequired) { //fighter total is not within limits
			checkResult += R_FAILURE;
			problemFound = true;
		} else {
			checkResult += R_OK;
		}
		checkResult += "<br>";

		// Item 9: the four per-size fighter rows (Ultralight → Light → Medium →
		// Heavy) were four near-identical blocks differing only in label, the
		// hangar-capacity formula, and the Ultralight-only "half a slot" note.
		// Drive them from a table instead. Each row's hangar formula is captured
		// at build time, so the figures — and the order — are identical. The loop
		// leaves totalFtrCurr/totalHangarCurr holding the Heavy (last) row's
		// values, matching the previous fall-through that later code relies on.
		var fighterRows = [
			{ label: "Ultralight Fighters", ftr: totalFtrXL,
			  hangar: (totalHangarH + totalHangarM + totalHangarL + hangarConversionNet) * 2 + totalHangarXL,
			  disclaimer: ((totalFtrXL > 0) || (totalHangarXL > 0)) ? " [Ultralights only require half a normal hangar slot]" : "" },
			{ label: "Light Fighters", ftr: totalFtrL,
			  hangar: totalHangarH + totalHangarM + totalHangarL + hangarConversionNet, disclaimer: "" },
			{ label: "Medium Fighters", ftr: totalFtrM,
			  hangar: totalHangarH + totalHangarM + hangarConversionNet, disclaimer: "" },
			{ label: "Heavy Fighters", ftr: totalFtrH,
			  hangar: totalHangarH + hangarConversionNet, disclaimer: "" }
		];
		for (var fr = 0; fr < fighterRows.length; fr++) {
			totalFtrCurr = fighterRows[fr].ftr;
			totalHangarCurr = fighterRows[fr].hangar;
			if (totalFtrCurr > 0 || totalHangarCurr > 0) { //do not show if there are no fighters/hangars in this segment
				checkResult += " - " + fighterRows[fr].label + ": " + totalFtrCurr;
				checkResult += " (allowed up to " + totalHangarCurr + ")";
				checkResult += fighterRows[fr].disclaimer; //empty for all but Ultralight
				if (totalFtrCurr > totalHangarCurr) { //fighter total is not within limits
					checkResult += R_TOOMANY;
					problemFound = true;
				} else {
					checkResult += R_OK;
				}
				checkResult += "<br>";
			}
		}

		//small flights (do not show if there aren't any!)
		if (noSmallFlights > 0) {
			checkResult += " - Small Flights (< 6 craft): " + noSmallFlights;
			if (noSmallFlights > 1) { //fighter total is not within limits
				checkResult += " <span class='fc-bad'>TOO MANY!</span> (up to 1 allowed)";
				problemFound = true;
			} else {
				checkResult += R_OK;
			}
			checkResult += "<br>";
		}


		if (specialFighters.length > 0) { //do not show if there are no fighters that require special hangars
			/*let's show details even if there are no hangars at all
			if (specialHangars.length == 0){
				checkResult += "No special hangars for special fighters. FAILURE!";
				checkResult += "<br>";
				problemFound = true;
			}else*/{ //calculate total amount and type of special fighters
				// Item 7: sum [name, amount] pairs by name. The originals did this
				// with a sort + shift/pop/push + idx-cursor while-loop; this helper
				// sorts the same way (Array.sort's default string coercion of each
				// [name, amount] pair) and merges adjacent equal names, yielding the
				// identical grouped array in the identical order.
				var sumByName = function (pairs) {
					pairs.sort();
					var out = [];
					for (var p = 0; p < pairs.length; p++) {
						if (out.length > 0 && out[out.length - 1][0] == pairs[p][0]) {
							out[out.length - 1][1] += pairs[p][1];
						} else {
							out.push([pairs[p][0], pairs[p][1]]);
						}
					}
					return out;
				};
				var totalSpecialFighters = sumByName(specialFighters);
				var totalSpecialHangars = sumByName(specialHangars);
				//determine if there is enough special hangars for each type of special fighter
				for (i = 0; i < totalSpecialFighters.length; i++) {
					var match = false;
					for (j = 0; j < totalSpecialHangars.length; j++) {
						if (totalSpecialFighters[i][0] == totalSpecialHangars[j][0]) {
							checkResult += " - " + totalSpecialFighters[i][0] + ": " + totalSpecialFighters[i][1];
							checkResult += " (allowed up to " + totalSpecialHangars[j][1] + ")";
							if (totalSpecialFighters[i][1] > totalSpecialHangars[j][1]) { //fighter total is not within limits
								checkResult += R_FAILURE;
								problemFound = true;
							} else {
								checkResult += R_OK;
							}
							checkResult += "<br>";
							match = true;
						}
					}
					if (match == false) {
						checkResult += " - " + totalSpecialFighters[i][0] + ": " + totalSpecialFighters[i][1];
						checkResult += " (allowed up to 0) <span class='fc-bad'>FAILURE!</span><br>";
						problemFound = true;
					}
				}
			}
		}

		//make list of small craft in fleet contain only unique values...
		var smallCraftUsedUnique = smallCraftUsed.filter(function (item, pos) {
			return smallCraftUsed.indexOf(item) == pos;
		})

		//list each small craft size used separately!
		for (var sc = 0; sc < smallCraftUsedUnique.length; sc++) {
			var scSize = smallCraftUsedUnique[sc];
			//Default shuttle pools ("shuttles", "minbari flyers", etc.) are reported once
			//in the Breaching Pods & Shuttles section below — skip here to avoid duplication.
			if (defaultShuttleKeyList.indexOf(scSize) !== -1) continue;
			totalFtrCurr = 0;
			totalHangarCurr = 0;
			for (var nh = 0; nh < totalFtrOther.length; nh++) {
				if (totalFtrOther[nh][0] == scSize) {//this is small craft type we're looking for!
					totalFtrCurr = totalFtrOther[nh][1];
				}
			}
			for (var nh = 0; nh < totalHangarOther.length; nh++) {
				if (totalHangarOther[nh][0] == scSize) {//this is small craft type we're looking for!
					totalHangarCurr = totalHangarOther[nh][1];
				}
			}
			//Title-case the slot key for display ("shuttles" → "Shuttles", "minesweeping
			//shuttles" → "Minesweeping Shuttles"). Mirrors the pattern used in shipwindow.js.
			var scLabel = scSize.split(' ').map(function (w) { return w.charAt(0).toUpperCase() + w.slice(1); }).join(' ');
			/* Two categories carry a MINIMUM as well as (or instead of) a maximum, and they are not the
			   same rule:
			     - 'Fighter Squadrons' are treated as fighters, so half the capacity must be filled. Kept
			       EXACTLY as it was, unrounded halving included: those capacities are fractional (0.5 on
			       several Star Wars hulls) and rounding them up would move existing verdicts.
			     - a $noHangarRequired category (Mapmaker Probes) has NO maximum at all, and the same 50%
			       minimum the ordinary fighter rule uses - Math.ceil, matching minFtrRequired above.
			       Walker capacities are 6/18/36 so the rounding never bites; it is written this way
			       because it IS the fighter rule, applied to a custom category.
			   Every other custom category is unchanged: no minimum, "allowed up to N" (the Torvalus
			   Stiletto and the rest). WALKERS_OF_SIGMA_PLAN.md 3.13. */
			//Docking Bays (3.14): credit this category with the box cost of every bought ship its
			//bays could take, capped at the boxes those bays actually have.
			if (dockingBayPools[scSize]) {
				var dbp = dockingBayPools[scSize];
				var bayShipBoxTotal = 0;
				fleetShipsForBays.forEach(function (fs) {
					if (dbp.classes[fs.phpclass]) bayShipBoxTotal += window.HangarShared.shipBoxesForUnitSize(fs.unitSize);
				});
				var credited = Math.min(bayShipBoxTotal, dbp.capacity);
				if (credited > 0) {
					totalFtrCurr += credited;
					dockedShipBoxesByCategory[scSize] = credited;
				}
			}
			var scNoMaximum = (noHangarMaxCraftTypes.indexOf(scSize) !== -1);
			var scMinRequired = 0;
			if (scSize == 'Fighter Squadrons') {
				scMinRequired = totalHangarCurr / 2;
			} else if (scNoMaximum) {
				scMinRequired = Math.ceil(totalHangarCurr / 2);
			}

			checkResult += " - " + scLabel + ": " + totalFtrCurr;
			if (dockedShipBoxesByCategory[scSize]) {
				checkResult += " (incl. " + dockedShipBoxesByCategory[scSize] + " Docking Bay boxes of ships)";
			}
			if (scNoMaximum) {
				checkResult += (scMinRequired > 0)
					? " (at least " + scMinRequired + " required, no maximum)"
					: " (no hangar space required)";
			} else if (scSize != 'Fighter Squadrons') { //standard
				checkResult += " (allowed up to " + totalHangarCurr + ")";
			} else { //Fighter Squadrons get treated as fighters - eg. half are required
				checkResult += " (allowed between " + scMinRequired + " and " + totalHangarCurr + ")";
			}
			if (!scNoMaximum && totalFtrCurr > totalHangarCurr) { //small craft total is not within limits
				checkResult += R_TOOMANY;
				problemFound = true;
			} else if (scMinRequired > 0 && totalFtrCurr < scMinRequired) {
				checkResult += R_FAILURE;
				problemFound = true;
			} else {
				checkResult += R_OK;
			}
			checkResult += "<br>";
		}
		checkResult += "<br>";

		//Lets just check Assault shuttle/Breaching Pod capacity separately using their own variables.
		//Reset totalHangarAS to the pre-BP-loop value (then apply hangar conversions). The BP
		//assignment loop decrements totalHangarAS when BPs overflow into AS slots, which would
		//otherwise make the AS report show a spurious failure: e.g. Decurion + 24 AS + 6 BPs
		//would report "Total Assault Shuttles: 24 (allowed up to 22) FAILURE" alongside the
		//real "Total Breaching Pods: 6 (allowed up to 4) FAILURE". The AS hangar capacity
		//for AS units doesn't actually shrink because the player overcommitted BPs — the BP
		//report is the right place to surface that failure.
		totalHangarAS = preBPHangarAS - hangarConversionNet; //Deduct any Hangar conversions here.

		// Effective BP capacity = guaranteed dedicated slots + size-based overflow
		// capped by the physical AS/H/M slots that actually exist to host them.
		//
		// The cap is the GROSS pool of overflow-capable slots, NOT the slots left
		// free after fighters/other small craft are placed. BPs and fighters
		// compete for the same Heavy/Medium slots, but that competition is the
		// Fighters check's job — when BPs borrow H/M slots the assignment loop
		// physically removes them from totalHangarH/M, which is what drops the
		// fighter allowance (e.g. 24 medium → 22 after 2 BPs). Clamping BP
		// capacity by the *remaining* free slots as well would double-penalise the
		// same over-commit: a single fleet would fail BOTH the BP check and the
		// Fighter check for one shortage. Capping by gross slots still catches the
		// genuine impossibility (more BPs than there are AS/H/M slots to host),
		// which the per-ship assignment loop also surfaces via unassignedBPs.
		//
		// AS slots only accept AS units (per hangarAcceptsCategory), so the AS pool
		// is shared by AS units and BP overflow; H/M slots are shared by fighters
		// (incl. Light/Ultralight spillover) and BP overflow.
		var grossASForBP = Math.max(0, preBPHangarAS - hangarConversionNet);
		var grossHMForBP = Math.max(0, preBPHangarH + preBPHangarM + hangarConversionNet);
		var grossOverflowSlots = grossASForBP + grossHMForBP;
		var totalBPCapacity = totalBPDedicated + Math.min(totalBPSizeCap, grossOverflowSlots);

		// Free (post-fighter) overflow slots — used only by the shuttle-overflow
		// maths below to work out how many spare fighter slots armed shuttles can
		// still borrow after fighters and BP overflow have taken theirs. Distinct
		// from the gross figure above: shuttles get whatever is genuinely left
		// over, whereas BP *capacity* is judged against the gross slot pool.
		var freeASForBP = Math.max(0, preBPHangarAS - hangarConversionNet - totalFtrAS);
		var hmPoolCapacity = preBPHangarH + preBPHangarM + hangarConversionNet;
		var lightOverflow = Math.max(0, totalFtrL - totalHangarL);
		var xlOverflow = Math.max(0, totalFtrXL - totalHangarXL) / 2;
		var hmPoolDemand = totalFtrH + totalFtrM + lightOverflow + xlOverflow;
		var freeHMForBP = Math.max(0, hmPoolCapacity - hmPoolDemand);

		checkResult += "<br><b><u>Breaching Pods & Shuttles:</u></b><br><br>";
		checkResult += " Total Breaching Pods: " + totalBPUsage;
		checkResult += " (allowed up to " + totalBPCapacity + ")";
		if (totalBPUsage > totalBPCapacity || unassignedBPs > 0) {
			checkResult += R_FAILURE;
			if (unassignedBPs > 0) {
				if (totalBPUsage > totalBPCapacity) {
					checkResult += " (Not enough Breaching Pod Capacity)";
				} else {
					checkResult += " (Not enough hangar slots on ships with Breaching Pod capacity)";
				}
			}
			problemFound = true;
		} else {
			if (bpHangarSlotsUsed > 0) {
				checkResult += " (" + bpHangarSlotsUsed + " fighters slot" + (bpHangarSlotsUsed === 1 ? "" : "(s)") + " used)";
			}
			checkResult += R_OK;
		}
		checkResult += "<br>";

		checkResult += " Total Assault Shuttles: " + totalFtrAS;
		checkResult += " (allowed up to " + totalHangarAS + ")";
		if (totalFtrAS > totalHangarAS) { //Asssault Shuttle total is not within limits
			checkResult += R_FAILURE;
			problemFound = true;
		} else {
			checkResult += R_OK;
		}
		checkResult += "<br>";

		//Default shuttle pool — leftover hangar capacity that auto-fills with shuttles/flyers.
		//Always displayed (even when no armed shuttle variants are bought) so the player can
		//see the pool that armed-shuttle units (ArmedFlyer, future ArmedShuttleEA, etc.) draw from.
		//Rules clarification: armed-shuttle variants (hangarRequired='shuttles') may also use
		//any spare *fighter* slot (H/M/L/XL) — but NOT Assault Shuttle or Breaching Pod slots.
		//So shuttle overflow past the default pool spills into unused fighter capacity.
		var totalShuttleUsage = 0;
		for (var nh = 0; nh < totalFtrOther.length; nh++) {
			if (defaultShuttleKeyList.indexOf(totalFtrOther[nh][0]) !== -1) {
				totalShuttleUsage += totalFtrOther[nh][1];
			}
		}
		// Spare fighter slots available for shuttle overflow. Mirrors the BP free-pool maths:
		//  - HM pool: subtract any BP overflow that already consumed HM slots (BPs prefer AS,
		//    then HM, per the BP capacity calc above).
		//  - L / XL pools: simple capacity − usage; smaller-fighter spillover already accounted
		//    for in hmPoolDemand so leftover L/XL slots really are free.
		var bpOverflowDemand = Math.max(0, totalBPUsage - totalBPDedicated);
		var bpHMUsed = Math.min(Math.max(0, bpOverflowDemand - freeASForBP), freeHMForBP);
		var spareHMForShuttle = Math.max(0, freeHMForBP - bpHMUsed);
		var spareLForShuttle = Math.max(0, totalHangarL - totalFtrL);
		var spareXLForShuttle = Math.max(0, totalHangarXL - totalFtrXL);
		var spareFighterSlotsForShuttle = spareHMForShuttle + spareLForShuttle + spareXLForShuttle;
		var shuttleOverflow = Math.max(0, totalShuttleUsage - totalShuttleCapacity);

		checkResult += " Shuttles: " + totalShuttleUsage;
		checkResult += " (allowed up to " + totalShuttleCapacity + ")";
		if (shuttleOverflow === 0) {
			checkResult += R_OK;
		} else if (shuttleOverflow <= spareFighterSlotsForShuttle) {
			checkResult += " (+" + shuttleOverflow + " fighter slot" + (shuttleOverflow === 1 ? "" : "s") + " used)";
			checkResult += R_OK;
		} else {
			checkResult += " (needs " + shuttleOverflow + " fighter slot" + (shuttleOverflow === 1 ? "" : "s") + ", " + spareFighterSlotsForShuttle + " spare)";
			checkResult += R_FAILURE;
			problemFound = true;
		}
		checkResult += "<br>";

		//The report opens in its own window (#fleetcheck, gamelobby.php), whose head carries the
		//"Fleet Correctness Report" title and the tournament-rules line this used to start with:
		//the verdict, then any caution, then the checks, laid out by fleetCheckRowsHtml.
		var report = problemFound
			? '<div class="fc-overall fc-overall--bad"><span class="fc-overall-label">Overall</span><span class="fc-overall-value">Failed</span></div>'
			: '<div class="fc-overall fc-overall--ok"><span class="fc-overall-label">Overall</span><span class="fc-overall-value">OK</span></div>';

		if (warningFound) {
			report += '<div class="fc-caution"><div class="fc-caution-head">Caution: unchecked or non-canon elements found - check the details below</div>'
				+ gamedata.fleetCheckRowsHtml(warningText) + '</div>';
		}

		//alert(checkResult); //alert will be truncated by browser
		document.getElementById("fleetchecktxt").innerHTML = report + gamedata.fleetCheckRowsHtml(checkResult);
		gamedata.openLobbyModal("fleetcheck");
	}, //endof function checkChoices

	/* The Fleet Correctness Report's lines as rows. checkChoices writes them as it always has,
	   with <br> between them; this only lays them out: a <u>/<b> heading becomes a section title,
	   an <i> hull name a sub-title, a " - " line an indented item, a line carrying a verdict takes
	   that verdict's rail, and the blank lines that used to space the report are dropped (the CSS
	   spaces it now). */
	fleetCheckRowsHtml: function fleetCheckRowsHtml(text) {
		return String(text).split(/<br\s*\/?>/i).map(function (line) {
			line = line.trim();
			if (line === "") return "";
			if (/^<(u|b)>\s*<(u|b)>/i.test(line)) {
				return '<h3 class="fc-section">' + line.replace(/<\/?(u|b)>/gi, "").replace(/:\s*$/, "") + '</h3>';
			}

			var cls = "fc-line";
			if (/^-\s/.test(line)) {
				cls += " fc-line--item";
				line = line.replace(/^-\s*/, "");
			} else if (/^<i>[^<]*<\/i>$/i.test(line)) {
				cls += " fc-line--hull";
			}
			if (line.indexOf("fc-bad") !== -1) cls += " fc-line--bad";
			else if (line.indexOf("fc-warn") !== -1) cls += " fc-line--warn";
			else if (line.indexOf("fc-ok") !== -1) cls += " fc-line--ok";
			return '<div class="' + cls + '">' + line + '</div>';
		}).join("");
	},



	constructFleetList: function constructFleetList() {
		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);

		$(".ship.bought").remove();
		for (var i in gamedata.ships) {
			// Reset ship ids to avoid ending up with elements with the same id

			//Unique temp ids assigned when purchaseed now - DK 30.3.31
			//		gamedata.ships[i].id = Date.now() + Math.random().toString(36).substr(2, 5);	

			var ship = gamedata.ships[i];
			if (ship.slot != slotid) continue;
			//ONE builder for both fleet-list row paths - see gamedata.enhancementListHtml.
			//This rebuild is exactly where a row-only addition gets lost.
			var enhancementHtml = gamedata.enhancementListHtml(ship);
			var displayType = ship.shipClass;
			var display = gamedata.rowDisplay(ship);

			//Pre-battle damage: re-derived from the ship, not carried in the old markup -
			//this rebuild is exactly where the previous ' (damaged)' suffix was lost.
			var damageBadge = gamedata.damagedShipBadge(ship);

			//Re-derived from the ship, exactly like the badge above - this rebuild is where a
			//row-only addition gets lost (REINFORCEMENTS_PLAN.md §4 Stage 1).
			var reinforcementClass = gamedata.isReinforcementRow(ship) ? ' reinforcement' : '';

			var h = $('<div class="ship bought' + reinforcementClass + ' slotid_' + ship.slot + ' shipid_' + ship.id + '" data-shipindex="' + ship.id + '">' +
				damageBadge +
				'<span class="shipname name">' + display.name + '</span>' +
				//Class and cost in one box, so a narrow fleet column wraps them onto a line together.
				'<span class="boughtClassCost"><span class="boughtShiptype">' + displayType + '</span>' +
				'<span class="boughtPointCost">' + display.cost + 'p</span></span>' +
				enhancementHtml +
				gamedata.rowActionsHtml(ship) +
				'</div>');
			h.appendTo("#fleet");
		}

		$(".ship.bought .remove").bind("click", function (e) {
			var id = $(this).closest(".ship").data('shipindex');

			for (var i in gamedata.ships) {
				if (gamedata.ships[i].id == id) {
					gamedata.ships.splice(i, 1);
					break;
				}
			}
			$('.ship.bought.shipid_' + id).remove();
			gamedata.calculateFleet();
			// This is done to update it immediately and more importantly,
			// to assign new id's to all fleet entries
			gamedata.constructFleetList();
			gamedata.populateFleetDropdown();
		});

		$("#fleet").off("click", ".showship").on("click", ".showship", function (e) {
			var id = $(this).closest(".ship").data("shipindex");
			for (var i in gamedata.ships) {
				if (gamedata.ships[i].id == id) {
					gamedata.onShipContextMenu(gamedata.ships[i].phpclass, gamedata.ships[i].faction, gamedata.ships[i].id, true);
					break;
				}
			}
		});

		//if (ship.mine) {
		$("#fleet").off("click", ".editship").on("click", ".editship", function (e) {
			var id = $(this).closest(".ship").data("shipindex");
			for (var i in gamedata.ships) {
				if (gamedata.ships[i].id == id) {
					gamedata.editShip(gamedata.ships[i]);
					break;
				}
			}
		});

		$("#fleet").off("click", ".copyship").on("click", ".copyship", function (e) {
			var id = $(this).closest(".ship").data("shipindex");
			for (var i in gamedata.ships) {
				if (gamedata.ships[i].id == id) {
					gamedata.copyShip(gamedata.ships[i]);
					break;
				}
			}
		});

		/* REINFORCEMENTS_PLAN.md §4 Stage 1 - the re-flag link. DELEGATED ONLY, unlike the four
		   closure bindings updateFleet also makes: this method runs from the inline
		   parseServerData at page load, long before anything can be bought, so the handler is
		   always in place by the time a row exists - and binding it only here means a row added
		   by updateFleet cannot end up with two handlers and toggle twice. */
		$("#fleet").off("click", ".reinforcetoggle").on("click", ".reinforcetoggle", function (e) {
			gamedata.toggleReinforcement($(this).closest(".ship").data("shipindex"));
		});

		/* The two group headers AS the buy-target selector (user request 2026-08-28). Delegated for
		   the same reason the re-flag link above is, only more so: applyFleetGrouping destroys and
		   rewrites both headers on every poll, after every purchase and on every re-flag, so a
		   closure binding would be thrown away and re-made constantly.
		   The `change` binding is what keeps the highlight honest when the player uses the OTHER
		   control - the "Buy as Reinforcement" tick in the store's filter strip - because that
		   writes the same checkbox without going through setBuyTarget. Bound on document rather
		   than on #fleet: the checkbox lives in the store panel, nowhere near the fleet list. */
		$("#fleet").off("click", ".fleet-group-header").on("click", ".fleet-group-header", function (e) {
			gamedata.setBuyTarget($(this).data("buytarget"));
		});

		$(document).off("change.buytarget", "#reinforcementModeToggle")
			.on("change.buytarget", "#reinforcementModeToggle", function (e) {
				gamedata.applyFleetGrouping();
			});
		//}

		//After every row is written, never before: it sorts the rows it finds in the DOM.
		gamedata.applyFleetGrouping();

		gamedata.calculateFleet();
	},

	calculateFleet: function calculateFleet() {
		var slotid = gamedata.selectedSlot;
		if (!slotid) return;

		var points = gamedata.fleetCost();
		var maxPoints = gamedata.getMaxPoints();

		/* Fleet Builder with "Unlimited" unticked: the cap is a live input the player types
		   into, so it takes the place of the .max readout rather than being written into it
		   - rewriting .max here on every recalculation would destroy the field (and their
		   caret) mid-keystroke. */
		var capIsEditable = maxPoints != -1 && gamedata.builderMaxPoints !== null;
		$('.max-points-input').toggle(capIsEditable);
		$('.max').toggle(!capIsEditable);

		//Your Fleet shows ONE figure: points left, or with no limit points spent (.lb-buy-spent).
		//.max exists only in Fleet Builder, beside its cap controls.
		$('.lb-buy-spent').toggle(maxPoints == -1);

		if (maxPoints == -1) {
			$('.max').html('<span class="unlimited-points-text2">Unlimited</span>');
			$('.max-points-units').hide();
			$('.remaining-points-container').hide();
		} else {
			var remainingPoints = maxPoints - points;
			if (!capIsEditable) $('.max').html(maxPoints);
			$('.remaining').html(remainingPoints);
			$('.max-points-units').show();
			// Ensure container is shown, and units are visible inside it
			$('.remaining-points-container').show();
			$('.remaining-points-units').show();
		}

		$('.current').html(points);
		return points;
	},


	isMyShip: function isMyShip(ship) {
		return ship.userid == gamedata.thisplayer;
	},

	orderShipListOnName: function orderShipListOnName(shipList) {
		var swapped = true;

		for (var x = 1; x < shipList.length && swapped; x++) {
			swapped = false;

			for (var y = 0; y < shipList.length - x; y++) {
				if (shipList[y + 1].shipClass < shipList[y].shipClass) {
					var temp = shipList[y];
					shipList[y] = shipList[y + 1];
					shipList[y + 1] = temp;
					swapped = true;
				}
			}
		}
	},

	/*alternate sorting method - by point value*/
	orderShipListOnPV: function orderShipListOnPV(shipList) {
		var swapped = true;

		for (var x = 1; x < shipList.length && swapped; x++) {
			swapped = false;

			for (var y = 0; y < shipList.length - x; y++) {
				if (shipList[y + 1].pointCost > shipList[y].pointCost) {
					//top-down
					var temp = shipList[y];
					shipList[y] = shipList[y + 1];
					shipList[y + 1] = temp;
					swapped = true;
				}
			}
		}
	},

	orderStringList: function orderStringList(stringList) {
		var swapped = true;

		for (var x = 1; x < stringList.length && swapped; x++) {
			swapped = false;

			for (var y = 0; y < stringList.length - x; y++) {
				if (stringList[y + 1] < stringList[y]) {
					var temp = stringList[y];
					stringList[y] = stringList[y + 1];
					stringList[y + 1] = temp;
					swapped = true;
				}
			}
		}
	},


	/* ── The Faction Picker and the Store (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §4.3, Stage 5) ──
	   Picking a faction and browsing its ships are two steps. The picker (#lbFactionPicker, a window
	   on a desktop and a full-screen sheet on a phone) holds the six groups and stops at FACTION
	   level; choosing a row closes it and scopes the Store column to that one faction
	   (selectStoreFaction). The tier / Custom chips live in the picker because they decide which
	   factions can be picked, and filterFactionList applies them together with its search box. Its
	   footer is the faction randomiser (rollFaction, Stage 7). The groups run down two or three
	   columns on a desktop, each group whole (final refinements, plan §12.12). */

	//Custom Factions only: the sub-group a custom faction's name puts it in (plan §4.3); the picker
	//lists them alphabetically. Babylon 5 Wars has no test - it takes every custom faction the others do not
	//(user, Stage 5: What If, Great Crusade Orieni, House Valheru, Custom Ships, Drakh, Thirdspace,
	//Barada Imperium, Ch'Lonas Cooperative).
	customSubgroups: [
		{ name: "Babylon 5 Wars", test: null },
		{ name: "Nexus", test: /^Nexus\b/ },
		{ name: "Escalation Wars", test: /^Escalation Wars\b/ },
		{ name: "Other Universe", test: /^(BSG|12 Colonies of Kobol|Star Trek|Star Wars|StarTrek|StarWars|Trek)\b|^The System$/ }
	],

	//The short tier tag on a picker row.
	factionTierTag: function factionTierTag(tier) {
		var match = /^Tier ([123])$/.exec(tier);
		if (match) return "T" + match[1];
		if (tier === "Tier Ancients") return "Ancient";
		if (tier === "Tier Other") return "Other";
		return "";
	},

	parseFactions: function parseFactions(jsonFactions) {
		var list = $("#factionList");
		list.empty();
		//The groups are built here, then dealt into fixed columns by layoutPickerColumns.
		var columns = $('<div class="lb-picker-cols"></div>').appendTo(list);
		let factionList = [];

		const groups = {
			"Major Factions": [],
			"League of Non-Aligned Worlds": [],
			"Minor Factions": [],
			"Ancients": [],
			"Other Factions": [],
			"Custom Factions": []
		};

		for (let faction of jsonFactions) {
			const powerRating = gamedata.getPowerRating(faction);
			const lowerPower = powerRating.toLowerCase();
			const isCustom = lowerPower.includes("custom");

			// Every CUSTOM faction is a Custom Faction (user, Stage 5) - Drakh, Thirdspace, Barada,
			// Ch'Lonas and Custom Ships too, whatever group their power rating also names. Their
			// tier still comes from the rating, for the tier filter. The rest: Minor > Major >
			// League > Ancients > Other.
			let groupName = "Other Factions";
			if (isCustom) groupName = "Custom Factions";
			else if (lowerPower.includes("minor")) groupName = "Minor Factions";
			else if (lowerPower.includes("major")) groupName = "Major Factions";
			else if (lowerPower.includes("league")) groupName = "League of Non-Aligned Worlds";
			else if (lowerPower.includes("ancients")) groupName = "Ancients";
			else if (lowerPower.includes("other")) groupName = "Other Factions";

			const tierMatch = powerRating.match(/Tier\s*([123]|Ancients|Other)/i);
			const tier = tierMatch ? "Tier " + tierMatch[1] : "Unknown";

			groups[groupName].push({ faction, powerRating, isCustom, tier });
		}

		//A faction row: a button, so it is reachable and pickable from the keyboard.
		const factionRow = function (entry) {
			factionList.push(entry.faction);
			const row = $('<button type="button" class="lb-faction"></button>').attr({
				"data-faction": entry.faction,
				"data-custom": entry.isCustom ? "true" : "false",
				"data-tier": entry.tier,
				"title": entry.powerRating
			}).toggleClass("lb-faction--custom", entry.isCustom);
			$('<span class="lb-faction-name"></span>').text(entry.faction).appendTo(row);
			$('<span class="lb-faction-tag"></span>').text(gamedata.factionTierTag(entry.tier)).appendTo(row);
			return row;
		};

		//A group: a disclosure header (its count is filterFactionList's) over a body of rows. Every
		//group starts open, as the old list's did; a Custom sub-group starts closed.
		const factionGroup = function (name, isSub) {
			const group = $('<div class="lb-fgroup"></div>').toggleClass("lb-fgroup--sub", !!isSub)
				.toggleClass("is-collapsed", !!isSub);
			const head = $('<button type="button" class="lb-fgroup-head"><span class="lb-disclosure" aria-hidden="true"></span>'
				+ '<span class="lb-fgroup-name"></span><span class="lb-fgroup-count"></span></button>')
				.attr("aria-expanded", isSub ? "false" : "true");
			head.find(".lb-fgroup-name").text(name);
			group.append(head, '<div class="lb-fgroup-body"></div>');
			return group;
		};

		// ✅ Fixed order of groups
		const groupOrder = ["Major Factions", "League of Non-Aligned Worlds", "Minor Factions", "Ancients", "Other Factions", "Custom Factions"];

		for (let groupName of groupOrder) {
			const entries = groups[groupName];
			if (entries.length === 0) continue;
			entries.sort((a, b) => a.faction.localeCompare(b.faction));

			const group = factionGroup(groupName, false).toggleClass("lb-fgroup--custom", groupName === "Custom Factions")
				.toggleClass("lb-fgroup--keep", gamedata.pickerKeepWithPrevious.indexOf(groupName) !== -1);
			const body = group.children(".lb-fgroup-body");

			if (groupName !== "Custom Factions") {
				entries.forEach(entry => body.append(factionRow(entry)));
			} else {
				const subBodies = gamedata.customSubgroups.map(function (sub) {
					return factionGroup(sub.name, true);
				});
				const catchAll = gamedata.customSubgroups.findIndex(sub => !sub.test);
				entries.forEach(function (entry) {
					let index = gamedata.customSubgroups.findIndex(sub => sub.test && sub.test.test(entry.faction));
					if (index === -1) index = catchAll;
					subBodies[index].children(".lb-fgroup-body").append(factionRow(entry));
				});
				//Listed alphabetically (user, Stage 5), whatever order the table above is in.
				subBodies.slice().sort(function (a, b) {
					return a.find(".lb-fgroup-name").text().localeCompare(b.find(".lb-fgroup-name").text());
				}).forEach(function (sub) {
					if (sub.find(".lb-faction").length) body.append(sub);
				});
			}

			columns.append(group);
		}

		gamedata.allShips = factionList;

		gamedata.pickerColumnCount = 0; //a new list: deal it out afresh
		gamedata.layoutPickerColumns();
		gamedata.filterFactionList();
		gamedata.markStoreFaction();
	},

	/* The picker's groups run down two or three columns on a desktop - and STAY in the column they
	   were dealt into (user: they must not jump about as groups open or the filters thin them out,
	   which a CSS multi-column box does as it rebalances). So the columns are dealt once, in list
	   order and none split, from each group's FULL size - every faction row, Custom's sub-groups
	   counted closed, as it opens - which no filter, search or disclosure changes. Only the window's
	   width does (1 column on a phone, 2, 3): it is dealt again when that count changes. */
	pickerColumnCount: 0,

	//Groups that never start a column: each is dealt together with the group listed above it (user,
	//2026-10-04: "Ancient sits underneath Minor Factions in the second column").
	pickerKeepWithPrevious: ["Ancients"],

	pickerColumnsWanted: function pickerColumnsWanted() {
		if (!window.matchMedia) return 1;
		if (window.matchMedia("(min-width: 860px)").matches) return 3;
		if (window.matchMedia("(min-width: 560px)").matches) return 2;
		return 1;
	},

	layoutPickerColumns: function layoutPickerColumns() {
		var holder = $("#factionList > .lb-picker-cols");
		if (!holder.length) return;
		var count = gamedata.pickerColumnsWanted();
		if (count === gamedata.pickerColumnCount) return;
		gamedata.pickerColumnCount = count;

		//In list order, whichever column each sits in now - as UNITS: a .lb-fgroup--keep group joins the
		//unit of the group above it (pickerKeepWithPrevious), so no split can put a column top between them.
		var units = [];
		holder.find(".lb-fgroup").filter(function () { return !$(this).hasClass("lb-fgroup--sub"); }).each(function () {
			if ($(this).hasClass("lb-fgroup--keep") && units.length) units[units.length - 1].push(this);
			else units.push([this]);
		});
		//A group's height in rows: its header (with the gap above it) and its rows as it opens.
		var weights = units.map(function (unit) {
			return unit.reduce(function (height, group) {
				var subs = $(group).find(".lb-fgroup--sub").length;
				return height + 1.5 + (subs ? subs * 1.1 : $(group).find(".lb-faction").length);
			}, 0);
		});

		//The split into `count` runs, in order, whose tallest run is shortest - and of those, the most
		//even (least sum of squares). Six groups and three columns at most, so every pair of cut
		//points is simply tried.
		var sum = function (from, to) { var s = 0; for (var i = from; i < to; i++) s += weights[i]; return s; };
		var n = units.length, best = null;
		var consider = function (ends) {
			var from = 0, tallest = 0, squares = 0;
			ends.forEach(function (end) { var h = sum(from, end); tallest = Math.max(tallest, h); squares += h * h; from = end; });
			if (!best || tallest < best.tallest || (tallest === best.tallest && squares < best.squares)) {
				best = { ends: ends, tallest: tallest, squares: squares };
			}
		};
		if (count === 1 || n <= 1) {
			consider([n]);
		} else if (count === 2) {
			for (var a = 1; a < n; a++) consider([a, n]);
		} else {
			for (var i = 1; i < n; i++) {
				for (var j = i; j < n; j++) consider([i, j, n]);
			}
		}
		best = best.ends;

		var start = 0;
		var made = best.map(function (end) {
			var column = $('<div class="lb-picker-col"></div>').append([].concat.apply([], units.slice(start, end)));
			start = end;
			return column;
		});
		holder.empty().append(made).css("--picker-cols", String(best.length));
	},

	/* The picker's filters: a row shows when its tier is ticked, it passes Show Custom (and its
	   Show Only Customs mode), and its name contains the search text. A group shows while any row in
	   it does, and its count is how many. While a search is typed every group is shown open
	   (.is-searching), so a match is never hidden inside a closed one. */
	filterFactionList: function filterFactionList() {
		var list = $("#factionList");
		if (!list.length) return;

		var tiers = $(".tier-filter:checked").map(function () { return $(this).attr("data-tier"); }).get();
		//All / None read pressed while every tier chip - or none - is on.
		$("#lbTierAll").attr("aria-pressed", tiers.length === $(".tier-filter").length ? "true" : "false");
		$("#lbTierNone").attr("aria-pressed", tiers.length === 0 ? "true" : "false");
		var showCustom = $("#toggleCustom").is(":checked");
		var onlyCustom = showCustom && $("#customSelect").val() === "showOnlyCustom";
		var search = String($("#factionSearch").val() || "").trim().toLowerCase();

		list.find(".lb-faction").each(function () {
			var isCustom = this.getAttribute("data-custom") === "true";
			var visible = tiers.indexOf(this.getAttribute("data-tier")) !== -1
				&& (showCustom ? (!onlyCustom || isCustom) : !isCustom)
				&& (search === "" || this.getAttribute("data-faction").toLowerCase().indexOf(search) !== -1);
			this.hidden = !visible;
		});

		list.find(".lb-fgroup").each(function () {
			var shown = $(this).find(".lb-faction").filter(function () { return !this.hidden; }).length;
			this.hidden = shown === 0;
			$(this).children(".lb-fgroup-head").find(".lb-fgroup-count").text(shown);
		});

		list.toggleClass("is-searching", search !== "");
		var anyShown = list.find(".lb-faction").filter(function () { return !this.hidden; }).length > 0;
		$("#factionListEmpty").prop("hidden", anyShown);

		//The randomiser rolls from these rows: nothing to roll with none, and a roll the filters now
		//hide is forgotten.
		$("#lbRollFaction").prop("disabled", !anyShown);
		var rolled = gamedata.rolledFactionRow();
		if (gamedata.rolledFaction !== null && (!rolled || rolled.hidden)) gamedata.showRolledFaction(null);
	},

	//The picker's All / None chips: every tier chip on, or every one off. Custom is left as it is - it
	//is a different question (which customs may be picked, with its own mode and warning).
	setAllTierFilters: function setAllTierFilters(on) {
		$(".tier-filter").prop("checked", !!on);
		gamedata.filterFactionList();
	},

	/* The randomiser (plan §4.5, Stage 7), in place of the three off-site Wheel of Names links. It
	   rolls one of the rows the list shows right now - whatever the tier boxes, Show Custom and the
	   search leave (a row in a closed group counts; it is only folded away) - so it can only suggest a
	   faction the player could pick by hand. It points the faction out rather than picking it: the
	   row is marked, its group opened and scrolled to, and the footer names it beside Choose, so
	   rolling again costs nothing (picking would close the window and fetch the faction's ships). */
	rolledFaction: null,

	rolledFactionRow: function rolledFactionRow() {
		if (gamedata.rolledFaction === null) return null;
		return $("#factionList .lb-faction").filter(function () {
			return this.getAttribute("data-faction") === gamedata.rolledFaction;
		})[0] || null;
	},

	rollFaction: function rollFaction() {
		var rows = $("#factionList .lb-faction").filter(function () { return !this.hidden; });
		if (!rows.length) return;
		gamedata.showRolledFaction(rows[Math.floor(Math.random() * rows.length)]);
	},

	//Mark a rolled row and name it in the footer; null forgets the roll.
	showRolledFaction: function showRolledFaction(row) {
		$("#factionList .lb-faction.is-rolled").removeClass("is-rolled");
		gamedata.rolledFaction = row ? row.getAttribute("data-faction") : null;

		var text = $("#lbRollResult");
		$("#lbRollChoose").prop("hidden", !row);
		if (!row) {
			text.text("Rolls one of the factions listed above.");
			return;
		}

		var tag = gamedata.factionTierTag(row.getAttribute("data-tier"));
		text.empty().append("Rolled ",
			$('<b class="lb-roll-name"></b>').text(gamedata.rolledFaction)
				.toggleClass("lb-roll-name--custom", row.getAttribute("data-custom") === "true"),
			tag ? $('<span class="lb-faction-tag"></span>').text(tag) : null);

		//Its closed group opened, and the row centred in the list. The list is scrolled directly:
		//scrollIntoView could also move the page under the window.
		$(row).parents(".lb-fgroup.is-collapsed").removeClass("is-collapsed")
			.children(".lb-fgroup-head").attr("aria-expanded", "true");
		var list = document.getElementById("factionList");
		list.scrollTop += row.getBoundingClientRect().top - list.getBoundingClientRect().top
			- (list.clientHeight - row.offsetHeight) / 2;

		//Re-added after a reflow, so its flash plays again when the same faction comes up twice running.
		void row.offsetWidth;
		$(row).addClass("is-rolled");
	},

	//The picker row of the Store's faction reads "current".
	markStoreFaction: function markStoreFaction() {
		$("#factionList .lb-faction").each(function () {
			var current = this.getAttribute("data-faction") === gamedata.storeFaction;
			$(this).toggleClass("is-current", current).attr("aria-current", current ? "true" : null);
			var tag = gamedata.factionTierTag(this.getAttribute("data-tier"));
			$(this).find(".lb-faction-tag").text(current ? (tag ? tag + " · current" : "current") : tag);
		});
	},

	openFactionPicker: function openFactionPicker() {
		//The search box takes focus where a keyboard is at hand; on a touch screen that would throw
		//the on-screen keyboard over the list, so the window itself takes it instead.
		var fine = window.matchMedia && window.matchMedia("(pointer: fine)").matches;
		gamedata.openLobbyModal("lbFactionPicker", fine ? "#factionSearch" : null);

		//The Store's faction in view - its closed Custom sub-group opened if need be.
		var current = $("#factionList .lb-faction.is-current");
		if (!current.length || current[0].hidden) return;
		current.parents(".lb-fgroup.is-collapsed").removeClass("is-collapsed")
			.children(".lb-fgroup-head").attr("aria-expanded", "true");
		current[0].scrollIntoView({ block: "nearest" });
	},

	//The Store's faction: null until one is picked.
	storeFaction: null,

	//The Store's container for one faction's ships, made on first use unless noCreate.
	storeFactionNode: function storeFactionNode(faction, noCreate) {
		var store = document.getElementById("store");
		if (!store) return null;
		for (var i = 0; i < store.children.length; i++) {
			if (store.children[i].getAttribute("data-faction") === faction) return store.children[i];
		}
		if (noCreate) return null;

		var node = document.createElement("div");
		node.className = "lb-store-faction";
		node.setAttribute("data-faction", faction);
		store.appendChild(node);
		return node;
	},

	/* Scope the Store to one faction. Its ships are fetched the first time it is picked
	   (ajaxInterface.getShipsForFaction, one faction at a time as before) and kept, so switching
	   back to it is instant. A failed or unanswered fetch is retried by picking the faction again. */
	selectStoreFaction: function selectStoreFaction(faction) {
		if (!faction) return;
		gamedata.storeFaction = faction;

		var node = gamedata.storeFactionNode(faction);
		$("#store").children(".lb-store-faction").each(function () {
			this.hidden = this !== node;
		});

		if (node.getAttribute("data-state") !== "loaded") {
			node.setAttribute("data-state", "loading");
			$(node).html('<p class="lb-store-note">Loading ships…</p>');

			window.ajaxInterface.getShipsForFaction(faction, function (factionShips) {
				$(node).empty();
				gamedata.parseShips(factionShips);
				node.setAttribute("data-state", "loaded");
				gamedata.applyCustomShipFilter();
				gamedata.applyStoreCategory();
				gamedata.updateStoreBar();
			}, function () {
				node.setAttribute("data-state", "failed");
				$(node).html('<p class="lb-store-note">Could not load this faction\'s ships. Choose it again to retry.</p>');
				gamedata.applyStoreCategory();
			});
		}

		gamedata.markStoreFaction();
		gamedata.applyStoreCategory();
		gamedata.updateStoreBar();
	},

	//The bar over the Store: the faction's name, its power rating and, once loaded, its ship count.
	updateStoreBar: function updateStoreBar() {
		var bar = $("#lbStoreBar");
		if (!bar.length) return;

		var faction = gamedata.storeFaction;
		if (!faction) {
			$("#lbStoreFaction").text("No faction chosen");
			$("#lbStoreMeta").text("Choose a faction to see its ships.");
			$("#lbSwitchFaction").text("Choose Faction");
			bar.removeClass("lb-store-bar--custom lb-store-bar--set");
			return;
		}

		var rating = gamedata.getPowerRating(faction);
		var meta = rating ? rating.split(/\s*[;,]\s*/).filter(Boolean) : [];
		var node = gamedata.storeFactionNode(faction, true);
		if (node && node.getAttribute("data-state") === "loaded") {
			var count = $(node).find(".ship").length;
			meta.push(count + (count === 1 ? " ship" : " ships"));
		}

		$("#lbStoreFaction").text(faction);
		$("#lbStoreMeta").text(meta.join(" · "));
		$("#lbSwitchFaction").text("Switch Faction");
		bar.addClass("lb-store-bar--set").toggleClass("lb-store-bar--custom", rating.toLowerCase().indexOf("custom") !== -1);
	},

	//The Purchase bar's pressed category chip: a parseShips category index as a string, or null
	//for All.
	storeCategory: null,

	showStoreCategory: function showStoreCategory(cat) {
		gamedata.storeCategory = (cat === null || cat === undefined || cat === "") ? null : String(cat);
		gamedata.applyStoreCategory();
	},

	/* The category chips (user, Stage 5; the mockup's): one pressed shows only that size category
	   of the Store's faction, opened; All shows every category as it was. A chip whose category the
	   faction has none of is disabled, and if the pressed one is among them - after a switch of
	   faction - All takes over. Called on a chip, on a switch of faction and once its ships load. */
	applyStoreCategory: function applyStoreCategory() {
		var node = gamedata.storeFaction ? gamedata.storeFactionNode(gamedata.storeFaction, true) : null;
		var loaded = !!node && node.getAttribute("data-state") === "loaded";
		var present = {};
		$(node).find(".lb-cat").each(function () {
			present[this.getAttribute("data-cat")] = true;
		});
		if (loaded && gamedata.storeCategory !== null && !present[gamedata.storeCategory]) gamedata.storeCategory = null;

		$(".lb-cat-chip").each(function () {
			var cat = this.getAttribute("data-cat");
			var pressed = cat === "" ? gamedata.storeCategory === null : cat === gamedata.storeCategory;
			this.setAttribute("aria-pressed", pressed ? "true" : "false");
			this.disabled = cat !== "" && !present[cat];
		});

		$("#store .lb-cat").each(function () {
			var match = gamedata.storeCategory === null || this.getAttribute("data-cat") === gamedata.storeCategory;
			this.hidden = !match;
			if (match && gamedata.storeCategory !== null) {
				$(this).children(".lb-cat-body").show();
				$(this).children(".lb-cat-head").attr("aria-expanded", "true");
			}
		});
	},

	/* ── Lobby windows: the Faction Picker and the Fleet Correctness Report ──────────────────
	   One shell (.lb-modal in gameLobby.css). Closed by ×, a click on the overlay or Escape, and
	   focus goes back to whatever opened it. */
	lobbyModalOpener: {},

	openLobbyModal: function openLobbyModal(id, focusSelector) {
		var modal = document.getElementById(id);
		if (!modal) return;
		if (modal.hidden) gamedata.lobbyModalOpener[id] = document.activeElement;
		modal.hidden = false;
		document.body.classList.add("lb-modal-open");

		var target = (focusSelector && modal.querySelector(focusSelector)) || modal.querySelector(".lb-modal-panel");
		if (target) target.focus();
	},

	closeLobbyModal: function closeLobbyModal(id) {
		var modal = document.getElementById(id);
		if (!modal || modal.hidden) return;
		modal.hidden = true;
		if (!document.querySelector(".lb-modal:not([hidden])")) document.body.classList.remove("lb-modal-open");

		var opener = gamedata.lobbyModalOpener[id];
		if (opener && document.contains(opener) && opener.focus) opener.focus();
	},

	/* The title bar's Map & Scenario button folds the Map Preview / Scenario Description / Game Rules
	   section away, so the Store starts near the top of the page (plan §12.13). Remembered per game in
	   this browser only - a convenience: where storage is unavailable it simply opens every time. */
	initBriefToggle: function initBriefToggle() {
		var button = document.getElementById("lbBriefToggle");
		var brief = document.getElementById("lbBrief");
		if (!button || !brief) return; //Fleet Builder has neither
		var key = "fv.lobbyBriefClosed." + gamedata.gameid;

		var show = function (open) {
			brief.hidden = !open;
			button.setAttribute("aria-expanded", open ? "true" : "false");
		};

		var closed = false;
		try { closed = window.localStorage.getItem(key) === "1"; } catch (e) { /* no storage */ }
		show(!closed);

		button.addEventListener("click", function () {
			var open = brief.hidden;
			show(open);
			//Its zone labels are sized for the width the map is SHOWN at, which was none while folded.
			if (open) gamedata.drawMapPreview();
			try {
				if (open) window.localStorage.removeItem(key);
				else window.localStorage.setItem(key, "1");
			} catch (e) { /* no storage */ }
		});
	},

	//Called once from gamelobby.php's ready handler.
	initPurchasePanel: function initPurchasePanel() {
		gamedata.initBriefToggle();
		$("#lbSwitchFaction").on("click", gamedata.openFactionPicker);

		$(".lb-modal").on("click", function (e) {
			if (e.target === this) gamedata.closeLobbyModal(this.id);
		}).on("click", "[data-close]", function () {
			gamedata.closeLobbyModal($(this).closest(".lb-modal").attr("id"));
		});

		//Escape closes the top window - unless a confirm dialog sits over it, which it would
		//otherwise close behind, or a <select>'s list is open (the picker's Show Customs), whose own
		//Escape closes just the list. :open is not supported everywhere; where it is not, the
		//browser's native list keeps its keys to itself anyway.
		$(document).on("keydown.lbmodal", function (e) {
			if (e.key !== "Escape" || $(".confirm:visible").length) return;
			try {
				if (document.querySelector("select:open")) return;
			} catch (err) { /* no :open */ }
			var open = $(".lb-modal").filter(function () { return !this.hidden; }).last();
			if (!open.length) return;
			e.preventDefault();
			gamedata.closeLobbyModal(open.attr("id"));
		});

		$("#factionList").on("click", ".lb-fgroup-head", function () {
			var group = $(this).parent().toggleClass("is-collapsed");
			$(this).attr("aria-expanded", group.hasClass("is-collapsed") ? "false" : "true");
		}).on("click", ".lb-faction", function () {
			gamedata.showRolledFaction(null); //a faction is picked: the roll has done its job
			gamedata.selectStoreFaction(this.getAttribute("data-faction"));
			gamedata.closeLobbyModal("lbFactionPicker");
		});

		//The picker's columns are dealt again only when the window's width changes their number.
		if (window.matchMedia) {
			["(min-width: 860px)", "(min-width: 560px)"].forEach(function (query) {
				var mq = window.matchMedia(query);
				var relayout = function () { gamedata.layoutPickerColumns(); };
				if (mq.addEventListener) mq.addEventListener("change", relayout);
				else if (mq.addListener) mq.addListener(relayout);
			});
		}

		//The tier chips' All / None.
		$("#lbTierAll").on("click", gamedata.setAllTierFilters.bind(gamedata, true));
		$("#lbTierNone").on("click", gamedata.setAllTierFilters.bind(gamedata, false));

		//The randomiser, and its Choose - the rolled row's own click.
		$("#lbRollFaction").on("click", gamedata.rollFaction);
		$("#lbRollChoose").on("click", function () {
			$(gamedata.rolledFactionRow()).trigger("click");
		});

		//Enter in the search box picks the first faction it leaves.
		$("#factionSearch").on("input", gamedata.filterFactionList).on("keydown", function (e) {
			if (e.key !== "Enter") return;
			e.preventDefault();
			var first = $("#factionList .lb-faction").filter(function () { return !this.hidden; }).first();
			if (this.value.trim() !== "" && first.length) first.trigger("click");
		});

		//A size category's header opens and closes it.
		$("#store").on("click", ".lb-cat-head", function () {
			var body = $(this).next(".lb-cat-body");
			$(this).attr("aria-expanded", body.css("display") === "none" ? "true" : "false");
			body.stop(true, true).slideToggle(150);
		});

		$(".lb-cat-chip").on("click", function () {
			gamedata.showStoreCategory(this.getAttribute("data-cat"));
		});

		gamedata.applyStoreCategory();
		gamedata.updateStoreBar();
	},

	/* ── Top of the page: Teams | Scenario Description | Map Preview ─────────────────────────
	   CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §4.1 / §4.2 / §11.3 (Stage 4). A Fleet Builder lobby has
	   none of the three - no teams, no map, no scenario - so each of these returns quietly when its
	   element is not on the page. */

	/* Team colours, by the game's own rule (the gate in gamedata.js, plan §11.4): a participant in a
	   2-team game sees them RELATIVE - their own team green, the other team red, and on the map a
	   team-mate's slot ally blue; an observer, or anyone in a 3+-team game, sees each team by its
	   NUMBER (mapPreview.teamColor). The lobby's colours are therefore the VIEWER's, and change the
	   moment they take a slot - which is why they are repainted on every poll. */
	getLobbyTeams: function getLobbyTeams() {
		var teams = [];
		for (var i in gamedata.slots) {
			var team = parseInt(gamedata.slots[i].team, 10);
			if (teams.indexOf(team) === -1) teams.push(team);
		}
		return teams.sort(function (a, b) { return a - b; });
	},

	//The viewer's team, or null for an observer. Taking a slot on the other team moves a player out
	//of their old one (DBManager::takeSlot), so a player only ever holds slots on one team.
	getLobbyViewerTeam: function getLobbyViewerTeam() {
		for (var i in gamedata.slots) {
			if (gamedata.slots[i].playerid == gamedata.thisplayer) return parseInt(gamedata.slots[i].team, 10);
		}
		return null;
	},

	isLobbyColourRelative: function isLobbyColourRelative() {
		return gamedata.getLobbyViewerTeam() !== null && gamedata.getLobbyTeams().length === 2;
	},

	getLobbyTeamColor: function getLobbyTeamColor(team) {
		if (gamedata.isLobbyColourRelative()) {
			return team == gamedata.getLobbyViewerTeam() ? mapPreview.RELATIVE.own : mapPreview.RELATIVE.enemy;
		}
		return mapPreview.teamColor(team, gamedata.getLobbyTeams().length);
	},

	getLobbySlotColor: function getLobbySlotColor(slot) {
		if (gamedata.isLobbyColourRelative()) {
			if (slot.playerid == gamedata.thisplayer) return mapPreview.RELATIVE.own;
			return slot.team == gamedata.getLobbyViewerTeam() ? mapPreview.RELATIVE.ally : mapPreview.RELATIVE.enemy;
		}
		return mapPreview.teamColor(slot.team, gamedata.getLobbyTeams().length);
	},

	//The map's size in hexes; an open map (-1x-1) is drawn at 84 x 60, as it always was here.
	getLobbyMapSize: function getLobbyMapSize() {
		var match = String(gamedata.gamespace || "").match(/^(-?\d+)x(-?\d+)$/);
		var width = match ? parseInt(match[1], 10) : -1;
		var height = match ? parseInt(match[2], 10) : -1;
		if (width > 0 && height > 0) return { width: width, height: height };
		return { width: 84, height: 60 };
	},

	/* The Map Preview panel: every slot's deployment zone in its colour and the map template's
	   pre-placed terrain (rules.terrainLayout), drawn by mapPreview.js - the same drawing as Create
	   Game's Teams & Map step. Random terrain has no markers: it is not placed until the game starts. */
	drawMapPreview: function drawMapPreview() {
		var canvas = document.getElementById("mapPreview");
		if (!canvas || !window.mapPreview) return;

		var me = gamedata.thisplayer;
		var slots = [];
		for (var i in gamedata.slots) slots.push(gamedata.slots[i]);
		//The viewer's own zones last, so they sit on top where zones overlap.
		slots.sort(function (a, b) {
			return ((a.playerid == me) - (b.playerid == me)) || (a.slot - b.slot);
		});

		//One label per team, on its first slot's zone - the viewer's own, on their team. Team-mates
		//usually share a zone, and a label per slot would print them over each other.
		var labelSlot = {};
		slots.forEach(function (slot) {
			var current = labelSlot[slot.team];
			if (!current || (slot.playerid == me && current.playerid != me)) labelSlot[slot.team] = slot;
		});

		var size = gamedata.getLobbyMapSize();
		mapPreview.paint(canvas, {
			width: size.width,
			height: size.height,
			zones: slots.map(function (slot) {
				return {
					x: slot.depx, y: slot.depy, w: slot.depwidth, h: slot.depheight,
					rgb: gamedata.getLobbySlotColor(slot),
					label: labelSlot[slot.team] === slot ? "TEAM " + slot.team : null
				};
			}),
			terrain: gamedata.rules ? gamedata.rules.terrainLayout : null
		});

		gamedata.renderMapLegend();
	},

	renderMapLegend: function renderMapLegend() {
		var legend = $("#lbMapLegend");
		if (!legend.length) return;

		var item = function (swatchHtml, text) {
			return '<span class="lb-legend-item">' + swatchHtml + text + "</span>";
		};
		var teamSwatch = function (rgb) {
			return '<span class="lb-swatch" style="background:rgb(' + rgb.join(",") + ')"></span>';
		};
		var terrainSwatch = function (alpha) {
			return '<span class="lb-swatch lb-swatch--terrain" style="opacity:' + alpha + '"></span>';
		};

		var relative = gamedata.isLobbyColourRelative();
		var viewerTeam = gamedata.getLobbyViewerTeam();
		var html = "";
		gamedata.getLobbyTeams().forEach(function (team) {
			html += item(teamSwatch(gamedata.getLobbyTeamColor(team)), "Team " + team + (relative && team === viewerTeam ? " (you)" : ""));
		});

		//A team-mate's slot is drawn ally blue - say so, when there is one.
		if (relative) {
			for (var i in gamedata.slots) {
				var slot = gamedata.slots[i];
				if (slot.team == viewerTeam && slot.playerid != gamedata.thisplayer) {
					html += item(teamSwatch(mapPreview.RELATIVE.ally), "Ally");
					break;
				}
			}
		}

		var kinds = mapPreview.terrainKinds(gamedata.rules ? gamedata.rules.terrainLayout : null);
		if (kinds.solid) html += item(terrainSwatch(mapPreview.TERRAIN_ALPHA), "Asteroids &amp; Moons");
		if (kinds.fields) html += item(terrainSwatch(mapPreview.FIELD_TERRAIN_ALPHA), "Dust &amp; Meteor Swarms");

		legend.html(html);
	},

	/* After createSlots, on every poll: each team block in its colour, and the counts in the Teams
	   panel's head. */
	paintLobbyTeams: function paintLobbyTeams() {
		var container = $("#lobbyTeamsContainer");
		if (!container.length) return;

		var teams = gamedata.getLobbyTeams();
		container.toggleClass("lb-teams-grid--multi", teams.length > 2);
		container.find(".team-section").each(function () {
			var team = parseInt($(this).attr("data-team-id"), 10);
			this.style.setProperty("--rail", "rgb(" + gamedata.getLobbyTeamColor(team).join(",") + ")");
		});

		var total = 0, filled = 0, ready = 0;
		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			total++;
			if (playerManager.isOccupiedSlot(slot)) {
				filled++;
				if (slot.lastphase >= "-2") ready++;
			}
		}
		$("#lbTeamsMeta").text(filled + " / " + total + " filled" + (ready ? " · " + ready + " ready" : ""));
	},

	/* The Scenario Description panel: the Game Rules chips - scenarioCard.ruleChips, the very list
	   Create Game's Summary step previewed - and, for a game created with the structured scenario
	   (tac_game.scenario, the raw JSON text gamelobby.php hands over), its fact grid. A game created
	   before that has none: gamelobby.php has already filled the grid from its free-text
	   description, and it is left alone. Nothing here changes in a lobby, so this runs once.
	   inServiceDate: the game's In-Service Date year, or null - a chip here; gamelobby.php has
	   already locked the Store's ISD filter to it (Stage 6). */
	renderScenarioPanel: function renderScenarioPanel(scenarioRaw, inServiceDate, isPrivate) {
		var chips = $("#lbRuleChips");
		if (!chips.length) return;

		var slots = [];
		for (var i in gamedata.slots) slots.push(gamedata.slots[i]);
		var unlimited = slots.length > 0 && slots.every(function (slot) { return slot.points == -1; });
		chips.html(scenarioCard.renderRuleChips(scenarioCard.ruleChips(gamedata.rules, {
			unlimitedPoints: unlimited,
			inServiceDate: inServiceDate,
			isPrivate: isPrivate
		})));

		var facts = scenarioCard.render(scenarioRaw);
		if (facts) $("#lbScenarioFacts").html(facts);
	},

	getPlayerTeam: function getPlayerTeam(id) {
		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			if (slot.playerid == gamedata.thisplayer) return slot.team;
		}
	},

	getSlotData: function getSlotData(id) {
		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			if (slot.slot == id) return slot;
		}
	},

	/*old, simple version*/
	/*
	parseShips: function(jsonShips){
		for (var faction in jsonShips){
			var shipList = jsonShips[faction];
			
			this.orderShipListOnName(shipList);
			gamedata.setShipsFromFaction(faction, shipList);

			for (var index = 0; index < jsonShips[faction].length; index++){
				var ship = shipList[index];
				var targetNode = document.getElementById(ship.faction);

				var h = $('<div oncontextmenu="gamedata.onShipContextMenu(this);return false;" class="ship" data-id="'+ship.id+'" data-faction="'+ faction +'" data-shipclass="'+ship.phpclass+'"><span class="shiptype">'+ship.shipClass+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="addship clickable">Add to fleet</span></div>');
					h.appendTo(targetNode);
			}
	
			$(".addship").bind("click", this.buyShip);
		}
	},*/

	/*prepares ship class name for display - will contain lots of information besides class name itself!*/
	prepareClassName: function (ship) {
		//name: actualname (limited variant custom)
		//italics if actual variant!
		var displayName = ship.shipClass;
		var addOn = '';

		switch (ship.occurence) {
			case 'unique':
				addOn = 'Q';
				break;
			case 'rare':
				addOn = 'R';
				break;
			case 'uncommon':
				addOn = 'U';
				break;
			case 'common':
				addOn = 'C';
				break;
			default: //assume something atypical
				addOn = 'X';
		}
		if ((ship.limited > 0) && (ship.limited < 100)) { //else no such info necessary
			addOn = addOn + ' ' + ship.limited + '%';
		}
		if (ship.unofficial == 'S') {
			addOn = addOn + ' ' + 'SEMI-CUSTOM';
		} else if (ship.unofficial == true) {
			addOn = addOn + ' ' + 'CUSTOM';
		}

		displayName = displayName + ' (' + addOn + ')';
		//A variant's row is indented by the Store's CSS (.lb-ship.variant); only its NAME is italic.
		if (ship.variantOf != '') {
			displayName = '<b>' + displayName + '</b>';
		} else {
			displayName = '<b>' + displayName + '</b>';
		}

		return displayName;
	}, //endof prepareClassName

	/*returns a small size-class tag for fighter entries, eg. [L] or [H].
	  Mirrors the fleet checker: explicit hangarRequired wins; default 'fighters'
	  falls back to jinkinglimit classification.*/
	getFighterSizeTag: function (ship) {
		var size = ship.hangarRequired;
		if (size === 'fighters' || size === '' || size == null) {
			if (ship.jinkinglimit >= 99) size = 'ultralight';
			else if (ship.jinkinglimit >= 10) size = 'light';
			else if (ship.jinkinglimit >= 8) size = 'medium';
			else if (ship.jinkinglimit >= 6) size = 'heavy';
		}
		switch (size) {
			case 'ultralight': return '[U]';
			case 'light': case 'light fighters': return '[L]';
			case 'medium': case 'medium fighters': return '[M]';
			case 'heavy': case 'heavy fighters': case 'normal': return '[H]';
			case 'superheavy': case 'superheavy fighters': return '[SHF]';
			default: return '';
		}
	},

	/* One Store row: name (+ fighter size), cost, then Add to fleet · Show details. A base design and
	   its variant differ only in rowClass ('storeship' / 'variant' - the variant is indented and its
	   NAME italic, prepareClassName) and in detailsId, the id "Show details" passes (a variant has
	   always passed its base design's). The links are buttons, so they can be reached by keyboard. */
	storeShipRow: function storeShipRow(ship, rowClass, isCustomFaction, faction, isFighter, detailsId) {
		//"Custom" for the Store's Show Custom = a CUSTOM ship in an OFFICIAL faction (the ones it
		//highlights). A custom faction's own ships always show: the Faction Picker's Show Custom,
		//a separate setting, is what let it be picked (Stage 5).
		var isCustomShip = !isCustomFaction && ship.unofficial === true;
		var customShipHighlight = isCustomShip ? ' highlight-custom-ship' : '';
		var pointCostFull = ship.pointCost;
		if (ship.flight && (ship.maxFlightSize != 1)) pointCostFull = pointCostFull + ' (' + pointCostFull / 6 + ' ea.)';//for fighters: display price per craft, too!
		var sizeTag = isFighter ? this.getFighterSizeTag(ship) : '';

		//data-cost is the BASE point cost the Cost filter reads (for a flight,
		//the full-flight price shown in the row, not the per-craft one).
		var h = $('<div oncontextmenu="return false;" class="ship lb-ship ' + rowClass + '" data-custom="' + isCustomShip
			+ '" data-isd="' + ship.isd + '" data-cost="' + ship.pointCost + '">'
			+ '<span class="lb-ship-name"><span class="shiptype' + customShipHighlight + '">' + this.prepareClassName(ship) + '</span>'
			+ (sizeTag ? ' <span class="fightersize">' + sizeTag + '</span>' : '') + '</span>'
			+ '<span class="lb-ship-cost">' + pointCostFull + '</span>'
			+ '<span class="lb-ship-links"><button type="button" class="lb-linkbtn addship">Add to fleet</button>'
			+ '<span class="lb-ship-sep" aria-hidden="true">·</span>'
			+ '<button type="button" class="lb-linkbtn showship">Show details</button></span></div>');

		var buyHandler = gamedata.isBulkRow(ship) ? this.buyBulk.bind(this, ship.phpclass) : this.buyShip.bind(this, ship.phpclass);
		$(".addship", h).on("click", buyHandler);
		$(".showship", h).on("click", gamedata.onShipContextMenu.bind(this, ship.phpclass, faction, detailsId, false));
		return h;
	},

	/*prepares fleet list for purchases for display*/
	parseShips: function (jsonShips) {
		for (var faction in jsonShips) {
			//The Store's container for this faction (gamedata.selectStoreFaction).
			var targetNode = gamedata.storeFactionNode(faction);
			var ship;
			var shipV;
			var shipList = Object.values(jsonShips[faction]);
			var powerRating = gamedata.getPowerRating(faction);
			var isCustomFaction = powerRating.toLowerCase().includes("custom");

			this.orderShipListOnPV(shipList); //perhaps more appropriate here, as alphabetical order will be shot to hell anyway

			gamedata.setShipsFromFaction(faction, shipList);

			//show separately: immobile objects (bases/OSATs), every ship size, fighters, mines
			var sizeClassHeaders = ['Fighters', 'Light Combat Vessels', 'Medium Ships', 'Heavy Combat Vessels', 'Capital Ships', 'Immobile Structures', 'Mines'];
			for (var categoryIndex = 6; categoryIndex >= 0; categoryIndex--) {
				if (categoryIndex === 6 && gamedata.rules && !gamedata.rules.allowMines && !gamedata.rules.fleetTest) continue;
				if (faction === "Terrain" && categoryIndex < 5) continue; // Terrain faction has no ships or fighters

				// Create a fragment for this size category
				var fragment = document.createDocumentFragment();

				//display header - a disclosure button (gamedata.initPurchasePanel opens and closes it)
				//with the category's count
				var startClosed = ((categoryIndex === 1 && faction !== "Deneth Tribes" && faction !== "Thirdspace" && faction !== "Usuuth Coalition" && faction !== "Civilians" && faction !== "Barada Imperium" && faction.indexOf("Nexus") === -1) || categoryIndex === 5 || categoryIndex === 6); // 1 = LCVs, 5 = Immobile Structures, 6 = Mines
				if (faction === "Terrain") {
					startClosed = false;
				}

				var headerElem = $('<button type="button" class="lb-cat-head" aria-expanded="' + (startClosed ? 'false' : 'true') + '">'
					+ '<span class="lb-disclosure" aria-hidden="true"></span><span class="lb-cat-name">' + sizeClassHeaders[categoryIndex] + '</span>'
					+ '<span class="lb-cat-count"></span></button>');
				var categoryContainer = $('<div class="lb-cat-body"' + (startClosed ? ' style="display:none"' : '') + '></div>');
				var shipCount = 0; // Track if we actually add anything to this category

				// Don't append to fragment yet, wait to see if it's empty

				var activeShipList = shipList;
				if (categoryIndex === 6) {
					activeShipList = shipList.slice();
					this.orderShipListOnName(activeShipList);
				}

				for (var index = 0; index < activeShipList.length; index++) {
					ship = activeShipList[index];
					if (gamedata.rules && !gamedata.rules.allowMines && ship.mine && !gamedata.rules.fleetTest) continue; //Skip mines if not allowed in scenario

					if (categoryIndex == 6) { //Mines
						if (ship.mine != true) continue;
					} else if (categoryIndex == 5) { //bases and OSATs, size does not matter
						if (ship.mine == true || (ship.base != true && ship.osat != true)) continue; //check if it's a base or OSAT
					} else if (categoryIndex == 4) { //Capital Ships
						if (ship.mine == true || ship.shipSizeClass != 3) continue;
						if (ship.base == true || ship.osat == true) continue;
						if (ship.hangarRequired === 'LCVs') continue;
					} else if (categoryIndex == 3) { //Heavy Combat Vessels
						if (ship.mine == true || ship.shipSizeClass != 2) continue;
						if (ship.base == true || ship.osat == true) continue;
						if (ship.hangarRequired === 'LCVs') continue;
					} else if (categoryIndex == 2) { //Medium Ships
						if (ship.mine == true || ship.shipSizeClass != 1) continue;
						if (ship.base == true || ship.osat == true) continue;
						if (ship.hangarRequired === 'LCVs') continue;
					} else if (categoryIndex == 1) { //Light Combat Vessels
						if (ship.hangarRequired !== 'LCVs') continue;
						if (ship.mine == true || ship.base == true || ship.osat == true) continue;
					} else { //fighters! check max size - they should be -1, but 0 isn't used...
						if (ship.mine == true || ship.shipSizeClass > 0) continue;//check if it's of correct size
						if ((ship.base == true) || (ship.osat == true)) continue; //check if it's not a base or OSAT
						if (ship.hangarRequired === 'LCVs') continue;
					}
					if (ship.variantOf != '') continue;//check if it's not a variant, we're looking only for base designs here...
					//ok, display...
					categoryContainer.append(this.storeShipRow(ship, 'storeship', isCustomFaction, faction, categoryIndex === 0, ship.id));
					shipCount++;
					//search for variants of the base design above...
					for (var indexV = 0; indexV < activeShipList.length; indexV++) {
						shipV = activeShipList[indexV];
						if (shipV.variantOf != ship.shipClass) continue;//that's not a variant of current base ship

						//"Show details" passes the BASE design's id, as it always has.
						categoryContainer.append(this.storeShipRow(shipV, 'variant', isCustomFaction, faction, categoryIndex === 0, ship.id));
						shipCount++;
					} //end of variant
				} //end of base design

				// Only append the header and container if this category actually has ships
				//One .lb-cat per category, carrying its index for the Purchase bar's category chips
				//(gamedata.showStoreCategory).
				if (shipCount > 0) {
					headerElem.find(".lb-cat-count").text(shipCount);
					var category = document.createElement("div");
					category.className = "lb-cat";
					category.setAttribute("data-cat", categoryIndex);
					category.appendChild(headerElem[0]);
					category.appendChild(categoryContainer[0]);
					fragment.appendChild(category);
				}

				// Append the entire fragment for this size class to the DOM at once
				targetNode.appendChild(fragment);

			} //end of size


		} //end of faction
	}, //endof parseShips


	//Function called by the Store's Show Custom and the Name / Cost / ISD filters. Show Custom is the
	//Purchase bar's own box (#toggleCustomShips), not the Faction Picker's (Stage 5).
	applyCustomShipFilter: function () {
		const showCustom = $("#toggleCustomShips").is(":checked");
		const isdValue = parseInt($("#isdFilter").val(), 10);
		//Cost filter: hide anything that costs MORE than the figure typed. Read once,
		//outside the per-ship loop, like the other two.
		const costValue = parseInt($("#costFilter").val(), 10);
		const nameFilter = $("#nameFilter").val().toLowerCase().trim();

		//Every faction the Store has loaded, shown or not, so a switch back finds it filtered.
		$("#store .lb-store-faction").each(function () {
			const $faction = $(this);

			$faction.find(".ship").each(function () {
				const $ship = $(this);
				const isCustom = $ship.data("custom") === true || $ship.data("custom") === "true";
				const shipISD = parseInt($ship.data("isd"), 10);
				const shipCost = parseFloat($ship.data("cost"));

				let visible = true;

				if (!showCustom && isCustom) visible = false;
				if (!isNaN(isdValue) && shipISD > isdValue) visible = false;
				if (!isNaN(costValue) && !isNaN(shipCost) && shipCost > costValue) visible = false;

				// Name filter logic
				if (nameFilter.length > 0) {
					const shipName = $ship.find(".shiptype").text().toLowerCase();
					if (shipName.indexOf(nameFilter) === -1) visible = false;
				}

				$ship.toggle(visible);
			});

			//A size category the filters have emptied is left out, header and all; the count is
			//what is left in it.
			$faction.find(".lb-cat").each(function () {
				const shown = $(this).find(".ship").filter(function () {
					return this.style.display !== "none";
				}).length;
				$(this).toggleClass("is-empty", shown === 0).find(".lb-cat-count").text(shown);
			});
		});
	},

	goToWaiting: function goToWaiting() { },

	parseServerData: function parseServerData(serverdata) {
		if (serverdata == null) {
			window.location = "games.php";
			return;
		}

		if (!serverdata.id) return;

		gamedata.turn = serverdata.turn;
		gamedata.gamephase = serverdata.phase;
		gamedata.activeship = serverdata.activeship;
		gamedata.gameid = serverdata.id;
		gamedata.slots = serverdata.slots;
		//gamedata.ships = serverdata.ships;
		gamedata.thisplayer = serverdata.forPlayer;
		gamedata.maxpoints = serverdata.points;
		gamedata.status = serverdata.status;
		gamedata.gamespace = serverdata.gamespace;
		gamedata.rules = serverdata.rules;

		//REINFORCEMENTS_PLAN.md §4 Stage 1 - the buy-mode toggle ships hidden in the markup and
		//is revealed here, because this is the first point at which gamedata.rules exists. Runs
		//on every poll; both calls inside are no-ops when nothing has changed.
		gamedata.applyReinforcementRule();

		if (gamedata.status == "ACTIVE") {
			window.location = "game.php?gameid=" + gamedata.gameid;
		}

		//Prune here
		if (gamedata.rules && gamedata.rules.fleetTest === 1) {
			var mySlot = null;
			for (var slotKey in serverdata.slots) {
				if (serverdata.slots[slotKey].playerid == gamedata.thisplayer) {
					mySlot = {};
					mySlot[slotKey] = serverdata.slots[slotKey];
					break;
				}
			}
			gamedata.slots = mySlot || serverdata.slots;
			this.createSlots();
			this.enableBuy();
			this.constructFleetList();
			//this.drawMapPreview();						
		} else {
			this.createSlots();
			this.paintLobbyTeams();
			this.enableBuy();
			this.constructFleetList();
			this.drawMapPreview();
		}

	},

	createNewSlot: function createNewSlot(data) {
		var teamId = data.team;
		var teamSection = $("#lobbyTeamsContainer .team-section[data-team-id='" + teamId + "']");

		if (teamSection.length === 0) {
			var teamTemplate = $("#lobbyTeamTemplate").children().clone();
			teamTemplate.attr("data-team-id", teamId);
			teamTemplate.find(".team-number").text(teamId);
			//In team order, whatever order the slots come in - a copied team's slots can be numbered
			//below another team's. Colours are paintLobbyTeams' job.
			var later = $("#lobbyTeamsContainer .team-section").filter(function () {
				return parseInt($(this).attr("data-team-id"), 10) > parseInt(teamId, 10);
			}).first();
			if (later.length) teamTemplate.insertBefore(later);
			else $("#lobbyTeamsContainer").append(teamTemplate);
			teamSection = teamTemplate;
		}

		var target = teamSection.find(".slotcontainer");
		var template = $("#slottemplatecontainer .slot");
		var actual = template.clone(true).appendTo(target);

		actual.data("slotid", data.slot);
		actual.addClass("slotid_" + data.slot);
		gamedata.setSlotData(data);
	},

	createSlots: function createSlots() {
		var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
		if (selectedSlot && selectedSlot.playerid != gamedata.thisplayer) {
			$('.slot.slotid_' + selectedSlot.slot).removeClass("selected");
			gamedata.selectedSlot = null;
		}

		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			var slotElement = $('.slot.slotid_' + slot.slot);

			if (!slotElement.length) {
				gamedata.createNewSlot(slot);
			} else {
				gamedata.setSlotData(slot);
			}

			slotElement = $('.slot.slotid_' + slot.slot);
			var data = slotElement.data();
			if (playerManager.isOccupiedSlot(slot)) {
				var player = playerManager.getPlayerInSlot(slot);
				slotElement.data("playerid", player.id);
				slotElement.addClass("taken");
				$(".playername", slotElement).html(player.name);

				//Only show select button if it's a viable option
				if (slot.playerid == gamedata.thisplayer && slot.slot !== gamedata.selectedSlot) $(".selectslot", slotElement).show();

				if (slot.playerid == gamedata.thisplayer && slot.slot == gamedata.selectedSlot ||
					slot.playerid !== gamedata.thisplayer)
					$(".selectslot", slotElement).hide();
				//if() $(".selectslot", slotElement).hide();

				if (slot.lastphase >= "-2") {
					slotElement.addClass("ready");
				}

				if (player.id == gamedata.thisplayer) {
					if (gamedata.selectedSlot == null) gamedata.selectedSlot = slot.slot;
					$(".leaveslot, .leaveslot-label", slotElement).show();
				} else $(".leaveslot, .leaveslot-label", slotElement).hide();
			} else {
				$(".leaveslot, .leaveslot-label", slotElement).hide();

				slotElement.attr("data-playerid", "");
				slotElement.removeClass("taken");
				$(".playername", slotElement).html("");

				slotElement.removeClass("ready");
			}

			if (gamedata.selectedSlot == slot.slot) {
				gamedata.selectSlot(slot);
			}
		}
	},

	setSlotData: function setSlotData(data) {
		var slot = $(".slot.slotid_" + data.slot);
		$(".name", slot).html(data.name);
		if (gamedata.rules && gamedata.rules.fleetTest === 1) data.points = -1;
		$(".points", slot).text(data.points == -1 ? "Unlimited" : data.points + " pts");

		//The zone itself is on the Map Preview; the row only says when a slot arrives LATE
		//(Delayed Deployment) - turn 1 is the norm and says nothing.
		$(".depavailable", slot).text(data.depavailable);
		slot.toggleClass("lb-slot--late", parseInt(data.depavailable, 10) > 1);
	},

	clickTakeslot: function clickTakeslot() {
		var slot = $(".slot").has($(this));
		var slotid = slot.data("slotid");
		var newSlot = playerManager.getSlotById(slotid);

		// block if player already has confirmed fleet (in any slot)
		for (var i in gamedata.slots) { //check all slots
			var checkSlot = gamedata.slots[i];
			if (checkSlot.lastphase >= "-2") { //this slot has ready fleet
				var player = playerManager.getPlayerInSlot(checkSlot);
				if (player.id == gamedata.thisplayer && checkSlot.team !== newSlot.team) { //Player has a readied slot in another team
					window.confirm.error("You've confirmed a fleet for this game, you cannot change teams now!", function () { });
					return;
				}
			}
		}

		ajaxInterface.submitSlotAction("takeslot", slotid, function () {
			window.updateTierFilter();
			//ajaxInterface.startPollingGamedata();
		});
	},

	onLeaveSlotClicked: function onLeaveSlotClicked() {
		var slot = $(".slot").has($(this));
		var slotid = slot.data("slotid");

		var slotFull = playerManager.getSlotById(slotid);

		//block if player already has confirmed fleet (in this slot)
		if (slotFull.lastphase >= "-2") {
			window.confirm.error("You have already confirmed your fleet for this slot!", function () { });
			return;
		}

		ajaxInterface.submitSlotAction("leaveslot", slotid, function (serverdata) {
			window.updateTierFilter();

			var hasOtherSlots = 0;
			// Use serverdata.slots explicitly. If missing (e.g. game deleted), assume empty list (0 slots).
			var slotsToCheck = serverdata && serverdata.slots ? serverdata.slots : [];

			for (var i in slotsToCheck) { //check all slots
				var checkSlot = slotsToCheck[i];
				if (checkSlot.playerid == gamedata.thisplayer) { //this slot has ready fleet
					hasOtherSlots++;
				}
			}

			//UPDATE: gamedata slots IS updated now via the response from slot.php, so we check if we have ANY other slots.
			if (hasOtherSlots === 0) {
				window.location = "games.php"; //Leave to main lobby if player has no other slots here.
			} else {
				ajaxInterface.startPollingGamedata();
			}
		});
	},

	enableBuy: function enableBuy() {
		var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
		if (selectedSlot && selectedSlot.playerid == gamedata.thisplayer) {
			$(".buy").show();
		} else {
			$(".buy").hide();
		}
	},

	buyBulk: function buyBulk(shipclass) {
		var ship = gamedata.getShipByType(shipclass);

		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);
		if (selectedSlot.lastphase >= "-2") {
			window.confirm.error("This slot has already bought a fleet!", function () { });
			return false;
		}

		$(".confirm").remove();

		window.confirm.showBuyBulk(ship, gamedata.doBuyBulk);
	},

	/* ⚠️ A ship's enhancementOptions, copied so that writing to the copy cannot reach the
	   original. Each option is ITSELF an array whose index 2 holds the chosen count, and
	   every buy/edit path writes that index in place - so a shallow [...] of the outer array
	   leaves a copied row sharing its counts with the row it was copied from, and editing
	   one silently rewrote the other. */
	cloneEnhancementOptions: function cloneEnhancementOptions(ship) {
		if (!ship || !ship.enhancementOptions) return [];

		return ship.enhancementOptions.map(function (option) {
			return Array.isArray(option) ? option.slice() : option;
		});
	},

	/* ⭐ Read the bulk dialog's spinners onto `ship` and price the result. ONE reader for
	   all three bulk paths (buy, edit, copy), so the pricing convention below is stated
	   once instead of three times.

	     baseCost - the BARE hull's cost, with no enhancements in it. Passed in rather than
	                read off ship.pointCost, because on an edit or copy that field already
	                has the previous enhancements folded in and re-folding compounds them.

	   Every count is rewritten, including back down to 0: an edit that TAKES an enhancement
	   away has to clear the option it was recorded in, or lobbyEnhancements.apply would
	   re-apply it to the rebuilt unit for free. */
	readBulkPurchase: function readBulkPurchase(ship, quantity, baseCost) {
		ship.bulkBuy = parseInt(quantity, 10) || 1;

		ship.pointCostEnh = 0;
		ship.pointCostEnh2 = 0;

		//do note enhancements bought (if any)
		var enhNo = 0;
		var noTaken = 0;
		var target = $(".selectAmount.shpenh" + enhNo);
		while (typeof target.data("enhPrice") != 'undefined') { //as long as there are enhancements defined...
			noTaken = target.data("count");
			ship.enhancementOptions[enhNo][2] = noTaken > 0 ? noTaken : 0;

			if (noTaken > 0) { //enhancement picked - note value!
				if (!ship.enhancementOptions[enhNo][6]) { //this is an actual enhancement (as opposed to option)
					ship.pointCostEnh += target.data("enhCost"); // Cost is per-unit
				} else { //this is an option
					ship.pointCostEnh2 += target.data("enhCost"); // Cost is per-unit
				}
			}

			//go to next enhancement
			enhNo++;
			target = $(".selectAmount.shpenh" + enhNo);
		}

		//Fold the per-unit enhancement cost into pointCost, exactly as doBuyShip does with
		//the dialog total. That single convention (see rowPointCost) is what lets a bulk
		//row be priced, edited and saved by the same code as any other unit.
		//pointCostSysEnh is the THIRD bucket (WEAPON_ENHANCEMENTS_PLAN.md D5): per-system
		//refits are bought from the ship window, never from this dialog, so the two loops
		//above cannot rebuild them - it is added, never recomputed. Leaving it out here is
		//exactly the bug D5 exists to prevent: an edit that silently refunds every refit
		//while leaving it applied.
		ship.pointCost = baseCost + ship.pointCostEnh + ship.pointCostEnh2 + (ship.pointCostSysEnh || 0);
	},

	doBuyBulk: function doBuyBulk(results, shipclass) {
		var ship = gamedata.getShipByType(shipclass);

		ship.userid = gamedata.thisplayer;
		//The class name is the STEM, not the final unit name: the SERVER numbers the
		//minted copies "Gravitic Mine #1, #2, ..." (BuyingGamePhase::process). Same for
		//OSATs as for mines - a bulk purchase is interchangeable units, so neither offers
		//a name box.
		ship.name = ship.shipClass;
		//REINFORCEMENTS_PLAN.md §4 Stage 1 - same read as doBuyShip. A bulk row is ONE object
		//standing for N units and BuyingGamePhase mints the copies with `clone $ship`, a shallow
		//copy, so this one boolean reaches every unit in the row.
		//canBeReinforcement: a base/OSAT/Terrain bulk row is on the board on turn 1 whatever the
		//buy panel says, and the flag on it is actively harmful - see the note there.
		ship.reinforcement = gamedata.buyingReinforcement() && gamedata.canBeReinforcement(ship);

		//A store blueprint's pointCost is pristine by definition.
		gamedata.readBulkPurchase(ship, results.quantity, ship.pointCost);

		/* Cost of the fleet WITH this purchase in it. This used to be a second, hand-rolled
		   copy of calculateFleet's sum; it is now the same fleetCost() the panel displays,
		   so the "you cannot afford that" line can never disagree with the pts-left figure
		   the player is looking at. */
		if (!gamedata.canAfford(ship)) {
			$(".confirm").remove();
			window.confirm.error("You cannot afford that Unit purchase!", function () { });
			return;
		}

		ship.slot = gamedata.selectedSlot;

		$(".confirm").remove();
		gamedata.updateFleet(ship);
		gamedata.calculateFleet();
		gamedata.drawMapPreview(); // Redraw map to show unitfields
	},

	/* Re-open a bought bulk row and write the changes back. doEditShip's shape, minus
	   everything a bulk row cannot have (no name box, no flight-size selector, no missile
	   pickers - getMissileOptions returns nothing for a non-flight hull anyway), plus the
	   quantity.

	   The arguments come from the dialog's OK handler rather than off `this`, because the
	   bulk dialog reads its fields and tears itself down before calling back. */
	doEditBulk: function doEditBulk(results, shipclass, ship, originalShipData) {
		if (!ship) return;

		//Captured BEFORE pointCost is overwritten - by then it is no longer the bare hull's,
		//and the no-blueprint stand-in below needs the bare one.
		var pristinePointCost = gamedata.getPristinePointCost(ship);

		gamedata.readBulkPurchase(ship, results.quantity, pristinePointCost);

		if (!gamedata.canAffordEdit(ship)) {
			//Put the row back exactly as it was before returning.
			ship.name = originalShipData.name;
			ship.pointCost = originalShipData.pointCost;
			ship.bulkBuy = originalShipData.bulkBuy;
			ship.enhancementOptions = originalShipData.enhancementOptions ? [...originalShipData.enhancementOptions] : [];
			ship.pointCostEnh = originalShipData.pointCostEnh;
			ship.pointCostEnh2 = originalShipData.pointCostEnh2;
			ship.pointCostSysEnh = originalShipData.pointCostSysEnh;
			if (window.systemEnhancements) {
				ship.systemEnhancements = systemEnhancements.clone(originalShipData.systemEnhancements);
			}
			$(".confirm").remove();
			window.confirm.error("You cannot afford those edits!", function () { });
			return;
		}

		var newPointCost = ship.pointCost;

		//Remove old row from the Fleet List first - updateFleet re-adds it at the end.
		var id = ship.id;
		for (var i in gamedata.ships) {
			if (gamedata.ships[i].id == id) {
				delete gamedata.ships[i];
				break;
			}
		}
		$('.ship.bought.shipid_' + id).remove();

		var baseShip = gamedata.getShipByType(ship.phpclass);
		if (!baseShip) {
			//Loaded fleets may not have their faction set yet when editing, so do this now.
			//Register a COPY carrying the BARE hull's cost, not `ship` itself: `ship` is holding
			//the folded total by this point, and whatever is registered here becomes the
			//blueprint every later lookup of this class finds - including the next edit of this
			//same row, which would then take an inflated cost as its baseline.
			var standIn = jQuery.extend({}, ship);
			standIn.pointCost = pristinePointCost;
			/* REINFORCEMENTS_PLAN.md §4 Stage 1 - and STRIP THE REINFORCEMENT FLAG off the
			   stand-in. setShipsFromFaction runs every entry through new Ship(json), whose ctor
			   copies EVERY key onto the instance, so whatever is registered here becomes the
			   blueprint that every later getShipByType of this class returns - and an ad-hoc
			   reinforcement:true would then be minted onto every FUTURE purchase of the class,
			   silently. Exactly the hazard the pointCost line above exists for, and this branch
			   fires only on a LOADED fleet, which is precisely the reinforcement-heavy case. */
			delete standIn.reinforcement;
			gamedata.setShipsFromFaction(ship.faction, [standIn]);
			baseShip = gamedata.getShipByType(ship.phpclass);
		}

		//Same reset list as doEditShip: EVERY enhancement-mutated ship-level stat must
		//return to its blueprint value before re-applying, or enhancements kept through an
		//edit compound on each pass.
		ship.systems = baseShip.systems;
		ship.notes = baseShip.notes;
		ship.forwardDefense = baseShip.forwardDefense;
		ship.sideDefense = baseShip.sideDefense;
		ship.iniativebonus = baseShip.iniativebonus;
		ship.critRollMod = baseShip.critRollMod;
		ship.toHitBonus = baseShip.toHitBonus;
		ship.turncost = baseShip.turncost;
		ship.turndelaycost = baseShip.turndelaycost;
		ship.pivotcost = baseShip.pivotcost;
		ship.signature = baseShip.signature;
		ship.detectedSignature = baseShip.detectedSignature;
		ship.IFFSystem = baseShip.IFFSystem;

		if (ship.flight) {
			//the flight-shaped MineClass customs land here; a ship-shaped mine or OSAT does not
			ship.freethrust = baseShip.freethrust;
			ship.hasNavigator = baseShip.hasNavigator;
			ship.offensivebonus = baseShip.offensivebonus;
			lobbyEnhancements.resetEnhancementMarkersFighter(ship);
		} else {
			lobbyEnhancements.resetEnhancementMarkersShip(ship);
		}

		ship.pointCost = newPointCost;
		ship.userid = gamedata.thisplayer;

		//single enhancement entry point (markers reset above, so this re-applies the
		//kept/changed enhancements to the rebuilt unit)
		lobbyEnhancements.apply(ship);

		//Pre-battle damage: the rebuild above replaced ship.systems wholesale, so the preview
		//has to be painted onto the new objects. A bulk MINE keys its damage per copy, so
		//lowering the quantity trims the copies that no longer exist; a bulk OSAT's damage is
		//per-system and applies to every copy, so changing the quantity leaves it alone.
		var damageTrimmed = window.battleDamage
			&& battleDamage.onShipRebuilt(ship, battleDamage.ordinalCount(originalShipData));

		//The React window renders from this same mutated ship object, so just re-render it.
		var wasVisible = window.shipWindowManagerReact
			&& window.shipWindowManagerReact.ships.indexOf(ship) !== -1;

		$(".confirm").remove();
		gamedata.updateFleet(ship);

		if (wasVisible) {
			window.shipWindowManagerReact.update();
		}

		if (damageTrimmed) {
			confirm.warning("Quantity reduced - pre-battle damage on the units that were removed has been discarded.");
		}

		gamedata.drawMapPreview(); // Redraw map to show unitfields
	},

	/* Copy a bought bulk row. Opens the bulk dialog pre-filled with what the original
	   carries, so the player can vary the quantity or the enhancements before accepting -
	   which is the point of copying rather than raising the original's quantity. */
	copyBulk: function copyBulk(copiedShip) {
		var newShip = gamedata.getShipByType(copiedShip.phpclass);
		if (!newShip) {
			//Loaded fleets may not have their faction set yet, so register the class now - at
			//the BARE hull's cost, same reasoning as in doEditBulk.
			var standIn = jQuery.extend({}, copiedShip);
			standIn.pointCost = gamedata.getPristinePointCost(copiedShip);
			/* REINFORCEMENTS_PLAN.md §4 Stage 1 - and STRIP THE REINFORCEMENT FLAG off the
			   stand-in. setShipsFromFaction runs every entry through new Ship(json), whose ctor
			   copies EVERY key onto the instance, so whatever is registered here becomes the
			   blueprint that every later getShipByType of this class returns - and an ad-hoc
			   reinforcement:true would then be minted onto every FUTURE purchase of the class,
			   silently. Exactly the hazard the pointCost line above exists for, and this branch
			   fires only on a LOADED fleet, which is precisely the reinforcement-heavy case. */
			delete standIn.reinforcement;
			gamedata.setShipsFromFaction(copiedShip.faction, [standIn]);
			newShip = gamedata.getShipByType(copiedShip.phpclass);
		}

		newShip.name = copiedShip.name;
		//All three together: pointCost has the per-unit enhancements folded in, and
		//getPristinePointCost peels them back off using the other two.
		newShip.pointCost = copiedShip.pointCost;
		newShip.pointCostEnh = copiedShip.pointCostEnh;
		newShip.pointCostEnh2 = copiedShip.pointCostEnh2;
		newShip.bulkBuy = copiedShip.bulkBuy;
		//REINFORCEMENTS_PLAN.md §4 Stage 1 - same as copyShip: the copy starts where the
		//original is. doCopyBulk receives this object unchanged, so nothing more is needed.
		newShip.reinforcement = Boolean(copiedShip.reinforcement);
		newShip.enhancementOptions = gamedata.cloneEnhancementOptions(copiedShip);
		//DEEP clone - sharing the payload object would make damaging either row damage both.
		if (window.battleDamage) {
			newShip.preBattleDamage = battleDamage.clone(copiedShip.preBattleDamage);
		}
		//Per-system refits: same story, same DEEP clone. Applied to the copy's own systems,
		//which are a fresh blueprint clone - the copy must not share the original's rows.
		if (window.systemEnhancements) {
			newShip.pointCostSysEnh = copiedShip.pointCostSysEnh || 0;
			newShip.systemEnhancements = systemEnhancements.clone(copiedShip.systemEnhancements);
			systemEnhancements.apply(newShip);
		}

		$(".confirm").remove();

		window.confirm.showBuyBulk(newShip, gamedata.doCopyBulk, true, 'copy');
	},

	doCopyBulk: function doCopyBulk(results, shipclass, ship, originalShipData) {
		if (!ship) return;

		//`ship` here is already a fresh blueprint clone built by copyBulk, so its systems
		//need no rebuilding - only pricing and the payload carried across.
		gamedata.readBulkPurchase(ship, results.quantity, gamedata.getPristinePointCost(ship));

		if (!gamedata.canAfford(ship)) {
			$(".confirm").remove();
			window.confirm.error("You cannot afford that Unit purchase!", function () { });
			return;
		}

		//The copy is a NEW purchase, so it is named from the class and numbered by the
		//server exactly as a fresh bulk buy is - never after the row it came from.
		ship.name = ship.shipClass;
		ship.userid = gamedata.thisplayer;
		ship.slot = gamedata.selectedSlot;

		var damageTrimmed = window.battleDamage
			&& battleDamage.onShipRebuilt(ship, battleDamage.ordinalCount(originalShipData));

		$(".confirm").remove();
		gamedata.updateFleet(ship);
		gamedata.calculateFleet();

		if (damageTrimmed) {
			confirm.warning("Quantity reduced - pre-battle damage on the units that were removed was not copied.");
		}

		gamedata.drawMapPreview(); // Redraw map to show unitfields
	},

	buyShip: function buyShip(shipclass) {
		var ship = gamedata.getShipByType(shipclass);

		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);
		if (selectedSlot.lastphase >= "-2") {
			window.confirm.error("This slot has already bought a fleet!", function () { });
			return false;
		}

		$(".confirm").remove();

		window.confirm.showShipBuy(ship, gamedata.doBuyShip);

	},


	doBuyShip: function doBuyShip() {
		var shipclass = $(this).data().shipclass;
		var ship = gamedata.getShipByType(shipclass);

		var name = $(".confirm input[name=shipname]").val();
		ship.name = name;
		ship.userid = gamedata.thisplayer;
		//REINFORCEMENTS_PLAN.md §4 Stage 1. Read off the buy panel, NOT off `ship` - this is a
		//pristine blueprint clone (getShipByType deep-copies gamedata.allShips) and never
		//carries the flag. A reinforcement costs the same and comes out of the same pool, so
		//nothing below has to know about it.
		//canBeReinforcement: a base, an OSAT and Terrain deploy on turn 1 whatever the buy panel
		//says, so the flag can never come true for them and does real damage - see the note there.
		ship.reinforcement = gamedata.buyingReinforcement() && gamedata.canBeReinforcement(ship);

		if ($(".confirm .totalUnitCostAmount").length > 0) {
			ship.pointCost = $(".confirm .totalUnitCostAmount").data("value");
		}

		if (!gamedata.canAfford(ship)) {
			$(".confirm").remove();
			window.confirm.error("You cannot afford that ship!", function () { });
			return;
		}

		if (ship.flight) {
			var flightSize = $(".fighterAmount").html();
			if (!flightSize) {
				flightSize = 1;
			}
			ship.flightSize = Math.floor(flightSize);
		}

		//do note enhancements bought (if any)
		var enhNo = 0;
		var noTaken = 0;
		var target = $(".selectAmount.shpenh" + enhNo);
		while (typeof target.data("enhPrice") != 'undefined') { //as long as there are enhancements defined...
			noTaken = target.data("count");
			if (noTaken > 0) { //enhancement picked - note!
				ship.enhancementOptions[enhNo][2] = noTaken;
				if (!ship.enhancementOptions[enhNo][6]) { //this is an actual enhancement (as opposed to option) - note value!
					if (ship.flight) {
						ship.pointCostEnh += target.data("enhCost") * flightSize;
					} else {
						ship.pointCostEnh += target.data("enhCost");
					}
				} else { //this is an option - still note value, just separately!
					if (ship.flight) {
						ship.pointCostEnh2 += target.data("enhCost") * flightSize;
					} else {
						ship.pointCostEnh2 += target.data("enhCost");
					}
				}
			}
			//go to next enhancement
			enhNo++;
			target = $(".selectAmount.shpenh" + enhNo);
		}

		if ($(".confirm .selectAmount").length > 0) {
			if (ship.flight) {

				// and get the amount of launchers on a fighter
				var nrOfLaunchers = 0;

				for (var j in ship.systems[1].systems) {
					var fighterSystem = ship.systems[1].systems[j];

					if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
						nrOfLaunchers++;
					}
				}

				// get all selections of missiles
				var missileOptions = $(".confirm .selectAmount");

				for (var k = 0; k < missileOptions.length; k++) {
					var firingMode = $(missileOptions[k]).data("firingMode");

					// divide the bought missiles over the missileArrays
					var boughtAmount = $(".confirm .selectAmount." + firingMode).data("value");

					// perLauncher should always get you an integer as result. The UI handles
					// buying of missiles that way.
					var perLauncher = boughtAmount;

					for (var i in ship.systems) {
						var fighter = ship.systems[i];

						for (var j in fighter.systems) {
							var fighterSystem = fighter.systems[j];

							if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
								// find the correct index, depending on the firingMode
								for (var index in fighterSystem.firingModes) {
									if (fighterSystem.firingModes[index] == firingMode) {
										fighterSystem.missileArray[index].amount = perLauncher;
									}
								}
							}
						}
					}
				}
			} else { }
		}

		$(".confirm").remove();
		gamedata.updateFleet(ship);
		//gamedata.populateFleetDropdown();		
	},


	copyShip: function copyShip(copiedShip) {

		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);
		if (selectedSlot.lastphase >= "-2") {
			window.confirm.error("You have already readied your fleet!", function () { });
			return false;
		}

		if (gamedata.isBulkRow(copiedShip)) {
			gamedata.copyBulk(copiedShip);
			return;
		}

		var newShip = gamedata.getShipByType(copiedShip.phpclass);
		if (!newShip) {
			//Loaded fleets may not have their faction set yet when editing, so do this now.
			//A copy at the BARE hull's cost, same reasoning as in doEditShip: copiedShip's pointCost
			//includes its enhancements, and what is registered here becomes the blueprint every
			//later lookup of this class finds.
			var standIn = jQuery.extend({}, copiedShip);
			standIn.pointCost = gamedata.getPristinePointCost(copiedShip);
			/* REINFORCEMENTS_PLAN.md §4 Stage 1 - and STRIP THE REINFORCEMENT FLAG off the
			   stand-in. setShipsFromFaction runs every entry through new Ship(json), whose ctor
			   copies EVERY key onto the instance, so whatever is registered here becomes the
			   blueprint that every later getShipByType of this class returns - and an ad-hoc
			   reinforcement:true would then be minted onto every FUTURE purchase of the class,
			   silently. Exactly the hazard the pointCost line above exists for, and this branch
			   fires only on a LOADED fleet, which is precisely the reinforcement-heavy case. */
			delete standIn.reinforcement;
			gamedata.setShipsFromFaction(copiedShip.faction, [standIn]);
			newShip = gamedata.getShipByType(copiedShip.phpclass);
		}

		newShip.name = copiedShip.name;
		newShip.pointCost = copiedShip.pointCost;
		newShip.flightSize = copiedShip.flightSize;
		//REINFORCEMENTS_PLAN.md §4 Stage 1: a copy starts in the same place as its original,
		//front-line or hyperspace. newShip is a fresh blueprint clone and carries nothing.
		newShip.reinforcement = Boolean(copiedShip.reinforcement);
		//Rows copied, not just the outer array - see cloneEnhancementOptions. Copying a ship
		//and then changing the copy's enhancements used to rewrite the original's counts too.
		newShip.enhancementOptions = gamedata.cloneEnhancementOptions(copiedShip);
		//Pre-battle damage (§5.3): a copy starts equally damaged. DEEP clone - sharing the
		//payload object would make editing either ship edit both.
		if (window.battleDamage) {
			newShip.preBattleDamage = battleDamage.clone(copiedShip.preBattleDamage);
		}
		//Per-system refits (WEAPON_ENHANCEMENTS_PLAN.md §5.2): a copy starts equally refitted,
		//and DEEP-cloned for the same reason - every write replaces a row in place, so a shared
		//array would make editing one copy's refits edit the other's. newShip's systems are a
		//fresh blueprint clone, so apply() paints them onto objects the original does not share.
		if (window.systemEnhancements) {
			newShip.pointCostSysEnh = copiedShip.pointCostSysEnh || 0;
			newShip.systemEnhancements = systemEnhancements.clone(copiedShip.systemEnhancements);
			systemEnhancements.apply(newShip);
		}

		// Copy ammo counts
		if (newShip.flight && copiedShip.flight) {
			for (var i in newShip.systems) {
				if (copiedShip.systems[i]) {
					var fighter = newShip.systems[i];
					var copiedFighter = copiedShip.systems[i];
					for (var j in fighter.systems) {
						if (copiedFighter.systems[j]) {
							var weapon = fighter.systems[j];
							var copiedWeapon = copiedFighter.systems[j];
							if (weapon.missileArray && copiedWeapon.missileArray) {
								for (var k in weapon.missileArray) {
									if (copiedWeapon.missileArray[k]) {
										weapon.missileArray[k].amount = copiedWeapon.missileArray[k].amount;
									}
								}
							}
						}
					}
				}
			}
		}

		$(".confirm").remove();

		window.confirm.showShipEdit(newShip, gamedata.doCopyShip, 'copy');
	},


	doCopyShip: function doCopyShip() {
		var ship = $(this).data().ship;

		if ($(".confirm .totalUnitCostAmount").length > 0) {
			ship.pointCost = $(".confirm .totalUnitCostAmount").data("value");
		}
		var newPointCost = ship.pointCost;

		if (!gamedata.canAfford(ship)) {
			$(".confirm").remove();
			window.confirm.error("You cannot afford this ship!", function () { });
			return;
		}

		//Pre-battle damage (§5.3): the line below rebuilds the ship from its blueprint, so
		//carry the payload across the rebuild (deep-cloned by battleDamage.clone).
		//The size that must not change under the payload is flightSize for a flight and
		//bulkBuy for a bulk purchase - both key their damage by ordinal. Asked of
		//battleDamage rather than named here, so this and onShipRebuilt cannot pick
		//different fields for a unit that is both (the flight-shaped MineClass customs).
		var copiedFlightSize = window.battleDamage ? battleDamage.ordinalCount(ship) : null;
		var copiedDamage = window.battleDamage ? battleDamage.clone(ship.preBattleDamage) : null;
		//REINFORCEMENTS_PLAN.md §4 Stage 1 - captured for exactly the reason the damage above is:
		//the next line REBUILDS the ship from its blueprint, which carries no ad-hoc property, so
		//the flag copyShip set on this object is about to be thrown away.
		var copiedReinforcement = Boolean(ship.reinforcement);

		ship = gamedata.getShipByType(ship.phpclass); //Faction already set if not already when we called copyShip()

		var name = $(".confirm input[name=shipname]").val();
		ship.name = name;
		ship.pointCost = newPointCost;
		ship.userid = gamedata.thisplayer;
		ship.reinforcement = copiedReinforcement;
		if (copiedDamage) ship.preBattleDamage = copiedDamage;

		if (ship.flight) {
			var flightSize = $(".fighterAmount").html();
			if (!flightSize) {
				flightSize = 1;
			}
			ship.flightSize = Math.floor(flightSize);
		}

		//do note enhancements bought (if any)
		var enhNo = 0;
		var noTaken = 0;
		var target = $(".selectAmount.shpenh" + enhNo);
		while (typeof target.data("enhPrice") != 'undefined') { //as long as there are enhancements defined...
			noTaken = target.data("count");
			if (noTaken > 0) { //enhancement picked - note!
				ship.enhancementOptions[enhNo][2] = noTaken;
				if (!ship.enhancementOptions[enhNo][6]) { //this is an actual enhancement (as opposed to option) - note value!
					if (ship.flight) {
						ship.pointCostEnh += target.data("enhCost") * flightSize;
					} else {
						ship.pointCostEnh += target.data("enhCost");
					}
				} else { //this is an option - still note value, just separately!
					if (ship.flight) {
						ship.pointCostEnh2 += target.data("enhCost") * flightSize;
					} else {
						ship.pointCostEnh2 += target.data("enhCost");
					}
				}
			}
			//go to next enhancement
			enhNo++;
			target = $(".selectAmount.shpenh" + enhNo);
		}

		if ($(".confirm .selectAmount").length > 0) {
			if (ship.flight) {

				// and get the amount of launchers on a fighter
				var nrOfLaunchers = 0;

				for (var j in ship.systems[1].systems) {
					var fighterSystem = ship.systems[1].systems[j];

					if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
						nrOfLaunchers++;
					}
				}

				// get all selections of missiles
				var missileOptions = $(".confirm .selectAmount");

				for (var k = 0; k < missileOptions.length; k++) {
					var firingMode = $(missileOptions[k]).data("firingMode");

					// divide the bought missiles over the missileArrays
					var boughtAmount = $(".confirm .selectAmount." + firingMode).data("value");

					// perLauncher should always get you an integer as result. The UI handles
					// buying of missiles that way.
					var perLauncher = boughtAmount;

					for (var i in ship.systems) {
						var fighter = ship.systems[i];

						for (var j in fighter.systems) {
							var fighterSystem = fighter.systems[j];

							if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
								// find the correct index, depending on the firingMode
								for (var index in fighterSystem.firingModes) {
									if (fighterSystem.firingModes[index] == firingMode) {
										fighterSystem.missileArray[index].amount = perLauncher;
									}
								}
							}
						}
					}
				}
			} else { }
		}

		//Pre-battle damage (§5.3): render the carried payload onto the freshly built ship,
		//and RESHAPE it if the copy was made at a different flight size / bulk count -
		//a smaller copy drops the ordinals it no longer has, a larger one pads from #1.
		var copyDamageDiscarded = window.battleDamage
			&& battleDamage.onShipRebuilt(ship, copiedFlightSize);

		$(".confirm").remove();
		gamedata.updateFleet(ship);

		if (copyDamageDiscarded) {
			confirm.warning("Size reduced - pre-battle damage on the units above the new size was not copied.");
		}
		//gamedata.populateFleetDropdown();
	},


	editShip: function editShip(ship) {
		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);
		if (selectedSlot.lastphase >= "-2") {
			window.confirm.error("You have already readied your fleet!", function () { });
			return false;
		}

		$(".confirm").remove();

		//A bulk row is a whole PURCHASE - quantity plus the enhancements every unit in it
		//carries - so it goes to the bulk dialog. The ship dialog has no quantity control,
		//and its "Total cost" would read as one unit's while the fleet is charged for N.
		if (gamedata.isBulkRow(ship)) {
			window.confirm.showBuyBulk(ship, gamedata.doEditBulk, true);
			return;
		}

		window.confirm.showShipEdit(ship, gamedata.doEditShip);
	},

	doEditShip: function doEditShip() {
		var ship = $(this).data().ship;
		var originalShipData = $(this).data().originalShipData; //Fetch original data before edits?

		//Captured BEFORE pointCost is overwritten with the dialog's total - by then it is no longer
		//the bare hull's, and the no-blueprint stand-in below needs the bare one.
		var pristinePointCost = gamedata.getPristinePointCost(ship);

		if ($(".confirm .totalUnitCostAmount").length > 0) {
			/* The dialog totals base + the SHIP-LEVEL enhancements it renders. Per-system refits
			   are bought from the ship window and have no row in it, so they have to be added
			   back on - otherwise this line silently REFUNDS every refit while leaving it applied,
			   which is the whole reason D5 makes them a third bucket. Added before the
			   affordability test below, so the check prices what the player will actually pay. */
			ship.pointCost = $(".confirm .totalUnitCostAmount").data("value") + (ship.pointCostSysEnh || 0);
		}
		var newPointCost = ship.pointCost;

		if (!gamedata.canAffordEdit(ship)) {
			//Reset the relevant info on ship before exiting Edit window.
			ship.name = originalShipData.name;
			ship.pointCost = originalShipData.pointCost;
			ship.flightSize = originalShipData.flightSize;
			ship.enhancementOptions = originalShipData.enhancementOptions ? [...originalShipData.enhancementOptions] : [],
				ship.pointCostEnh = originalShipData.pointCostEnh;
			ship.pointCostEnh2 = originalShipData.pointCostEnh2;
			ship.pointCostSysEnh = originalShipData.pointCostSysEnh;
			if (window.systemEnhancements) {
				ship.systemEnhancements = systemEnhancements.clone(originalShipData.systemEnhancements);
			}
			$(".confirm").remove();
			window.confirm.error("You cannot afford those edits!", function () { });
			return;
		}

		//Remove old ship from Fleet List first
		var id = ship.id;
		for (var i in gamedata.ships) {
			if (gamedata.ships[i].id == id) {
				delete gamedata.ships[i];
				break;
			}
		}
		$('.ship.bought.shipid_' + id).remove();

		var baseShip = gamedata.getShipByType(ship.phpclass);
		if (!baseShip) {
			//Loaded fleets may not have their faction set yet when editing, so do this now.
			//Register a COPY carrying the BARE hull's cost, not `ship` itself: `ship` is holding the
			//dialog's total by this point, and whatever is registered here becomes the blueprint
			//every later lookup of this class finds - including the next edit of this same ship,
			//which would then take an inflated cost as its baseline and double-count all over again.
			var standIn = jQuery.extend({}, ship);
			standIn.pointCost = pristinePointCost;
			/* REINFORCEMENTS_PLAN.md §4 Stage 1 - and STRIP THE REINFORCEMENT FLAG off the
			   stand-in. setShipsFromFaction runs every entry through new Ship(json), whose ctor
			   copies EVERY key onto the instance, so whatever is registered here becomes the
			   blueprint that every later getShipByType of this class returns - and an ad-hoc
			   reinforcement:true would then be minted onto every FUTURE purchase of the class,
			   silently. Exactly the hazard the pointCost line above exists for, and this branch
			   fires only on a LOADED fleet, which is precisely the reinforcement-heavy case. */
			delete standIn.reinforcement;
			gamedata.setShipsFromFaction(ship.faction, [standIn]);
			baseShip = gamedata.getShipByType(ship.phpclass);
		}

		ship.systems = baseShip.systems; //reset systems to default to default values
		ship.notes = baseShip.notes; //reset notes to default to default values
		ship.forwardDefense = baseShip.forwardDefense;
		ship.sideDefense = baseShip.sideDefense;
		//Stage 3 lobbyEnhancements review: EVERY enhancement-mutated ship-level stat
		//must return to its blueprint value before re-applying, or enhancements kept
		//through an edit compound on each pass (ELITE_CREW ini/crit/to-hit, IPSH_EETH
		//turn delay, MINE_SIGN signature, ELITE_SW pivot, ... were never reset).
		ship.iniativebonus = baseShip.iniativebonus;
		ship.critRollMod = baseShip.critRollMod;
		ship.toHitBonus = baseShip.toHitBonus;
		ship.turncost = baseShip.turncost;
		ship.turndelaycost = baseShip.turndelaycost;
		ship.pivotcost = baseShip.pivotcost;
		ship.signature = baseShip.signature;
		ship.detectedSignature = baseShip.detectedSignature;
		ship.IFFSystem = baseShip.IFFSystem;

		//Now clear enhancements markers, so these get updated again when ship window next opened.
		if (ship.flight) {
			ship.freethrust = baseShip.freethrust;
			ship.hasNavigator = baseShip.hasNavigator;
			ship.offensivebonus = baseShip.offensivebonus;
			lobbyEnhancements.resetEnhancementMarkersFighter(ship);
		} else {
			lobbyEnhancements.resetEnhancementMarkersShip(ship);
		}

		var name = $(".confirm input[name=shipname]").val();
		ship.name = name;
		ship.pointCost = newPointCost;
		ship.pointCostEnh = 0;
		ship.pointCostEnh2 = 0;
		ship.userid = gamedata.thisplayer;

		if (ship.flight) {
			var flightSize = $(".fighterAmount").html();
			if (!flightSize) {
				flightSize = 1;
			}
			ship.flightSize = Math.floor(flightSize);
		}

		//do note enhancements bought (if any)
		var enhNo = 0;
		var nowTaken = 0;
		var target = $(".selectAmount.shpenh" + enhNo);
		while (typeof target.data("enhPrice") != 'undefined') { //as long as there are enhancements defined...
			nowTaken = target.data("count");
			if (nowTaken > 0) { //enhancement picked - note!
				ship.enhancementOptions[enhNo][2] = nowTaken;
				if (!ship.enhancementOptions[enhNo][6]) { //this is an actual enhancement (as opposed to option) - note value!
					if (ship.flight) {
						ship.pointCostEnh += target.data("enhCost") * flightSize;
					} else {
						ship.pointCostEnh += target.data("enhCost");
					}
				} else { //this is an option - still note value, just separately!
					if (ship.flight) {
						ship.pointCostEnh2 += target.data("enhCost") * flightSize;
					} else {
						ship.pointCostEnh2 += target.data("enhCost");
					}
				}
			} else {
				ship.enhancementOptions[enhNo][2] = 0;
			}
			//go to next enhancement
			enhNo++;
			target = $(".selectAmount.shpenh" + enhNo);
		}

		if ($(".confirm .selectAmount").length > 0) {
			if (ship.flight) {

				// and get the amount of launchers on a fighter
				var nrOfLaunchers = 0;

				for (var j in ship.systems[1].systems) {
					var fighterSystem = ship.systems[1].systems[j];

					if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
						nrOfLaunchers++;
					}
				}

				// get all selections of missiles
				var missileOptions = $(".confirm .selectAmount");

				for (var k = 0; k < missileOptions.length; k++) {
					var firingMode = $(missileOptions[k]).data("firingMode");

					// divide the bought missiles over the missileArrays
					var boughtAmount = $(".confirm .selectAmount." + firingMode).data("value");

					// perLauncher should always get you an integer as result. The UI handles
					// buying of missiles that way.
					var perLauncher = boughtAmount;

					for (var i in ship.systems) {
						var fighter = ship.systems[i];

						for (var j in fighter.systems) {
							var fighterSystem = fighter.systems[j];

							if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
								// find the correct index, depending on the firingMode
								for (var index in fighterSystem.firingModes) {
									if (fighterSystem.firingModes[index] == firingMode) {
										fighterSystem.missileArray[index].amount = perLauncher;
									}
								}
							}
						}
					}
				}
			} else { }
		}

		//Stage 3: single enhancement entry point (markers + apply flag were reset
		//above, so this re-applies the kept/changed enhancements to the rebuilt ship)
		lobbyEnhancements.apply(ship);

		//Pre-battle damage (§5.3): the edit above replaced ship.systems wholesale from the
		//blueprint, so the preview has to be painted onto the new objects. A flight whose
		//SIZE changed - or a bulk row whose COUNT changed - is reshaped rather than wiped:
		//shrinking drops the ordinals that no longer exist, growing pads the new ones from
		//#1. Only the shrink is reported, because only a shrink loses anything.
		var previousSize = (originalShipData && window.battleDamage)
			? battleDamage.ordinalCount(originalShipData) : null;
		var damageDiscarded = window.battleDamage
			&& battleDamage.onShipRebuilt(ship, previousSize);

		/* Per-system refits (WEAPON_ENHANCEMENTS_PLAN.md §5.2). `ship.systems` was replaced
		   wholesale from the blueprint above, so the refits have to be painted back onto the new
		   objects - after lobbyEnhancements.apply, because a refit stacks ON TOP of ELITE_CREW's
		   thruster bump (its PRICE is pinned to the blueprint independently, per D10).
		   The phpclass cannot change in an edit, so the stored systemids stay valid and the rows
		   are carried across; apply()'s own name check drops anything that does not line up.
		   Then the D11 sweep, which has to run AFTER onShipRebuilt has repainted the damage
		   preview - that is what performs the structure cascade. */
		var refitsDropped = [];
		if (window.systemEnhancements) {
			systemEnhancements.apply(ship);
			var sysEnhBefore = ship.pointCostSysEnh || 0;
			refitsDropped = systemEnhancements.dropDestroyed(ship);
			//pointCost had the OLD refit total folded in; take the refunded difference back off.
			if (refitsDropped.length) ship.pointCost -= (sysEnhBefore - (ship.pointCostSysEnh || 0));
		}

		//The React window renders from this same mutated ship object, so no
		//destroy/rebuild dance is needed - just re-render if it is open.
		var wasVisible = window.shipWindowManagerReact
			&& window.shipWindowManagerReact.ships.indexOf(ship) !== -1;

		$(".confirm").remove();
		gamedata.updateFleet(ship);

		if (wasVisible) {
			window.shipWindowManagerReact.update();
		}

		if (damageDiscarded) {
			confirm.warning("Size reduced - pre-battle damage on the units above the new size has been discarded.");
		}
		if (refitsDropped.length) {
			confirm.warning(systemEnhancements.describeRemoved(refitsDropped));
		}
		//gamedata.populateFleetDropdown();
	},


	/*The hull's PRISTINE point cost - what it costs with nothing bought on it.

	  Normally that is simply the blueprint's, but gamedata.allShips is filled LAZILY, one faction
	  at a time, as the player expands that faction in the store (onFactionClicked -> parseShips).
	  Loading a saved fleet never triggers that, so a loaded ship routinely has no blueprint at all -
	  which is what the "Loaded fleets may not have their faction set yet" fallbacks below are about.

	  With no blueprint, derive it from the ship rather than falling back to ship.pointCost. Every
	  path that writes pointCost folds the enhancement cost INTO it - doBuyShip and doEditShip store
	  the buy dialog's total, doLoadFleet adds the saved enhvalue back on - and pointCostEnh /
	  pointCostEnh2 hold exactly that folded-in amount, so subtracting them is correct on a bought,
	  loaded or already-edited ship alike.

	  This is not cosmetic. The edit dialog totals as base + enhancements, so a "base" that already
	  contains the enhancements counts them twice: saved fleet 55 (Primus, hull 830 + 85 of
	  enhancements = 915) opened its edit window at 1000 and cost 1000 if accepted unchanged.*/
	getPristinePointCost: function getPristinePointCost(ship) {
		var blueprint = gamedata.getShipByType(ship.phpclass);
		if (blueprint) return blueprint.pointCost;

		//All THREE buckets are peeled: pointCostSysEnh is folded into pointCost by the same
		//paths as the other two (WEAPON_ENHANCEMENTS_PLAN.md D5), so leaving it in would make
		//"pristine" contain the refits and count them twice on the next edit.
		var base = ship.pointCost - (ship.pointCostEnh || 0) - (ship.pointCostEnh2 || 0) - (ship.pointCostSysEnh || 0);
		//a flight's pointCost is scaled to the size it was bought or loaded at (doBuyShip,
		//doLoadFleet), while the blueprint cost this stands in for is always the six-craft one
		if (ship.flight && ship.flightSize > 0) base = (base / ship.flightSize) * 6;
		return Math.round(base);
	},

	getShipByType: function getShipByType(type) {

		for (var race in gamedata.allShips) {
			for (var i in gamedata.allShips[race]) {
				var ship = gamedata.allShips[race][i];

				if (ship.phpclass == type) {
					var shipRet = jQuery.extend(true, {}, ship);

					// to avoid two different flights pointing to the
					// same fighter object, also extend each fighter
					// individually. (This solves the bug of setting
					// missile amounts, that suddenly are set for all
					// the fighters of the same type.)
					for (var i in shipRet.systems) {
						shipRet.systems[i] = jQuery.extend(true, {}, ship.systems[i]);

						if (shipRet.flight) {
							// in case of a flight, also do the systems of the fighters
							for (var j in shipRet.systems[i].systems) {
								shipRet.systems[i].systems[j] = jQuery.extend(true, {}, ship.systems[i].systems[j]);
							}
						} else {
							// to avoid problems with ammo and normal ships, also do the
							// ship systems

						}
					}

					return shipRet;
				}
			}
		}

		return null;
	},

	onReadyClicked: function onReadyClicked() {
		var points = gamedata.calculateFleet();

		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);
		var slotElement = $('.slot.slotid_' + selectedSlot.slot);

		if (selectedSlot.lastphase >= "-2") {
			window.confirm.error("You have already confirmed your fleet for this game!", function () { });
			return;
		}

		if (points == 0) {
			window.confirm.error("You have to buy at least one ship!", function () { });
			return;
		}

		// Fleet Test Check
		if (gamedata.rules && gamedata.rules.fleetTest === 1) {
			window.confirm.error("You cannot Ready up in a Fleet test game!", function () { });
			return;
		}

		//Pre-battle damage (D1): no rule gates it, so the Ready confirm is where a fleet
		//carrying damaged or crippled units says so out loud. confirm.confirm renders HTML.
		var readyMessage = "Are you sure you wish to ready your fleet?";
		if (window.battleDamage && battleDamage.fleetHasDamage()) {
			//Only WARNING: is the warning; the rest is ordinary body text, so it carries its
			//own class (see confirm.css .prebattle-note).
			readyMessage += '<span class="prebattle-note">'
				+ '<span class="prebattle-note-label">WARNING:</span> '
				+ 'This fleet includes units with pre-battle damage and/or critical effects.'
				+ '</span>';
		}

		//REINFORCEMENTS_PLAN.md §2.1 - a reinforcement group with no way in. Same note styling
		//as the pre-battle warning above, and deliberately a WARNING the player confirms rather
		//than a refusal: an ally's jump gate would make the fleet perfectly legal, and a lobby
		//client cannot see one (it is served no ships at all).
		readyMessage += gamedata.readyReinforcementWarning();

		// Pass the submission function as a callback, not invoke it immediately
		confirm.confirm(readyMessage, function () {
			selectedSlot.lastphase = -2;
			ajaxInterface.submitGamedata();
			slotElement.addClass("ready");
			ajaxInterface.startPollingGamedata();
		});

	},

	onLeaveClicked: function onLeaveClicked() {

		var safeToLeave = true;

		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			if (slot.playerid !== null && slot.playerid !== gamedata.thisplayer) safeToLeave = false;
		}

		if (!safeToLeave) {
			var mySlots = gamedata.getMySlots();
			for (var i in mySlots) {
				var slot = mySlots[i];
				if (slot.lastphase >= "-2") {
					window.confirm.error("You have already confirmed a fleet for this game, you cannot now leave!", function () { });
					return;
				} else {
					window.location = "gamelobby.php?gameid=" + gamedata.gameid + "&leave=true";
				}
			}
		} else {
			window.location = "gamelobby.php?gameid=" + gamedata.gameid + "&leave=true";
		}

	},

	//The save half now lives in client/savedFleets.js so game.php can drive it too
	//(PREBATTLE_DAMAGE_PLAN.md §7.1). The load/dropdown/delete UI stays here, where its
	//cachedFleets / fleetDropdownList / fleetDropdownButton closures are.
	onSaveClicked: function onSaveClicked() {
		savedFleets.saveCurrentFleet();
	},

	doSaveFleet: function doSaveFleet() {
		savedFleets.doSaveCurrentFleet();
	},

	//Called by savedFleets after a successful save so the dropdown reflects the new fleet.
	refreshSavedFleets: function refreshSavedFleets() {
		ajaxInterface.getSavedFleets(function (fleets) {
			cachedFleets = fleets;
			gamedata.populateFleetDropdown(cachedFleets);
		});
	},
	/*
		filterSavedFleet: function filterSavedFleet(cachedFleets) {
				const slot = playerManager.getSlotById(gamedata.selectedSlot);
				if(slot){ //sometimes slot hasn't been selected yet.
					var slotPoints = slot.points ?? 0;
					var spentPoints = 0;
					for (var i in gamedata.ships) {
						var lship = gamedata.ships[i];
						if (lship.slot != gamedata.selectedSlot) continue;
						spentPoints += lship.pointCost;
					}
					const pointsAvailable = slotPoints - spentPoints;
	
					const filtered = cachedFleets.filter(fleet => fleet.points <= pointsAvailable);
					return filtered;
				}else{
					return cachedFleets;				
				}	
		},		
	*/
	// Populate dropdown list
	populateFleetDropdown: function populateFleetDropdown() {
		//The saved-fleet menu under Load a Fleet. Its look is gameLobby.css's .lb-fleetmenu-* (Stage 5:
		//the page's dark window style, where it used to be white rows styled inline).
		fleetDropdownList.innerHTML = '';

		//let filteredFleets = gamedata.filterSavedFleet(cachedFleets);

		if (!cachedFleets || cachedFleets.length === 0) {
			const empty = document.createElement('div');
			empty.className = 'lb-fleetmenu-note';
			empty.textContent = 'No saved fleets available';
			fleetDropdownList.appendChild(empty);
			return;
		}

		// Split fleets into user and default
		const userFleets = cachedFleets.filter(f => f.userid !== 0);
		const defaultFleets = cachedFleets.filter(f => f.userid === 0);

		// Helper to render a fleet item
		const renderFleetItem = (fleet) => {
			const item = document.createElement('div');
			item.className = 'lb-fleetmenu-item' + (fleet.userid === 0 ? ' lb-fleetmenu-item--default' : '');

			// ✅ Load fleet if you click anywhere on item (except lock/delete)
			//Pre-battle damage (D3): the confirm carries a checkbox per kind of saved
			//state this fleet actually holds, each defaulted on.
			item.addEventListener('click', () => {
				confirm.showLoadFleet(fleet.name, { hasDamage: fleet.hasDamage, hasCrits: fleet.hasCrits }, (choices) => {
					gamedata.loadSavedFleet(fleet.id, choices);
					fleetDropdownList.style.display = 'none';
					fleetDropdownButton.textContent = 'LOAD A FLEET';
				});
			});

			// Padlock - its own clickable area (public / private)
			const lockSpan = document.createElement('span');
			lockSpan.className = 'lb-fleetmenu-lock ' + (fleet.isPublic ? 'is-public fa-solid fa-unlock' : 'fa-solid fa-lock');
			lockSpan.title = fleet.isPublic ? 'Public - click to make it private' : 'Private - click to make it public';

			lockSpan.addEventListener('click', (e) => {
				e.stopPropagation();
				const newStatus = fleet.isPublic ? 0 : 1;
				confirm.confirm(
					"Are you sure you wish to change this fleet's availability?",
					() => gamedata.changeFleetPublic(fleet.id, newStatus)
				);
			});

			// Fleet name, and a player's fleet's #id (what Load Fleet by #ID takes)
			const nameSpan = document.createElement('span');
			nameSpan.className = 'lb-fleetmenu-name';
			nameSpan.textContent = fleet.name;
			if (fleet.userid !== 0) {
				const idSpan = document.createElement('span');
				idSpan.className = 'lb-fleetmenu-id';
				idSpan.textContent = '#' + fleet.id;
				nameSpan.appendChild(idSpan);
			}

			//Pre-battle damage: badge a fleet that carries battle damage and/or critical
			//effects, so the state is visible before the load dialog asks about it.
			let damageSpan = null;
			if (fleet.hasDamage || fleet.hasCrits) {
				damageSpan = document.createElement('span');
				//Same icon as gamedata.damagedShipBadge, so the dropdown and the fleet list
				//read as one idea - change BOTH or neither.
				damageSpan.className = 'lb-fleetmenu-damage fa-solid fa-screwdriver-wrench';
				damageSpan.title = fleet.hasDamage && fleet.hasCrits
					? 'Carries battle damage and critical effects'
					: (fleet.hasDamage ? 'Carries battle damage' : 'Carries critical effects');
			}
			// Points
			const pointsSpan = document.createElement('span');
			pointsSpan.className = 'lb-fleetmenu-points';
			pointsSpan.textContent = `${fleet.points} pts`;

			if (fleet.userid !== 0) item.appendChild(lockSpan);
			item.appendChild(nameSpan);
			if (damageSpan) item.appendChild(damageSpan);
			item.appendChild(pointsSpan);

			// Delete button (only for non-default fleets)
			if (fleet.userid !== 0) {
				const deleteBtn = document.createElement('span');
				deleteBtn.className = 'lb-fleetmenu-delete';
				deleteBtn.textContent = '✖';
				deleteBtn.title = 'Delete this saved fleet';

				deleteBtn.addEventListener('click', (e) => {
					e.stopPropagation();
					confirm.confirm(
						"Are you sure you wish to delete this saved fleet?",
						() => gamedata.deleteSavedFleet(fleet.id, fleet.name)
					);
				});
				item.appendChild(deleteBtn);
			}

			fleetDropdownList.appendChild(item);
		};

		// Render user fleets first
		userFleets.forEach(renderFleetItem);

		// Then the default fleets (no lock, no delete), under their own heading
		if (defaultFleets.length > 0) {
			const divider = document.createElement('div');
			divider.className = 'lb-fleetmenu-head';
			divider.textContent = 'Default fleets';
			fleetDropdownList.appendChild(divider);

			defaultFleets.forEach(renderFleetItem);
		}
	},

	checkFleetCost: function checkFleetCost(listId) {
		var pointsAvailable = 0;

		const slot = playerManager.getSlotById(gamedata.selectedSlot);
		const fleet = cachedFleets.find(f => f.id === listId);

		if (slot) { //sometimes slot hasn't been selected yet.
			//getMaxPoints, not slot.points: a Fleet Builder slot is always unlimited, and
			//the typed cap is what the rest of the panel is already enforcing.
			var slotPoints = gamedata.getMaxPoints();

			if (slotPoints === -1) return true; // Unlimited points

			pointsAvailable = slotPoints - gamedata.fleetCost();
		}
		if (!fleet) return false;
		if (fleet.points > pointsAvailable) {
			return false;
		} else {
			return true;
		}
	},


	//`choices` (optional) = {includeDamage, includeCriticals} from the load confirm (D3).
	loadSavedFleet: function loadSavedFleet(listId, choices) {

		var canAfford = gamedata.checkFleetCost(listId);

		if (canAfford) {

			ajaxInterface.loadSavedFleet(listId, choices || {}, function (response) {
				//console.log("AJAX response:", ships); // debug raw response

				if (response.ships && Array.isArray(response.ships) && response.ships.length > 0) {
					gamedata.doLoadFleet(response.ships, response.critDesc, response.critTransient, response.systemEnhancementNotice);
					fleetDropdownButton.textContent = 'LOAD A FLEET';
					//confirm.warning("Fleet loaded!");
				} else {
					console.error("Load failed:", response.ships);
					confirm.fleetNotice("That fleet could not be loaded.");
				}
			});
		} else {
			confirm.fleetNotice("You cannot afford this fleet.");
			return;
		}
	},

	loadSavedFleetById: function loadSavedFleetById(listId) {
		//A fleet loaded by typed ID is not in cachedFleets, so whether it carries damage
		//or criticals is unknown here - showLoadFleet offers both boxes, and a flag for a
		//kind the fleet does not have is simply a no-op server-side.
		confirm.showLoadFleet("saved fleet with #ID " + listId, {}, (choices) => {
			gamedata.doLoadSavedFleetById(listId, choices);
			fleetDropdownList.style.display = 'none';
			fleetDropdownButton.textContent = 'LOAD A FLEET';
		});
	},


	doLoadSavedFleetById: function doLoadSavedFleetById(listId, choices) {
		ajaxInterface.loadSavedFleet(listId, choices || {}, function (response) {
			//console.log("AJAX response:", response.ships); // debug raw response
			if (response.list && !response.list.isPublic && response.list.userid !== gamedata.thisplayer) {
				confirm.fleetNotice("That fleet was not shared by its owner, so it cannot be loaded.");
				return;
			}

			//Need to add a check here of points here as it's not checked via Saved Fleet List, and return error if it's over what's allowed.
			//Same cap and same fleet-cost sum as the buy panel (getMaxPoints/fleetCost), so
			//a Fleet Builder limit applies to a fleet loaded by #ID too.
			const maxPoints = gamedata.getMaxPoints();
			const pointsAvailable = maxPoints - gamedata.fleetCost();
			if (response.list && pointsAvailable < response.list.points) {
				if (maxPoints !== -1) { // Unlimited points
					confirm.fleetNotice("Not enough points available for this fleet (" + response.list.points + "pts needed).");
					return;
				}
			}

			if (response.ships && Array.isArray(response.ships) && response.ships.length > 0) {
				gamedata.doLoadFleet(response.ships, response.critDesc, response.critTransient, response.systemEnhancementNotice);
				fleetDropdownButton.textContent = 'LOAD A FLEET';
				//confirm.warning("Fleet loaded!");
			} else {
				if (response.ships) console.error("Load failed:", response.ships);
				confirm.fleetNotice("No fleet found with that ID.");
			}
		});
	},

	/* sysEnhNotice: strings the server produced while re-validating this fleet's per-system
	   refits against the CURRENT blueprint (WEAPON_ENHANCEMENTS_PLAN.md §4.7.1). Usually
	   empty; reported once, after the load, because a silent point change on a loaded fleet
	   is the kind of thing that gets noticed three battles later. */
	doLoadFleet: function doLoadFleet(fleet, critDesc, critTransient, sysEnhNotice) {
		if (!Array.isArray(fleet)) {
			console.error("doLoadFleet: expected array, got", fleet);
			return;
		}

		/* A saved fleet outlives the game it was saved from, so it can carry units this scenario
		   does not allow. Those units are LEFT OUT, one by one - the rest of the fleet still loads -
		   and one window says what was left out (showLeftOutNotice); nothing loads only when nothing
		   is left. Leaving units out only lowers the fleet's cost, so the caller's affordability
		   check still holds. Checked here because doLoadFleet is the one funnel both load paths
		   (dropdown and load-by-#ID) come through.
		   - Mines, without the 'Allow Mines' rule (constructStore skips mines on the same test).
		     This used to refuse the WHOLE fleet; per unit since 2026-09-25 (user), like the ISD.
		   - In-Service Date (Stage 6): a numeric ISD above the game's cutoff - the Store's own
		     test (applyCustomShipFilter), so a unit loads exactly when it could have been bought,
		     and an ISD of 0 or text ("Ancient") always passes. */
		var minesBarred = !!(gamedata.rules && !gamedata.rules.allowMines && !gamedata.rules.fleetTest);
		var minesLeftOut = false;
		var tooLate = [];
		fleet = fleet.filter(function (listShip) {
			if (!listShip) return true; // holes are skipped below
			if (minesBarred && listShip.mine) {
				minesLeftOut = true;
				return false;
			}
			if (gamedata.inServiceDate && parseInt(listShip.isd, 10) > gamedata.inServiceDate) {
				tooLate.push(listShip);
				return false;
			}
			return true;
		});

		if (minesLeftOut || tooLate.length) {
			var loadsNothing = !fleet.some(function (listShip) { return !!listShip; });
			gamedata.showLeftOutNotice(minesLeftOut, tooLate, loadsNothing);
			if (loadsNothing) return;
		}

		//Pre-battle damage (D3): kinds this fleet HAD that the player chose not to load.
		//Reported once, after the load, so a mis-click is obvious rather than silent.
		var declinedDamage = false;
		var declinedCriticals = false;

		for (var i = 0; i < fleet.length; i++) {
			var listShip = fleet[i];
			if (!listShip) continue; // skip holes

			var ship = new Ship(listShip);

			// make sure these are present and are the correct type
			ship.userid = parseInt(gamedata.thisplayer, 10);
			ship.slot = parseInt(gamedata.selectedSlot, 10);
			ship.loaded = true;
			/* REINFORCEMENTS_PLAN.md §0 - A SAVED FLEET *DOES* REMEMBER REINFORCEMENT STATUS
			   (user request 2026-08-28), and this is where it comes back. tac_saved_ship carries
			   the flag, DBManager::getSavedShips reads it onto the ship and it rides the
			   loadSavedFleet.php payload as an ordinary public property.

			   ⚠️ GATED ON THE RULE, so a fleet saved from a game that HAD Allow Reinforcements
			   still loads entirely front-line into one that does not. Without this the flag would
			   sit on rows that no group header, no Reinforce link and no server-side reader would
			   ever act on - invisible in the lobby, and dropped at buy time by
			   BuyingGamePhase::process, which checks the rule before believing the claim.

			   Written explicitly rather than left to the `new Ship(listShip)` copy so the property
			   exists (as a real boolean) on every lobby ship object and isReinforcementRow never
			   has to test for its absence.

			   Only the purchase-time flag is restored: arrivalTurn/arrivalVia are in-play state
			   and are never saved, so a reloaded reinforcement is back in hyperspace exactly as a
			   freshly bought one is. */
			ship.reinforcement = gamedata.reinforcementsAllowed() && Boolean(listShip.reinforcement);

			if (ship.flight) {
				// preserve original indexing for fighters
				for (let j = 0; j < ship.systems.length; j++) {
					if (ship.systems[j] === null || ship.systems[j] === undefined) {
						delete ship.systems[j]; // leaves hole, preserves indices
					}
				}
			} else {
				// regular ships — safe to reindex		
				ship.systems = ship.systems.filter(sys => sys !== null && sys !== undefined);
			}

			if (ship.flight) {
				ship.pointCost = (ship.pointCost / 6) * ship.flightSize;
			}

			//pointCost is a SINGLE unit's cost with enhancements folded in (see
			//rowPointCost), and a saved row stores the two apart - so add them back.
			//For a bulk row that is the PER-UNIT figure; rowPointCost multiplies up.
			if (ship.pointCostEnh !== 0) {
				ship.pointCost = ship.pointCost + ship.pointCostEnh;
			}
			/* The third bucket (WEAPON_ENHANCEMENTS_PLAN.md D5). Kept separate from
			   pointCostEnh above rather than folded into the stored enhvalue, because
			   loadSavedFleet has just RE-PRICED the refits against the current blueprint
			   (§4.7.1) - the server splits the stored total back apart so these two do not
			   double-count. Applied here too: the systems came off the blueprint. */
			if (window.systemEnhancements) {
				ship.pointCostSysEnh = parseFloat(ship.pointCostSysEnh) || 0;
				ship.pointCost = ship.pointCost + ship.pointCostSysEnh;
				systemEnhancements.apply(ship);
			}

			/* Pre-battle damage (§6). The payload the server returned is ALREADY filtered
			   to the player's choices - do NOT re-filter here, one filter server-side or
			   the two can disagree about what actually gets written at buy time.
			   preBattleAvailable is display-only and is dropped before the buy POST. */
			if (window.battleDamage) {
				//toPlainObject, not a bare assignment: an EMPTY payload comes back from PHP
				//as the JSON array [], and an array silently drops sys/ftr again the moment
				//the buy POST stringifies it (see battleDamage.get).
				ship.preBattleDamage = battleDamage.toPlainObject(listShip.preBattleDamage);
				ship.preBattleCritDesc = critDesc || {};
				//{critClass: true} for the one-turn ones, so the editable critical list can
				//label them "turn 1 only" instead of showing them as lasting wounds.
				ship.preBattleCritTransient = critTransient || {};

				var available = listShip.preBattleAvailable || {};
				var loaded = battleDamage.contents(ship.preBattleDamage);
				if (available.damage && !loaded.damage) declinedDamage = true;
				if (available.criticals && !loaded.criticals) declinedCriticals = true;

				battleDamage.applyToShip(ship);
			}

			gamedata.updateFleet(ship);
		}

		if (declinedDamage || declinedCriticals) {
			var skipped = [];
			if (declinedDamage) skipped.push("battle damage");
			if (declinedCriticals) skipped.push("critical effects");
			confirm.fleetNotice("Fleet loaded. Saved " + skipped.join(" and ") + " were not applied.");
		}

		if (Array.isArray(sysEnhNotice) && sysEnhNotice.length > 0) {
			confirm.fleetNotice("System enhancements changed since this fleet was saved: "
				+ sysEnhNotice.join(" "));
		}

		//gamedata.populateFleetDropdown();
	},

	/* doLoadFleet's one window for the units it left out of a saved fleet: mines as a single
	   sentence (a mine fleet can carry dozens), units past the In-Service Date as a list (.fleetNotice*
	   in confirm.css). The names are the player's own text, so they are escaped. */
	showLeftOutNotice: function showLeftOutNotice(minesLeftOut, tooLate, loadsNothing) {
		var esc = scenarioCard.escapeHtml;
		var html = "";

		if (loadsNothing) {
			html += '<p class="fleetNoticeLead">Nothing was loaded: no unit in this fleet is allowed in this scenario.</p>';
		}
		if (minesLeftOut) {
			html += '<p class="fleetNoticeReason">Mines were not loaded with this fleet, as mines are not allowed in this scenario.</p>';
		}
		if (tooLate.length) {
			html += '<p class="fleetNoticeReason">These units entered service after this game\'s In-Service Date of '
				+ '<span class="fleetNoticeYear">' + esc(gamedata.inServiceDate) + '</span>, so they were not loaded:</p>'
				+ '<ul class="fleetNoticeList">'
				+ tooLate.map(function (listShip) {
					var shipClass = String(listShip.shipClass || "");
					var name = String(listShip.name || shipClass);
					return '<li class="fleetNoticeItem">'
						+ '<span class="fleetNoticeName">' + esc(name)
						+ (shipClass && shipClass !== name ? '<span class="fleetNoticeClass">' + esc(shipClass) + "</span>" : "")
						+ "</span>"
						+ '<span class="fleetNoticeMeta">ISD ' + esc(listShip.isd) + "</span>"
						+ "</li>";
				}).join("")
				+ "</ul>";
		}

		confirm.fleetNoticeHtml(html, loadsNothing ? "Fleet Not Loaded" : "Units Not Loaded");
	},

	//To change the availability of a saved fleet
	changeFleetPublic: function changeFleetPublic(listId) {
		ajaxInterface.changeFleetPublic(listId, function (response) {
			//console.log("AJAX response:", ships); // debug raw response
			if (response && response.success) {
				var setting = response.newStatus ? 'shared' : 'private';

				//Fleet selection doesn't poll anymore, so need to change it manually on front end so padlock displays correctly. 
				for (var i in cachedFleets) {
					var fleet = cachedFleets[i];
					if (fleet.id == response.id) cachedFleets[i].isPublic = response.newStatus;
				}

				fleetDropdownButton.textContent = 'LOAD A FLEET';
				gamedata.populateFleetDropdown(cachedFleets);
				confirm.fleetNotice("Fleet availability changed to " + setting + ".");
			} else {
				console.error("Availability change failed:", response);
				confirm.fleetNotice("Failed to change fleet availability.");
			}
		});
	},


	deleteSavedFleet: function (listId, fleetName) {
		ajaxInterface.deleteSavedFleet(listId, function (response) {
			if (response && response.success) {
				// ✅ Only update UI after server confirms deletion
				cachedFleets = cachedFleets.filter(f => f.id !== listId);
				gamedata.populateFleetDropdown(cachedFleets);

				confirm.fleetNotice(fleetName + " deleted.");
			} else {
				console.error("Delete failed:", response);
				confirm.fleetNotice("Failed to delete " + fleetName + ".");
			}
		});
	},


	getMySlots: function getMySlots() {
		var mySlots = [];
		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			if (slot && slot.playerid == gamedata.thisplayer) mySlots.push(slot);
		}
		return mySlots;
	},

	onSelectSlotClicked: function onSelectSlotClicked(e) {
		var slotElement = $(".slot").has($(this));
		var slotid = slotElement.data("slotid");
		var slot = playerManager.getSlotById(slotid);

		if (slot.playerid == gamedata.thisplayer) gamedata.selectSlot(slot);
	},

	selectSlot: function selectSlot(slot) {
		// Find previously selected slot and re-show its selectslot element
		var previous = $(".slot.selected");
		if (previous.length) {
			previous.removeClass("selected");
			$(".selectslot", previous).show();  // Immediately show the select button back
		}

		// Select the new slot and hide its selectslot element
		var current = $(".slot.slotid_" + slot.slot);
		current.addClass("selected");
		$(".selectslot", current).hide(); // Hide the select button for the selected slot

		gamedata.selectedSlot = slot.slot;
		this.constructFleetList();

		// Re-populate dropdown (filters by points) but do NOT re-fetch from server
		if (window.cachedFleets && window.cachedFleets.length > 0) {
			if (gamedata.populateFleetDropdown) {
				gamedata.populateFleetDropdown();
			}
		}

	},

	onShipContextMenu: function onShipContextMenu(phpclass, faction, id, fleetList) {
		var ship;

		//Ship object depends on whether it's generic window based on phpclass, or whether it's from player's fleet list.
		if (fleetList) {
			ship = gamedata.getFleetShipById(id);
		} else {
			ship = gamedata.getShip(phpclass, faction);
		}

		gamedata.fleetWindowOpen = Boolean(fleetList);

		//Fleet ships show their purchased enhancements; apply() is one-shot per ship
		//build (see lobbyEnhancements.js) so repeated opens are safe. Store blueprints
		//are the SHARED gamedata.allShips objects and never have enhancements taken -
		//don't run the mutator over them at all.
		if (fleetList) {
			lobbyEnhancements.apply(ship);
		}

		//Ship-window redesign Stage 3: the React window (same stack as game.php).
		window.shipWindowManagerReact.open(ship);
		return false;
	},


	getFleetShipById: function getFleetShipById(id) {
		// Ensure that gamedata.ships is an array and id is compared correctly
		for (var i in gamedata.ships) {
			if (gamedata.ships[i].id === id) {
				gamedata.displayedShip = gamedata.ships[i].phpclass;
				gamedata.displayedFaction = gamedata.ships[i].faction;
				return gamedata.ships[i];
			}
		}
		//Or return generic shipu sing default method if can't be found in fleet choices.
		return gamedata.getShip(id);
	},

	/*
	setShipsFromFaction: function setShipsFromFaction(faction, jsonShips) {
		gamedata.allShips[faction] = Object.keys(window.staticShips[faction]).map(function (shipClass) {
			return new Ship(window.staticShips[faction][shipClass]);
		})
	},
	*/

	getShip: function getShip(phpclass, faction) {
		var actPhpclass;
		var actFaction;
		if (faction != null) { //faction provided
			actPhpclass = phpclass;
			actFaction = faction;
			gamedata.displayedShip = phpclass;
			gamedata.displayedFaction = faction;
		} else { //recall last opened!
			actPhpclass = gamedata.displayedShip;
			actFaction = gamedata.displayedFaction;
		}

		if (!gamedata.allShips[actFaction]) {
			throw new Error("Unable to find faction " + actFaction)
		}

		return gamedata.allShips[actFaction].find(ship => ship.phpclass == actPhpclass);
	},

	setShipsFromFaction: function setShipsFromFaction(faction, jsonShips) {
		var ships = Array.isArray(jsonShips) ? jsonShips : Object.values(jsonShips);
		gamedata.allShips[faction] = ships.map(function (ship) {
			return new Ship(ship);
		})
	},

	isTerrain: function isTerrain(shipSizeClass, userid) {
		if (shipSizeClass == 5 || userid == -5) return true;
		return false;

	},


	/*
	  ====================================================================
	  LEGACY / ARCHIVED — checkChoices_LEGACY (preserved for posterity)
	  --------------------------------------------------------------------
	  This is the original checkChoices, kept verbatim and INERT. It is
	  never called — the active implementation is the checkChoices above
	  it. Retained so the long-evolved fleet-check logic can be diffed and
	  referenced as the new version is incrementally simplified.
	  The only difference in the new active version is the Item 10 fix:
	  the variant-count switch on the already-seen-hull path here writes
	  nHull.X++ (the wrong/previous object) instead of oHull.X++.
	  ====================================================================
	  
	checkChoices_LEGACY: function () {
		/*this is for interaction with $outOfTier array in ship SCS
		indicates PROBLEM => (count->current count; limit->accepted count max; text->warning text if over limit)
		*/
		/*
		var outOfTierArray = new Array('WARLOCK', 'EMINE'); //list of allowed entries - must match object below
		var outOfTierList = {
			'WARLOCK': { count: 0, limit: 0, text: 'Warlock is above Tier 1' }, //Warlock: not allowed
			'EMINE': { count: 0, limit: 6, text: 'Massed EMines are above Tier 1 (up to 6 are allowed)' } //EMines: up to 6 EMines allowed
		};

		var warningText = ""
		var checkResult = "";
		var problemFound = false;
		var warningFound = false;
		var slotid = gamedata.selectedSlot;
		var selectedSlot = playerManager.getSlotById(slotid);

		var totalPointsSpent = 0;
		var units10 = 0;
		var units33 = 0;
		var points10 = 0;
		var points33 = 0;
		var totalU = 0;
		var totalR = 0;
		var jumpDrivePresent = false;
		var capitalShips = 0;
		var totalShips = 0;
		var customShipPresent = false;
		var enhancementPresent = false;
		var uniqueShipPresent = false;
		var ancientUnitPresent = false;
		var specialVariantPresent = false;
		var staticPresent = false;
		var nonCombatPresent = false;
		var shipTable = [];
		var noSmallFlights = 0;

		var specialFighters = [];
		var specialHangars = [];
		var specialFtrAmt = 0;
		var specialFtrName = '';
		var specialHgrAmt = 0;
		var specialHgrName = '';
		var totalHangarH = 0; //hangarspace for heavy fighters
		var totalHangarM = 0; //hangarspace for medium fighters
		var totalHangarL = 0; //hangarspace for light fighters
		var totalHangarXL = 0; //hangarspace for ultralight fighters
		var totalHangarAS = 0;//total Assault Shuttle/Breaching pod slots		
		var totalHangarOther = new Array(); //other hangarspace
		var totalFtrH = 0;//total heavy fighters
		var totalFtrM = 0;//total medium fighters
		var totalFtrL = 0;//total light fighters
		var totalFtrXL = 0;//total ultralight fighters
		var totalFtrAS = 0;//total Assault Shuttle/Breaching pods
		var hangarConversionsF = 0; //How many converted hangar slots TO fighter slots.
		var hangarConversionsAS = 0; //How many converted hangar slots TO Assault Shuttle slots.		
		var totalFtrOther = new Array();//total other small craft
		var smallCraftUsed = new Array();//small craft sizes that happen to be present, whether as hangar space or actual craft
		var totalShuttleCapacity = 0; //sum of default shuttle/flyer pool capacity across the fleet (excludes minesweeping shuttles)
		var defaultShuttleKeyList = []; //distinct lship.fighters keys used by default shuttle pools (e.g. "shuttles", "minbari flyers")

		var totalEnhancementsValue = 0;
		var totalBPSizeCap = 0;     //sum of per-ship size-based BP caps (1/2/4 with x2 for Assault hulls)
		var totalBPDedicated = 0;   //sum of dedicated "Breaching Pods" slots declared in ship.fighters
		var totalBPUsage = 0;
		var shipHangarProfiles = [];
		var breachingPodsList = [];

		for (var i in gamedata.ships) {
			var lship = gamedata.ships[i];
			if (lship.slot != slotid) continue;

			totalPointsSpent += lship.pointCost;

			// 10%/33% deployment brackets use the BASE ship cost only (no ammo, no
			// enhancements). lship.pointCost is overwritten at purchase to the post-
			// purchase total (base + ammo + enhancements); the canonical base lives on
			// the catalog entry. For flights, catalog cost is for a full 6-craft flight,
			// so scale by actual flightSize/6 to mirror confirm.js getTotalCost.
			var bracketBaseCost = lship.pointCost;
			var catalogShip = gamedata.getShipByType(lship.phpclass);
			if (catalogShip) {
				bracketBaseCost = catalogShip.pointCost;
				if (lship.flight && lship.flightSize) {
					bracketBaseCost = bracketBaseCost * (lship.flightSize / 6);
				}
			}

			if (lship.limited == 10) {
				points10 += bracketBaseCost;
				units10 += 1;
			}
			if (lship.limited == 33) {
				points33 += bracketBaseCost;
				units33 += 1;
			}
			totalEnhancementsValue += lship.pointCostEnh;
			var vLetter = gamedata.variantLetter(lship);
			var hull = lship.variantOf;
			var hullFound;
			hullFound = false;
			if (hull == "") hull = lship.shipClass; //ship is either base itself, or base is indicated in variantOf variable
			for (var j in shipTable) {
				var oHull = shipTable[j];
				if (oHull.name == hull) {
					hullFound = true;
					oHull.Total++;
					if (lship.hangarRequired != '') { //let's require sticking to hull limit if ANY ship of this hull requires it
						oHull.hangarRequired = true;
					}
					switch (vLetter) {
						case 'Q':
							oHull.Q++;
							totalR++;
							uniqueShipPresent = true;
							break;
						case 'R':
							oHull.R++;
							totalR++;
							break;
						case 'U':
							oHull.U++;
							totalU++;
							break;
						case 'C':
							oHull.C++;
							break;
						default:
							nHull.X++;
					}
				}
			}
			if (hullFound == false) {
				var nHull = { name: hull, Total: 1, Q: 0, R: 0, U: 0, C: 0, X: 0, isFtr: false, hangarRequired: false };
				if (lship.flight) {
					nHull.isFtr = lship.flight;
				}
				if (lship.hangarRequired != '') {
					nHull.hangarRequired = true;
				}
				switch (vLetter) {
					case 'Q':
						nHull.Q++;
						totalR++; //Unique is treated more or less the same as Rare
						uniqueShipPresent = true;
						break;
					case 'R':
						nHull.R++;
						totalR++;
						break;
					case 'U':
						nHull.U++;
						totalU++;
						break;
					case 'C':
						nHull.C++;
						break;
					default:
						nHull.X++;
						specialVariantPresent = true;
				}
				shipTable.push(nHull);
			}
			if (lship.factionAge > 2) {
				ancientUnitPresent = true;
			}



			//potentially out-of-Tier elements
			for (var potProblem in lship.outOfTier) {
				var potProblemCount = lship.outOfTier[potProblem];
				if (potProblemCount > 0) {
					var outOfTierEntry = outOfTierList[potProblem];
					if (outOfTierEntry) outOfTierEntry.count += potProblemCount;
				}
			}


			if (!lship.flight) {
				totalShips++;

				// Apply HANG_BP slot conversion to lship.fighters so every downstream
				// consumer in this loop (BP totals, hangar tallies, getDefaultShuttles)
				// sees the post-conversion shape. Mirrors the server-side mutation in
				// Enhancements::setEnhancementsShip.
				//
				// HANG_MSW is deliberately NOT applied here — minesweeping shuttles
				// still count as default shuttle capacity for fleet-check purposes;
				// only the auto-populated *type* changes at game-load (HangarOps step 3).
				//
				// Snapshot the original on first encounter so subsequent fleet-check
				// passes restore-then-reapply (otherwise enhCount changes would stack).
				if (!lship._originalFighters) {
					lship._originalFighters = JSON.parse(JSON.stringify(lship.fighters || {}));
				} else {
					lship.fighters = JSON.parse(JSON.stringify(lship._originalFighters));
				}
				if (lship.enhancementOptions) {
					for (var preEnh in lship.enhancementOptions) {
						var preEnhID = lship.enhancementOptions[preEnh][0];
						var preConvNum = lship.enhancementOptions[preEnh][2] || 0;
						if (preConvNum <= 0) continue;
						//HANG_BP — convert default shuttle slots into dedicated Breaching
						//Pod slots. Default shuttles auto-fill leftover hangar capacity,
						//so adding to "Breaching Pods" implicitly steals from that pool;
						//no explicit "shuttles" decrement needed. Mirrors the server-side
						//mutation in Enhancements::setEnhancementsShip (HANG_BP case).
						if (preEnhID === "HANG_BP") {
							lship.fighters["Breaching Pods"] = (lship.fighters["Breaching Pods"] || 0) + preConvNum;
						}
					}
				}

				// Calculate Breaching Pod capacity for this ship - only if it has suitable hangar capacity.
				// Dedicated "Breaching Pods" slots in ship.fighters (e.g. Decurion's 4 side-bay pod racks)
				// are guaranteed BP capacity, additive to the size-based limit, and BPs prefer them first.
				var hasBPCompatibleHangar = false;
				var shipBPDedicated = lship.fighters["Breaching Pods"] || 0;
				var shipSlots = {
					"heavy": lship.fighters["heavy"] || lship.fighters["normal"] || 0,
					"medium": lship.fighters["medium"] || 0,
					"assault shuttles": lship.fighters["assault shuttles"] || 0,
					"breaching pods": shipBPDedicated
				};

				if (shipSlots["heavy"] > 0 || shipSlots["medium"] > 0 || shipSlots["assault shuttles"] > 0 || shipSlots["breaching pods"] > 0) {
					hasBPCompatibleHangar = true;
				}

				var shipBPLimit = 0;
				if (hasBPCompatibleHangar) {
					shipBPLimit = 1;
					if (lship.Enormous || lship.base || lship.smallBase) {
						shipBPLimit = 4;
					} else if (lship.shipSizeClass >= 3) { // Capital ships
						shipBPLimit = 2;
					}
					// Double for Assault units (hull type as requested)
					if (lship.shipClass.toLowerCase().indexOf("assault") !== -1) {
						shipBPLimit *= 2;
					}
					// The size-based cap is how many of THIS ship's own AS/Heavy/Medium
					// slots it may dedicate to pods — it can't exceed the slots the ship
					// actually has to host them. Dedicated "Breaching Pods" racks are
					// counted separately (totalBPDedicated) and don't host size-cap pods.
					// Without this clamp a ship that converted its ONLY hangar box into a
					// BP rack (e.g. Urik'hal: capacity 1 → 1 rack, 0 fighter slots) would
					// still contribute its full size cap to the fleet pool, letting those
					// phantom slots be borrowed by another carrier's pods.
					var shipOwnOverflowSlots = shipSlots["heavy"] + shipSlots["medium"] + shipSlots["assault shuttles"];
					shipBPLimit = Math.min(shipBPLimit, shipOwnOverflowSlots);
					totalBPSizeCap += shipBPLimit;
					totalBPDedicated += shipBPDedicated;
				}

				// Record ship profile for per-ship validation
				var shipProfile = {
					id: lship.id,
					name: lship.shipClass,
					bpLimit: shipBPLimit,           //original size-based cap (immutable)
					bpDedicated: shipBPDedicated,   //original dedicated BP slot count (immutable)
					bpLimitRemaining: shipBPLimit,  //decremented as BPs are assigned
					slots: shipSlots
				};
				shipHangarProfiles.push(shipProfile);

				// Check if ship has converted Hangar Space (adjust ship-specific profile too)
				for (var enh in lship.enhancementOptions) {
					if (lship.enhancementOptions[enh][6]) { // Hangar conversion is an option
						var convNum = lship.enhancementOptions[enh][2];
						if (lship.enhancementOptions[enh][0] === "HANG_F") {
							hangarConversionsF += convNum;
							shipProfile.slots["assault shuttles"] -= convNum;
							shipProfile.slots["heavy"] += convNum;
						}
						if (lship.enhancementOptions[enh][0] === "HANG_AS") {
							hangarConversionsAS += convNum;
							// Deduct from heavy then medium
							var toDeduct = convNum;
							var taken = Math.min(toDeduct, shipProfile.slots["heavy"]);
							shipProfile.slots["heavy"] -= taken;
							toDeduct -= taken;
							if (toDeduct > 0) {
								shipProfile.slots["medium"] -= toDeduct;
							}
							shipProfile.slots["assault shuttles"] += convNum;
						}
						//HANG_BP/HANG_MSW have already been baked into lship.fighters
						//up-front (see _originalFighters snapshot block above), so
						//shipBPDedicated / shipSlots / totalBPDedicated already include
						//the conversion. Nothing further to do here.
					}
				}

				//check for custom hangars
				if (lship.customFighter) {
					for (var h in lship.customFighter) {
						specialHgrName = h;
						specialHgrAmt = lship.customFighter[h];
						specialHangars.push([specialHgrName, specialHgrAmt]);
					}
					//console.table(specialHangars);
				}


				//check hangar space available...
				for (var h in lship.fighters) {
					var amount = lship.fighters[h];
					if (h == "normal" || h == "heavy") {
						totalHangarH += amount;
					} else if (h == "medium") {
						totalHangarM += amount;
					} else if (h == "light") {
						totalHangarL += amount;
					} else if (h == "ultralight") {
						totalHangarXL += amount;
					} else if (h == "assault shuttles") {
						totalHangarAS += amount;
					} else if (h == "Breaching Pods") {
						//Dedicated BP slots are folded into totalBPCapacity above
						//(plus per-ship shipSlots["breaching pods"] for assignment).
						//Don't add them to totalHangarOther / smallCraftUsed — that
						//would re-render them as a separate "Breaching Pods: X (allowed up to Y)"
						//small-craft row alongside the main BP report.
					} else { //something other than fighters
						var found = false;
						for (var nh in totalHangarOther) {
							if (totalHangarOther[nh][0] == h) {//this is small craft type we're looking for!
								found = true;
								totalHangarOther[nh][1] += amount;
							}
						}
						if (found != true) { //such craft wasn't encountered yet
							if(h == "minesweeping shuttles" || h == "cargo shuttles") continue; //These are not bought, don't add to checker.
							totalHangarOther.push(new Array(h, amount));
							smallCraftUsed.push(h);
						}
					}
				}

				//Stage S: integrated fighters (SHAD_FTRL) are BOUGHT as an enhancement,
				//not deployed as separate flights — but per the rules they count toward
				//the ship's fighter maximum. Consume one MEDIUM fighter-slot per bought
				//integrated fighter (ShadowMediumFighterFlight is a medium craft) so a
				//player can't buy 6 integrated fighters AND also deploy 6 separate Shadow
				//fighters. The pools are aggregated in the totalFtrPresent vs
				//totalHangarAvailable check below, so charging them to totalFtrM is exact
				//even though the carrier declares its capacity as 'normal'.
				for (var senh in lship.enhancementOptions) {
					if (lship.enhancementOptions[senh][0] === "SHAD_FTRL") {
						var shadFtrBought = lship.enhancementOptions[senh][2] || 0;
						if (shadFtrBought > 0) totalFtrM += shadFtrBought;
						break;
					}
				}

				//Default shuttle slots auto-populate any leftover hangar capacity
				//(see HangarOps::populateInitialHangarUsage step 3 on the server).
				//Surface them as 'shuttles' capacity so armed-shuttle variants
				//(ArmedFlyer for Minbari, future ArmedShuttleEA, etc.) — which set
				//hangarRequired='shuttles' — can be bought against this pool. We
				//deliberately don't push to smallCraftUsed: the report row only
				//appears when the player actually buys armed shuttles, so empty
				//rows don't clutter ships that just have leftover default shuttles.
				var defaultShuttles = shipManager.systems.getDefaultShuttles(lship);
				if (defaultShuttles.count > 0 && defaultShuttles.key !== "minesweeping shuttles") {
					var defaultKey = defaultShuttles.key;
					var foundDefault = false;
					for (var nh in totalHangarOther) {
						if (totalHangarOther[nh][0] == defaultKey) {
							foundDefault = true;
							totalHangarOther[nh][1] += defaultShuttles.count;
						}
					}
					if (!foundDefault) {
						totalHangarOther.push(new Array(defaultKey, defaultShuttles.count));
					}
					//POOR CREW "cannot purchase Armed Shuttles, or accommodate them in Fleet Checker":
					//this hull contributes NO armed-shuttle berths to the fleet. It still RECEIVES its
					//own (unarmed) default shuttles, which is why only totalShuttleCapacity is skipped
					//and the totalHangarOther entry above is left alone - that entry is reported in the
					//Breaching Pods & Shuttles section, not used as armed-shuttle capacity.
					//Server twins: HangarOps::defaultShuttleLeftoverBoxes and
					//suppressDefaultShuttlesForArmed, which make the same exclusion when apportioning
					//bought armed shuttles across carriers - the two MUST agree or the lobby and the
					//game will disagree about how many default shuttles a carrier ends up holding.
					if (!window.HangarShared.crewBlocksArmedShuttles(lship)) {
						totalShuttleCapacity += defaultShuttles.count;
					}
					if (defaultShuttleKeyList.indexOf(defaultKey) === -1) {
						defaultShuttleKeyList.push(defaultKey);
					}
				}

				//ship may actually require hangar, too! but this must be specified directly
				if (lship.hangarRequired != '') { //classify based on explicit info from craft
					if (lship.hangarRequired == 'Breaching Pods') {
						totalBPUsage += 1 / lship.unitSize;
					} else {
						var found = false;
						for (var nh in totalFtrOther) {
							if (totalFtrOther[nh][0] == lship.hangarRequired) {//this is small craft type we're looking for!
								found = true;
								totalFtrOther[nh][1] += 1 / lship.unitSize; //always 1 craft in this case!
							}
						}
						if (found != true) { //such craft wasn't encountered yet
							totalFtrOther.push(new Array(lship.hangarRequired, 1 / lship.unitSize));
							smallCraftUsed.push(lship.hangarRequired);
						}
					}
				}
			} else {//note presence of fighters
				totalShips++; //well, total units anyway... rules say "one other unit present" and indicate that unit may be a fighter flight as well

				//check for presence of small flights: if for something flight size of 6 is allowed, then anything less counts as small flight
				if ((lship.flightSize < 6) && (lship.maxFlightSize >= 6)) noSmallFlights++;

				var smallCraftSize = '';
				if (lship.hangarRequired != 'fighters') { //classify based on explicit info from craft
					smallCraftSize = lship.hangarRequired;
				} else {//classify depending on jinking limit...
					if (lship.jinkinglimit >= 99) { //ultralight jinking limit is unlimited
						smallCraftSize = 'ultralight';
					} else if (lship.jinkinglimit >= 10) {
						smallCraftSize = 'light';
					} else if (lship.jinkinglimit >= 8) {
						smallCraftSize = 'medium';
					} else if (lship.jinkinglimit >= 6) {
						smallCraftSize = 'heavy';
					} else {
						smallCraftSize = 'NOT RECOGNIZED';
					}
				}
				//Stage S: separate Shadow fighter flights are scenario-only after the
				//integrated-fighter patch and do NOT consume the fleet's fighter
				//allowance (the carrier's integrated fighters already account for the
				//hull's fighter maximum via SHAD_FTRL). Skip the hangar-space tally for
				//them entirely; totalShips++ above still counts them as a unit present.
				var isShadowFighterFlight = (lship.faction == "Shadow Association");

				//now translate size into hangar space used...
				if (smallCraftSize != '' && !isShadowFighterFlight) {
					if (lship.customFtrName) {
						specialFtrAmt = lship.flightSize / lship.unitSize;
						specialFtrName = lship.customFtrName;
						specialFighters.push([specialFtrName, specialFtrAmt]);
					}

					if (smallCraftSize == "Breaching Pods") {
						var podsInFlight = lship.flightSize / lship.unitSize;
						totalBPUsage += podsInFlight;
						for (var p = 0; p < podsInFlight; p++) {
							breachingPodsList.push({ id: lship.id });
						}
					} else if (smallCraftSize == "heavy") {
						totalFtrH += lship.flightSize / lship.unitSize;
					} else if (smallCraftSize == "medium") {
						totalFtrM += lship.flightSize / lship.unitSize;
					} else if (smallCraftSize == "light") {
						totalFtrL += lship.flightSize / lship.unitSize;
					} else if (smallCraftSize == "ultralight") {
						totalFtrXL += lship.flightSize / lship.unitSize;
					} else if (smallCraftSize == "assault shuttles") {
						totalFtrAS += lship.flightSize / lship.unitSize;
					} else { //something other than standard fighters
						var found = false;
						for (var nh in totalFtrOther) {
							if (totalFtrOther[nh][0] == smallCraftSize) {//this is small craft type we're looking for!
								found = true;
								totalFtrOther[nh][1] += lship.flightSize / lship.unitSize;
							}
						}
						if (found != true) { //such craft wasn't encountered yet
							totalFtrOther.push(new Array(smallCraftSize, lship.flightSize / lship.unitSize));
							smallCraftUsed.push(smallCraftSize);
						}
					}
				}
			}
			if (jumpDrivePresent == false) { //if already found there's no point
				for (var a in lship.systems) {
					var sSystem = lship.systems[a];
					if (sSystem.name == 'jumpEngine') jumpDrivePresent = true;
				}
			}
			if (lship.shipSizeClass >= 3) capitalShips++;
			if (lship.unofficial == true) { //as opposed to eg. 'S'
				customShipPresent = true;
				warningFound = true;
			}
			if ((lship.base == true) || (lship.osat == true && !lship.mine)) staticPresent = true;
			if (lship.isCombatUnit != true) nonCombatPresent = true;
			//check for presence of enhancements
			if (!enhancementPresent) { //if already found - no point in checking
				for (var enhNo in lship.enhancementOptions) if (!lship.enhancementOptions[enhNo][6]) { //only if enhancement isn't really an option
					if (lship.enhancementOptions[enhNo][2] > 0) {
						enhancementPresent = true;
					}
				}
			}

		} //end of loop at ships preparing data

		var calcPoints = selectedSlot.points;
		if (calcPoints == -1) { //If unlimited points, assess against points spent so far.
			calcPoints = totalPointsSpent;
		}

		checkResult = "Total fleet limit: " + (calcPoints == -1 ? "Unlimited" : calcPoints) + "<br><br>";

		//check: overall fleet traits
		checkResult += "Jump engine: "; //Jump Engine present?
		if (jumpDrivePresent) {
			checkResult += " present";
		} else {
			checkResult += " NOT present! (at least one is required)";
			problemFound = true;
		}
		checkResult += "<br>";

		checkResult += "Capital ships: " + capitalShips + ": "; //Capital Ship present?
		//var capsRequired = Math.floor(calcPoints/3000);//1 per 3000, round down; so 1 at 3000, 2 at 6000, 3 at 9000, 10 at 30000
		//let's decrease the requirement at larger battles: 1 per 4000, round up, with first 2499 not counted; so 1 at 2500, 2 at 6500, 3 at 10500, 10 at 42500
		var capsRequired = 0;
		if (!ancientUnitPresent) { //regular limit: one per 5000 points, starting at 3000
			if (calcPoints >= 3000) {
				//capsRequired = Math.ceil((calcPoints-2499)/4000); //previous: one per 4000 points above 2499
				capsRequired = Math.ceil(calcPoints / 5000);
			}
		} else { //Ancient-level limit: one per 15000 points, starting at 5000			
			if (calcPoints >= 5000) {
				capsRequired = Math.ceil(calcPoints / 15000);
			}
		}

		checkResult += " (min. " + capsRequired + ")";
		if (capitalShips >= capsRequired) { //tournament rules: at least 1; changed for scalability
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		} else {
			checkResult += " <b><span style='color: red;'>FAILED!</span></b>";
			problemFound = true;
		}
		checkResult += "<br>";

		//Ancient units present?
		if (ancientUnitPresent) {
			warningText += "<br> - Ancient unit(s) present! Seek opponent's permission first. Fleet restrictions adjusted to Ancients.";
			warningFound = true;
		}
		//Custom units present?
		if (customShipPresent) {
			warningText += "<br> - Custom unit(s) present! Seek opponent's permission first.";
			warningFound = true;
		}
		//enhanced units present?
		if (enhancementPresent) {
			warningText += "<br> - Enhancement(s) present! Seek opponent's permission first. Total value: " + totalEnhancementsValue;
			warningFound = true;
		}
		//unique units present?
		if (uniqueShipPresent) {
			warningText += "<br> - Unique unit(s) present! Seek opponent's permission first.";
			warningFound = true;
		}
		//unchecked variant present?
		if (specialVariantPresent) {
			warningText += "<br> - Special deployment unit(s) present! See particular unit description.";
			warningFound = true;
		}

		//Static structures present?
		if (staticPresent) {
			checkResult += "Static structures present! They're not allowed in pickup battle.<br>";
			problemFound = true;
		}

		//non-combat units present?
		if (nonCombatPresent) {
			checkResult += "Non-Combat units present! They're not allowed in pickup battle.<br>";
			problemFound = true;
		}


		//potentially out-of-Tier elements
		for (var outOfTierIndex = 0; outOfTierIndex < outOfTierArray.length; outOfTierIndex++) {
			var problemName = outOfTierArray[outOfTierIndex];

			var potProblemEntry = outOfTierList[problemName];
			if (potProblemEntry && (potProblemEntry.count > potProblemEntry.limit)) {
				checkResult += potProblemEntry.text + " <b><span style='color: red;'>NOT OK!</span></b>" + "<br>";
				problemFound = true;
			}
		}


		checkResult += "<br>";


		var limit10 = Math.floor(calcPoints * 0.1);
		var limit33 = Math.floor(calcPoints * 0.33);
		var oneOverAllowed = false;
		checkResult += "<br><u><b>Deployment restrictions:</b></u><br><br>";
		checkResult += " - 10% bracket: " + points10 + "/" + limit10 + ": ";
		if (points10 <= limit10) {
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		} else {
			if (units10 == 1 && oneOverAllowed == false) { //only 1 unit, and this exception wasn't used yet
				//oneOverAllowed = true; //re-checked rules, Restricted and Limited pools should be checked separately
				checkResult += "<span style='color: #33cc33;'>OK</span> (one single ship is allowed to break limit)";
			} else {
				checkResult += "<b><span style='color: red;'>FAILED!</span></b> (too many points in this deployment bracket)";
				problemFound = true;
			}
		}
		checkResult += "<br>";
		checkResult += " - 33% bracket: " + points33 + "/" + limit33 + ": ";
		if (points33 <= limit33) {
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		} else {
			if (units33 == 1 && oneOverAllowed == false) { //only 1 unit, and this exception wasn't used yet
				//oneOverAllowed = true;//re-checked rules, Restricted and Limited pools should be checked separately
				checkResult += "<span style='color: #33cc33;'>OK</span> (one single ship is allowed to break limit)";
			} else {
				checkResult += "<b><span style='color: red;'>FAILED!</span></b> (too many points in this deployment bracket)";
				problemFound = true;
			}
		}
		if (points10 > 0 && totalShips < 2) {
			checkResult += "<br>Restricted (10%) ship present without escort! Such a rare ship needs to be accompanied by at least one other unit, unless it's Dargan or a Minbari ship.";
			problemFound = true;
		}
		checkResult += "<br><br>";

		//variant restrictions
		checkResult += "<br><u><b>Variant restrictions:</b></u><br><br>";
		var limitPerHull = Math.floor(calcPoints / 1100); //turnament rules: 3, but it's for 3500 points
		if (ancientUnitPresent) { //Ancients have way fewer total units...
			limitPerHull = Math.floor(calcPoints / 3000);
		}
		limitPerHull = Math.max(limitPerHull, 2); //always allow at least 2!
		var currRlimit = 0;
		var currUlimit = 0;
		var sumVar = 0;
		for (var j in shipTable) {
			var currHull = shipTable[j];
			checkResult += " <i>" + currHull.name + "</i><br>";
			checkResult += " - Total: " + currHull.Total;
			//if ((!currHull.isFtr) && (!currHull.hangarRequired)){ //fighter total is not limited; also, let's not limit units requiring hangar slots! (this isn't in the rules but I think LCV logic demands it)
			if (!currHull.hangarRequired) { //actually there MAY be hangarless fighters - they should be limited per hull (well, per flight) just like ships!
				checkResult += " (allowed " + limitPerHull + ")";
				if (currHull.Total > limitPerHull) {
					checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
					problemFound = true;
				} else {
					checkResult += " <span style='color: #33cc33;'>OK</span>";
				}
			}
			checkResult += "<br>";
			currRlimit = Math.ceil(currHull.Total / 9);
			currUlimit = Math.ceil(currHull.Total / 3);
			sumVar = currHull.R + currHull.Q + currHull.U;
			if (sumVar > 0) {
				checkResult += " - Uncommon/Rare/Unique: " + sumVar + " (allowed " + currUlimit + ")";
				if (sumVar > currUlimit) {
					checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
					problemFound = true;
				} else {
					checkResult += " <span style='color: #33cc33;'>OK</span>";
				}
				checkResult += "<br>";
			}
			sumVar = currHull.R + currHull.Q;
			if (sumVar > 0) {
				checkResult += " - Rare/Unique: " + sumVar + " (allowed " + currRlimit + ")";
				if (sumVar > currRlimit) {
					checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
					problemFound = true;
				} else {
					checkResult += " <span style='color: #33cc33;'>OK</span>";
				}
				checkResult += "<br>";
			}
			sumVar = currHull.X;
			if (sumVar > 0) {
				checkResult += " - Special: " + sumVar;
				checkResult += " CORRECTNESS NOT CHECKED!";
				warningFound = true;
				checkResult += "<br>";
			}
			checkResult += "<br>";
		}
		checkResult += "<br>";

		//total Uncommon/Rare units in fleet	    
		var limitUTotal = 0;
		var limitRTotal = 0;

		if (ancientUnitPresent) { //Ancients have way fewer total units...
			limitUTotal = Math.floor(calcPoints / 4000);
		} else if ((calcPoints - 1500) > 0) {
			limitUTotal = Math.floor((calcPoints - 1500) / 1000); //limit Uncommon units per fleet; turnament rules: 2, but it's for 3500 points
		}

		limitUTotal = Math.max(limitUTotal, 2); //always allow at least 2! 
		limitRTotal = Math.floor(limitUTotal / 2); //limit Rare units per fleet; turnament rules: 1, but it's for 3500 points    
		var limitUTotalResult = "<span style='color: #33cc33;'>OK</span>";
		var limitRTotalResult = "<span style='color: #33cc33;'>OK</span>";
		if (totalU > limitUTotal) {
			limitUTotalResult = " <b><span style='color: red;'>TOO MANY!</span></b>";
			//checkResult += "FAILED: You have " + totalU + " Uncommon units, out of " + limitUTotal + " allowed for fleet.<br><br>" ;
			problemFound = true;
		}
		if (totalR > limitRTotal) {
			limitRTotalResult = " <b><span style='color: red;'>TOO MANY!</span></b>";
			//checkResult += "FAILED: You have " + totalR + " Rare/Unique units, out of " + limitRTotal + " allowed for fleet.<br><br>" ;
			problemFound = true;
		}
		checkResult += 'Total Uncommon units: ' + totalU + ' (allowed ' + limitUTotal + ') ' + limitUTotalResult + '<br>';
		checkResult += 'Total Rare/Unique units: ' + totalR + ' (allowed ' + limitRTotal + ') ' + limitRTotalResult + '<br><br>';


		//fighters!
		//ultralights count as half a fighter when accounting for hangar space used - IF packed into something other than ultralight hangars...

		// Snapshot fleet-wide hangar totals before the BP assignment loop
		// mutates them — needed below to compute the effective BP cap, which
		// must exclude AS/H/M slots already claimed by non-BP small craft.
		var preBPHangarAS = totalHangarAS;
		var preBPHangarH = totalHangarH;
		var preBPHangarM = totalHangarM;

		// Per-Ship Breaching Pod Assignment and Deduction.
		// Pass 1: fill dedicated "Breaching Pods" hangar slots first — these
		// are guaranteed BP capacity and don't consume the ship's size-based
		// BP cap (e.g. Decurion's 4 side-bay pod racks).
		// Pass 2: overflow into AS/Heavy/Medium slots, capped by the ship's
		// size-based bpLimitRemaining (1/2/4 with x2 for Assault hulls).
		// Count of BPs that had to borrow an AS/Heavy/Medium hangar slot in Pass 2
		// (i.e. didn't land in a dedicated "Breaching Pods" rack). This is the true
		// "hangar slots used by BPs" figure — derived from the actual assignment
		// rather than a fleet-wide totalBPUsage - totalBPDedicated subtraction, which
		// can't tell one ship's dedicated racks apart from another's borrowed slots.
		var bpHangarSlotsUsed = 0;
		var unassignedBPs = 0;
		for (var bpIdx = 0; bpIdx < breachingPodsList.length; bpIdx++) {
			var assigned = false;
			for (var shIdx = 0; shIdx < shipHangarProfiles.length; shIdx++) {
				var ship = shipHangarProfiles[shIdx];
				if (ship.slots["breaching pods"] > 0) {
					ship.slots["breaching pods"]--;
					assigned = true;
					break;
				}
			}
			if (!assigned) {
				for (var shIdx = 0; shIdx < shipHangarProfiles.length; shIdx++) {
					var ship = shipHangarProfiles[shIdx];
					if (ship.bpLimitRemaining > 0) {
						// Check for suitable slot: AS > Heavy > Medium
						if (ship.slots["assault shuttles"] > 0) {
							ship.slots["assault shuttles"]--;
							totalHangarAS--;
							assigned = true;
						} else if (ship.slots["heavy"] > 0) {
							ship.slots["heavy"]--;
							totalHangarH--;
							assigned = true;
						} else if (ship.slots["medium"] > 0) {
							ship.slots["medium"]--;
							totalHangarM--;
							assigned = true;
						}

						if (assigned) {
							ship.bpLimitRemaining--;
							bpHangarSlotsUsed++;
							break;
						}
					}
				}
			}
			if (!assigned) unassignedBPs++;
		}

		var hangarConversionNet = hangarConversionsF - hangarConversionsAS; //Positive is more fighter slots, negative if more AS.
		var totalHangarAvailable = totalHangarH + totalHangarM + totalHangarL + (totalHangarXL / 2) + hangarConversionNet;
		var minFtrRequired = Math.ceil(totalHangarAvailable / 2);
		var totalFtrPresent = totalFtrH + totalFtrM + totalFtrL + (totalFtrXL / 2);
		var totalFtrCurr = 0;
		var totalHangarCurr = 0;

		checkResult += "<br><b><u>Fighters:</u></b><br>";
		checkResult += "<br> Total Fighters: " + totalFtrPresent;
		checkResult += " (select between " + minFtrRequired + " and " + totalHangarAvailable + ")";
		if ((totalFtrXL > 0) || (totalHangarXL > 0)) { //add disclaimer because sums will not add up straight
			checkResult += " <i>[Note - Ultralights only use half a hangar slot]</i>";
		}
		if (totalFtrPresent > totalHangarAvailable || totalFtrPresent < minFtrRequired) { //fighter total is not within limits
			checkResult += " <b><span style='color: red;'>FAILURE!</span></b>";
			problemFound = true;
		} else {
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		}
		checkResult += "<br>";

		totalFtrCurr = totalFtrXL;
		totalHangarCurr = (totalHangarH + totalHangarM + totalHangarL + hangarConversionNet) * 2 + totalHangarXL;
		if (totalFtrCurr > 0 || totalHangarCurr > 0) { //do not show if there are no fighters/hangars in this segment
			checkResult += " - Ultralight Fighters: " + totalFtrCurr;
			checkResult += " (allowed up to " + totalHangarCurr + ")";
			if ((totalFtrXL > 0) || (totalHangarXL > 0)) { //add disclaimer because sums will not add up straight.
				checkResult += " <i>[Ultralights only require half a normal hangar slot]</i>";
			}
			if (totalFtrCurr > totalHangarCurr) { //fighter total is not within limits
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
				problemFound = true;
			} else {
				checkResult += " <span style='color: #33cc33;'>OK</span>";
			}
			checkResult += "<br>";
		}

		totalFtrCurr = totalFtrL;
		totalHangarCurr = totalHangarH + totalHangarM + totalHangarL + hangarConversionNet;
		if (totalFtrCurr > 0 || totalHangarCurr > 0) { //do not show if there are no fighters/hangars in this segment
			checkResult += " - Light Fighters: " + totalFtrCurr;
			checkResult += " (allowed up to " + totalHangarCurr + ")";
			if (totalFtrCurr > totalHangarCurr) { //fighter total is not within limits
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
				problemFound = true;
			} else {
				checkResult += " <span style='color: #33cc33;'>OK</span>";
			}
			checkResult += "<br>";
		}

		totalFtrCurr = totalFtrM;
		totalHangarCurr = totalHangarH + totalHangarM + hangarConversionNet;
		if (totalFtrCurr > 0 || totalHangarCurr > 0) { //do not show if there are no fighters/hangars in this segment
			checkResult += " - Medium Fighters: " + totalFtrCurr;
			checkResult += " (allowed up to " + totalHangarCurr + ")";
			if (totalFtrCurr > totalHangarCurr) { //fighter total is not within limits
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
				problemFound = true;
			} else {
				checkResult += " <span style='color: #33cc33;'>OK</span>";
			}
			checkResult += "<br>";
		}

		totalFtrCurr = totalFtrH;
		totalHangarCurr = totalHangarH + hangarConversionNet;
		if (totalFtrCurr > 0 || totalHangarCurr > 0) { //do not show if there are no fighters/hangars in this segment			
			checkResult += " - Heavy Fighters: " + totalFtrCurr;
			checkResult += " (allowed up to " + totalHangarCurr + ")";
			if (totalFtrCurr > totalHangarCurr) { //fighter total is not within limits
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
				problemFound = true;
			} else {
				checkResult += " <span style='color: #33cc33;'>OK</span>";
			}
			checkResult += "<br>";
		}

		//small flights (do not show if there aren't any!)
		if (noSmallFlights > 0) {
			checkResult += " - Small Flights (< 6 craft): " + noSmallFlights;
			if (noSmallFlights > 1) { //fighter total is not within limits
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b> (up to 1 allowed)";
				problemFound = true;
			} else {
				checkResult += " <span style='color: #33cc33;'>OK</span>";
			}
			checkResult += "<br>";
		}


		if (specialFighters.length > 0) { //do not show if there are no fighters that require special hangars
		
			{ //calculate total amount and type of special fighters
				var totalSpecialFighters = [];
				specialFighters.sort();
				var idx = 0;
				while (specialFighters.length > 0) {
					if (totalSpecialFighters.length == 0) {
						totalSpecialFighters.push([specialFighters[0][0], specialFighters[0][1]]);
						specialFighters.shift();
					} else {
						if (totalSpecialFighters[idx][0] == specialFighters[0][0]) {
							var totalFighterName = totalSpecialFighters[idx][0];
							var totalAmountToAdd = totalSpecialFighters[idx][1];
							totalAmountToAdd += specialFighters[0][1];
							totalSpecialFighters.pop();
							totalSpecialFighters.push([totalFighterName, totalAmountToAdd]);
							specialFighters.shift();
						} else {
							totalSpecialFighters.push([specialFighters[0][0], specialFighters[0][1]]);
							specialFighters.shift();
							idx++;
						}
					}
				}
				//calculate total amount and type of special hangars
				var totalSpecialHangars = [];
				specialHangars.sort();
				idx = 0;
				while (specialHangars.length > 0) {
					if (totalSpecialHangars.length == 0) {
						totalSpecialHangars.push([specialHangars[0][0], specialHangars[0][1]]);
						specialHangars.shift();
					} else {
						if (totalSpecialHangars[idx][0] == specialHangars[0][0]) {
							var totalFighterName = totalSpecialHangars[idx][0];
							var totalAmountToAdd = totalSpecialHangars[idx][1];
							totalAmountToAdd += specialHangars[0][1];
							totalSpecialHangars.pop();
							totalSpecialHangars.push([totalFighterName, totalAmountToAdd]);
							specialHangars.shift();
						} else {
							totalSpecialHangars.push([specialHangars[0][0], specialHangars[0][1]]);
							specialHangars.shift();
							idx++;
						}
					}
				}

				//determine if there is enough special hangars for each type of special fighter
				for (i = 0; i < totalSpecialFighters.length; i++) {
					var match = false;
					for (j = 0; j < totalSpecialHangars.length; j++) {
						if (totalSpecialFighters[i][0] == totalSpecialHangars[j][0]) {
							checkResult += " - " + totalSpecialFighters[i][0] + ": " + totalSpecialFighters[i][1];
							checkResult += " (allowed up to " + totalSpecialHangars[j][1] + ")";
							if (totalSpecialFighters[i][1] > totalSpecialHangars[j][1]) { //fighter total is not within limits
								checkResult += " <b><span style='color: red;'>FAILURE!</span></b>";
								problemFound = true;
							} else {
								checkResult += " <span style='color: #33cc33;'>OK</span>";
							}
							checkResult += "<br>";
							match = true;
						}
					}
					if (match == false) {
						checkResult += " - " + totalSpecialFighters[i][0] + ": " + totalSpecialFighters[i][1];
						checkResult += " (allowed up to 0) <b><span style='color: red;'>FAILURE!</span></b><br>";
						problemFound = true;
					}
				}
			}
		}

		//make list of small craft in fleet contain only unique values...
		var smallCraftUsedUnique = smallCraftUsed.filter(function (item, pos) {
			return smallCraftUsed.indexOf(item) == pos;
		})

		//list each small craft size used separately!
		for (var sc in smallCraftUsedUnique) {
			var scSize = smallCraftUsedUnique[sc];
			//Default shuttle pools ("shuttles", "minbari flyers", etc.) are reported once
			//in the Breaching Pods & Shuttles section below — skip here to avoid duplication.
			if (defaultShuttleKeyList.indexOf(scSize) !== -1) continue;
			totalFtrCurr = 0;
			totalHangarCurr = 0;
			for (var nh in totalFtrOther) {
				if (totalFtrOther[nh][0] == scSize) {//this is small craft type we're looking for!
					totalFtrCurr = totalFtrOther[nh][1];
				}
			}
			for (var nh in totalHangarOther) {
				if (totalHangarOther[nh][0] == scSize) {//this is small craft type we're looking for!
					totalHangarCurr = totalHangarOther[nh][1];
				}
			}
			//Title-case the slot key for display ("shuttles" → "Shuttles", "minesweeping
			//shuttles" → "Minesweeping Shuttles"). Mirrors the pattern used in shipwindow.js.
			var scLabel = scSize.split(' ').map(function (w) { return w.charAt(0).toUpperCase() + w.slice(1); }).join(' ');
			checkResult += " - " + scLabel + ": " + totalFtrCurr;
			if (scSize != 'Fighter Squadrons') { //standard
				checkResult += " (allowed up to " + totalHangarCurr + ")";
			} else { //Fighter Squadrons get treated as fighters - eg. half are required
				var halfH = totalHangarCurr / 2;
				checkResult += " (allowed between " + halfH + " and " + totalHangarCurr + ")";
			}
			if (totalFtrCurr > totalHangarCurr) { //small craft total is not within limits
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b>";
				problemFound = true;
			} else if ((scSize == 'Fighter Squadrons') && (totalFtrCurr < totalHangarCurr / 2)) {
				checkResult += " <b><span style='color: red;'>FAILURE!</span></b>";
				problemFound = true;
			} else {
				checkResult += " <span style='color: #33cc33;'>OK</span>";
			}
			checkResult += "<br>";
		}
		checkResult += "<br>";

		//Lets just check Assault shuttle/Breaching Pod capacity separately using their own variables.
		//Reset totalHangarAS to the pre-BP-loop value (then apply hangar conversions). The BP
		//assignment loop decrements totalHangarAS when BPs overflow into AS slots, which would
		//otherwise make the AS report show a spurious failure: e.g. Decurion + 24 AS + 6 BPs
		//would report "Total Assault Shuttles: 24 (allowed up to 22) FAILURE" alongside the
		//real "Total Breaching Pods: 6 (allowed up to 4) FAILURE". The AS hangar capacity
		//for AS units doesn't actually shrink because the player overcommitted BPs — the BP
		//report is the right place to surface that failure.
		totalHangarAS = preBPHangarAS - hangarConversionNet; //Deduct any Hangar conversions here.

		// Effective BP capacity = guaranteed dedicated slots + size-based overflow
		// capped by the physical AS/H/M slots that actually exist to host them.
		//
		// The cap is the GROSS pool of overflow-capable slots, NOT the slots left
		// free after fighters/other small craft are placed. BPs and fighters
		// compete for the same Heavy/Medium slots, but that competition is the
		// Fighters check's job — when BPs borrow H/M slots the assignment loop
		// physically removes them from totalHangarH/M, which is what drops the
		// fighter allowance (e.g. 24 medium → 22 after 2 BPs). Clamping BP
		// capacity by the *remaining* free slots as well would double-penalise the
		// same over-commit: a single fleet would fail BOTH the BP check and the
		// Fighter check for one shortage. Capping by gross slots still catches the
		// genuine impossibility (more BPs than there are AS/H/M slots to host),
		// which the per-ship assignment loop also surfaces via unassignedBPs.
		//
		// AS slots only accept AS units (per hangarAcceptsCategory), so the AS pool
		// is shared by AS units and BP overflow; H/M slots are shared by fighters
		// (incl. Light/Ultralight spillover) and BP overflow.
		var grossASForBP = Math.max(0, preBPHangarAS - hangarConversionNet);
		var grossHMForBP = Math.max(0, preBPHangarH + preBPHangarM + hangarConversionNet);
		var grossOverflowSlots = grossASForBP + grossHMForBP;
		var totalBPCapacity = totalBPDedicated + Math.min(totalBPSizeCap, grossOverflowSlots);

		// Free (post-fighter) overflow slots — used only by the shuttle-overflow
		// maths below to work out how many spare fighter slots armed shuttles can
		// still borrow after fighters and BP overflow have taken theirs. Distinct
		// from the gross figure above: shuttles get whatever is genuinely left
		// over, whereas BP *capacity* is judged against the gross slot pool.
		var freeASForBP = Math.max(0, preBPHangarAS - hangarConversionNet - totalFtrAS);
		var hmPoolCapacity = preBPHangarH + preBPHangarM + hangarConversionNet;
		var lightOverflow = Math.max(0, totalFtrL - totalHangarL);
		var xlOverflow = Math.max(0, totalFtrXL - totalHangarXL) / 2;
		var hmPoolDemand = totalFtrH + totalFtrM + lightOverflow + xlOverflow;
		var freeHMForBP = Math.max(0, hmPoolCapacity - hmPoolDemand);

		checkResult += "<br><b><u>Breaching Pods & Shuttles:</u></b><br><br>";
		checkResult += " Total Breaching Pods: " + totalBPUsage;
		checkResult += " (allowed up to " + totalBPCapacity + ")";
		if (totalBPUsage > totalBPCapacity || unassignedBPs > 0) {
			checkResult += " <b><span style='color: red;'>FAILURE!</span></b>";
			if (unassignedBPs > 0) {
				if (totalBPUsage > totalBPCapacity) {
					checkResult += " (Not enough Breaching Pod Capacity)";
				} else {
					checkResult += " (Not enough hangar slots on ships with Breaching Pod capacity)";
				}
			}
			problemFound = true;
		} else {
			if (bpHangarSlotsUsed > 0) {
				checkResult += " (" + bpHangarSlotsUsed + " fighters slot" + (bpHangarSlotsUsed === 1 ? "" : "(s)") + " used)";
			}
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		}
		checkResult += "<br>";

		checkResult += " Total Assault Shuttles: " + totalFtrAS;
		checkResult += " (allowed up to " + totalHangarAS + ")";
		if (totalFtrAS > totalHangarAS) { //Asssault Shuttle total is not within limits
			checkResult += " <b><span style='color: red;'>FAILURE!</span></b>";
			problemFound = true;
		} else {
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		}
		checkResult += "<br>";

		//Default shuttle pool — leftover hangar capacity that auto-fills with shuttles/flyers.
		//Always displayed (even when no armed shuttle variants are bought) so the player can
		//see the pool that armed-shuttle units (ArmedFlyer, future ArmedShuttleEA, etc.) draw from.
		//Rules clarification: armed-shuttle variants (hangarRequired='shuttles') may also use
		//any spare *fighter* slot (H/M/L/XL) — but NOT Assault Shuttle or Breaching Pod slots.
		//So shuttle overflow past the default pool spills into unused fighter capacity.
		var totalShuttleUsage = 0;
		for (var nh in totalFtrOther) {
			if (defaultShuttleKeyList.indexOf(totalFtrOther[nh][0]) !== -1) {
				totalShuttleUsage += totalFtrOther[nh][1];
			}
		}
		// Spare fighter slots available for shuttle overflow. Mirrors the BP free-pool maths:
		//  - HM pool: subtract any BP overflow that already consumed HM slots (BPs prefer AS,
		//    then HM, per the BP capacity calc above).
		//  - L / XL pools: simple capacity − usage; smaller-fighter spillover already accounted
		//    for in hmPoolDemand so leftover L/XL slots really are free.
		var bpOverflowDemand = Math.max(0, totalBPUsage - totalBPDedicated);
		var bpHMUsed = Math.min(Math.max(0, bpOverflowDemand - freeASForBP), freeHMForBP);
		var spareHMForShuttle = Math.max(0, freeHMForBP - bpHMUsed);
		var spareLForShuttle = Math.max(0, totalHangarL - totalFtrL);
		var spareXLForShuttle = Math.max(0, totalHangarXL - totalFtrXL);
		var spareFighterSlotsForShuttle = spareHMForShuttle + spareLForShuttle + spareXLForShuttle;
		var shuttleOverflow = Math.max(0, totalShuttleUsage - totalShuttleCapacity);

		checkResult += " Shuttles: " + totalShuttleUsage;
		checkResult += " (allowed up to " + totalShuttleCapacity + ")";
		if (shuttleOverflow === 0) {
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		} else if (shuttleOverflow <= spareFighterSlotsForShuttle) {
			checkResult += " (+" + shuttleOverflow + " fighter slot" + (shuttleOverflow === 1 ? "" : "s") + " used)";
			checkResult += " <span style='color: #33cc33;'>OK</span>";
		} else {
			checkResult += " (needs " + shuttleOverflow + " fighter slot" + (shuttleOverflow === 1 ? "" : "s") + ", " + spareFighterSlotsForShuttle + " spare)";
			checkResult += " <b><span style='color: red;'>FAILURE!</span></b>";
			problemFound = true;
		}
		checkResult += "<br>";

		if (warningFound) {
			checkResult = "<u>CAUTION: Unchecked or non-canon elements found - check text below details.</u>" + warningText + "<br><br>" + checkResult;
		}

		if (problemFound) {
			checkResult = "Overall: <b><span style='color: red; font-weight: 850;'>FAILED!</span></b><br><br>" + checkResult;
		} else {
			checkResult = "Overall: <b><span style='color: #33cc33;'>OK!</span></b><br><br>" + checkResult;
		}

		checkResult = "<span style='font-size:14px; font-weight:bold; text-decoration: underline;'>FLEET CORRECTNESS REPORT</span><br><i>(Based on tournament rules, modified for scalability)</i><br><br>" + checkResult;

		//alert(checkResult); //alert will be truncated by browser
		var targetDiv = document.getElementById("fleetcheck");
		targetDiv.style.display = "block";
		var targetSpan = document.getElementById("fleetchecktxt");
		targetSpan.innerHTML = checkResult;

		//alert("Fleet check updated!");
	}, //endof function checkChoices
	*/	

};

window.animation = {
	animateWaiting: function animateWaiting() { }
};

/*==========================================================================
  Ship-window redesign Stage 3a (SHIPWINDOW_REDESIGN_PLAN.md §4.2): React
  ship-window + system-info bootstrap for the lobby.

  The lobby has no webglScene/PhaseDirector, so the React components' UI events
  (relayed page-agnostically through window.uiEvents, Stage 2a) are consumed by
  the small handler below instead: system hover/click shows the same React
  SystemInfo popup players see in game, window ✕ closes the window, and every
  action-flavoured event (weapon selection, hangar dialogs, thrust...) is simply
  ignored - the lobby is read-only by construction (gamedata.waiting is true and
  gamephase is -2, so SystemIcon's action branches never fire anyway).

  Runs at DOM-ready: all deferred bundles (UI.bundle defines window.UIManager,
  the legacy bundle defines window.ShipWindowManager + window.uiEvents) have
  executed by then.
  ==========================================================================*/
jQuery(function () {
	if (!window.UIManager || !window.ShipWindowManager || !window.uiEvents) {
		console.error("Lobby React bootstrap: UI bundle or relay missing - ship windows disabled.");
		return;
	}

	var uiManager = new window.UIManager($("body")[0]);
	window.shipWindowManagerReact = new window.ShipWindowManager(uiManager);

	var getBoundingBox = function (element) {
		if (!element) return { top: 0, left: 0, right: 0, bottom: 0, width: 0, height: 0 };
		if (element.getBoundingClientRect) return element.getBoundingClientRect();
		return $(element)[0].getBoundingClientRect(); //jQuery-wrapped element
	};

	/*Which popup is open, and is it the STICKY (interactive) kind? Mirrors
	  PhaseStrategy.systemInfoState: a hover popup is dismissed by mouse-out, an
	  interactive menu survives until it is explicitly closed - otherwise moving the
	  cursor off the icon to reach the menu's own buttons would close it.*/
	var systemInfoState = null;

	var showInfo = function (payload) {
		if (systemInfoState && systemInfoState.menu) return;   //a sticky menu wins over hover
		uiManager.showSystemInfo({
			ship: payload.ship,
			selectedShip: null,
			system: payload.system,
			boundingBox: getBoundingBox(payload.element)
		});
		systemInfoState = { menu: false };
	};

	/*Pre-battle damage (PREBATTLE_DAMAGE_PLAN.md §5.2) gave the lobby its first
	  ACTIONABLE system menu, so a click now opens SystemInfoMenu when the system has
	  something to offer (canDoAnything, via canShowSystemInfoMenu) and falls back to the
	  read-only popup otherwise. Mirrors PhaseStrategy.showSystemInfo's menu branch.*/
	var showMenu = function (payload) {
		uiManager.showSystemInfoMenu({
			ship: payload.ship,
			selectedShip: null,
			system: payload.system,
			boundingBox: getBoundingBox(payload.element)
		});
		systemInfoState = { menu: true };
	};

	var hideInfo = function (force) {
		if (!systemInfoState) return;
		if (systemInfoState.menu && !force) return;
		uiManager.hideSystemInfo();
		systemInfoState = null;
	};

	window.uiEvents.setHandler(function (name, payload) {
		switch (name) {
			case 'SystemMouseOver':
				if (payload.showInfo === false) {
					hideInfo(false);
				} else {
					showInfo(payload);
				}
				break;
			case 'SystemClicked': //tap/click = show info too (the touch path relies on it)
				if (uiManager.canShowSystemInfoMenu(payload.ship, payload.system)) {
					showMenu(payload);
				} else {
					hideInfo(true);
					showInfo(payload);
				}
				break;
			//Pre-battle damage: clicking a bought flight's fighter health bar opens the
			//synthetic per-ordinal fighter menu in the same #systemInfoReact root.
			case 'FighterDamageClicked':
				uiManager.showFighterDamageMenu({
					ship: payload.ship,
					fighter: payload.fighter,
					boundingBox: getBoundingBox(payload.element)
				});
				systemInfoState = { menu: true };
				break;
			//Same idea for a bought bulk mine purchase: one row per copy, structure only.
			case 'MineDamageClicked':
				uiManager.showMineDamageMenu({
					ship: payload.ship,
					boundingBox: getBoundingBox(payload.element)
				});
				systemInfoState = { menu: true };
				break;
			case 'SystemMouseOut':
				hideInfo(false);
				break;
			case 'CloseSystemInfo':
				hideInfo(true);
				break;
			case 'CloseShipWindow':
				window.shipWindowManagerReact.close(payload.ship);
				hideInfo(true);
				break;
			//everything else: game-only events with no meaning in the lobby
		}
	});

	/*A sticky menu has no ✕ and the lobby has no webglScene to relay CloseSystemInfo,
	  so a click anywhere outside it dismisses it. System icons and the menu's own body
	  stop propagation, so this only sees clicks on the page behind them - the closest()
	  test is belt-and-braces for anything inside the menu that does not.*/
	$(document).on('click.preBattleDamageMenu', function (e) {
		if (!systemInfoState || !systemInfoState.menu) return;
		if (e.target && e.target.closest && e.target.closest('#systemInfoReact')) return;
		hideInfo(true);
	});
});
