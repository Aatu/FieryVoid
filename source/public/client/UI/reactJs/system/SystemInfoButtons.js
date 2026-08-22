import * as React from "react";
import styled from "styled-components"
import { Clickable } from "../styled";

import FiringModeSelector from "./FiringModeSelector";
import SelfRepairList from "./SelfRepairList";
import StructureSelfRepairList from "./StructureSelfRepairList";  // GTS_Triad
import AdaptiveArmorList from "./AdaptiveArmorList";
import HyachComputerList from "./HyachComputerList";
import HyachSpecialistsList from "./HyachSpecialistsList";
import ShieldGeneratorList from "./ShieldGeneratorList";
import PowerCapacitor from "./PowerCapacitor";
import JumpEngineMenu from "./JumpEngineMenu";
import SystemActivation from "./SystemActivation";
import SystemPowerSettings from "./SystemPowerSettings";
import MineSettingsList from "./MineSettingsList";
import ProximityMineSettingsList from "./ProximityMineSettingsList";
import GraviticAugmenterMenu from "./GraviticAugmenterMenu";
import MinorThoughtPulsarMenu from "./MinorThoughtPulsarMenu";
import ApplyDamageMenu from "./ApplyDamageMenu";

const Container = styled.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`;

const ButtonRow = styled.div`
    display: flex;
    flex-wrap: wrap;
`;

const Button = styled.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${props => props.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    mix-blend-mode: ${props => props.$blend || 'normal'};
    ${Clickable}

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;

class SystemInfoButtons extends React.Component {
	constructor(props) {
		super(props);
	}

	online(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		shipManager.power.onOnlineClicked(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	offline(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canOffline(ship, system)) {
			return;
		}

		shipManager.power.onOfflineClicked(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	allOnline(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		shipManager.power.onlineAll(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	allOffline(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canOffline(ship, system)) {
			return;
		}

		shipManager.power.offlineAll(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	overload(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		shipManager.power.onOverloadClicked(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	stopOverload(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		shipManager.power.onStopOverloadClicked(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	boost(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		shipManager.power.clickPlus(ship, system);
	}

	deboost(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		shipManager.power.clickMinus(ship, system);
	}

	addShots(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canAddShots(ship, system)) {
			return;
		}

		weaponManager.changeShots(ship, system, 1);
	}

	reduceShots(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canReduceShots(ship, system)) {
			return;
		}

		weaponManager.changeShots(ship, system, -1);
	}

	removeFireOrderMulti(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canRemoveFireOrderMulti(ship, system)) {
			return;
		}

		weaponManager.removeFiringOrderMulti(ship, system);
		//        webglScene.customEvent('CloseSystemInfo');
	}

	removeFireOrder(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canRemoveFireOrder(ship, system)) {
			return;
		}

		weaponManager.removeFiringOrder(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}
	removeFireOrderAll(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canRemoveFireOrder(ship, system)) {
			return;
		}
		weaponManager.removeFiringOrderAll(ship, system);
		webglScene.customEvent('CloseSystemInfo');
	}

	allChangeFiringMode(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canChangeFiringMode(ship, system)) {
			return;
		}
		//change firing mode of self
		weaponManager.onModeClicked(ship, system);
		//check which mode was set
		var modeSet = system.firingMode;
		//set this mode on ALL similar weapons that aren't declared and can change firing mode
		var allWeapons = [];
		if (ship.flight) {
			allWeapons = ship.systems
				.map(fighter => fighter.systems)
				.reduce((all, weapons) => all.concat(weapons), [])
				.filter(system => system.weapon);
		} else {
			allWeapons = ship.systems.filter(system => system.weapon);
		}
		//group by BASE displayName (trailing pairing letter stripped) so paired Kirishiac weapons
		//('Antigravity Beam A'/'...B', 'Hypergraviton Beam A'/'...B', etc.) all change together;
		//normal weapons (stable displayName) are unaffected. See weaponManager.stripPairingSuffix.
		var baseName = weaponManager.stripPairingSuffix(system.displayName);
		var similarWeapons = new Array();
		for (var i = 0; i < allWeapons.length; i++) {
			if (baseName === weaponManager.stripPairingSuffix(allWeapons[i].displayName)) {
				if (system.weapon) {
					similarWeapons.push(allWeapons[i]);
				}
			}
		}
		for (var i = 0; i < similarWeapons.length; i++) {
			var weapon = similarWeapons[i];
			if (weapon.firingMode == modeSet) continue;
			if (!canChangeFiringMode(ship, weapon)) continue;
			var originalMode = weapon.firingMode; //so mode is properly reset for weapon that cannot have desired mode set for some reason!
			var iterations = 0;
			while (weapon.firingMode != modeSet && iterations < 2) {
				weaponManager.onModeClicked(ship, weapon);
				if (weapon.firingMode == 1) {
					iterations++; //if an entire iteration passed and mode wasn't found, then mode cannot be reached	
				}
			}
			//reset mode back if necessary! (this one is guaranteed to be available)
			if (weapon.firingMode != modeSet) while (weapon.firingMode != originalMode) {
				weaponManager.onModeClicked(ship, weapon);
			}
		}
		//webglScene.customEvent('CloseSystemInfo');
	}

	changeFiringMode(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canChangeFiringMode(ship, system)) {
			return;
		}
		weaponManager.onModeClicked(ship, system);
		//webglScene.customEvent('CloseSystemInfo');
	}



	selectAllWeapons(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		weaponManager.selectAllWeapons(ship, system, "forceSelect");
		webglScene.customEvent('CloseSystemInfo');
	}

	deselectAllWeapons(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		weaponManager.selectAllWeapons(ship, system, "forceDeselect");
		webglScene.customEvent('CloseSystemInfo');
	}

	/*declare this weapon to be eligible for defensive fire this turn*/
	declareSelfIntercept(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canSelfIntercept(ship, system)) {
			return;
		}
		weaponManager.onDeclareSelfInterceptSingle(ship, system);
		if (system.canSplitShots) var finished = system.checkFinished(); //Do not close system info buttons if player can still selfintercept
		if (finished) webglScene.customEvent('CloseSystemInfo');
	}
	/*declare all similar undeclared weapons for defensive fire this turn*/
	declareSelfInterceptAll(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		weaponManager.onDeclareSelfInterceptSingleAll(ship, system);
		if (system.canSplitShots) var finished = system.checkFinished();
		if (finished) webglScene.customEvent('CloseSystemInfo');
	}

	/*declare this weapon to be eligible for defensive fire this turn*/
	remSelfIntercept(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		if (!canRemIntercept(ship, system)) {
			return;
		}
		weaponManager.removeSelfInterceptSingle(ship, system);
		//if(system.canSplitShots) var finished = system.checkFinished(); //Do not close system info buttons if player can still selfintercept
		//if(finished) webglScene.customEvent('CloseSystemInfo');
	}

	/* Dead code: activation is rendered via the <SystemActivation> component (see render()),
	   which owns its own doActivate/doDeactivate handlers. These methods were never bound to
	   any button and are kept commented for reference only.
	activate(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doActivate();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	deactivate(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDeactivate();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	*/


	//switch Adaptive Armor, Hyach Computer or Specialists display to next damage/FC class
	nextCurrClass(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.nextCurrClass();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

	prevCurrClass(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.prevCurrClass();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}


	/*
	//Adaptive Armor increase rating for current class
	AAincrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doIncrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	//Adaptive Armor decrease rating for current class
	AAdecrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	*/
	/*Adaptive Armor propagate setting for current damage type*/
	/*
	AApropagate(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		var dmgType = system.getCurrDmgType();
		var allocated = system.getCurrAllocated();
		//loop through all own units and increase setting for this dmg type until this level is achieved (or as high as possible otherwise)
		var allOwnAA = [];
		for (var i in gamedata.ships) {
			var otherUnit = gamedata.ships[i];
			if (otherUnit.userid != ship.userid) continue; //ignore other players' units
			if (shipManager.isDestroyed(otherUnit)) continue; //ignore destroyed units
			//now find AA controllers, if any...
			if (otherUnit.flight) {
				for (var iFtr = 0; iFtr < otherUnit.systems.length; iFtr++) {
					var ftr = otherUnit.systems[iFtr];
					if (ftr) for (var iSys = 0; iSys < ftr.systems.length; iSys++) {
						var ctrl = ftr.systems[iSys];
						if (ctrl) if (ctrl.displayName == "Adaptive Armor Controller") {
							allOwnAA.push(ctrl);
							break;//no point looking for SECOND AA Controller on a fighter
						}
					}
				}
	
			} else {
				for (var iSys = 0; iSys < otherUnit.systems.length; iSys++) {
					var ctrl = otherUnit.systems[iSys];
					if (ctrl.displayName == "Adaptive Armor Controller") {
						allOwnAA.push(ctrl);
						break;//no point looking for SECOND AA Controller on a ship
					}
				}
			}
		}
	
		//for each Controller: set allocated level to desired if possible
		for (var c = 0; c < allOwnAA.length; c++) {
			var ctrl = allOwnAA[c];
			ctrl.setCurrDmgType(dmgType); //set damage type to desired (or none)
			while (
				ctrl.getCurrAllocated() < allocated // level lower than desired
				&& ctrl.canIncrease() //level can be increased
			) {
				ctrl.doIncrease();
			}
		}
	
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	*/

	/*Hyach Computer increase rating for current class*/
	/*
	BFCPincrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doIncrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	BFCPdecrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	BFCPpropagate(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		var FCType = system.getCurrFCType();
		var allocated = system.getCurrAllocated();
		//loop through all own units and increase setting for this dmg type until this level is achieved (or as high as possible otherwise)
		var allOwnBFCP = [];
		for (var i in gamedata.ships) {
			var otherUnit = gamedata.ships[i];
			if (otherUnit.userid != ship.userid) continue; //ignore other players' units
			if (shipManager.isDestroyed(otherUnit)) continue; //ignore destroyed units
			//now find Hyach Computers, if any...
			if (otherUnit.flight) {
				for (var iFtr = 0; iFtr < otherUnit.systems.length; iFtr++) {
					var ftr = otherUnit.systems[iFtr];
					if (ftr) for (var iSys = 0; iSys < ftr.systems.length; iSys++) {
						var ctrl = ftr.systems[iSys];
						if (ctrl) if (ctrl.displayName == "Computer") {
							allOwnBFCP.push(ctrl);
							break;//no point looking for SECOND Computer on a fighter, actually Hyach should never have any, so just future proofing.
						}
					}
				}
	
			} else {
				for (var iSys = 0; iSys < otherUnit.systems.length; iSys++) {
					var ctrl = otherUnit.systems[iSys];
					if (ctrl.displayName == "Computer") {
						allOwnBFCP.push(ctrl);
						break;//no point looking for SECOND AA Controller on a ship
					}
				}
			}
		}
	
		//for each Computer: set allocated level to desired if possible
		for (var c = 0; c < allOwnBFCP.length; c++) {
			var ctrl = allOwnBFCP[c];
			ctrl.setCurrFCType(FCType); //set damage type to desired (or none)
			while (
				ctrl.getCurrAllocated() < allocated // level lower than desired
				&& ctrl.canIncrease() //level can be increased
			) {
				ctrl.doIncrease();
			}
		}
	
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	*/

	/*
	Specselect(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doSelect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	Specunselect(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doUnselect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	Specincrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doUse();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	Specdecrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	*/

	/*Thirdspace Shield increase health
	TSShieldIncrease25(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doIncrease25();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldIncrease10(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doIncrease10();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldIncrease5(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doIncrease5();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldIncrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doIncrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	TSShieldDecrease(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldDecrease5(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease5();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldDecrease10(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease10();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldDecrease25(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doDecrease25();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	TSShieldGenSelect(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doSelect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	
	ThoughtShieldGenSelect(e) {
		e.stopPropagation(); e.preventDefault();
		const { ship, system } = this.props;
		system.doSelect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	*/


	render() {
		const { ship, selectedShip, system } = this.props;

		if (!canDoAnything(ship, system)) {
			return null;
		}

		//Lobby: pre-battle damage is the only action, and the rows below read plumbing
		//that page does not load (weaponManager) - see the note on canDoAnything.
		if (gamedata.gamephase === -2) {
			return (
				<Container>
					<ApplyDamageMenu ship={ship} system={system} />
				</Container>
			);
		}

		return (
			<Container>
				{canSystemPowerSettings(ship, system) && <SystemPowerSettings ship={ship} system={system} />}
				<ButtonRow>
					{canAddShots(ship, system) && <Button title="More shots" onClick={this.addShots.bind(this)} img="./img/plussquare.png"></Button>}
					{canReduceShots(ship, system) && <Button title="Less shots" onClick={this.reduceShots.bind(this)} img="./img/minussquare.png"></Button>}
					{canRemoveFireOrderMulti(ship, system) && <Button title="Remove last fire order" onClick={this.removeFireOrderMulti.bind(this)} img="./img/unfiringSmall.png"></Button>}
					{canRemoveFireOrder(ship, system) && <Button title="Remove all fire orders (RMB = All weapons selected)" onClick={this.removeFireOrder.bind(this)} onContextMenu={this.removeFireOrderAll.bind(this)} img="./img/firing.png"></Button>}
				</ButtonRow>

				{(canChangeFiringMode(ship, system) || canSelfIntercept(ship, system) || canRemIntercept(ship, system)) && (
					<FiringModeSelector ship={ship} system={system} showModes={canChangeFiringMode(ship, system)}>
						{canSelfIntercept(ship, system) && <Button title="Allow interception (RMB = All systems selected)" onClick={this.declareSelfIntercept.bind(this)} onContextMenu={this.declareSelfInterceptAll.bind(this)} img="./img/addSelfIntercept.png"></Button>}
						{canRemIntercept(ship, system) && <Button title="Remove an intercept order" onClick={this.remSelfIntercept.bind(this)} onContextMenu={this.remSelfIntercept.bind(this)} img="./img/remSelfIntercept.png"></Button>}
					</FiringModeSelector>
				)}
				<ButtonRow>
					{canSelectAllWeapons(ship, system) && <Button title="Select all weapons of this type" onClick={this.selectAllWeapons.bind(this)} img="./img/selectAllWeapons.png" $blend="screen"></Button>}
					{canSelectAllWeapons(ship, system) && <Button title="Deselect all weapons of this type" onClick={this.deselectAllWeapons.bind(this)} img="./img/deselectAllWeapons.png" $blend="screen"></Button>}
				</ButtonRow>

				{canSystemActivation(ship, system) && <SystemActivation ship={ship} system={system} />}

				{/* Adaptive Armor List Integration */}
				{/* Adaptive Armor List Integration */}
				{canAA(ship, system) && <AdaptiveArmorList ship={ship} system={system} />}

				{/* Legacy AA Controls - Commented out for revamp
				{canAAdisplayCurrClass(ship, system) && <Button title={getAAcurrClassName(ship, system)} img={getAAcurrClassImg(ship, system)}></Button>}
				{canAAdisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canAAdisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/AAclasses/iconNext.png"></Button>}
				{canAAincrease(ship, system) && <Button onClick={this.AAincrease.bind(this)} img="./img/systemicons/AAclasses/iconPlus.png"></Button>}
				{canAAdecrease(ship, system) && <Button onClick={this.AAdecrease.bind(this)} img="./img/systemicons/AAclasses/iconMinus.png"></Button>}
				{canAApropagate(ship, system) && <Button title="Propagate setting" onClick={this.AApropagate.bind(this)} img="./img/systemicons/AAclasses/iconPropagate.png"></Button>}
                */}

				{canBFCP(ship, system) && <HyachComputerList system={system} ship={ship} />}
				{/*
				{canBFCPdisplayCurrClass(ship, system) && <Button title={getBFCPcurrClassName(ship, system)} img={getBFCPcurrClassImg(ship, system)}></Button>}
				{canBFCPdisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/BFCPclasses/iconNext.png"></Button>}
				{canBFCPincrease(ship, system) && <Button onClick={this.BFCPincrease.bind(this)} img="./img/systemicons/BFCPclasses/iconPlus.png"></Button>}
				{canBFCPdecrease(ship, system) && <Button onClick={this.BFCPdecrease.bind(this)} img="./img/systemicons/BFCPclasses/iconMinus.png"></Button>}
				{canBFCPpropagate(ship, system) && <Button title="Propagate setting" onClick={this.BFCPpropagate.bind(this)} img="./img/systemicons/BFCPclasses/iconPropagate.png"></Button>}
				*/}

				{canSpec(ship, system) && <HyachSpecialistsList system={system} ship={ship} />}
				{/*
				{canSpecdisplayCurrClass(ship, system) && <Button title={getSpeccurrClassName(ship, system)} img={getSpeccurrClassImg(ship, system)}></Button>}
				{canSpecdisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canSpecdisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconNext.png"></Button>}
				{canSpecselect(ship, system) && <Button onClick={this.Specselect.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}
				{canSpecunselect(ship, system) && <Button onClick={this.Specunselect.bind(this)} img="./img/systemicons/Specialistclasses/unselect.png"></Button>}
				{canSpecincrease(ship, system) && <Button onClick={this.Specincrease.bind(this)} img="./img/systemicons/Specialistclasses/iconPlus.png"></Button>}
				{canSpecdecrease(ship, system) && <Button onClick={this.Specdecrease.bind(this)} img="./img/systemicons/Specialistclasses/iconMinus.png"></Button>}
				*/}

				{canMineSettings(ship, system) && <MineSettingsList system={system} ship={ship} />}
				{canProxMineSettings(ship, system) && <ProximityMineSettingsList system={system} ship={ship} />}

				{canGraviticAugmenter(ship, system) && <GraviticAugmenterMenu system={system} ship={ship} />}

				{canMinorThoughtPulsar(ship, system) && <MinorThoughtPulsarMenu system={system} ship={ship} />}


				{(canTSShield(ship, system) || canTSShieldGen(ship, system)) && <ShieldGeneratorList system={system} ship={ship} />}
				{/*
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus25.png"></Button>}
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus10.png"></Button>}
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus5.png"></Button>}
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease.bind(this)} img="./img/systemicons/BFCPclasses/iconPlus.png"></Button>}
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease.bind(this)} img="./img/systemicons/BFCPclasses/iconMinus.png"></Button>}
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus5.png"></Button>}
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus10.png"></Button>}
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus25.png"></Button>}
				{canTSShieldGendisplayCurrClass(ship, system) && <Button title={getTSShieldGencurrClassName(ship, system)} img={getTSShieldGencurrClassImg(ship, system)}></Button>}
				{canTSShieldGendisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canTSShieldGendisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconNext.png"></Button>}
				{canTSShieldGenSelect(ship, system) && <Button onClick={this.TSShieldGenSelect.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}
				*/}

				{(canThoughtShield(ship, system) || canThoughtShieldGen(ship, system)) && <ShieldGeneratorList system={system} ship={ship} />}
				{/*
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus25.png"></Button>}
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus10.png"></Button>}
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus5.png"></Button>}
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease.bind(this)} img="./img/systemicons/BFCPclasses/iconPlus.png"></Button>}
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease.bind(this)} img="./img/systemicons/BFCPclasses/iconMinus.png"></Button>}
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus5.png"></Button>}
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus10.png"></Button>}
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus25.png"></Button>}
				{canThoughtShieldGendisplayCurrClass(ship, system) && <Button title={getTSShieldGencurrClassName(ship, system)} img={getTSShieldGencurrClassImg(ship, system)}></Button>}
				{canThoughtShieldGendisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canThoughtShieldGendisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconNext.png"></Button>}
				{canThoughtShieldGenSelect(ship, system) && <Button onClick={this.TSShieldGenSelect.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}
				*/}


				{canSelfRepairList(ship, system) && <SelfRepairList ship={ship} system={system} readOnly={!canEditSelfRepairList(ship, system)} />}

				{canStructureSelfRepairList(ship, system) && <StructureSelfRepairList ship={ship} system={system} readOnly={!canEditStructureSelfRepairList(ship, system)} />}   {/* GTS_Triad */}

				{canPowerCapacitor(ship, system) && <PowerCapacitor ship={ship} system={system} />}

				{canJumpEngineMenu(ship, system) && <JumpEngineMenu ship={ship} system={system} />}

			</Container>
		)
	}
}

// Check if this is a touch device, the system is a weapon, and it can be fired this phase
const canSelectAllWeapons = (ship, system) => {
	if (!window.matchMedia("(pointer: coarse)").matches) return false;
	if (!system.weapon) return false;
	if (isHoldingVortex(ship, system)) return false; //JUMP_POINTS_PLAN.md Stage 5 - see isHoldingVortex

	if (gamedata.gamephase != 3 && !system.ballistic && !system.preFires) return false;
	if (gamedata.gamephase != 1 && system.ballistic) return false;
	if (gamedata.gamephase != 5 && system.preFires) return false;

	return true;
};

//can do something with Adaptive Armor Controller
const canAA = (ship, system) => (gamedata.gamephase === 1) && (system.name == 'adaptiveArmorController');
const canAAdisplayCurrClass = (ship, system) => canAA(ship, system) && system.getCurrClass() != '';
/*
const getAAcurrClassImg = (ship, system) => './img/systemicons/AAclasses/' + system.getCurrClass() + '.png';
const getAAcurrClassName = (ship, system) => system.getCurrClass();
const canAAincrease = (ship, system) => canAA(ship, system) && system.canIncrease() != '';
const canAAdecrease = (ship, system) => canAA(ship, system) && system.canDecrease() != '';
const canAApropagate = (ship, system) => canAA(ship, system) && system.canPropagate() != '';
*/

//Gravitic Augmenter's own green menu (handles its own mode-cycling + activation/targeting):
// modes 1 & 2 in Initial Orders (phase 1), mode 3 rotation menu in Pre-Firing (phase 5).
//Modes 1 & 2 (Initial Orders): once an order exists the green menu hides and the standard
//"remove fire order" button (canRemoveFireOrder) handles cancellation.
//Mode 3 (Pre-Firing): the green menu stays visible after targeting so the player can still
//adjust the rotation; the standard remove button handles cancellation alongside it.
const canGraviticAugmenter = (ship, system) => system.name === 'GraviticAugmenter' && gamedata.isMyShip(ship) &&
	!system.stowed && //docked with its Orbital - stowed, cannot activate any mode
	!shipManager.power.isOffline(ship, system) &&
	//A spent & locked Augmenter (already committed a Mode 1/2 order in Initial Orders) is done for
	//the turn — don't re-offer its green menu (e.g. "Engage Gravity Shifting") in Pre-Firing.
	!(typeof system.isSpentLocked === 'function' && system.isSpentLocked()) &&
	((gamedata.gamephase === 1 && !weaponManager.hasFiringOrder(ship, system)) ||
	 (gamedata.gamephase === 5));

//Minor Thought Pulsar's own free thrust-allocation menu (replaces its old firing-mode presets).
//Shown in the Firing phase for my own units when the weapon is a live, powered Minor Thought Pulsar
//(before OR after targeting, so the player can pre-set the allocation and then adjust it).
const canMinorThoughtPulsar = (ship, system) => system.name === 'MinorThoughtPulsar' &&
	gamedata.gamephase === 3 && gamedata.isMyShip(ship) &&
	!system.stowed &&
	!shipManager.systems.isDestroyed(ship, system) &&
	!shipManager.power.isOffline(ship, system);

const canMineSettings = (ship, system) => (gamedata.gamephase === -1) && (ship.mine) && (ship.spawned == -1 && gamedata.turn == 1 || ship.spawned == gamedata.turn - 1) && (system.name == 'CaptorMine' || system.name == 'MineControllerDEW');

const canProxMineSettings = (ship, system) => (gamedata.gamephase === -1) && (ship.mine) && (ship.spawned == -1 && gamedata.turn == 1 || ship.spawned == gamedata.turn - 1) && (system.name == 'ProximityMine');

//can do something with Hyach Computer
const canBFCP = (ship, system) => (gamedata.gamephase === 1) && (system.name == 'hyachComputer');

/*const canBFCPdisplayCurrClass = (ship, system) => canBFCP(ship, system) && system.getCurrClass() != '';
const getBFCPcurrClassImg = (ship, system) => './img/systemicons/BFCPclasses/' + system.getCurrClass() + '.png';
const getBFCPcurrClassName = (ship, system) => system.getCurrClass();
const canBFCPincrease = (ship, system) => canBFCP(ship, system) && system.canIncrease() != '';
const canBFCPdecrease = (ship, system) => canBFCP(ship, system) && system.canDecrease() != '';
const canBFCPpropagate = (ship, system) => canBFCP(ship, system) && system.canPropagate() != '';
*/

//can do something with Hyach Specialists
//const canSpec = (ship, system) => (gamedata.gamephase === 1) && system.name === 'hyachSpecialists';
const canSpec = (ship, system) => system.name === 'hyachSpecialists';
/*const canSpecdisplayCurrClass = (ship, system) => canSpec(ship, system) && system.getCurrClass() != '';
const getSpeccurrClassImg = (ship, system) => './img/systemicons/Specialistclasses/' + system.getCurrClass() + '.png';
const getSpeccurrClassName = (ship, system) => system.getCurrClass();
const canSpecselect = (ship, system) => canSpec(ship, system) && system.canSelect() != '';
const canSpecunselect = (ship, system) => canSpec(ship, system) && system.canUnselect() != '';
const canSpecincrease = (ship, system) => canSpec(ship, system) && system.canUse() != '';
const canSpecdecrease = (ship, system) => canSpec(ship, system) && system.canDecrease() != '';*/

//can do something with Thirdspace Shields
const canTSShield = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThirdspaceShield';
/*const canTSShieldIncrease = (ship, system) => canTSShield(ship, system) && system.canIncrease() != '';
const canTSShieldDecrease = (ship, system) => canTSShield(ship, system) && system.canDecrease() != '';*/
//can do something with Thirdspace Shield Generator
const canTSShieldGen = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThirdspaceShieldGenerator';
/*const canTSShieldGendisplayCurrClass = (ship, system) => canTSShieldGen(ship, system) && system.getCurrClass() != '';
const getTSShieldGencurrClassImg = (ship, system) => './img/systemicons/ShieldGenclasses/' + system.getCurrClass() + '.png';
const getTSShieldGencurrClassName = (ship, system) => system.getCurrClass();
const canTSShieldGenSelect = (ship, system) => canTSShieldGen(ship, system) && system.canSelect() != '';*/

//can do something with Thought Shields
const canThoughtShield = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThoughtShield';
/*const canThoughtShieldIncrease = (ship, system) => canThoughtShield(ship, system) && system.canIncrease() != '';
const canThoughtShieldDecrease = (ship, system) => canThoughtShield(ship, system) && system.canDecrease() != '';*/
//can do something with Thirdspace Shield Generator
const canThoughtShieldGen = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThoughtShieldGenerator';
/*const canThoughtShieldGendisplayCurrClass = (ship, system) => canThoughtShieldGen(ship, system) && system.getCurrClass() != '';
const getThoughtShieldGencurrClassImg = (ship, system) => './img/systemicons/ShieldGenclasses/' + system.getCurrClass() + '.png';
const getThoughtShieldGencurrClassName = (ship, system) => system.getCurrClass();
const canThoughtShieldGenSelect = (ship, system) => canThoughtShieldGen(ship, system) && system.canSelect() != '';*/

//Self Repair queue menu. Shown for my own SelfRepair systems in EVERY phase so the player can
//review the priorities they set (it was too easy to forget them once phase 1 passed). It is only
//EDITABLE in Initial Orders (phase 1); in any other phase the list opens VIEW-ONLY (see
//canEditSelfRepairList -> readOnly prop, which disables the inputs/buttons/row-dragging).
const canSelfRepairList = (ship, system) => gamedata.isMyShip(ship) && (system.name == 'SelfRepair');
//Editable only in Initial Orders; elsewhere the menu is view-only.
const canEditSelfRepairList = (ship, system) => canSelfRepairList(ship, system) && gamedata.gamephase === 1;

const canStructureSelfRepairList = (ship, system) => gamedata.isMyShip(ship) && (system.name == 'StructureSelfRepair' || system.name == 'CoopStructureSelfRepair');   // GTS_Triad
const canEditStructureSelfRepairList = (ship, system) => canStructureSelfRepairList(ship, system) && gamedata.gamephase === 1;    // GTS_Triad

/* Pre-battle damage (PREBATTLE_DAMAGE_PLAN.md §5.2): allocate damage/destruction to a
   BOUGHT ship in the gamelobby. gamephase -2 is the lobby and nothing else, and in the
   lobby userid 0 marks the STORE blueprint (ShipWindowManager.isLeftSide uses the same
   test) - so this can never light up in game.php, where every other can* predicate
   already requires a positive gamephase.
   Flights are excluded: their damage is per-fighter ORDINAL and is edited in
   FighterDamageMenu instead (a fighter's individual weapons are not damageable).
   MINES likewise: a bulk purchase is one object plus a count, its damage is per-copy
   ORDINAL, and only its Structure can take any - so it gets MineDamageMenu (opened from
   the section health bar) and no per-system menu at all. canApplyMineDamage is what says
   the bar is still clickable for them. */
/* ⭐ Has the player already readied this fleet? Once they have, the fleet has been POSTed
   and nothing authored afterwards is ever submitted - so every pre-battle damage editor
   closes, exactly as buying, editing and removing units already do.
   Asked of gamedata (gamelobby.js) rather than restated here, so this and the lobby's own
   "You have already confirmed your fleet" refusals cannot drift apart. The typeof guard is
   for game.php, whose gamedata has no such method - though the gamephase === -2 test in
   front of every caller means it is never reached there. */
const fleetIsCommitted = () =>
	typeof gamedata.fleetIsCommitted === 'function' && gamedata.fleetIsCommitted();

export const canApplyPreBattleDamage = (ship, system) =>
	gamedata.gamephase === -2 && !fleetIsCommitted() && ship && ship.userid != 0
	&& !ship.flight && !ship.mine && !isPseudoSystem(system);

/* Per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §6.1): the damage predicate PLUS a
   live offer for this system. The offer list is generated server-side and already encodes
   the hull-age gate, the Ancient-weapon gate and the pseudo-system filter (D3/D6), so there
   is nothing to re-derive here - if the server offered nothing, there is nothing to show.

   ⚠️ The two predicates are DELIBERATELY ASYMMETRIC on `destroyed`, and must stay that way:
   canApplyPreBattleDamage has to keep passing for a destroyed system, because you must be
   able to un-destroy what you just destroyed (SystemIcon's destroyed short-circuit still
   opens this menu) - while this one must NOT, because you cannot refit a wreck (D11). Do
   not "tidy" them into one predicate.

   fleetIsCommitted() is inherited from the predicate above: a refit bought after Ready is
   never submitted but WOULD be charged locally. */
export const canApplySystemEnhancements = (ship, system) =>
	canApplyPreBattleDamage(ship, system)
	&& Boolean(window.systemEnhancements)
	&& !shipManager.systems.isDestroyed(ship, system)
	&& systemEnhancements.offersFor(ship, system).length > 0;

/* A bought lobby mine: the section health bar opens the synthetic per-copy editor.
   Guarded on the mine having structure worth dialling - a 1-box proximity mine has
   nothing to edit, since any damage at all would destroy it and a destroyed mine is
   simply one you did not buy. */
export const canApplyMineDamage = (ship) =>
	gamedata.gamephase === -2 && !fleetIsCommitted() && ship && ship.userid != 0
	&& Boolean(ship.mine) && battleDamage.mineMaxHealth(ship) > 1;

//A system with no structure of its own, hidden from the icon grid, or untargetable by
//construction (RammingAttack and friends) is an ABILITY, not a box on the SCS.
const isPseudoSystem = (system) => !system
	|| !(system.maxhealth > 0)
	|| system.isTargetable === false
	|| Boolean(system.hideInShipWindow)
	|| Array.isArray(system.systems);   //a fighter unit inside a flight

/* ⚠️ The gamelobby (gamephase -2) does NOT load client/weaponManager.js, and several
   predicates below dereference `weaponManager` with no phase guard in front of it
   (canAddShots, canRemoveFireOrderMulti, canRemoveFireOrder, canSelfIntercept…). Walking
   them there is a ReferenceError, not a `false`. Pre-battle damage is the only action the
   lobby offers and every other predicate requires a positive gamephase anyway, so answer
   the question directly instead of evaluating them. Same branch in hasStyledMenu and in
   render() below - all three walk the same list. */
export const canDoAnything = (ship, system) => {
	//EITHER lobby editor opens the menu - see hasStyledMenu, which walks the same pair.
	if (gamedata.gamephase === -2) {
		return canApplyPreBattleDamage(ship, system) || canApplySystemEnhancements(ship, system);
	}

	return canOffline(ship, system) || canOnline(ship, system)
		|| canOverload(ship, system) || canStopOverload(ship, system) || canBoost(ship, system)
		|| canDeBoost(ship, system) || canAddShots(ship, system) || canReduceShots(ship, system) || canRemoveFireOrderMulti(ship, system)
		|| canRemoveFireOrder(ship, system) || canChangeFiringMode(ship, system)
		|| canSelfIntercept(ship, system) || canRemIntercept(ship, system) || canAA(ship, system) || canBFCP(ship, system) || canSpec(ship, system) || canTSShield(ship, system)
		|| canThoughtShield(ship, system) || canTSShieldGen(ship, system) || canThoughtShieldGen(ship, system)
		|| canSelfRepairList(ship, system) || canActivate(ship, system) || canDeactivate(ship, system) || canPowerCapacitor(ship, system) || canJumpEngineMenu(ship, system) || canSystemActivation(ship, system) || canSelectAllWeapons(ship, system)
		|| canMineSettings(ship, system) || canProxMineSettings(ship, system) || canGraviticAugmenter(ship, system) || canMinorThoughtPulsar(ship, system);
};

//powerLocked: system may not be voluntarily powered down right now (Antigravity Beam while its Kirishiac Orbital is deployed)
const canOffline = (ship, system) => gamedata.gamephase === 1 && (system.canOffLine || system.powerReq > 0) && !system.powerLocked && !shipManager.power.isOffline(ship, system) && !shipManager.power.getBoost(system) && !weaponManager.hasFiringOrder(ship, system);

// A system forced offline by a cooldown / forced-shutdown crit cannot be powered back
// on by the player (it auto-recovers when the crit expires); onOnlineClicked/onlineAll
// also reject it at the source.
// JUMP_POINTS_PLAN.md Stage 5: a ship maintaining a jump point stays dark for the WHOLE turn, so a
// system it shut down cannot be powered back on until Maintain is switched off. setOnline refuses
// it at the source too; this is what makes the button read as unavailable rather than warn on click.
const canOnline = (ship, system) => gamedata.gamephase === 1 && shipManager.power.isOffline(ship, system) && !shipManager.power.isForcedOffline(ship, system) && !shipManager.power.isVortexLockedOffline(ship, system);

//change December 2021: can start overloading even if no Power is available, to be balanced at end of turn
const canOverload = (ship, system) => gamedata.gamephase === 1 && !shipManager.power.isOffline(ship, system) && system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system) /*&& shipManager.power.canOverload(ship, system)*/;

const canStopOverload = (ship, system) => gamedata.gamephase === 1 && system.weapon && system.overloadable && shipManager.power.isOverloading(ship, system) && (system.overloadshots >= system.extraoverloadshots || system.overloadshots == 0);

const canBoost = (ship, system) => system.boostable && gamedata.gamephase === 1 && shipManager.power.canBoost(ship, system) && (!system.isScanner() || system.id == shipManager.power.getHighestSensorsId(ship)) && system.name !== 'ThirdspaceShieldGenerator' && system.name !== 'powerCapacitor' && system.name !== 'PowerCapacitor';

const canDeBoost = (ship, system) => gamedata.gamephase === 1 && Boolean(shipManager.power.getBoost(system)) && system.name !== 'ThirdspaceShieldGenerator' && system.name !== 'powerCapacitor' && system.name !== 'PowerCapacitor';
/* Code for boosting systems in other phases.  Not longer need anymore since Shading Field got converted to notes
const isBoostPhase = (system) => {
	// If boostOtherPhases is an array, check if the current gamephase is included
	if (system.boostOtherPhases.length > 0) {
		return system.boostOtherPhases.includes(gamedata.gamephase);
	}
	
	// Default: only in phase 1
	return gamedata.gamephase === 1;
};
	
const canBoost = (ship, system) =>
	system.boostable &&
	isBoostPhase(system) &&
	shipManager.power.canBoost(ship, system) &&
	(!system.isScanner() || system.id === shipManager.power.getHighestSensorsId(ship));
	
const canDeBoost = (ship, system) =>
	isBoostPhase(system) && 
	shipManager.power.canDeboost(ship, system) && 
	Boolean(shipManager.power.getBoost(system));
*/
//getFiringOrder returns whichever current-turn order it finds first, INTERCEPT orders included, so
//without this guard the +/- shots buttons would offer to edit a manual interception's shot count.
//Interception resolution ignores ->shots entirely, so it was only ever cosmetic - but the buttons
//should not appear for an order they cannot change.
const isShotEditableOrder = (ship, system) => {
	const fire = weaponManager.getFiringOrder(ship, system);
	return fire && fire.type !== 'intercept' && fire.type !== 'selfIntercept' ? fire : null;
};

const canAddShots = (ship, system) => {
	if (isHoldingVortex(ship, system)) return false;
	if (!system.weapon || !system.canChangeShots || !weaponManager.hasFiringOrder(ship, system)) return false;
	const fire = isShotEditableOrder(ship, system);
	return Boolean(fire) && fire.shots < system.maxVariableShots;
};

const canReduceShots = (ship, system) => {
	if (isHoldingVortex(ship, system)) return false;
	if (!system.weapon || !system.canChangeShots || !weaponManager.hasFiringOrder(ship, system)) return false;
	const fire = isShotEditableOrder(ship, system);
	return Boolean(fire) && fire.shots > 1;
};

/* JUMP_POINTS_PLAN.md Stage 5 - a Jump Engine that is HOLDING a vortex open shows none of the
   fire-order controls. It cannot declare anything (one vortex per ship at a time, enforced in
   Firing::getVortexDeclarationBlock), and the order it does hold is the Maintain declaration -
   which is cancelled by throwing the Maintain toggle to OFF, not by a generic "remove fire order"
   button that would leave the ship's systems shut down with nothing holding them that way.

   Deliberately gated on HOLDING A VORTEX, not on being a jump engine: on the turn a vortex is
   DECLARED there is no unit yet, and the ordinary remove-and-redeclare idiom is still how the
   player re-aims it (plan section 3.5). */
const isHoldingVortex = (ship, system) => system.name === 'jumpEngine'
	&& typeof system.getHeldVortex === 'function' && Boolean(system.getHeldVortex());

const canRemoveFireOrderMulti = (ship, system) => system.weapon && weaponManager.hasOrderForMode(system) && system.canSplitShots && !isHoldingVortex(ship, system);
//A "spent & locked" Gravitic Augmenter (order committed, outside its declaration phase) must not
//offer a remove button — e.g. its ballistic Mode 1/2 order reads as active in the Firing phase via
//hasFiringOrder's phase-3 branch, but Initial-Orders support cannot be un-fired mid-turn.
const canRemoveFireOrder = (ship, system) => system.weapon && weaponManager.hasFiringOrder(ship, system)
	&& !(typeof system.isSpentLocked === 'function' && system.isSpentLocked())
	&& !isHoldingVortex(ship, system);

//The Gravitic Augmenter cycles its own modes inside its green menu, and the Minor Thought Pulsar
//replaces firing modes entirely with its free thrust-allocation menu — both opt out of the generic
//firing-mode selector grid. So does anything carrying hideFiringModeSelector: the Jump Engine's
//seven "modes" are the vortex FACING (JUMP_POINTS_PLAN.md section 3.1), which is chosen on the map
//as part of the declaration, not by picking a letter out of a grid.
const canChangeFiringMode = (ship, system) => system.weapon && !ship.mine && !system.stowed && !system.hideFiringModeSelector && system.name !== 'GraviticAugmenter' && system.name !== 'MinorThoughtPulsar' && ((gamedata.gamephase === 1 && system.ballistic) || (gamedata.gamephase === 5 && system.preFires) || (gamedata.gamephase === 3 && !system.ballistic && !system.preFires)) && (!weaponManager.hasFiringOrder(ship, system) || system.multiModeSplit) && (Object.keys(system.firingModes).length > 1);

//can declare eligibility for interception: charged, recharge time >1 turn, intercept rating >0, no firing order
const canSelfIntercept = (ship, system) => system.weapon && weaponManager.canSelfInterceptSingle(ship, system);
//Non-split weapons hold only a single order, so their self-intercept is cancelled via the
//top-row "remove fire order" button; only split-capable weapons need an in-menu intercept-remove
//button to peel off one of several orders.
const canRemIntercept = (ship, system) => system.weapon && system.canSplitShots && weaponManager.canRemInterceptSingle(ship, system);

//GraviticAugmenter excluded: its Activate/Deactivate lives in its own green menu, not the generic SystemActivation box.
//jumpEngine joins the exclusions: its activation pair is the Maintain toggle, which JumpEngineMenu
//renders as a labelled row of its own (JUMP_POINTS_PLAN.md Stage 5). A bare Activate button beside
//it would be the same switch twice, unlabelled.
const canActivate = (ship, system) => system.canActivate && typeof system.canActivate === 'function' && system.canActivate() && system.name !== 'powerCapacitor' && system.name !== 'PowerCapacitor' && system.name !== 'GraviticAugmenter' && system.name !== 'jumpEngine'; //Used to manually fire weapons/systems that don't need to target e.g. Second Sight/Thoughwave
const canDeactivate = (ship, system) => system.canDeactivate && typeof system.canDeactivate === 'function' && system.canDeactivate() && system.name !== 'powerCapacitor' && system.name !== 'PowerCapacitor' && system.name !== 'GraviticAugmenter' && system.name !== 'jumpEngine';

//JUMP_POINTS_PLAN.md Stage 5 - the Maintain panel. Shown while the engine COULD be told to maintain
//(canMaintainVortex: phase 1, my ship, engine alive and powered, a vortex of ours open and in range,
//and not on the turn the four-turn cap closes it) and while it already IS, so the player can change
//their mind before committing.
const canJumpEngineMenu = (ship, system) => system.name === 'jumpEngine'
	&& typeof system.canMaintainVortex === 'function'
	&& (system.canMaintainVortex() || system.canDeactivate());

const canPowerCapacitor = (ship, system) => {
	if (system.name === 'powerCapacitor' || system.name === 'PowerCapacitor') {
		//console.log("canPowerCapacitor TRUE");
		return true;
	}
	//console.log("canPowerCapacitor FALSE: ", system.name);
	return false;
}

export const canSystemPowerSettings = (ship, system) => {
	return canOffline(ship, system) || canOnline(ship, system) ||
		canOverload(ship, system) || canStopOverload(ship, system) ||
		(system.boostable && (canBoost(ship, system) || canDeBoost(ship, system)));
};

export const canSystemActivation = (ship, system) => {
	if (canPowerCapacitor(ship, system)) return false;//power capacitor is handled by its own component
	if (system.name === 'GraviticAugmenter') return false;//handled by its own green menu, not the generic activation box

	if (system.canActivate && typeof system.canActivate === 'function' && system.canActivate()) return true;
	if (system.canDeactivate && typeof system.canDeactivate === 'function' && system.canDeactivate()) return true;

	return false;
};

export const hasStyledMenu = (ship, system) => {
	//see the note on canDoAnything: no weaponManager in the lobby. The menu opens if EITHER
	//lobby editor has something to offer - the enhancement half can pass where the damage
	//half does not, and vice versa (they are asymmetric on `destroyed`).
	if (gamedata.gamephase === -2) {
		return canApplyPreBattleDamage(ship, system) || canApplySystemEnhancements(ship, system);
	}

	return canAA(ship, system) ||
		canBFCP(ship, system) ||
		canSpec(ship, system) ||
		canMineSettings(ship, system) ||
		canProxMineSettings(ship, system) ||
		canGraviticAugmenter(ship, system) ||
		canMinorThoughtPulsar(ship, system) ||
		(canTSShield(ship, system) || canTSShieldGen(ship, system)) ||
		(canThoughtShield(ship, system) || canThoughtShieldGen(ship, system)) ||
		canSelfRepairList(ship, system) ||
		canStructureSelfRepairList(ship, system) ||    // GTS_Triad
		canPowerCapacitor(ship, system) ||
		canSystemPowerSettings(ship, system) ||
		canSystemActivation(ship, system) ||
		canChangeFiringMode(ship, system) ||
		canSelfIntercept(ship, system) ||
		canRemIntercept(ship, system);
};


export default SystemInfoButtons;

/* //Replaced with FiringModeSelecter component - DK 1.2.26
const getFiringModes = (ship, system, changeFiringMode, allChangeFiringMode) => {
	if (system.parentId >= 0) {
		let parentSystem = shipManager.systems.getSystem(ship, system.parentId);
	
		if (parentSystem.parentId >= 0) {
			parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
			//$(".parentsystem_" + parentSystem.id).addClass("modes");
			//let modebutton = $(".mode", $(".parentsystem_" + parentSystem.id));
		} else {
			//$(".parentsystem_" + parentSystem.id).addClass("modes");
			//let modebutton = $(".mode", systemwindow);
		}
	
		console.log(parentSystem.firingModes[parentSystem.firingMode]);
		//modebutton.html("<span>" + parentSystem.firingModes[parentSystem.firingMode].substring(0, 1) + "</span>");
	} else {
		
		console.log(system.firingModes, system.firingMode);

		const firingMode = system.firingModes[system.firingMode + 1] ? system.firingModes[system.firingMode + 1] : system.firingModes[1];

		let img = '';

		if (system.iconPath) {
			img = `./img/systemicons/${system.iconPath}`;
		} else {
			img = `./img/systemicons/${system.name}.png`;
		}
		
		var textTitle = "set mode " + firingMode + " (R = mass)"; 
		return <Button title={textTitle} onClick={changeFiringMode} onContextMenu={allChangeFiringMode}  img={img}>{firingMode.substring(0, system.modeLetters)}</Button>;
	}
}

/*getFiringModesCurr - display current firing mode (no effect on click)* /
const getFiringModesCurr = (ship, system) => {
	if (system.parentId >= 0) { //...obsolete...
		/*
		let parentSystem = shipManager.systems.getSystem(ship, system.parentId);	
		if (parentSystem.parentId >= 0) {
			parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
		} else {
		}
		* /
	} else {
		const firingMode = system.firingModes[system.firingMode] ? system.firingModes[system.firingMode] : system.firingModes[1];
		let img = '';
		if (system.iconPath) {
			img = `./img/systemicons/${system.iconPath}`;
		} else {
			img = `./img/systemicons/${system.name}.png`;
		}
		
		var textTitle = "current mode: " + firingMode; 
		return <Button title={textTitle} img={img}>{firingMode.substring(0, system.modeLetters)}</Button>;
	}
} //endof getFiringModesCurr
*/
