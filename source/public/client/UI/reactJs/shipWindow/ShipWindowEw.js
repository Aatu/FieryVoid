import * as React from "react";
import styled, { css } from "styled-components"

import theme from "../styled/theme";

/*The EW target rows (OEW/DIST/SOEW/SDEW) already show the target's name as their
  visible text, so the native `title` hover tooltip that repeated it was redundant.
  Suppressed 2026-07-19 (user request, game.php EW list). Flip this to true to bring
  the hover tooltip back.*/
const SHOW_EW_TARGET_TOOLTIP = false;

/*EW panel (SHIPWINDOW_REDESIGN_PLAN.md Stages 1c/1e, vertical top-right layout after
  the 2026-07-16 feedback round): occupies the `ew` grid area - the top-right corner
  cell of the ship window's SCS grid - as a vertical list. Same numbers, same maths
  (getAmount and the ConstrainedEW special cases are unchanged).

  Stage 1e - the redesign's one deliberate functionality addition: OEW/DIST/SOEW/SDEW
  target names are interactive. Click scrolls the map to the target (guarded by
  shipManager.shouldBeHidden so stealthed/undeployed targets never leak position);
  hover emphasises that entry's EW line sprite on the map via the EwTargetHighlight
  custom event (EWIconContainer.highlightForTarget - a hidden target has no sprite, so
  it safely no-ops).*/

const EwPanel = styled.div`
    grid-area: ew;
    justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${theme.colors.panelBgGlass};
    border: 1px solid ${theme.colors.line};
    padding: 3px 4px 3px;
`;

/*title bar spans the panel edge-to-edge (negative margins cancel EwPanel's padding)
  with the same white text on shaded-blue fill as the Hit Chart / Notes buttons
  (rgba(73,103,145,0.25) - the shared header-bar blue used by CtrlButton /
  HitChartPanel section names / ShipNotesPanel titles); nowrap keeps
  "Electronic Warfare" on one line in the 120px panel*/
const EwTitle = styled.div`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${theme.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 3px 4px;
    border-bottom: 1px solid ${theme.colors.line};
`;

const Row = styled.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${theme.colors.text};
    padding-top: 1px;
`;

const RowLabel = styled.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${theme.colors.textAccent};
    white-space: nowrap;
    margin-left: 1px;     
`;

const RowValue = styled.span`
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    margin-right: 2px;    
`;

const RowTarget = styled.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: right;
    color: ${theme.colors.textAccent};
    ${props => props.$interactive && css`
        cursor: pointer;
        &:hover {
            color: ${theme.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;

class ShipWindowEw extends React.Component {

    componentWillUnmount() {
        //window closed mid-hover: make sure no EW line stays emphasised
        if (this.activeHighlight && window.webglScene) {
            window.uiEvents.relay('EwTargetHighlight', {
                shipId: this.props.ship.id,
                targetId: this.activeHighlight.targetId,
                type: this.activeHighlight.type,
                active: false
            });
            this.activeHighlight = null;
        }
    }

    onTargetClick(target, event) {
        event.stopPropagation();
        if (!window.webglScene) return;
        if (shipManager.shouldBeHidden(target)) return; //enemy, stealthed or undeployed - do not leak its position

        window.uiEvents.relay('ScrollToShip', { shipId: target.id });
    }

    setTargetHighlight(target, type, active) {
        if (!window.webglScene) return;

        window.uiEvents.relay('EwTargetHighlight', {
            shipId: this.props.ship.id,
            targetId: target.id,
            type: type,
            active: active
        });
        this.activeHighlight = active ? { targetId: target.id, type: type } : null;
    }

    render() {
        const { ship } = this.props;

        const deployTurn = shipManager.getTurnDeployed(ship);
        if (deployTurn > gamedata.turn) { //Selected ship is not deployed yet - DK May 2025
            return (
                <EwPanel>
                    <EwTitle>Electronic Warfare</EwTitle>
                    <Row key={`dew-scs-${ship.id}`}><RowLabel>Deploys on turn</RowLabel><RowValue>{deployTurn}</RowValue></Row>
                </EwPanel>
            );
        }

        return (
            <EwPanel>
                <EwTitle>Electronic Warfare</EwTitle>
                {getShipRows(ship)}
                {getTargetRows(ship, this)}
            </EwPanel>
        );
    }

}

const getShipRows = ship => {
    let list = [];

    list.push(<Row key={`dew-scs-${ship.id}`}><RowLabel>DEW</RowLabel><RowValue>{formatEW(ew.getDefensiveEW(ship))}</RowValue></Row>);
    var CCEWamount = Math.max(0, ew.getCCEW(ship) - ew.getDistruptionEW(ship));
    list.push(<Row key={`ccew-scs-${ship.id}`}><RowLabel>CCEW</RowLabel><RowValue>{formatEW(CCEWamount)}</RowValue></Row>);

    let bdew = ew.getBDEW(ship) * 0.25;
    let detectSEW = ew.getDetectSEW(ship); //Detect stealth
    let detectMEW = ew.getDetectMEW(ship); //Detect mines

    if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) bdew = ew.getBDEW(ship) * 0.2;

    if (bdew) {
        list.push(<Row key={`bdew-scs-${ship.id}`}><RowLabel>BDEW</RowLabel><RowValue>{formatEW(bdew)}</RowValue></Row>);
    }

    if (detectMEW) {
        list.push(<Row key={`DetectMEW-scs-${ship.id}`}><RowLabel>Detect Mines</RowLabel><RowValue>{formatEW(detectMEW)}</RowValue></Row>);
    }

    if (detectSEW) {
        list.push(<Row key={`DetectSEW-scs-${ship.id}`}><RowLabel>Detect Stealth</RowLabel><RowValue>{formatEW(detectSEW)}</RowValue></Row>);
    }

    return list;
}

const getTargetRows = (ship, component) => {
    //interactive only where a map exists to scroll/highlight (game.php + replay;
    //the lobby, come Stage 3, has no webglScene)
    const interactive = Boolean(window.webglScene);

    return ship.EW
        .filter(ewEntry => ewEntry.turn === gamedata.turn)
        .filter(ewEntry => ewEntry.type === "OEW" || ewEntry.type === "DIST" || ewEntry.type === "SOEW" || ewEntry.type === "SDEW")
        .map(ewEntry => {
            const target = gamedata.getShip(ewEntry.targetid);

            return (
                <Row key={`${ewEntry.type}-scs-${ship.id}-${ewEntry.targetid}`}>
                    <RowLabel>{ewEntry.type}</RowLabel>
                    <RowTarget
                        $interactive={interactive}
                        title={SHOW_EW_TARGET_TOOLTIP ? target.name : undefined}
                        onClick={interactive ? component.onTargetClick.bind(component, target) : undefined}
                        onMouseEnter={interactive ? () => component.setTargetHighlight(target, ewEntry.type, true) : undefined}
                        onMouseLeave={interactive ? () => component.setTargetHighlight(target, ewEntry.type, false) : undefined}
                    >{target.name}</RowTarget>
                    <RowValue>{getAmount(ewEntry, ship)}</RowValue>
                </Row>
            );
        });
}


const getAmount = (ewEntry, ship) => {
    switch (ewEntry.type) {
        case 'SDEW':
            if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) {
                let result = ewEntry.amount * 0.333;
                result = Math.round(result * 3) / 3;
                return formatEW(result);
            } else {
                return formatEW(ewEntry.amount * 0.5);
            }
        case 'DIST':
            if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) {
                return formatEW(ewEntry.amount / 4);
            } else {
                return formatEW(ewEntry.amount / 3);
            }
        case 'OEW':
            return formatEW(Math.max(0, ewEntry.amount - ew.getDistruptionEW(ship)));
        default:
            return formatEW(ewEntry.amount);
    }
}

const formatEW = val => {
    return Math.round(val * 100) / 100;
}

export default ShipWindowEw;
