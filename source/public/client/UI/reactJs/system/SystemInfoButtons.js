import * as React from "react";
import styled from "styled-components"
import {Clickable} from "../styled";

const Container = styled.div`
    display:flex;
`;

const Button = styled.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${props => props.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    ${Clickable}
`;

class SystemInfoButtons extends React.Component {

    online(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.onOnlineClicked(ship, system);
        webglScene.customEvent('CloseSystemInfo');
    }

    offline(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        if (!canOffline(ship, system)) {
            return;
        }

        shipManager.power.onOfflineClicked(ship, system);
        webglScene.customEvent('CloseSystemInfo');
	}
	
	allOnline(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.onlineAll(ship, system);
        webglScene.customEvent('CloseSystemInfo');
    }

    allOffline(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        if (!canOffline(ship, system)) {
            return;
        }

        shipManager.power.offlineAll(ship, system);
        webglScene.customEvent('CloseSystemInfo');
    }
	
    overload(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.onOverloadClicked(ship, system);
        webglScene.customEvent('CloseSystemInfo');
    }

    stopOverload(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.onStopOverloadClicked(ship, system);
        webglScene.customEvent('CloseSystemInfo');
    }

    boost(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.clickPlus(ship, system);
    }

    deboost(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.clickMinus(ship, system);
	}

	addShots(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canAddShots(ship, system)) {
            return;
		}
		
        weaponManager.changeShots(ship, system, 1);
    }

    reduceShots(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canReduceShots(ship, system)) {
            return;
		}
		
        weaponManager.changeShots(ship, system, -1);
	}

	removeFireOrderMulti(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canRemoveFireOrderMulti(ship, system)) {
            return;
		}
		
        weaponManager.removeFiringOrderMulti(ship, system);
//        webglScene.customEvent('CloseSystemInfo');
	}
	
	removeFireOrder(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canRemoveFireOrder(ship, system)) {
            return;
		}
		
        weaponManager.removeFiringOrder(ship, system);
        webglScene.customEvent('CloseSystemInfo');
	}
	removeFireOrderAll(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canRemoveFireOrder(ship, system)) {
            return;
		}
        weaponManager.removeFiringOrderAll(ship, system);
        webglScene.customEvent('CloseSystemInfo');
	}

	allChangeFiringMode(e) {
		e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
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
		var similarWeapons = new Array();
		for (var i = 0; i < allWeapons.length; i++) {
			if (system.displayName === allWeapons[i].displayName) {
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
			while (weapon.firingMode!=modeSet && iterations < 2){
				weaponManager.onModeClicked(ship, weapon);
				if(weapon.firingMode == 1){
					iterations++; //if an entire iteration passed and mode wasn't found, then mode cannot be reached	
				}
			}
			//reset mode back if necessary! (this one is guaranteed to be available)
			if (weapon.firingMode!=modeSet) while (weapon.firingMode!=originalMode){
				weaponManager.onModeClicked(ship, weapon);
			}
		}
		//webglScene.customEvent('CloseSystemInfo');
	}
	
	changeFiringMode(e) {
        	e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canChangeFiringMode(ship, system)) {
            		return;
		}		
		weaponManager.onModeClicked(ship, system);
		//webglScene.customEvent('CloseSystemInfo');
	}
	
	
	/*declare this weapon to be eligible for defensive fire this turn*/
	declareSelfIntercept(e) {
        	e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canSelfIntercept(ship, system)) {
            		return;
		}		
		weaponManager.onDeclareSelfInterceptSingle(ship, system);
		if(system.canSplitShots) var finished = system.checkFinished(); //Do not close system info buttons if player can still selfintercept
		if(finished) webglScene.customEvent('CloseSystemInfo');
	}	
	/*declare all similar undeclared weapons for defensive fire this turn*/
	declareSelfInterceptAll(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		weaponManager.onDeclareSelfInterceptSingleAll(ship,system);
		if(weapon.canSplitShots) var finished = weapon.checkFinished();
		if(finished) webglScene.customEvent('CloseSystemInfo');
	}	

	/*declare this weapon to be eligible for defensive fire this turn*/
	remSelfIntercept(e) {
        	e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canRemIntercept(ship, system)) {
            		return;
		}		
		weaponManager.removeSelfInterceptSingle(ship, system);
		//if(system.canSplitShots) var finished = system.checkFinished(); //Do not close system info buttons if player can still selfintercept
		//if(finished) webglScene.customEvent('CloseSystemInfo');
	}	

	activate(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doActivate();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}	
	deactivate(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDeactivate();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	/*switch Adaptive Armor, Hyach Computer or Specialists display to next damage/FC class*/
	nextCurrClass(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.nextCurrClass();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	prevCurrClass(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.prevCurrClass();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}		
	
	/*Adaptive Armor increase rating for current class*/
	AAincrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doIncrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}		
	/*Adaptive Armor decrease rating for current class*/
	AAdecrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	/*Adaptive Armor propagate setting for current damage type*/
	AApropagate(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
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
				for (var iFtr=0;iFtr<otherUnit.systems.length;iFtr++){
					var ftr = otherUnit.systems[iFtr];
					if (ftr) for (var iSys=0;iSys<ftr.systems.length;iSys++){
						var ctrl = ftr.systems[iSys];
						if (ctrl) if (ctrl.displayName == "Adaptive Armor Controller"){
							allOwnAA.push(ctrl);
							break;//no point looking for SECOND AA Controller on a fighter
						}
					}
				}				
				/*
				
				
				allOwnAA = ship.systems
					.map(fighter => fighter.systems)
					.filter(system => system.displayName = "Adaptive Armor Controller");
					*/
			} else {
				for (var iSys=0;iSys<otherUnit.systems.length;iSys++){
					var ctrl = otherUnit.systems[iSys];
					if (ctrl.displayName == "Adaptive Armor Controller"){
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
			while(
				ctrl.getCurrAllocated() < allocated // level lower than desired
				&& ctrl.canIncrease() //level can be increased
			){
				ctrl.doIncrease();
			}
		}
		
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

	
	/*Hyach Computer increase rating for current class*/
	BFCPincrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doIncrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}		
	/*Hyach Computer decrease rating for current class*/
	BFCPdecrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	/*Hyach Computer propagate setting for current damage type*/
	BFCPpropagate(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
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
				for (var iFtr=0;iFtr<otherUnit.systems.length;iFtr++){
					var ftr = otherUnit.systems[iFtr];
					if (ftr) for (var iSys=0;iSys<ftr.systems.length;iSys++){
						var ctrl = ftr.systems[iSys];
						if (ctrl) if (ctrl.displayName == "Computer"){
							allOwnBFCP.push(ctrl);
							break;//no point looking for SECOND Computer on a fighter, actually Hyach should never have any, so just future proofing.
						}
					}
				}				

			} else {
				for (var iSys=0;iSys<otherUnit.systems.length;iSys++){
					var ctrl = otherUnit.systems[iSys];
					if (ctrl.displayName == "Computer"){
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
			while(
				ctrl.getCurrAllocated() < allocated // level lower than desired
				&& ctrl.canIncrease() //level can be increased
			){
				ctrl.doIncrease();
			}
		}
		
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

	/*Hyach Specialists increase rating for current class*/
	Specselect(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doSelect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}	
	Specunselect(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doUnselect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

	Specincrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doUse();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}		
	/*Hyach Specialists decrease rating for current class*/
	Specdecrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
	/*Thirdspace Shield increase health*/
	TSShieldIncrease25(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doIncrease25();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldIncrease10(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doIncrease10();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldIncrease5(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doIncrease5();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}	
	TSShieldIncrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doIncrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}	
	/*Thirdspace Shield decrease health*/
	TSShieldDecrease(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldDecrease5(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease5();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}	
	TSShieldDecrease10(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease10();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	TSShieldDecrease25(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doDecrease25();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}	
	/*Thirdspace Shield Generator Presets*/
	TSShieldGenSelect(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doSelect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

	/*Thought Shield Generator Presets*/
	ThoughtShieldGenSelect(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.doSelect();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
					
	/*Self Repair - display next system in need of repairs*/
	nextSRsystem(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.getNextSystem();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	/*Self Repair - change system priority*/
	SRPriorityUp(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.setRepairPriority(20);
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	SRPriorityDown(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.setRepairPriority(0);
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	SRPriorityCancel(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		system.setRepairPriority(-1);
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}
	
    render() {
		const {ship, selectedShip, system} = this.props;
		
		if (!canDoAnything) {
			return null;
		}
		
        return (
            <Container>
				{canOnline(ship, system) && <Button title="Power on (RMB = All systems selected)" onClick={this.online.bind(this)} onContextMenu={this.allOnline.bind(this)} img="./img/on.png"></Button>}
                {canOffline(ship, system) && <Button title="Power off (RMB = All systems selected)" onClick={this.offline.bind(this)} onContextMenu={this.allOffline.bind(this)} img="./img/off.png"></Button>}
                {canOverload(ship, system) && <Button title="Overload" onClick={this.overload.bind(this)} img="./img/overload.png"></Button>}
                {canStopOverload(ship, system) && <Button title="Stop overload" onClick={this.stopOverload.bind(this)} img="./img/overloading.png"></Button>}
                {canDeBoost(ship, system) && <Button title="Remove boost"onClick={this.deboost.bind(this)} img="./img/minussquare.png"></Button>}
                {canBoost(ship, system) && <Button title="Boost" onClick={this.boost.bind(this)} img="./img/plussquare.png"></Button>}
                {canAddShots(ship, system) && <Button title="More shots"onClick={this.addShots.bind(this)} img="./img/plussquare.png"></Button>}
                {canReduceShots(ship, system) && <Button title="Less shots" onClick={this.reduceShots.bind(this)} img="./img/minussquare.png"></Button>}
				{canRemoveFireOrderMulti(ship, system) && <Button title="Remove last fire order" onClick={this.removeFireOrderMulti.bind(this)} img="./img/unfiringSmall.png"></Button>}
				{canRemoveFireOrder(ship, system) && <Button title="Remove all fire orders (RMB = All weapons selected)" onClick={this.removeFireOrder.bind(this)} onContextMenu={this.removeFireOrderAll.bind(this)} img="./img/firing.png"></Button>}
				
				{canChangeFiringMode(ship, system) && getFiringModesCurr(ship, system)}
				{canChangeFiringMode(ship, system) && getFiringModes(ship, system, this.changeFiringMode.bind(this), this.allChangeFiringMode.bind(this))}
				{canSelfIntercept(ship, system) && <Button title="Allow interception (RMB = All systems selected)" onClick={this.declareSelfIntercept.bind(this)} onContextMenu={this.declareSelfInterceptAll.bind(this)} img="./img/addSelfIntercept.png"></Button>}
				{canRemIntercept(ship, system) && <Button title="Remove an intercept order" onClick={this.remSelfIntercept.bind(this)} onContextMenu={this.remSelfIntercept.bind(this)} img="./img/remSelfIntercept.png"></Button>}				

				{canActivate(ship, system) && <Button title="Activate" onClick={this.activate.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}
				{canDeactivate(ship, system) && <Button title="Deactivate" onClick={this.deactivate.bind(this)} img="./img/systemicons/Specialistclasses/unselect.png"></Button>}		

				{canAAdisplayCurrClass(ship, system) && <Button title={getAAcurrClassName(ship,system)} img={getAAcurrClassImg(ship,system)}></Button>}
				{canAAdisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canAAdisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/AAclasses/iconNext.png"></Button>}
				{canAAincrease(ship, system) && <Button onClick={this.AAincrease.bind(this)} img="./img/systemicons/AAclasses/iconPlus.png"></Button>}
				{canAAdecrease(ship, system) && <Button onClick={this.AAdecrease.bind(this)} img="./img/systemicons/AAclasses/iconMinus.png"></Button>}
				{canAApropagate(ship, system) && <Button title="Propagate setting" onClick={this.AApropagate.bind(this)} img="./img/systemicons/AAclasses/iconPropagate.png"></Button>}
				
				{canBFCPdisplayCurrClass(ship, system) && <Button title={getBFCPcurrClassName(ship,system)} img={getBFCPcurrClassImg(ship,system)}></Button>}
				{canBFCPdisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/BFCPclasses/iconNext.png"></Button>}
				{canBFCPincrease(ship, system) && <Button onClick={this.BFCPincrease.bind(this)} img="./img/systemicons/BFCPclasses/iconPlus.png"></Button>}
				{canBFCPdecrease(ship, system) && <Button onClick={this.BFCPdecrease.bind(this)} img="./img/systemicons/BFCPclasses/iconMinus.png"></Button>}
				{canBFCPpropagate(ship, system) && <Button title="Propagate setting" onClick={this.BFCPpropagate.bind(this)} img="./img/systemicons/BFCPclasses/iconPropagate.png"></Button>}
			
				{canSpecdisplayCurrClass(ship, system) && <Button title={getSpeccurrClassName(ship,system)} img={getSpeccurrClassImg(ship,system)}></Button>}
				{canSpecdisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canSpecdisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconNext.png"></Button>}
				{canSpecselect(ship, system) && <Button onClick={this.Specselect.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}
				{canSpecunselect(ship, system) && <Button onClick={this.Specunselect.bind(this)} img="./img/systemicons/Specialistclasses/unselect.png"></Button>}					
				{canSpecincrease(ship, system) && <Button onClick={this.Specincrease.bind(this)} img="./img/systemicons/Specialistclasses/iconPlus.png"></Button>}
				{canSpecdecrease(ship, system) && <Button onClick={this.Specdecrease.bind(this)} img="./img/systemicons/Specialistclasses/iconMinus.png"></Button>}

				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus25.png"></Button>}
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus10.png"></Button>}
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus5.png"></Button>}	
				{canTSShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease.bind(this)} img="./img/systemicons/BFCPclasses/iconPlus.png"></Button>}				
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease.bind(this)} img="./img/systemicons/BFCPclasses/iconMinus.png"></Button>}				
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus5.png"></Button>}
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus10.png"></Button>}
				{canTSShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus25.png"></Button>}
				{canTSShieldGendisplayCurrClass(ship, system) && <Button title={getTSShieldGencurrClassName(ship,system)} img={getTSShieldGencurrClassImg(ship,system)}></Button>}
				{canTSShieldGendisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canTSShieldGendisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconNext.png"></Button>}					
				{canTSShieldGenSelect(ship, system) && <Button onClick={this.TSShieldGenSelect.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}		

				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus25.png"></Button>}
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus10.png"></Button>}
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconPlus5.png"></Button>}	
				{canThoughtShieldIncrease(ship, system) && <Button onClick={this.TSShieldIncrease.bind(this)} img="./img/systemicons/BFCPclasses/iconPlus.png"></Button>}				
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease.bind(this)} img="./img/systemicons/BFCPclasses/iconMinus.png"></Button>}				
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease5.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus5.png"></Button>}
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease10.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus10.png"></Button>}
				{canThoughtShieldDecrease(ship, system) && <Button onClick={this.TSShieldDecrease25.bind(this)} img="./img/systemicons/ShieldGenclasses/iconMinus25.png"></Button>}				
				{canThoughtShieldGendisplayCurrClass(ship, system) && <Button title={getTSShieldGencurrClassName(ship,system)} img={getTSShieldGencurrClassImg(ship,system)}></Button>}
				{canThoughtShieldGendisplayCurrClass(ship, system) && <Button title="Previous" onClick={this.prevCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconPrev.png"></Button>}
				{canThoughtShieldGendisplayCurrClass(ship, system) && <Button title="Next" onClick={this.nextCurrClass.bind(this)} img="./img/systemicons/Specialistclasses/iconNext.png"></Button>}					
				{canThoughtShieldGenSelect(ship, system) && <Button onClick={this.TSShieldGenSelect.bind(this)} img="./img/systemicons/Specialistclasses/select.png"></Button>}

											 
				{canSRdisplayCurrSystem(ship, system) && <Button title="Next" onClick={this.nextSRsystem.bind(this)} img="./img/systemicons/AAclasses/iconNext.png"></Button>}
				{canSRdisplayCurrSystem(ship, system) && <Button title={getSRdescription(ship,system)} img={getSRicon(ship,system)}></Button>}
				{canSRdisplayCurrSystem(ship, system) && <Button title="Highest priority" onClick={this.SRPriorityUp.bind(this)} img="./img/iconSRHigh.png"></Button>}
				{canSRdisplayCurrSystem(ship, system) && <Button title="Disable repair" onClick={this.SRPriorityDown.bind(this)} img="./img/iconSRLow.png"></Button>}
				{canSRdisplayCurrSystem(ship, system) && <Button title="Default priority" onClick={this.SRPriorityCancel.bind(this)} img="./img/iconSRCancel.png"></Button>}
				
            </Container>
        )
    }
}

//can do something with Adaptive Armor Controller
const canAA = (ship,system) => (gamedata.gamephase === 1) && (system.name == 'adaptiveArmorController'); 
const canAAdisplayCurrClass = (ship,system) => canAA(ship,system) && system.getCurrClass()!='';
const getAAcurrClassImg = (ship,system) => './img/systemicons/AAclasses/'+system.getCurrClass()+'.png'; 
const getAAcurrClassName = (ship,system) => system.getCurrClass(); 
const canAAincrease = (ship,system) => canAA(ship,system) && system.canIncrease()!='';
const canAAdecrease = (ship,system) => canAA(ship,system) && system.canDecrease()!='';
const canAApropagate = (ship,system) => canAA(ship,system) && system.canPropagate()!='';

//can do something with Hyach Computer
const canBFCP = (ship,system) => (gamedata.gamephase === 1) && (system.name == 'hyachComputer'); 
const canBFCPdisplayCurrClass = (ship,system) => canBFCP(ship,system) && system.getCurrClass()!='';
const getBFCPcurrClassImg = (ship,system) => './img/systemicons/BFCPclasses/'+system.getCurrClass()+'.png'; 
const getBFCPcurrClassName = (ship,system) => system.getCurrClass(); 
const canBFCPincrease = (ship,system) => canBFCP(ship,system) && system.canIncrease()!='';
const canBFCPdecrease = (ship,system) => canBFCP(ship,system) && system.canDecrease()!='';
const canBFCPpropagate = (ship,system) => canBFCP(ship,system) && system.canPropagate()!='';

//can do something with Hyach Specialists
//const canSpec = (ship, system) => (gamedata.gamephase === 1) && system.name === 'hyachSpecialists';
const canSpec = (ship, system) => system.name === 'hyachSpecialists';
const canSpecdisplayCurrClass = (ship,system) => canSpec(ship,system) && system.getCurrClass()!='';
const getSpeccurrClassImg = (ship,system) => './img/systemicons/Specialistclasses/'+system.getCurrClass()+'.png'; 
const getSpeccurrClassName = (ship,system) => system.getCurrClass();
const canSpecselect = (ship,system) => canSpec(ship,system) && system.canSelect()!='';
const canSpecunselect = (ship,system) => canSpec(ship,system) && system.canUnselect()!=''; 
const canSpecincrease = (ship,system) => canSpec(ship,system) && system.canUse()!='';
const canSpecdecrease = (ship,system) => canSpec(ship,system) && system.canDecrease()!='';

//can do something with Thirdspace Shields
const canTSShield = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThirdspaceShield';
const canTSShieldIncrease= (ship,system) => canTSShield (ship,system) && system.canIncrease()!='';
const canTSShieldDecrease= (ship,system) => canTSShield (ship,system) && system.canDecrease()!='';
//can do something with Thirdspace Shield Generator
const canTSShieldGen = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThirdspaceShieldGenerator';
const canTSShieldGendisplayCurrClass = (ship,system) => canTSShieldGen(ship,system) && system.getCurrClass()!='';
const getTSShieldGencurrClassImg = (ship,system) => './img/systemicons/ShieldGenclasses/'+system.getCurrClass()+'.png'; 
const getTSShieldGencurrClassName = (ship,system) => system.getCurrClass();
const canTSShieldGenSelect = (ship,system) => canTSShieldGen (ship,system) && system.canSelect()!='';

//can do something with Thought Shields
const canThoughtShield = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThoughtShield';
const canThoughtShieldIncrease = (ship,system) => canThoughtShield (ship,system) && system.canIncrease()!='';
const canThoughtShieldDecrease= (ship,system) => canThoughtShield (ship,system) && system.canDecrease()!='';
//can do something with Thirdspace Shield Generator
const canThoughtShieldGen = (ship, system) => (gamedata.gamephase === 1) && system.name === 'ThoughtShieldGenerator';
const canThoughtShieldGendisplayCurrClass = (ship,system) => canThoughtShieldGen(ship,system) && system.getCurrClass()!='';
const getThoughtShieldGencurrClassImg = (ship,system) => './img/systemicons/ShieldGenclasses/'+system.getCurrClass()+'.png'; 
const getThoughtShieldGencurrClassName = (ship,system) => system.getCurrClass();
const canThoughtShieldGenSelect= (ship,system) => canThoughtShieldGen (ship,system) && system.canSelect()!='';

//can do something with Self Repair...
const canSRdisplayCurrSystem = (ship,system) => (gamedata.gamephase === 1) && (system.name == 'SelfRepair') && (system.getCurrSystem()>=0); 
const getSRdescription = (ship,system) => system.getCurrSystemDescription(); 
const getSRicon = (ship,system) => system.getCurrSystemIcon(); 

export const canDoAnything = (ship, system) => canOffline(ship, system) || canOnline(ship, system) 
	|| canOverload(ship, system) || canStopOverload(ship, system) || canBoost(ship, system) 
	|| canDeBoost(ship, system) || canAddShots(ship, system) || canReduceShots(ship, system) || canRemoveFireOrderMulti(ship, system)
	|| canRemoveFireOrder(ship, system) || canChangeFiringMode(ship, system)
	|| canSelfIntercept(ship, system) || canRemIntercept(ship, system) || canAA(ship,system) || canBFCP(ship, system) || canSpec(ship,system) || canTSShield(ship,system) 
	|| canThoughtShield(ship,system) || canTSShieldGen(ship,system) || canThoughtShieldGen(ship,system) 
	|| canSRdisplayCurrSystem(ship,system) || canActivate(ship, system) || canDeactivate(ship, system);

const canOffline = (ship, system) => gamedata.gamephase === 1 && (system.canOffLine || system.powerReq > 0) && !shipManager.power.isOffline(ship, system) && !shipManager.power.getBoost(system) && !weaponManager.hasFiringOrder(ship, system);

const canOnline = (ship, system) => gamedata.gamephase === 1 && shipManager.power.isOffline(ship, system);

//change December 2021: can start overloading even if no Power is available, to be balanced at end of turn
const canOverload = (ship, system) => gamedata.gamephase === 1 && !shipManager.power.isOffline(ship, system) && system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system) /*&& shipManager.power.canOverload(ship, system)*/; 

const canStopOverload = (ship, system) => gamedata.gamephase === 1 && system.weapon && system.overloadable && shipManager.power.isOverloading(ship, system) && (system.overloadshots >= system.extraoverloadshots || system.overloadshots == 0);

const canBoost = (ship, system) => system.boostable && gamedata.gamephase === 1 && shipManager.power.canBoost(ship, system) && (!system.isScanner() || system.id == shipManager.power.getHighestSensorsId(ship));

const canDeBoost = (ship, system) => gamedata.gamephase === 1 && Boolean(shipManager.power.getBoost(system));
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
const canAddShots = (ship, system) => system.weapon && system.canChangeShots && weaponManager.hasFiringOrder(ship, system) && weaponManager.getFiringOrder(ship, system).shots < system.maxVariableShots;

const canReduceShots = (ship, system) => system.weapon && system.canChangeShots && weaponManager.hasFiringOrder(ship, system) && weaponManager.getFiringOrder(ship, system).shots > 1; 

const canRemoveFireOrderMulti = (ship, system) => system.weapon && weaponManager.hasOrderForMode(system) && system.canSplitShots;
const canRemoveFireOrder = (ship, system) => system.weapon && weaponManager.hasFiringOrder(ship, system);

const canChangeFiringMode = (ship, system) => system.weapon  && ((gamedata.gamephase === 1 && system.ballistic) || (gamedata.gamephase === 5 && system.preFires) || (gamedata.gamephase === 3 && !system.ballistic && !system.preFires)) && (!weaponManager.hasFiringOrder(ship, system) || system.multiModeSplit) && (Object.keys(system.firingModes).length > 1);

//can declare eligibility for interception: charged, recharge time >1 turn, intercept rating >0, no firing order
const canSelfIntercept = (ship, system) => system.weapon && weaponManager.canSelfInterceptSingle(ship, system);
const canRemIntercept = (ship, system) => system.weapon && system.canSplitShots && weaponManager.canRemInterceptSingle(ship, system);

const canActivate = (ship, system) => system.canActivate(); //Used to manually fire weapons/systems that don't need to target e.g. Second Sight/Thoughwave
const canDeactivate = (ship, system) => system.canDeactivate();  

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

/*getFiringModesCurr - display current firing mode (no effect on click)*/
const getFiringModesCurr = (ship, system) => {
	if (system.parentId >= 0) { //...obsolete...
		/*
		let parentSystem = shipManager.systems.getSystem(ship, system.parentId);	
		if (parentSystem.parentId >= 0) {
			parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
		} else {
		}
		*/
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

export default SystemInfoButtons;

