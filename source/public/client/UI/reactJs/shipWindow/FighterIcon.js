import * as React from "react";
import styled from "styled-components"
import SystemIcon from "../system/SystemIcon"


const FighterIconContainer = styled.div`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 114px;
    height: 150px; 
    background-image:url(${props => props.$img});
    background-color: black;
    background-size: 80%;
    background-position: center;
    background-repeat: no-repeat;
    border: 1px solid #496791;
    box-sizing: border-box;
    margin: 5px;
    filter: ${props => props.$destroyed ? 'blur(1px)' : 'none'};
    opacity: ${props => props.$destroyed ? '0.5' : '1'};
`;

const Container = styled.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`;

const ContainerSystems = styled(Container)`
    height: calc(50% - 16px);
    align-items: flex-end;
`;


const HealthBar = styled.div`
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: calc(100% - 4px);
    height: 16px;
    box-sizing: border-box;
    background-color: black;
    color: ${props => props.$health === 0 ? 'transparent' : 'white'};
    font-family: arial;
    font-size: 11px;
    text-shadow: black 0 0 6px, black 0 0 6px;
    border: 1px solid #496791;
    margin: 2px;

    &::before {
        box-sizing: border-box;
        content: "";
        position:absolute;
        width:  ${props => props.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        z-index: 0;
        background-color: ${props => props.$criticals ? '#ed6738' : '#427231'};
        border: 1px solid black;
    }
`;

const HealthText = styled.div`
    z-index: 1;
`;



class FighterIcon extends React.Component {

    onSystemMouseOver(event) {
        if (this.touchActive) return;

        // Mobile browsers fire a synthetic 'mouseover' event immediately after 'touchend'.
        // Because child SystemIcons call stopPropagation() on their touch events,
        // FighterIcon never activates its own this.touchActive shield.
        // We must detect if this is a synthetic event (e.g. sourceCapabilities.firesTouchEvents)
        if (event.nativeEvent && event.nativeEvent.sourceCapabilities && event.nativeEvent.sourceCapabilities.firesTouchEvents) {
            return;
        }

        let { ship } = this.props;

        webglScene.customEvent('SystemMouseOver', {
            ship: ship,
            system: ship,
            element: event.target
        });
    }

    onSystemMouseOut() {
        if (this.touchActive) return;
        webglScene.customEvent('SystemMouseOut');
    }

    onFighterTouchStart(event) {
        this.touchActive = true;
        this.ignoreNextClick = false;

        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
        }

        const target = event.currentTarget;
        const touch = event.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartY = touch.clientY;

        this.longPressTimer = setTimeout(() => {
            this.ignoreNextClick = true; // Prevent click from firing after long press
            let { ship } = this.props;

            // Long Press -> generic tooltip
            webglScene.customEvent('SystemMouseOver', {
                ship: ship,
                system: ship,
                element: target,
                showInfo: true
            });

            this.longPressTimer = null;
        }, 400); // 400ms hold required for info window
    }

    onFighterTouchMove(event) {
        if (!this.longPressTimer) return;

        const touch = event.touches[0];
        const dx = touch.clientX - this.touchStartX;
        const dy = touch.clientY - this.touchStartY;

        // Cancel if they move more than 10 pixels
        if (Math.abs(dx) > 10 || Math.abs(dy) > 10) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
    }

    onFighterTouchEnd(event) {
        if (this.longPressTimer) {
            // Timer didn't pop, meaning this was a short tap
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        } else {
            // Timer already fired (long press). Hide info on release.
            webglScene.customEvent('SystemMouseOut');
        }

        setTimeout(() => {
            this.touchActive = false;
        }, 300); // Clear touch active after giving click events time to fire/be ignored
    }

    render() {
        const { ship, fighter } = this.props;

        const destroyed = shipManager.systems.isDestroyed(ship, fighter);
        const disengaged = shipManager.criticals.isDisengagedFighter(fighter);
        /* old version - weapons and notWeapons
                return (
                    <FighterIconContainer destroyed={destroyed} img={fighter.iconPath} onMouseOver={this.onSystemMouseOver.bind(this)} onMouseOut={this.onSystemMouseOut}>
                        <Container>{toIcons(ship, fighter, getWeapons(fighter), destroyed)}</Container>
                        <ContainerSystems>{toIcons(ship, fighter, getNotWeapons(fighter), destroyed)}</ContainerSystems>
                        <HealthBar health={getStructureLeft(ship, fighter)} criticals={hasCriticals(fighter)}><HealthText>{fighter.maxhealth - damageManager.getDamage(ship, fighter)} / {fighter.maxhealth}</HealthText></HealthBar>
                    </FighterIconContainer>
                );
        */
        //new version - fwd and aft systems (unit creator decides the layout)
        return (
            <FighterIconContainer $destroyed={destroyed} $img={fighter.iconPath} onMouseOver={this.onSystemMouseOver.bind(this)} onMouseOut={this.onSystemMouseOut.bind(this)} onTouchStart={this.onFighterTouchStart.bind(this)} onTouchMove={this.onFighterTouchMove.bind(this)} onTouchEnd={this.onFighterTouchEnd.bind(this)}>
                <Container>{toIcons(ship, fighter, getFwdSystems(fighter), destroyed)}</Container>
                <ContainerSystems>{toIcons(ship, fighter, getAftSystems(fighter), destroyed)}</ContainerSystems>
                <HealthBar $health={getStructureLeft(ship, fighter)} $criticals={hasCriticals(fighter)}><HealthText>{fighter.maxhealth - damageManager.getDamage(ship, fighter)} / {fighter.maxhealth}</HealthText></HealthBar>
            </FighterIconContainer>
        );
    }

}
const getStructureLeft = (ship, system) => (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100;

const hasCriticals = (system) => shipManager.criticals.hasCriticals(system)

const getWeapons = fighter => fighter.systems.filter(system => system.weapon);

const getNotWeapons = fighter => fighter.systems.filter(system => !system.weapon);

const getFwdSystems = fighter => fighter.systems.filter(system => (system.location == 1));
const getAftSystems = fighter => fighter.systems.filter(system => (system.location != 1));

const toIcons = (ship, fighter, systems, destroyed) => {
    return systems.map((system, i) => {
        return <SystemIcon $destroyed={destroyed} fighter={true} scs key={`system-scs-fighter${fighter.id}-${ship.id}-${system.id}-${i}`} system={system} ship={ship} />
    })
}




export default FighterIcon;
