import * as React from "react";
import styled, { css } from "styled-components"

import theme from "../styled/theme";

/*The EW target rows (OEW/DIST/SOEW/SDEW) already show the target's name as their
  visible text, so the native `title` hover tooltip that repeated it was redundant.
  Suppressed 2026-07-19 (user request, game.php EW list). Flip this to true to bring
  the hover tooltip back.*/
const SHOW_EW_TARGET_TOOLTIP = false;

/*Colour-coded EW ROW LABELS (user request 2026-07-22) - only the labels are tinted,
  never the values or the target names. Muted pastels: light enough to read on the dark
  panel, distinct from the window's blue chrome without going garish.

  The original spec had two overlaps, resolved here (edit this one map to retune):
    - "SDEW" (the per-target Self-Defensive EW row) -> blue;
    - the two detection rows ("Detect Mines"/"Detect Stealth", the spec's MDEW/SDEW
      pair) -> purple;
    - "OEW" -> green (it was listed under both green and orange);
    - "SOEW" -> orange (pairs with DIST).
  Any label not in the map keeps the default accent colour.*/
const EW_LABEL_COLORS = {
    'DEW': '#aecdea',               //soft blue
    'CCEW': theme.colors.text,        //white     
    'SDEW': '#9ac1e5',              //soft blue
    'OEW': '#acd7a8',               //soft green
    'BDEW': '#8ac785',              //soft green
    'Detect Mines': '#bfa3db',      //soft purple
    'Detect Stealth': '#ccb6e2',    //soft purple
    'DIST': '#e6b98f',              //soft orange
    'SOEW': theme.colors.text,        //soft orange
    'OEW_HOSTILE': '#e49b9b',       //soft red - pseudo-label, see ewLabelColor
    'Saved EW': '#e0d39a',          //soft gold - the EW Detector allowance (WALKERS_OF_SIGMA_PLAN.md 3.8)
};

/*OEW is the one CONTEXTUAL label (user request 2026-07-23): it keeps the green while the
  window's ship is yours or a teammate's, and takes the OEW_HOSTILE red on anyone else's
  ship - i.e. that OEW is being pointed at your side. Guarded by isPlayerInGame() because
  an observer has no "side" (isMyorMyTeamShip is false for EVERY ship there, which would
  paint every window's OEW red); observers keep the neutral green.

  "OEW_HOSTILE" is a pseudo-label and can never collide with a real one: labels come from
  ewEntry.type (OEW/DIST/SOEW/SDEW) or the literals in getShipRows.*/
const ewLabelColor = (label, ship) => {
    if (label === 'OEW' && ship && gamedata.isPlayerInGame() && !gamedata.isMyorMyTeamShip(ship)) {
        return EW_LABEL_COLORS['OEW_HOSTILE'];
    }

    return EW_LABEL_COLORS[label] || theme.colors.textAccent;
};

/*EW panel (SHIPWINDOW_REDESIGN_PLAN.md Stages 1c/1e, vertical top-right layout after
  the 2026-07-16 feedback round): occupies the `ew` grid area - the top-right corner
  cell of the ship window's SCS grid - as a vertical list. Same numbers, same maths
  (getAmount and the ConstrainedEW special cases are unchanged).

  Stage 1e - the redesign's one deliberate functionality addition: OEW/DIST/SOEW/SDEW
  target names are interactive. Click scrolls the map to the target (guarded by
  shipManager.shouldBeHidden so stealthed/undeployed targets never leak position);
  hover emphasises that entry's EW line sprite on the map via the EwTargetHighlight
  custom event (EWIconContainer.highlightForTarget - a hidden target has no sprite, so
  it safely no-ops).

  The BDEW and Detect Mines (MDEW) rows are hover-interactive in the same spirit
  (2026-07-30): they raise the ship's blanket / mine-detection area on the map for as long
  as the pointer rests on the row (EwRangeHover -> PhaseStrategy -> ShipIcon.showBDEW /
  showMDEW). See setRangeOverlay.*/

const EwPanel = styled.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${props => props.$flight ? '' : css`
        grid-area: ew;
        justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    `}
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${theme.colors.panelBgGlass};
    border: 1px solid ${theme.colors.line};
    padding: 1px 4px 1px;
`;

/*title bar spans the panel edge-to-edge (negative margins cancel EwPanel's padding)
  with the same white text on shaded-blue fill as the Hit Chart / Notes buttons
  (rgba(73,103,145,0.25) - the shared header-bar blue used by CtrlButton /
  HitChartPanel section names / ShipNotesPanel titles); nowrap keeps
  "Electronic Warfare" on one line in the 120px panel*/
const EwTitle = styled.div`
    /*flex-centred fixed-height bar so the title sits dead-centre, consistent with every
      other chrome title/header bar (user request 2026-07-22)*/
    display: flex;
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${theme.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${theme.colors.line};
`;

const Row = styled.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${theme.colors.text};
    /*ONE row pitch for the whole panel - ship rows and $target rows alike (user 2026-07-30).
      It was 1px, which read as too tight between DEW and CCEW. The cause was not those rows
      (they are the same component with the same props as every other ship row) but the panel
      top: EwTitle's 2px bottom margin plus this padding gave DEW 3px of air above and 1px
      below, so the eye took the 3px as the intended rhythm and the 1px as a mistake. Matching
      the two settles it. At 1px the visible separation was mostly the fonts' own half-leading
      - 10px Consolas values in a 9px row - rather than anything deliberate.

      The first row still clears the title by 5px (2px title margin + 3px here) against 3px
      between rows; that extra is wanted, since a header rule reads better with more clearance
      than the rows it heads.*/
    padding-top: 2px;
    /*$target rows carry a wrappable ship name (see RowTarget), so they need more air than
      the single-line ship rows: without it a name's second line sits as close to the NEXT
      row's label as to its own first line, and the eye groups it with the wrong row.

      They also swap baseline alignment for centre: a target row's two children are the
      TargetMain block (label + name, internally baseline-aligned) and the value, so
      centring floats the value to the vertical middle of however many lines the name
      took - level with the single line of a short name, midway between the two lines of
      a wrapped one, with no line-counting needed. Ship rows KEEP baseline: their 8px
      label and 10px value never wrap, and centring them would shift the value off the
      label's baseline for no gain.*/
    ${props => props.$target && css`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${props => props.$hoverable && css`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`;

/*Label + name of a target row, grouped so the value outside can centre against the pair
  (see Row's $target branch). Baseline-aligned internally, which is what keeps the label
  level with the name's FIRST line rather than drifting down with it.*/
const TargetMain = styled.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`;

const RowLabel = styled.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${props => props.$color || theme.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`;

const RowValue = styled.span`
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`;

/*Ship names WRAP here rather than ellipsing on one line (user request 2026-07-24). The old
  single-line `white-space: nowrap` made targets ambiguous whenever two ships shared a prefix
  - "Shining Star Hunter-Killer flight #3" and "#4" both rendered as "Shining Star Hun..." -
  and names in the corpus average 21 chars against a target column that fits roughly 15.

  Wrap-in-place: the row keeps its LABEL / name / VALUE shape, the name just flows onto a
  second line inside its own column, so the value column stays aligned down the panel.

  Capped at TWO lines so no name can stretch the panel arbitrarily; the clamp still ellipses,
  so an over-long name reads as truncated rather than silently cut. -webkit-line-clamp needs
  the legacy -webkit-box display and is the only cross-browser "N lines then ellipsis"
  (Chrome/Edge/Safari, Firefox 68+); the unprefixed `line-clamp` is there for when that
  standardises. Row keeps align-items: baseline, so the label and value sit level with the
  name's FIRST line.*/
const RowTarget = styled.span`
    flex: 1 1 auto;
    min-width: 0;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    overflow: hidden;
    text-overflow: ellipsis;
    overflow-wrap: break-word; /*a single over-long token breaks instead of overflowing the column*/
    line-height: 1.2; /*tight, so the second line costs as little panel height as possible*/
    text-align: center;
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

        //same for a blanket/mine-detection area: a window closed under the cursor never fires
        //mouse-out on the row that raised it
        if (this.activeRangeOverlay) {
            this.setRangeOverlay(this.activeRangeOverlay, false);
        }
    }

    /*BDEW / Detect Mines row hover (user request 2026-07-30): while the pointer is on the row,
      draw that ship's blanket-EW or mine-detection area on the map - the same overlays
      ShipIcon.showBDEW/showMDEW raise when the ship itself is hovered on the map.

      Routed through the relay as EwRangeHover so PhaseStrategy owns the icon lookup, the
      position-leak guard and the "only sweep what this hover raised" bookkeeping; the lobby's
      uiEvents handler ignores the event, having no map to draw on.*/
    setRangeOverlay(type, active) {
        if (!window.webglScene) return;

        window.uiEvents.relay('EwRangeHover', {
            shipId: this.props.ship.id,
            type: type,
            active: active
        });
        this.activeRangeOverlay = active ? type : null;
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
        const { ship, flight } = this.props;

        /* ⭐ WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12) - THE MAPMAKER'S EW
           BLOCK, and it gets its OWN row list rather than the ship one.

           ⚠️ getShipRows() asks ew.getCCEW / getBDEW / getDetectSEW / getSavedEwAllowance about
           the unit, and every one of those is a question a flight was never meant to answer:
           its DEW row would come back as the MINE-DETECTION allowance (ew.getDefensiveEW is an
           alias for getEWLeft), and the Saved EW row would advertise an EW Detector allowance
           that EW::getDetectorAllowance explicitly refuses a flight. A flight has exactly one
           meaningful subset - its DEW, plus its OEW target rows - so that is what it renders.

           The target rows below are shared verbatim: getTargetRows filters on the EW array and
           a flight can only ever hold OEW entries in it.*/
        if (flight) {
            return (
                <EwPanel $flight>
                    <EwTitle>Electronic Warfare</EwTitle>
                    {getFlightRows(ship)}
                    {getTargetRows(ship, this)}
                </EwPanel>
            );
        }

        /*An undeployed ship used to replace this whole panel with a lone "Deploys on turn N"
          row. That message now lives where it belongs - the cyan "Deploying on Turn N" status
          banner in ShipWindow's getStatusBanners, which is visible on every panel rather than
          only this one - so the EW list simply renders as normal. It reads all zeroes for a
          unit still off the board, which is the honest answer.*/
        return (
            <EwPanel>
                <EwTitle>Electronic Warfare</EwTitle>
                {getShipRows(ship, this)}
                {getTargetRows(ship, this)}
            </EwPanel>
        );
    }

}

/* WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12) - the flight list. ONE row.

   ⭐ IT DOUBLES AS THE ALLOCATION COUNTER, which is why it is always rendered even at 0. During
   Initial Orders there is no committed DEW row yet (convertUnusedToDEW writes it at the
   commit), so ew.getFlightDEW falls back to the live remainder of the 3-point pool - i.e. this
   figure counts DOWN as the player spends points on OEW and settles as the flight's actual DEW
   the moment they commit. That is exactly how a ship's DEW row behaves, and it is what tells
   the player how much of the pool is left without a second 'available' line.

   ⚠️ NOT ew.getDefensiveEW: on a flight that is getEWLeft(), the MINE-DETECTION allowance.*/
const getFlightRows = (ship) => {
    const list = [
        <Row key={`dew-scs-${ship.id}`}>
            <RowLabel $color={ewLabelColor('DEW')}>DEW</RowLabel>
            <RowValue>{formatEW(ew.getFlightDEW(ship))}</RowValue>
        </Row>
    ];

    /* ⭐ A MAPMAKER CAN SAVE AN EW POINT TOO (user ruling 2026-09-10: "the effect applies to all
       Walker units"), so it gets the same row a ship does - SHARED, not copied, because the
       n / total form and the own-side guard are rules rather than layout. */
    const savedEwRow = getSavedEwRow(ship);
    if (savedEwRow) list.push(savedEwRow);

    return list;
}

/* THE EW DETECTOR ALLOWANCE ROW (WALKERS_OF_SIGMA_PLAN.md 3.8, Stage 10A/10B), shared by the
   ship list and the flight list. Returns null when there is no row to draw.

   Last in either list because it is an ALLOWANCE rather than an allocation: every row above it
   is EW that has been spent.

   ⚠️ OWN SIDE ONLY, and deliberately - the same ruling the stealth-toggle forecast carries. The
   number is a live read of where FRIENDLY detectors are, so rendering it on an enemy hull would
   answer "how many EW Detectors cover this hex" for a fleet the viewer is not in.
   isPlayerInGame() guards the observer case, who has no side and therefore sees no row.

   ⚠️ SUPPRESSED WHEN THE ALLOWANCE IS ZERO - which is not the same as "when the value is zero".
   A fleet with no detector is every game in the corpus but a handful, and a permanent
   "Saved EW 0" line on all of them is noise; but a unit that has SPENT its whole allowance in the
   late window still has an allowance, and the row disappearing at the moment it is used up would
   read as the feature breaking.

   ⭐ THE VALUE CHANGES MEANING WITH THE PHASE, and the "n / total" form is what says so. During
   Initial Orders and Movement there is nothing to spend it on yet, so the row is a forecast and
   shows the allowance alone. Once the Pre-Firing/Firing window opens it becomes a budget, and the
   player needs to see what is LEFT beside what they started with.

   ⚠️ THE FORECAST TRACKS THE PLAYER'S OWN CLICKING, which is what the user asked for: the
   allowance is the ladder clamped by the UNSPENT pool (ew.getSavedEwPool), so a unit that has
   just put its last point into OEW watches this row fall to zero rather than promising a saved
   point it can no longer keep.*/
const getSavedEwRow = (ship) => {
    if (!gamedata.isPlayerInGame() || !gamedata.isMyorMyTeamShip(ship)) return null;

    const savedEW = ew.getSavedEwAllowance(ship);
    if (savedEW <= 0) return null;

    const spendable = ew.isLateEwWindowOpen(ship) || (ew.isLateEwPhase() && gamedata.isMyShip(ship));
    const value = spendable
        ? `${formatEW(ew.getLateEwRemaining(ship))} / ${formatEW(savedEW)}`
        : formatEW(savedEW);

    return (
        <Row key={`savedew-scs-${ship.id}`}>
            <RowLabel $color={ewLabelColor('Saved EW')}>Saved EW</RowLabel>
            <RowValue>{value}</RowValue>
        </Row>
    );
}

const getShipRows = (ship, component) => {
    let list = [];

    /*Props that turn a row into a map-overlay hover switch (BDEW / Detect Mines only). Like the
      target rows, interactive only where a map exists to draw on - Boolean(window.webglScene) is
      false in the lobby, which then gets a plain, unhoverable row.*/
    const interactive = Boolean(window.webglScene);
    const rangeHover = type => interactive
        ? {
            $hoverable: true,
            onMouseEnter: () => component.setRangeOverlay(type, true),
            onMouseLeave: () => component.setRangeOverlay(type, false)
        }
        : {};

    list.push(<Row key={`dew-scs-${ship.id}`}><RowLabel $color={ewLabelColor('DEW')}>DEW</RowLabel><RowValue>{formatEW(ew.getDefensiveEW(ship))}</RowValue></Row>);
    var CCEWamount = Math.max(0, ew.getCCEW(ship) - ew.getDistruptionEW(ship));
    if (CCEWamount > 0) {
        list.push(<Row key={`ccew-scs-${ship.id}`}><RowLabel $color={ewLabelColor('CCEW')}>CCEW</RowLabel><RowValue>{formatEW(CCEWamount)}</RowValue></Row>);
    }

    let bdew = ew.getBDEW(ship) * 0.25;
    let detectSEW = ew.getDetectSEW(ship); //Detect stealth
    let detectMEW = ew.getDetectMEW(ship); //Detect mines

    if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) bdew = ew.getBDEW(ship) * 0.2;

    if (bdew) {
        list.push(<Row key={`bdew-scs-${ship.id}`} {...rangeHover('BDEW')}><RowLabel $color={ewLabelColor('BDEW')}>BDEW</RowLabel><RowValue>{formatEW(bdew)}</RowValue></Row>);
    }

    if (detectMEW) {
        list.push(<Row key={`DetectMEW-scs-${ship.id}`} {...rangeHover('MDEW')}><RowLabel $color={ewLabelColor('Detect Mines')}>Detect Mines</RowLabel><RowValue>{formatEW(detectMEW)}</RowValue></Row>);
    }

    if (detectSEW) {
        list.push(<Row key={`DetectSEW-scs-${ship.id}`}><RowLabel $color={ewLabelColor('Detect Stealth')}>Detect Stealth</RowLabel><RowValue>{formatEW(detectSEW)}</RowValue></Row>);
    }


    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.8, Stage 10A) - EW points this unit may HOLD
      BACK from Initial Orders and spend as late as the end of the Movement segment, granted by
      friendly EW Detectors in range. The row is built by getSavedEwRow above, which the FLIGHT
      list shares (Stage 12: a Mapmaker can save a point too); everything that used to be written
      out here now lives on that function.

      ⚠️ OWN SIDE ONLY, and deliberately - the same ruling the stealth-toggle forecast carries.
      The number is a live read of where FRIENDLY detectors are, so rendering it on an enemy hull
      would answer "how many EW Detectors cover this hex" for a fleet the viewer is not in.
      isPlayerInGame() guards the observer case, who has no side and therefore sees no row.

      ⚠️ SUPPRESSED WHEN THE ALLOWANCE IS ZERO - which is not the same as "when the value is zero".
      A fleet with no detector is every game in the corpus but a handful, and a permanent
      "Saved EW 0" line on all of them is noise; but a unit that has SPENT its whole allowance in
      the late window still has an allowance, and the row disappearing at the moment it is used up
      would read as the feature breaking.

      ⭐ STAGE 10B - THE VALUE CHANGES MEANING WITH THE PHASE, and the "n / total" form is what says
      so. During Initial Orders and Movement there is nothing to spend it on yet, so the row is a
      forecast and shows the allowance alone. Once the Pre-Firing/Firing window opens it becomes a
      budget, and the player needs to see what is LEFT beside what they started with.

      ⚠️ THE FORECAST TRACKS THE PLAYER'S OWN CLICKING, which is what the user asked for: the
      allowance is the ladder clamped by the UNSPENT pool (ew.getSavedEwPool), so a ship that has
      just put its last point into OEW watches this row fall to zero rather than promising a saved
      point it can no longer keep.*/
    const savedEwRow = getSavedEwRow(ship);
    if (savedEwRow) list.push(savedEwRow);

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
                <Row $target key={`${ewEntry.type}-scs-${ship.id}-${ewEntry.targetid}`}>
                    <TargetMain>
                        <RowLabel $color={ewLabelColor(ewEntry.type, ship)}>{ewEntry.type}</RowLabel>
                        <RowTarget
                            $interactive={interactive}
                            title={SHOW_EW_TARGET_TOOLTIP ? target.name : undefined}
                            onClick={interactive ? component.onTargetClick.bind(component, target) : undefined}
                            onMouseEnter={interactive ? () => component.setTargetHighlight(target, ewEntry.type, true) : undefined}
                            onMouseLeave={interactive ? () => component.setTargetHighlight(target, ewEntry.type, false) : undefined}
                        >{target.name}</RowTarget>
                    </TargetMain>
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
