import * as React from "react";
import styled from "styled-components"
import SystemIcon from "../system/SystemIcon"


/* Outer card frame: holds the border + dimensions but does NOT apply the
 * destroyed fade. The fade is applied on the inner FadedContent so the
 * OverlayLabel (a sibling of FadedContent) stays crisp.
 */
const FighterIconContainer = styled.div`
    position: relative;
    width: 114px;
    height: 150px;
    background-color: black;
    border: 1px solid #496791;
    box-sizing: border-box;
    margin: 5px;

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;
`;

/* Faded inner layer: background icon + flex column of system rows +
 * healthbar. opacity/filter live here so the overlay label can sit
 * outside this subtree and render at full opacity.
 */
const FadedContent = styled.div`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    background-image: url(${props => props.$img});
    background-size: 80%;
    background-position: center;
    background-repeat: no-repeat;
    filter: ${props => props.$destroyed ? 'blur(1px)' : 'none'};
    opacity: ${props => props.$destroyed ? '0.5' : '1'};
`;

/* Hangar Ops Stage 9.1: overlay label rendered above the faded icon when
 * a fighter has left the flight. Color encodes the state — cyan DOCKED
 * (matches the fleet-list "Docked" row), orange DROPOUT (the B5W dropout
 * mechanic, internally a DisengagedFighter critical applied when a
 * damaged fighter fails its dropout roll), red DESTROYED. Sibling of
 * FadedContent so the parent's opacity doesn't cascade in; border
 * matches $color.
 */
const OverlayLabel = styled.div`
    position: absolute;
    top: 55px;
    left: 9px;
    width: 96px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    padding: 5px 0;
    background-color: black;
    color: ${props => props.$color};
    border: 1px solid ${props => props.$color};
    z-index: 2;
    pointer-events: none;
    opacity: 0.7;
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

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;

    &::before {
        box-sizing: border-box;
        content: "";
        position:absolute;
        width:  ${props => props.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        z-index: 0;
        background-color: ${props => {
        if (props.$docked) return '#00b8e6';
        if (props.$criticals) return props.$criticalsBenign ? '#00ccff' : '#ed6738'; //cyan when the only crit is LaunchedThisTurn
        return '#427231';
    }};
        border: 1px solid black;
    }
`;

const HealthText = styled.div`
    z-index: 1;
`;



class FighterIcon extends React.Component {

    onSystemMouseOver(event) {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

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
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        webglScene.customEvent('SystemMouseOut');
    }

    onFighterTouchStart(event) {
        this.touchActive = true;
        this.ignoreNextClick = false;
        window.lastTouchActiveTime = Date.now();

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

    onFighterTouchCancel(event) {
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
        this.touchActive = false;
        webglScene.customEvent('SystemMouseOut');
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
        const docked = shipManager.criticals.isDockedFighter(fighter);
        //Hangar Ops Stage 21.7: a fighter that launched out of a docked flight as a
        //"- Split" row — gone from this flight, but NOT destroyed/dropped out.
        const launched = !docked && shipManager.criticals.isSplitLaunchedFighter(fighter);
        //DisengagedFighter is the B5W dropout mechanic — applied when a
        //damaged fighter fails its dropout roll (fighter.php::testCritical).
        //Render it as DROPOUT so the label matches the rulebook term.
        const droppedOut = !docked && !launched && shipManager.criticals.isDisengagedFighter(fighter);
        //Stage S (S-d): a Shadow integrated fighter cut off from its carrier (tether
        //structure-box destroyed) — STILL ALIVE and fighting, but can never land.
        const cutOff = shipManager.criticals.isCutOffFighter(fighter);

        //State-label precedence: DOCKED > LAUNCHED > DROPOUT > DESTROYED for a fighter
        //that has LEFT the flight (all flagged destroyed server-side). CUT OFF is
        //different — the fighter is alive — so it renders on a LIVING fighter (and is
        //superseded by the destroyed labels if it later dies and leaves).
        let overlay = null;
        if (destroyed) {
            //A dead fighter shows its departure/death state. (A cut-off fighter that
            //then dies is just DESTROYED — the CUT OFF badge is for LIVING cut-off craft.)
            if (docked)          overlay = <OverlayLabel $color="#00b8e6">DOCKED</OverlayLabel>;
            else if (launched)   overlay = <OverlayLabel $color="#00b8e6">SPLIT</OverlayLabel>;
            else if (droppedOut) overlay = <OverlayLabel $color="#ff8c00">DROPOUT</OverlayLabel>;
            else                 overlay = <OverlayLabel $color="#ff5252">DESTROYED</OverlayLabel>;
        } else if (cutOff) {
            //Living integrated fighter severed from its carrier — can fight, can't land.
            overlay = <OverlayLabel $color="#ff5252">CUT OFF</OverlayLabel>;
        }

        return (
            <FighterIconContainer $docked={docked} onMouseOver={this.onSystemMouseOver.bind(this)} onMouseOut={this.onSystemMouseOut.bind(this)} onTouchStart={this.onFighterTouchStart.bind(this)} onTouchMove={this.onFighterTouchMove.bind(this)} onTouchEnd={this.onFighterTouchEnd.bind(this)} onTouchCancel={this.onFighterTouchCancel.bind(this)}>
                <FadedContent $destroyed={destroyed} $img={fighter.iconPath}>
                    <Container>{toIcons(ship, fighter, getFwdSystems(fighter), destroyed)}</Container>
                    <ContainerSystems>{toIcons(ship, fighter, getAftSystems(fighter), destroyed)}</ContainerSystems>
                    <HealthBar $health={getStructureLeft(ship, fighter)} $criticals={hasCriticals(fighter)} $criticalsBenign={hasOnlyLaunchedThisTurn(fighter)} $docked={docked}><HealthText>{fighter.maxhealth - damageManager.getDamage(ship, fighter)} / {fighter.maxhealth}</HealthText></HealthBar>
                </FadedContent>
                {overlay}
            </FighterIconContainer>
        );
    }

}
const getStructureLeft = (ship, system) => (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100;

const hasCriticals = (system) => shipManager.criticals.hasCriticals(system)

//Colour the healthbar cyan rather than orange when LaunchedThisTurn (a benign
//-50 init penalty for having just launched) is the only critical on the fighter.
//Includes forInfo criticals to match hasCriticals above, which drives the orange.
const hasOnlyLaunchedThisTurn = (fighter) => shipManager.criticals.hasOnlyCritical(fighter, 'LaunchedThisTurn', false)

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
