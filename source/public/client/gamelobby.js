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
			case 'ZStarTrek (TOS) Federation':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'ZStarTrek (early) Federation':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'ZStarTrek Klingon':
				powerRating = 'Tier 2; Custom faction';
				break;
			case 'ZStarTrek (early) Suliban':
				powerRating = 'Tier 3; Custom faction';
				break;
			case 'ZStarWars':
				powerRating = 'Tier 2, Custom faction';
				break;
			case 'Star Wars Clone Wars':
				powerRating = 'Tier 2, Custom faction, Playtest';
				break;
			case 'ZTrek Playtest Other Factions':
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

		/* ⚠️ Only the SHIP dialog is read back off the DOM here. The bulk dialog shows TWO
		   spans carrying .totalUnitCostAmount - the per-unit cost and the whole row's total -
		   so which one this picked up would come down to the order the templates happened to
		   be prepended in. doEditBulk has already priced the ship through readBulkPurchase
		   by the time it asks, which is the same arithmetic the dialog displays. */
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

	/* Show or hide the buy-mode toggle, and force it off when the rule is not in play.
	   Called from parseServerData, i.e. on every poll, because gamedata.rules only exists
	   once the first payload has landed - the checkbox ships hidden in the markup. Cheap and
	   idempotent: .toggle() and .prop() are no-ops when nothing has changed. */
	applyReinforcementRule: function applyReinforcementRule() {
		var allowed = gamedata.reinforcementsAllowed();
		$(".reinforcement-mode-label").toggle(allowed);
		if (!allowed) $("#reinforcementModeToggle").prop("checked", false);
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
			'<span class="boughtShiptype">' + displayType + '</span>' +
			'<span class="boughtPointCost">' + display.cost + 'p</span>' +
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
		//times. Substituting them is a pure string-for-string swap, so the report
		//output is byte-identical; only the visual noise and typo risk drop.
		var R_OK = " <span style='color: #33cc33;'>OK</span>";
		var R_TOOMANY = " <b><span style='color: red;'>TOO MANY!</span></b>";
		var R_FAILURE = " <b><span style='color: red;'>FAILURE!</span></b>";
		var R_FAILED = " <b><span style='color: red;'>FAILED!</span></b>";

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

			//rowPointCost, not pointCost: a bulk row (mines, OSATs) is N units, and this
			//figure is what stands in for the fleet limit when the fleet has none.
			totalPointsSpent += gamedata.rowPointCost(lship);

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
					totalShuttleCapacity += defaultShuttles.count;
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
		/*if (calcPoints == -1) { //If unlimited points, assess against points spent so far.
			limit10 = totalPointsSpent;
			limit33 = totalPointsSpent;
		}*/

		//Rules note: a single over-limit ship in a bracket is tolerated (the
		//"one single ship is allowed to break limit" exception). The old
		//oneOverAllowed flag that gated this was always false (its only writes
		//are commented out, since Restricted/Limited pools are checked
		//separately), so the units10/units33 == 1 test alone is the live rule.
		checkResult += "<br><u><b>Deployment restrictions:</b></u><br><br>";
		checkResult += " - 10% bracket: " + points10 + "/" + limit10 + ": ";
		if (points10 <= limit10) {
			checkResult += R_OK;
		} else {
			if (units10 == 1) { //only 1 unit - allowed to break limit
				checkResult += "<span style='color: #33cc33;'>OK</span> (one single ship is allowed to break limit)";
			} else {
				checkResult += "<b><span style='color: red;'>FAILED!</span></b> (too many points in this deployment bracket)";
				problemFound = true;
			}
		}
		checkResult += "<br>";
		checkResult += " - 33% bracket: " + points33 + "/" + limit33 + ": ";
		if (points33 <= limit33) {
			checkResult += R_OK;
		} else {
			if (units33 == 1) { //only 1 unit - allowed to break limit
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
			checkResult += " <i>[Note - Ultralights only use half a hangar slot]</i>";
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
			  disclaimer: ((totalFtrXL > 0) || (totalHangarXL > 0)) ? " <i>[Ultralights only require half a normal hangar slot]</i>" : "" },
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
				checkResult += " <b><span style='color: red;'>TOO MANY!</span></b> (up to 1 allowed)";
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
			checkResult += " - " + scLabel + ": " + totalFtrCurr;
			if (scSize != 'Fighter Squadrons') { //standard
				checkResult += " (allowed up to " + totalHangarCurr + ")";
			} else { //Fighter Squadrons get treated as fighters - eg. half are required
				var halfH = totalHangarCurr / 2;
				checkResult += " (allowed between " + halfH + " and " + totalHangarCurr + ")";
			}
			if (totalFtrCurr > totalHangarCurr) { //small craft total is not within limits
				checkResult += R_TOOMANY;
				problemFound = true;
			} else if ((scSize == 'Fighter Squadrons') && (totalFtrCurr < totalHangarCurr / 2)) {
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
				'<span class="boughtShiptype">' + displayType + '</span>' +
				'<span class="boughtPointCost">' + display.cost + 'p</span>' +
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


	parseFactions: function parseFactions(jsonFactions) {
		$("#store").empty();
		let factionList = [];

		const groups = {
			"Major Factions": [],
			"League of Non-Aligned Worlds": [],
			"Minor Factions": [],
			"Ancients": [],
			"Other Factions": [],
			"Custom Factions": []
		};

		// Custom factions whose power rating also names a tier keyword (e.g. "Tier Ancients")
		// would be grouped by that keyword instead of as customs. List them here to force
		// them into Custom Factions while keeping their tier for the tier filter. Factions
		// NOT listed here keep the keyword grouping (Thirdspace stays under Ancients).
		const forceCustomGroup = ["The System"];

		for (let faction of jsonFactions) {
			const powerRating = gamedata.getPowerRating(faction);
			const lowerPower = powerRating.toLowerCase();
			const isCustom = lowerPower.includes("custom");

			// ✅ Grouping prioritizes Minor > Major > Ancients > Other > Custom
			let groupName = "Other Factions";
			if (isCustom && forceCustomGroup.includes(faction)) groupName = "Custom Factions";
			else if (lowerPower.includes("minor")) groupName = "Minor Factions";
			else if (lowerPower.includes("major")) groupName = "Major Factions";
			else if (lowerPower.includes("league")) groupName = "League of Non-Aligned Worlds";
			else if (lowerPower.includes("ancients")) groupName = "Ancients";
			else if (lowerPower.includes("other")) groupName = "Other Factions";
			else if (isCustom) groupName = "Custom Factions";

			const tierMatch = powerRating.match(/Tier\s*([123]|Ancients|Other)/i);
			const tier = tierMatch ? "Tier " + tierMatch[1] : "Unknown";

			groups[groupName].push({ faction, powerRating, isCustom, tier });
		}

		// ✅ Fixed order of groups
		const groupOrder = ["Major Factions", "League of Non-Aligned Worlds", "Minor Factions", "Ancients", "Other Factions", "Custom Factions"];

		for (let groupName of groupOrder) {
			const entries = groups[groupName];
			if (entries.length === 0) continue;

			const startClosed = false; // Factions start open as requested
			const iconText = startClosed ? '[+]' : '[-]';
			const groupHeader = $('<div class="factiongroup-header clickable" data-tier="' + groupName + '"><span class="faction-toggle-icon">' + iconText + '</span>' + groupName + '</div>').appendTo("#store");

			const displayStyle = startClosed ? 'display:none;' : 'display:block;';
			const groupContainer = $('<div class="faction-group-container" style="' + displayStyle + '"></div>').appendTo("#store");

			groupHeader.on("click", function (container) {
				return function () {
					container.slideToggle(150);
					var icon = $(this).find('.faction-toggle-icon');
					if (icon.text() === '[+]') {
						icon.text('[-]');
					} else {
						icon.text('[+]');
					}
				};
			}(groupContainer));

			entries.sort((a, b) => a.faction.localeCompare(b.faction));

			entries.forEach(({ faction, powerRating, isCustom, tier }) => {
				factionList.push(faction);

				const group = $('<div id="' + faction +
					'" class="' + faction +
					' faction shipshidden listempty" data-faction="' + faction +
					'" data-custom="' + (isCustom ? "true" : "false") +
					'" data-tier="' + tier +
					'"><div class="factionname name"><span class="faction-toggle-icon">[+]</span><span class="faction-display-name' +
					(isCustom ? ' custom-faction' : '') + '">' + faction +
					'</span><span class="tooltip">' + powerRating +
					'</span></div></div>');

				group.find('.factionname').on("click", this.expandFaction);

				groupContainer.append(group);
			});
		}

		gamedata.allShips = factionList;

		if (typeof window.updateTierFilter === "function") {
			window.updateTierFilter();  // ✅ Auto-filter after parsing
		}

	},

	drawMapPreview: function drawMapPreview() {
		const canvas = document.getElementById("mapPreview");
		const ctx = canvas.getContext("2d");
		if (!ctx) return;

		//const isLimited = $("#gamespacecheck").is(":checked");

		// Use fixed width/height if unlimited is selected
		var mapWidth = 0;
		var mapHeight = 0;

		const match = gamedata.gamespace?.match(/^(-?\d+)x(-?\d+)$/);
		if (match) {
			mapWidth = parseInt(match[1]);
			mapHeight = parseInt(match[2]);
		}

		if (mapWidth == -1) mapWidth = 84;
		if (mapHeight == -1) mapHeight = 60;

		// Clear canvas
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		// Margins and scale
		const margin = 10;
		const scaleX = (canvas.width - margin * 2) / mapWidth;
		const scaleY = (canvas.height - margin * 2) / mapHeight;
		const scale = Math.min(scaleX, scaleY); // Uniform scale

		// Calculate offset to center the map in the canvas
		const offsetX = (canvas.width - mapWidth * scale) / 2;
		const offsetY = (canvas.height - mapHeight * scale) / 2;

		// Draw black background inside the blue outline
		ctx.fillStyle = "#000000";
		ctx.fillRect(offsetX, offsetY, mapWidth * scale, mapHeight * scale);

		// Draw dotted white center lines, avoiding cross-over at center
		ctx.save();
		ctx.globalAlpha = 0.4; // Semi-transparent
		ctx.strokeStyle = "#496791";
		ctx.lineWidth = 1;
		ctx.setLineDash([4, 4]); // Dotted pattern: 6px line, 6px gap

		const centerX = offsetX + (mapWidth / 2) * scale;
		const centerY = offsetY + (mapHeight / 2) * scale;

		// Vertical line: from center up
		ctx.beginPath();
		ctx.moveTo(centerX + 6, centerY);
		ctx.lineTo(centerX + 6, offsetY);
		ctx.stroke();

		// Vertical line: from center down
		ctx.beginPath();
		ctx.moveTo(centerX + 6, centerY);
		ctx.lineTo(centerX + 6, offsetY + mapHeight * scale);
		ctx.stroke();

		// Horizontal line: from center left
		ctx.beginPath();
		ctx.moveTo(centerX + 6, centerY);
		ctx.lineTo(offsetX, centerY);
		ctx.stroke();

		// Horizontal line: from center right
		ctx.beginPath();
		ctx.moveTo(centerX + 6, centerY);
		ctx.lineTo(offsetX + mapWidth * scale, centerY);
		ctx.stroke();

		ctx.restore(); // Restore default dash       

		// Draw deployment zones
		$(".slot").each(function () {
			const slot = $(this);
			const slotId = slot.data("slotid");
			const data = gamedata.getSlotData(slotId);
			if (!data) return;
			const team = data.team;
			const player = gamedata.thisplayer;
			var playerTeam = gamedata.getPlayerTeam(slotId);

			const x = parseInt(data.depx) || 0;
			const y = parseInt(data.depy) || 0;
			const w = parseInt(data.depwidth) || 0;
			const h = parseInt(data.depheight) || 0;

			if (data.playerid == player) {
				ctx.fillStyle = "rgba(50, 200, 50, 0.4)"
				ctx.strokeStyle = "#66ff66";
			} else if (data.team == playerTeam) {
				ctx.fillStyle = "rgba(50, 50, 200, 0.4)";
				ctx.strokeStyle = "#6666ff";
			} else {
				ctx.fillStyle = "rgba(200, 50, 50, 0.4)";
				ctx.strokeStyle = "#ff6666";
			}
			// Adjust position to treat (x, y) as center
			const drawX = offsetX + (x - w / 2 + mapWidth / 2) * scale;
			const drawY = offsetY + (mapHeight / 2 - y - h / 2) * scale;

			ctx.fillRect(drawX + 6, drawY, w * scale, h * scale);

			ctx.strokeRect(drawX + 6, drawY, w * scale, h * scale);

			// Draw slot number in the center
			ctx.save(); // Save context state
			ctx.fillStyle = "white";
			ctx.font = "bold 14px Arial";
			ctx.textAlign = "center";
			ctx.textBaseline = "middle";
			ctx.fillText(team, (drawX + 6) + (w * scale) / 2, (drawY + 3) + (h * scale) / 2);
			ctx.restore(); // Restore to default state
		});

		// Draw map border (blue rectangle)
		ctx.strokeStyle = "#deebffaf";
		ctx.lineWidth = 2;
		ctx.strokeRect(offsetX, offsetY, mapWidth * scale, mapHeight * scale);
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
		if (ship.variantOf != '') {
			displayName = '&nbsp;&nbsp;&nbsp;<i>' + displayName + '</i>';
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

	/*prepares fleet list for purchases for display*/
	parseShips: function (jsonShips) {
		for (var faction in jsonShips) {
			var targetNode = document.getElementById(faction);
			var h;
			var ship;
			var shipV;
			var shipDisplayName;
			var shipList = Object.values(jsonShips[faction]);
			var pointCostFull = '';
			var powerRating = gamedata.getPowerRating(faction);
			var isCustomFaction = powerRating.toLowerCase().includes("custom");
			var isCustomShip;
			var isd;

			this.orderShipListOnPV(shipList); //perhaps more appropriate here, as alphabetical order will be shot to hell anyway

			gamedata.setShipsFromFaction(faction, shipList);

			//show separately: immobile objects (bases/OSATs), every ship size, fighters, mines
			var sizeClassHeaders = ['Fighters', 'Light Combat Vessels', 'Medium Ships', 'Heavy Combat Vessels', 'Capital Ships', 'Immobile Structures', 'Mines'];
			for (var categoryIndex = 6; categoryIndex >= 0; categoryIndex--) {
				if (categoryIndex === 6 && gamedata.rules && !gamedata.rules.allowMines && !gamedata.rules.fleetTest) continue;
				if (faction === "Terrain" && categoryIndex < 5) continue; // Terrain faction has no ships or fighters

				// Create a fragment for this size category
				var fragment = document.createDocumentFragment();

				//display header
				var isCollapsible = true; // All categories are collapsible now
				var startClosed = ((categoryIndex === 1 && ship.faction !== "Deneth Tribes" && ship.faction !== "Thirdspace" && ship.faction !== "Usuuth Coalition" && ship.faction !== "Civilians" && ship.faction !== "Barada Imperium" && ship.faction.indexOf("Nexus") === -1) || categoryIndex === 5 || categoryIndex === 6); // 1 = LCVs, 5 = Immobile Structures, 6 = Mines
				if (faction === "Terrain") {
					startClosed = false;
				}

				var iconText = startClosed ? '[+]' : '[-]';
				var headerElem = $('<div class="shipsizehdr clickable" data-faction="' + faction + '"><span class="toggleicon">' + iconText + '</span><span class="categoryType">' + sizeClassHeaders[categoryIndex] + ':</span></div>');

				var displayStyle = startClosed ? 'display:none;' : 'display:block;';
				var categoryContainer = $('<div class="category-container" style="' + displayStyle + '"></div>');
				var hasShips = false; // Track if we actually add anything to this category

				h = null; // We use headerElem for the header, and h for the ships later

				headerElem.on("click", function (container) {
					return function () {
						container.slideToggle(150);
						var icon = $(this).find('.toggleicon');
						if (icon.text() === '[+]') {
							icon.text('[-]');
						} else {
							icon.text('[+]');
						}
					};
				}(categoryContainer));

				// Don't append to fragment yet, wait to see if it's empty

				var activeShipList = shipList;
				if (categoryIndex === 6) {
					activeShipList = shipList.slice();
					this.orderShipListOnName(activeShipList);
				}

				for (var index = 0; index < activeShipList.length; index++) {
					ship = activeShipList[index];
					if (gamedata.rules && !gamedata.rules.allowMines && ship.mine && !gamedata.rules.fleetTest) continue; //Skip mines if not allowed in scenario

					isCustomShip = isCustomFaction || ship.unofficial === true;
					let customShipHighlight = (!isCustomFaction && ship.unofficial === true) ? ' highlight-custom-ship' : '';
					isd = ship.isd;
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
					shipDisplayName = this.prepareClassName(ship);
					pointCostFull = ship.pointCost;
					if (ship.flight && (ship.maxFlightSize != 1)) pointCostFull = pointCostFull + ' (' + pointCostFull / 6 + ' ea.)';//for fighters: display price per craft, too!
					var sizeTag = (categoryIndex === 0) ? this.getFighterSizeTag(ship) : '';
					var sizeTagHtml = sizeTag ? ' <span class="fightersize">' + sizeTag + '</span>' : '';
					//data-cost is the BASE point cost the Cost filter reads (for a flight,
					//the full-flight price shown in the row, not the per-craft one).
					h = $('<div oncontextmenu="return false;" class="ship storeship" data-custom="'
						+ isCustomShip + '" data-isd="'
						+ ship.isd
						+ '" data-cost="'
						+ ship.pointCost
						+ '"><span class="shiptype' + customShipHighlight + '">'
						+ shipDisplayName + '</span>'
						+ sizeTagHtml
						+ '<span class="pointcost">'
						+ pointCostFull + '</span> -<span class="addship clickable">Add to fleet</span> -<span class="showship clickable">Show details</span></div>');

					let buyHandler = gamedata.isBulkRow(ship) ? this.buyBulk.bind(this, ship.phpclass) : this.buyShip.bind(this, ship.phpclass);
					$(".addship", h).on("click", buyHandler);
					$(".showship", h).on("click", gamedata.onShipContextMenu.bind(this, ship.phpclass, faction, ship.id, false));

					categoryContainer.append(h); // We always use categoryContainer now
					hasShips = true;
					//search for variants of the base design above...
					for (var indexV = 0; indexV < activeShipList.length; indexV++) {
						shipV = activeShipList[indexV];
						if (shipV.variantOf != ship.shipClass) continue;//that's not a variant of current base ship

						isCustomShip = isCustomFaction || shipV.unofficial === true;
						let customShipHighlight = (!isCustomFaction && shipV.unofficial === true) ? ' highlight-custom-ship' : '';
						shipDisplayName = this.prepareClassName(shipV);
						pointCostFull = shipV.pointCost;
						if (shipV.flight && (shipV.maxFlightSize != 1)) pointCostFull = pointCostFull + ' (' + pointCostFull / 6 + ' ea.)';//for fighters: display price per craft, too!
						var sizeTagV = (categoryIndex === 0) ? this.getFighterSizeTag(shipV) : '';
						var sizeTagHtmlV = sizeTagV ? ' <span class="fightersize">' + sizeTagV + '</span>' : '';
						h = $('<div oncontextmenu="return false;" class="ship variant" data-custom="'
							+ isCustomShip
							+ '" data-isd="'
							+ shipV.isd
							+ '" data-cost="'
							+ shipV.pointCost
							+ '"><span class="shiptype' + customShipHighlight + '">'
							+ shipDisplayName + '</span>'
							+ sizeTagHtmlV
							+ '<span class="pointcost">'
							+ pointCostFull + '</span> -<span class="addship clickable">Add to fleet</span> -<span class="showship clickable">Show details</span></div>');

						let buyHandlerV = gamedata.isBulkRow(shipV) ? this.buyBulk.bind(this, shipV.phpclass) : this.buyShip.bind(this, shipV.phpclass);
						$(".addship", h).on("click", buyHandlerV);
						$(".showship", h).on("click", gamedata.onShipContextMenu.bind(this, shipV.phpclass, faction, ship.id, false));

						categoryContainer.append(h); // We always use categoryContainer now
						hasShips = true;
					} //end of variant
				} //end of base design

				// Only append the header and container if this category actually has ships
				if (hasShips) {
					fragment.appendChild(headerElem[0]);
					fragment.appendChild(categoryContainer[0]);
				}

				// Append the entire fragment for this size class to the DOM at once
				targetNode.appendChild(fragment);

			} //end of size


		} //end of faction
	}, //endof parseShips


	expandFaction: function expandFaction(event) {
		const clickedElement = $(this);
		const factionElement = clickedElement.parent();
		const faction = factionElement.data("faction");

		const isCurrentlyHidden = factionElement.hasClass("shipshidden");

		// Optimistic UI: Toggle immediately
		factionElement.toggleClass("shipshidden");

		var icon = clickedElement.find('.faction-toggle-icon');
		if (factionElement.hasClass("shipshidden")) {
			icon.text('[+]');
		} else {
			icon.text('[-]');
		}

		if (isCurrentlyHidden && factionElement.hasClass("listempty")) {
			window.ajaxInterface.getShipsForFaction(faction, function (factionShips) {
				gamedata.parseShips(factionShips);
				factionElement.removeClass("listempty"); // Only remove after successful load
				gamedata.applyCustomShipFilter(); // run after ships load
			}, function () {
				// Error Callback: Revert optimistic toggle if load fails
				factionElement.toggleClass("shipshidden");
			});
		}

		// Apply ship filter AFTER visibility toggled
		gamedata.applyCustomShipFilter();
	},

	//Function called by the Custom toggle and the Name / Cost / ISD filters.
	applyCustomShipFilter: function () {
		const showCustom = $("#toggleCustom").is(":checked");
		const isdValue = parseInt($("#isdFilter").val(), 10);
		//Cost filter: hide anything that costs MORE than the figure typed. Read once,
		//outside the per-ship loop, like the other two.
		const costValue = parseInt($("#costFilter").val(), 10);
		const nameFilter = $("#nameFilter").val().toLowerCase().trim();

		$(".faction").each(function () {
			const $faction = $(this);
			const isHidden = $faction.hasClass("shipshidden");

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

				$ship.toggle(visible && !isHidden);
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
			// Optional: color coding could be added here similar to createGame.js if desired
			$("#lobbyTeamsContainer").append(teamTemplate);
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
		$(".points", slot).html(data.points == -1 ? '<span class="unlimited-points-text">UNLIMITED</span>' : data.points);

		$(".depx", slot).html(data.depx);
		$(".depy", slot).html(data.depy);
		$(".deptype", slot).html(data.deptype);
		$(".depwidth", slot).html(data.depwidth);
		$(".depheight", slot).html(data.depheight);
		$(".depavailable", slot).html(data.depavailable);
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

		window.confirm.showBuyBulk(newShip, gamedata.doCopyBulk, true);
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

		var name = $(".confirm input").val();
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

		window.confirm.showShipEdit(newShip, gamedata.doCopyShip);
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

		var name = $(".confirm input").val();
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

		var name = $(".confirm input").val();
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
			//The dialog around this is .confirm.error - 16px bold #c94b1d - which shouted
			//the whole sentence in warning colours. Only NOTE: is the warning; the rest is
			//ordinary body text, so it carries its own class (see confirm.css).
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
		fleetDropdownList.innerHTML = '';

		//let filteredFleets = gamedata.filterSavedFleet(cachedFleets);

		if (!cachedFleets || cachedFleets.length === 0) {
			const empty = document.createElement('div');
			empty.textContent = '< No saved fleets available >';
			empty.style.textAlign = 'center';
			empty.style.padding = '4px 6px';
			fleetDropdownList.appendChild(empty);
			return;
		}

		// Split fleets into user and default
		const userFleets = cachedFleets.filter(f => f.userid !== 0);
		const defaultFleets = cachedFleets.filter(f => f.userid === 0);

		// Helper to render a fleet item
		const renderFleetItem = (fleet) => {
			const item = document.createElement('div');
			item.style.display = 'flex';
			item.style.justifyContent = 'space-between';
			item.style.alignItems = 'center';
			item.style.padding = '2px 2px';
			item.style.cursor = 'pointer';
			item.style.borderBottom = '1px solid #eee';

			// Hover effect
			item.addEventListener('mouseenter', () => item.style.background = '#f0f0f0');
			item.addEventListener('mouseleave', () => item.style.background = 'white');

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

			// Padlock
			const lockSpan = document.createElement('span');
			lockSpan.className = fleet.isPublic ? 'fa-solid fa-unlock' : 'fa-solid fa-lock';
			lockSpan.style.color = fleet.isPublic ? 'green' : 'orange';
			lockSpan.style.marginRight = '4px';
			lockSpan.style.cursor = 'pointer';

			// ✅ Make the clickable area bigger and isolated
			lockSpan.style.display = 'inline-flex';
			lockSpan.style.alignItems = 'center';
			lockSpan.style.justifyContent = 'center';
			lockSpan.style.width = '25px';
			lockSpan.style.height = '25px';

			lockSpan.addEventListener('click', (e) => {
				e.stopPropagation();
				const newStatus = fleet.isPublic ? 0 : 1;
				confirm.confirm(
					"Are you sure you wish to change this fleet's availability?",
					() => gamedata.changeFleetPublic(fleet.id, newStatus)
				);
			});

			// Fleet name
			const nameSpan = document.createElement('span');
			nameSpan.textContent = (fleet.userid !== 0) ? fleet.name + ' (#' + fleet.id + ')' : fleet.name;
			if (fleet.userid == 0) {
				nameSpan.style.marginLeft = '6px';
			}

			//Pre-battle damage: badge a fleet that carries battle damage and/or critical
			//effects, so the state is visible before the load dialog asks about it.
			let damageSpan = null;
			if (fleet.hasDamage || fleet.hasCrits) {
				damageSpan = document.createElement('span');
				//Same icon as gamedata.damagedShipBadge, so the dropdown and the fleet list
				//read as one idea - change BOTH or neither.
				damageSpan.className = 'fa-solid fa-screwdriver-wrench';
				damageSpan.style.color = '#c0392b';
				damageSpan.style.marginLeft = '6px';
				damageSpan.title = fleet.hasDamage && fleet.hasCrits
					? 'Carries battle damage and critical effects'
					: (fleet.hasDamage ? 'Carries battle damage' : 'Carries critical effects');
			}
			// Points
			const pointsSpan = document.createElement('span');
			pointsSpan.textContent = `${fleet.points}pts`;
			pointsSpan.style.margin = '0 6px';
			pointsSpan.style.color = '#555';
			pointsSpan.style.textAlign = 'right';

			const spacer = document.createElement('span');
			spacer.style.flexGrow = '1';

			if (fleet.userid !== 0) item.appendChild(lockSpan);
			item.appendChild(nameSpan);
			if (damageSpan) item.appendChild(damageSpan);
			item.appendChild(spacer);
			item.appendChild(pointsSpan);

			// Delete button (only for non-default fleets)
			if (fleet.userid !== 0) {
				const deleteBtn = document.createElement('span');
				deleteBtn.textContent = '✖';
				deleteBtn.style.color = 'red';
				deleteBtn.style.cursor = 'pointer';

				// ✅ Isolate the clickable area
				deleteBtn.style.display = 'inline-flex';
				deleteBtn.style.alignItems = 'center';
				deleteBtn.style.justifyContent = 'center';
				deleteBtn.style.width = '25px';
				deleteBtn.style.height = '25px';

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

		// Add a divider if default fleets exist
		if (defaultFleets.length > 0) {
			const divider = document.createElement('div');
			divider.textContent = '-----------------------------------------------------------------------------------';
			divider.style.textAlign = 'center';
			divider.style.color = '#2b2b2bff';
			divider.style.margin = '0px 0';
			divider.style.fontSize = '8px';
			divider.style.borderBottom = '1px solid #eee';
			fleetDropdownList.appendChild(divider);

			// Render default fleets (no delete button shown)
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

		/* 'Allow Mines' is a per-scenario rule, and a saved fleet outlives the game it was
		   saved from: a fleet built where mines were allowed will happily carry its mine
		   bulks into one where the buy panel never offers them (constructStore skips mines
		   on the same test). Refuse the WHOLE load rather than quietly dropping the
		   offending units - the fleet's stored `points` counted them, so a partial load
		   would put a fleet on the table that the player never saved, at a cost the
		   affordability check has already approved. Checked here because doLoadFleet is the
		   one funnel both load paths (dropdown and load-by-#ID) come through. */
		if (gamedata.rules && !gamedata.rules.allowMines && !gamedata.rules.fleetTest) {
			for (var m = 0; m < fleet.length; m++) {
				if (fleet[m] && fleet[m].mine) {
					confirm.fleetNotice("Saved fleet contains units not available for this scenario");
					return;
				}
			}
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
				console.error("Load failed:", ships);
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
					totalShuttleCapacity += defaultShuttles.count;
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
