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
    }

    offline(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        if (!canOffline(ship, system)) {
            return;
        }

        shipManager.power.onOfflineClicked(ship, system);
    }

    overload(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.onOverloadClicked(ship, system);
    }

    stopOverload(e) {
        e.stopPropagation(); e.preventDefault();
        const {ship, system} = this.props;
        shipManager.power.onStopOverloadClicked(ship, system);
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
	
	removeFireOrder(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canRemoveFireOrder(ship, system)) {
            return;
		}
		
        weaponManager.removeFiringOrder(ship, system);
	}

	changeFiringMode(e) {
        e.stopPropagation(); e.preventDefault();
		const {ship, system} = this.props;
		if (!canChangeFiringMode(ship, system)) {
            return;
		}
		
        weaponManager.onModeClicked(ship, system);
	}
	
	
    render() {
		const {ship, selectedShip, system} = this.props;
		
		if (!canDoAnything) {
			return null;
		}
		
        return (
            <Container>
                {canOnline(ship, system) && <Button onClick={this.online.bind(this)} img="./img/on.png"></Button>}
                {canOffline(ship, system) && <Button onClick={this.offline.bind(this)} img="./img/off.png"></Button>}
                {canOverload(ship, system) && <Button onClick={this.overload.bind(this)} img="./img/overload.png"></Button>}
                {canStopOverload(ship, system) && <Button onClick={this.stopOverload.bind(this)} img="./img/overloading.png"></Button>}
                {canBoost(ship, system) && <Button onClick={this.boost.bind(this)} img="./img/plussquare.png"></Button>}
                {canDeBoost(ship, system) && <Button onClick={this.deboost.bind(this)} img="./img/minussquare.png"></Button>}
                {canAddShots(ship, system) && <Button onClick={this.addShots.bind(this)} img="./img/plussquare.png"></Button>}
                {canReduceShots(ship, system) && <Button onClick={this.reduceShots.bind(this)} img="./img/minussquare.png"></Button>}
				{canRemoveFireOrder(ship, system) && <Button onClick={this.removeFireOrder.bind(this)} img="./img/firing.png"></Button>}
				{canChangeFiringMode(ship, system) && getFiringModes(ship, system, this.changeFiringMode.bind(this))}
            </Container>
        )
    }
}

export const canDoAnything = (ship, system) => canOffline(ship, system) || canOnline(ship, system) 
	|| canOverload(ship, system) || canStopOverload(ship, system) || canBoost(ship, system) 
	|| canDeBoost(ship, system) || canAddShots(ship, system) || canReduceShots(ship, system)
	|| canRemoveFireOrder(ship, system) || canChangeFiringMode(ship, system);

const canOffline = (ship, system) => gamedata.gamephase === 1 && (system.canOffLine || system.powerReq > 0) && !shipManager.power.isOffline(ship, system) && !shipManager.power.getBoost(system) && !weaponManager.hasFiringOrder(ship, system);

const canOnline = (ship, system) => gamedata.gamephase === 1 && shipManager.power.isOffline(ship, system);

const canOverload = (ship, system) => !shipManager.power.isOffline(ship, system) && system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system) && shipManager.power.canOverload(ship, system);

const canStopOverload = (ship, system) => system.weapon && system.overloadable && shipManager.power.isOverloading(ship, system);

const canBoost = (ship, system) => system.boostable && shipManager.power.canBoost(ship, system) && (!system.isScanner() || system.id == shipManager.power.getHighestSensorsId(ship));

const canDeBoost = (ship, system) => Boolean(shipManager.power.getBoost(system));

const canAddShots = (ship, system) => system.weapon && system.canChangeShots && weaponManager.hasFiringOrder(ship, system) && weaponManager.getFiringOrder(ship, system).shots < system.shots;

const canReduceShots = (ship, system) => system.weapon && system.canChangeShots && weaponManager.hasFiringOrder(ship, system) && weaponManager.getFiringOrder(ship, system).shots > 1; 

const canRemoveFireOrder = (ship, system) => system.weapon && weaponManager.hasFiringOrder(ship, system);

const canChangeFiringMode = (ship, system) => system.weapon  && ((gamedata.gamephase === 1 && system.ballistic) || (gamedata.gamephase === 3 && !system.ballistic)) && !weaponManager.hasFiringOrder(ship, system) && (Object.keys(system.firingModes).length > 1 || system.dualWeapon);


const getFiringModes = (ship, system, changeFiringMode) => {
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

		return <Button onClick={changeFiringMode} img={img}>{firingMode.substring(0, 1)}</Button>
	}
}

export default SystemInfoButtons;

