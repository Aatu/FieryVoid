import * as React from "react";
import styled from "styled-components"
import {Tooltip, TooltipHeader, TooltipEntry} from '../common'
import ShipInfo from "./ShipInfo";

const InfoHeader = TooltipHeader.extend`
    font-size: 12px;
`;

const SystemInfoTooltip = Tooltip.extend`
    position: absolute;
    z-index: 20000;
    ${props => Object.keys(props.position).reduce((style, key) => {
        return style + "\n" + key + ':' + props.position[key] + 'px;';
    }, '')}
    width: ${props => props.ship ? '300px' : '200px'};
    text-align: left;
    opacity:0.8;
`;

export const Entry = TooltipEntry.extend`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`;

export const Header = styled.span`
    color: white;
`;

const ShipNameHeader = styled.span`
    color: #C6E2FF;
`;

class SystemInfo extends React.Component {

    render() {
        const {ship, selectedShip, system, boundingBox} = this.props;

        if (system instanceof Ship) {
			var unitName = ship.shipClass;
			if (system.flight){ //display fighter name instead of flight name!
				unitName = system.systems[1].displayName;
			}
            return (
                <SystemInfoTooltip ship position={getPosition(boundingBox)}>
                    <InfoHeader><ShipNameHeader>{ship.name}</ShipNameHeader> - {unitName}</InfoHeader>
                    <ShipInfo ship={ship}/>
                </SystemInfoTooltip>
            );
        }
//special treatment for 'Special' entry (due to probable multiline)    
        var specialEntry = new Array;
        if (system.data.Special && system.data.Special != ""){
             specialEntry = system.data.Special.split('<br>');
        }
        var specialName = 'Special';
		var reactKey=0; //needed for react so each line has unique key!

        return (
            <SystemInfoTooltip position={getPosition(boundingBox)}>
                <InfoHeader>{system.displayName}</InfoHeader>
                {!ship.flight && getEntry('Structure', system.maxhealth - damageManager.getDamage(ship, system) + '/' + system.maxhealth)}
                {!ship.flight && getEntry('Armor', shipManager.systems.getArmour(ship, system))}
                {ship.flight && getEntry('Offensive bonus', ship.offensivebonus * 5)}
                
                {system.firingModes && getEntry('Firing mode', system.firingModes[system.firingMode])}
                
                {system.missileArray && Object.keys(system.missileArray).length > 0 && getEntry('Ammo Amount', system.missileArray[system.firingMode].amount)}

                {Object.keys(system.data).map((key, i) => (key != specialName && getEntry(key, system.data[key], 'data'+ i)))}

				{Object.keys(specialEntry).length > 0 && <Entry key={`special-${reactKey++}`}><Header>Special: </Header>&nbsp;</Entry>}
				{Object.keys(specialEntry).length > 0 && 
					Object.keys(specialEntry).map(i => <Entry key={`special-${reactKey++}`}>{specialEntry[i]}</Entry> )
				}

                {Object.keys(system.critData).length > 0 && getCriticals(system)}

                {(!gamedata.isMyShip(ship) && gamedata.gamephase == 3 && gamedata.waiting == false && gamedata.selectedSystems.length > 0 && selectedShip) && getCalledShot(ship, selectedShip, system)}
            </SystemInfoTooltip>
        )
    }
}

const getCalledShot = (ship, selectedShip, system) => {
    if (weaponManager.canCalledshot(ship, system, selectedShip)) {
        return [<InfoHeader key="calledHeader">Called shot</InfoHeader>].concat(
            gamedata.selectedSystems.map((weapon, i) => {
                if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) {
                    if (weaponManager.checkIsInRange(selectedShip, ship, weapon)) {
                        var value = weapon.firingMode;
                        value = weapon.firingModes[value];
                        if (system.id != null && !weaponManager.canWeaponCall(weapon)) {
                            return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header>CANNOT CALL SHOT</Entry>);
                        } else {
                            return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header> - Approx:  {weaponManager.calculateHitChange(selectedShip, ship, weapon, system.id)}%</Entry>);
                        }
                    } else {
                        return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header>NOT IN RANGE</Entry>);
                    }
                } else {
                    return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header>NOT IN ARC</Entry>);
                }
            }))
    } else {
        return [<InfoHeader key="calledHeader">Called shot</InfoHeader>].concat(
            <Entry>CANNOT TARGET</Entry>
        )
    }
}

const getCriticals = (system) => [<InfoHeader key="criticalHeader">Damage</InfoHeader>].concat(
        Object.keys(system.critData).map(i => {
            let noOfCrits = 0;
			var endEffectMin = 0;
			var endEffectMax = 0;
			var infinitePresent = false;
			var wearsOffText = "";	
			endEffectMin = 0;
			endEffectMax = 0;
			infinitePresent = false;
			wearsOffText = "";	
						
            for (const j in system.criticals) {
                if (system.criticals[j].phpclass == i) {
					noOfCrits++;				
					if(noOfCrits == 1){
						endEffectMin = system.criticals[j].turnend;
						endEffectMax = system.criticals[j].turnend;
						infinitePresent = (system.criticals[j].turnend == 0); //0 means infinite
					}
					if (system.criticals[j].turnend > 0){ 
						if (system.criticals[j].turnend > endEffectMax) endEffectMax = system.criticals[j].turnend;
						if ((system.criticals[j].turnend < endEffectMin) || (endEffectMin == 0)) endEffectMin = system.criticals[j].turnend;
					} else infinitePresent = true;
				}
            }
					
			if (endEffectMin>0){
				wearsOffText = " (until turn " + endEffectMin;
				if(infinitePresent){
					wearsOffText = wearsOffText+"+";
				} else if (endEffectMax > endEffectMin){
					wearsOffText = wearsOffText+"-"+endEffectMax;
				}
				wearsOffText = wearsOffText+")";
			}
			
            if (noOfCrits > 1) {
                return (<Entry key={`critical-${i}`}>({noOfCrits} x) {system.critData[i]} {wearsOffText}</Entry>);
            } else {
                return (<Entry key={`critical-${i}`}>{system.critData[i]} {wearsOffText}</Entry>);
            }
        })
    );

const getEntry = (header, value, key) => {
        if (value.replace) {
            value = value.replace(/<br>/gm , "\n");
        }

    return (
        <Entry key={key} ><Header>{header}: </Header>{value}</Entry>
    )
}

const getPosition = boundingBox => {
    const position = {};
    
    if (boundingBox.top > window.innerHeight / 2) {
        position.bottom = window.innerHeight - boundingBox.top;
    } else {
        position.top = boundingBox.top + boundingBox.height;
    }

    if (boundingBox.left > window.innerWidth / 2) {
        position.right = window.innerWidth - boundingBox.right;
    } else {
        position.left = boundingBox.left + boundingBox.width;
    }

    return position;
}

export default SystemInfo;
