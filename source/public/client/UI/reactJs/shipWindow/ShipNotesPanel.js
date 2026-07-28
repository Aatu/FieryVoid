import * as React from "react";
import styled from "styled-components";
import theme from "../styled/theme";
import buildComplement from "../helpers/buildComplement";

/*Lobby datasheet (SHIPWINDOW_REDESIGN_PLAN.md Stage 3b) — the always-visible
  replacement for the legacy window's overlap-prone `.notes` column. Two pieces:

  - ManoeuvreStats: the manoeuvre block (TC/TD, Acc/Pivot/Roll, Profile, Ini),
    rendered in the window's ctrl column directly beneath the Hit Chart button
    (user layout decision 2026-07-17: stats left, complement/notes right).
  - ShipNotesPanel (default export): hangar capacity (fighters + default
    shuttles), ship notes, metadata (limited/variant/ISD/custom flags) and
    purchased enhancements. In the grid window it occupies the `ew` grid area —
    the exact place the Electronic Warfare panel has in game.php (feedback
    2026-07-17) — so it reads as the same chrome and can never be obscured by
    ship icon elements. Flight windows render it as a side rail, compact
    windows (mines) as a full-width block.

  Both read the ship object AFTER lobbyEnhancements.apply() has mutated it, so
  enhancement-driven stat changes (Elite Crew initiative, Improved Thrust, ...)
  show live. Rendering is read-only — enhancement mutations stay in
  lobbyEnhancements (blueprints share static data, arch_client_system_shared_reference).*/

/*Transparent stack container - each Block inside is its own dotted-bordered panel
  (feedback 2026-07-17 round 2: Notes gets its own bordered box with a gap below
  Hangar Capacity, exactly like Ship Stats sits below the Hit Chart button; the 4px
  gap matches ControlsArea's).
  $grid: occupies the `ew` grid cell (150px). $full: full-width (compact mine
  windows). Default: 200px side rail (flights).*/
const Rail = styled.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${theme.colors.textAccent};
    ${props => {
        if (props.$grid) {
            return `
    grid-area: ew;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px;`;
        }
        if (props.$full) {
            return `
    width: 100%;
    padding: 4px;`;
        }
        return `
    flex: 0 0 auto;
    width: 200px;
    padding: 4px;`;
    }}
`;

const Block = styled.div`
    background-color: ${theme.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${props => props.$gold ? '#8a6d3b' : theme.colors.line};
    padding: 0 8px 3px;
`;

const BlockTitle = styled.div`
    /*flex-centred fixed-height bar (user request 2026-07-22): consistent vertical
      centring across every chrome title/header bar in the ship window*/
    display: flex;
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${props => props.$gold ? '#e8cf93' : theme.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${props => props.$gold ? 'rgba(169, 128, 56, 0.30)' : 'rgba(73, 103, 145, 0.25)'};
    border-bottom: 1px solid ${props => props.$gold ? '#8a6d3b' : theme.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`;

const Row = styled.div`
    padding: 1px 0;
`;

const CustomFlag = styled.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${theme.colors.custom};
`;

/*stat rows read like the Notes lines (feedback round 3): 10px, sentence-case
  labels in the notes blue, values in white*/
const StatRow = styled.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`;

const StatLabel = styled.span`
    font-size: 10px;
    color: ${theme.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`;

const StatValue = styled.span`
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${props => props.$changed ? theme.colors.custom : theme.colors.text};
    margin-right: 5px;
`;

/*Manoeuvre block, styled as a sibling of the ctrl buttons and the datasheet panels
  opposite - same 150px width (feedback 2026-07-17: chrome columns symmetric).
  $bare (game.php Ship Stats popup, user request 2026-07-26): rows only. The popup already
  has PopupHolder's frame and drops from a button that says "Ship Stats", so the panel's
  own border and title bar were both saying it twice. Width is kept so the label/value
  columns sit exactly where they do in the lobby block.*/
const StatsPanel = styled.div`
    width: 150px;
    box-sizing: border-box;
    ${props => props.$bare ? `
    padding: 0;` : `
    background-color: ${theme.colors.panelBgGlass};
    border: 1px dotted ${theme.colors.line};
    padding: 2px 4px 3px;`}
`;

const StatsTitle = styled.div`
    display: flex;
    /*centred, matching the Hit Chart button and every other title bar (user request
      2026-07-22); the bar-graph glyph centres alongside the text*/
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    gap: 4px; /*matches the Hit Chart button's icon/label gap so the title lines up*/
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${theme.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${theme.colors.line};
`;

/*Small CSS bar-graph glyph left of the "Ship Stats" title so the title lines up with
  the Hit Chart button's ⊕ directly above it (user request 2026-07-18). Drawn in CSS
  rather than an emoji to stay monochrome and match the chrome; sized to the 12px
  CtrlIcon footprint so "Ship Stats" starts at the same x as "Hit Chart".
  Exported because game.php's Ship Stats control button reuses the exact same glyph
  (user request 2026-07-26) - one definition, so the button and the block it opens can
  never drift apart.*/
export const StatsIcon = styled.span`
    display: inline-flex;
    align-items: flex-end;
    justify-content: center;
    gap: 1px;
    flex: 0 0 auto;
    width: 12px;
    height: 9px;
    i {
        display: block;
        width: 2px;
        background-color: ${theme.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;

const AgileRow = styled.div`
    text-align: center;
    font-size: 10px;
    color: ${theme.colors.warning};
    padding-top: 2px;
`;

/*Dedicated header for the Enhancements box (user request 2026-07-19): its own styled
  component so it can be tweaked freely without touching the other block titles
  (Notes / Hangar Capacity / Flight Stats). Seeded from the former gold BlockTitle
  ($gold) look, so it starts identical - change anything below to taste.*/
const EnhTitle = styled.div`
    /*flex-centred fixed-height bar (user request 2026-07-22), matching BlockTitle*/
    display: flex;
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: #e8cf93;
    background-color: rgba(169, 128, 56, 0.30);
    border-bottom: 1px solid #8a6d3b;
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`;

/*Enhancements as a standalone bottom-right grid panel (feedback round 3): keeping it
  out of the `ew` datasheet stack stops long enhancement lists inflating grid row 1
  and pushing Forward away from Primary.*/
const EnhArea = styled.div`
    grid-area: enh;
    justify-self: center;
    align-self: start; /*top of its cell - starts directly below the Starboard section (feedback round 5)*/
    /*>>> ENHANCEMENTS-BOX GAP <<< minimum space above the Enhancements box (between it
      and the Starboard section above), applied on BOTH game.php and the lobby since they
      share this component. Adjust this one value to taste.*/
    margin-top: 0px;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    /*$wide: 150px in the lobby (matches the datasheet panels); 130px in game (user
      2026-07-19: 150 too wide, matches the EW panel it sits below)*/
    width: ${props => props.$wide ? '150px' : '130px'};
    box-sizing: border-box;
    font-size: 10px;
    line-height: 1.4;
    color: ${theme.colors.enhText};
`;

//legacy parity: TC/TD to two decimals, Profile as defense * 5, Ini raw
const fix2 = (value) => (typeof value === 'number' ? value.toFixed(2) : value);

const profileText = (front, side) => (front * 5) + '/' + (side * 5);

//"<thrust> (<rate>)" - the ship tooltip's turn-cost format
const costText = (thrust, rate) => thrust + ' (' + fix2(rate) + ')';

/*The ship's stats AS THEY STAND THIS TURN, for the game.php Ship Stats popup (user
  request 2026-07-26; the lobby has no game state and keeps the blueprint figures it
  always showed). Every entry is paired with a `*Changed` flag - true when the live figure
  differs from the ship's own blueprint one - which the renderer paints in the custom
  yellow. A stat the SERVER already sends modified (an enhancement-raised initiative, a
  crit-changed rate) is not "changed" by that test and correctly isn't flagged: it IS the
  ship's current stat, and the delta this highlights is the transient one.

  Deliberately NOT live: Accel/decel, Pivot and Roll are flat thrust costs the client
  engine never modifies for ships - combat pivot (x1.5) exists only in the firing phase,
  which only flights may pivot in (movement.canPivot), and game flight windows carry no
  control block at all.*/
const liveShipStats = (ship) => {
    const manager = window.shipManager;
    if (!manager) return null;

    const stats = {};

    /*PROFILE. The ProfileIncreased critical (marine sabotage, sensor/computer loss) adds
      +1 to EVERY hit-location profile, exactly as weapon.php does at resolution time
      (`$defence += $targetCnC->hasCritical("ProfileIncreased")`) - the crit rides the CnC
      and each one is worth 5%. Deliberately EW-FREE (user request 2026-07-26): defensive
      EW is a per-shot modifier applied on top, not a change to the hull's own profile.
      The map tooltip's "Defence (F/S)" remains the place to read the EW-modified number.*/
    const cnc = manager.systems ? manager.systems.getSystemByName(ship, "CnC") : null;
    const profileMod = (cnc && manager.criticals)
        ? manager.criticals.hasCritical(cnc, "ProfileIncreased")
        : 0;
    stats.profile = profileText(ship.forwardDefense + profileMod, ship.sideDefense + profileMod);
    stats.profileChanged = profileMod !== 0;

    /*INITIATIVE. Nothing to recompute: the server hands us the delta ready-made.
      `iniativeadded` is (this turn's bonus + common modifiers) - the blueprint bonus,
      filled in by ShipClasses::onConstructed expressly "for display to player". It covers
      the sub-speed-5 penalty, the CnC criticals (comms disrupted, reduced initiative,
      tractor-held, hangar ops, LCV launched) and per-hull rules alike. It is non-zero for
      most ships most turns - the speed penalty alone is -10 per point under speed 5 - so
      expect this row to be yellow far more often than the others.*/
    const iniMod = ship.iniativeadded || 0;
    stats.initiative = (ship.iniativebonus || 0) + iniMod;
    stats.initiativeChanged = iniMod !== 0;

    /*TURN COST / TURN DELAY. Arithmetic mirrors the ship tooltip's (UI/ShipTooltip.js,
      itself a copy of movement.js calculateRequiredThrust) so the two readouts can never
      disagree: `ceil(speed x rate)` THRUST, a turn never costing less than 1, plus a flat
      +1 per docked LCV; the rate from getTurnCost/getTurnDelayCost already carries any
      attached ships; a submarine reversing pays 1.33x on the turn (but not the delay).
      Skipped when the engine or the ship's movement history is missing - every figure
      derives from the last committed move - leaving the blueprint rates on show.*/
    const move = manager.movement;
    if (move && typeof move.getTurnCost === 'function' && ship.movement && ship.movement.length > 0) {
        const speed = move.getSpeed(ship);
        const lcv = move.getDockedLcvTurnSurcharge(ship);

        const delayRate = move.getTurnDelayCost(ship);
        //turn rate first, THEN the sub penalty, THEN the speed multiply - the tooltip's
        //exact operand order, so no float rounding can flip a ceil() between the two
        let turnRate = move.getTurnCost(ship);
        if (ship.submarine && move.isGoingBackwards(ship)) turnRate = turnRate * 1.33;

        const turnCost = Math.max(1, Math.ceil(speed * turnRate)) + lcv;
        const turnDelay = Math.ceil(speed * delayRate) + lcv;

        /*thrust for this turn, with the rate it came from in parens - the ship tooltip's
          format. One deliberate divergence: the parenthesised turn rate here is the
          EFFECTIVE one (post sub-reversing 1.33x), so the row's own arithmetic reads
          straight; the tooltip prints the unmodified rate beside the modified thrust.*/
        stats.turnCost = costText(turnCost, turnRate);
        stats.turnDelay = costText(turnDelay, delayRate);

        /*Highlight test compares the WHOLE rendered string against the one the hull's own
          rate alone would produce (no attached ships, no docked LCVs, no sub penalty).
          Comparing only the thrust missed the Primus-with-attached-claw case (game 4072,
          user report 2026-07-26): a rate raised from e.g. 1.33 to 1.83 can still ceil() to
          the same thrust at low speed, so the row displayed a changed rate in plain white.
          Speed is common to both sides, so speed alone still can never light the row up.*/
        stats.turnCostChanged = stats.turnCost !== costText(Math.max(1, Math.ceil(speed * ship.turncost)), ship.turncost);
        stats.turnDelayChanged = stats.turnDelay !== costText(Math.ceil(speed * ship.turndelaycost), ship.turndelaycost);
    }

    return stats;
};

//Bases don't manoeuvre: only their Profile is relevant (feedback 2026-07-17),
//so the movement rows are dropped for them.
//`live` (game.php Ship Stats popup): show the stats as they stand THIS turn instead of
//the blueprint ones, yellowing whatever the game state has moved. Each row falls back to
//its blueprint figure on its own if the live one couldn't be worked out.
//`bare`: rows only, no title bar or panel frame - for the popup, whose own frame and
//button already carry both.
//            {Boolean(ship.agile) && <AgileRow>Agile ship</AgileRow>} Can re-add if we want the Agile notification here too
export const ManoeuvreStats = ({ ship, live, bare }) => {
    const mobile = !ship.base;
    const now = live ? liveShipStats(ship) : null;
    return (
        <StatsPanel $bare={bare}>
            {!bare && <StatsTitle><StatsIcon><i /><i /><i /></StatsIcon>Ship Stats</StatsTitle>}
            {mobile && <StatRow><StatLabel>Turn cost</StatLabel><StatValue $changed={Boolean(now && now.turnCostChanged)}>{now && now.turnCost ? now.turnCost : fix2(ship.turncost)}</StatValue></StatRow>}
            {mobile && <StatRow><StatLabel>Turn delay</StatLabel><StatValue $changed={Boolean(now && now.turnDelayChanged)}>{now && now.turnDelay ? now.turnDelay : fix2(ship.turndelaycost)}</StatValue></StatRow>}
            {mobile && <StatRow><StatLabel>Accel/decel</StatLabel><StatValue>{ship.accelcost}</StatValue></StatRow>}
            {mobile && <StatRow><StatLabel>Pivot</StatLabel><StatValue>{ship.pivotcost}</StatValue></StatRow>}
            {mobile && <StatRow><StatLabel>Roll</StatLabel><StatValue>{ship.rollcost}</StatValue></StatRow>}
            <StatRow><StatLabel>Profile - Front / Side</StatLabel><StatValue $changed={Boolean(now && now.profileChanged)}>{now ? now.profile : profileText(ship.forwardDefense, ship.sideDefense)}</StatValue></StatRow>
            {mobile && <StatRow><StatLabel>Initiative</StatLabel><StatValue $changed={Boolean(now && now.initiativeChanged)}>{now ? now.initiative : ship.iniativebonus}</StatValue></StatRow>}

        </StatsPanel>
    );
};

//standalone bottom-right Enhancements panel for the grid window (both the lobby and
//game.php, 2026-07-19); the rail keeps its inline block for flight/mine variants (no
//grid there)
export const EnhancementsPanel = ({ ship }) => {
    const enhLines = splitHtmlLines(ship.enhancementTooltip);
    if (enhLines.length === 0) return null;
    const wide = Boolean(window.gamedata) && window.gamedata.gamephase === -2; //lobby: 150px; game.php: 130px
    return (
        <EnhArea $wide={wide}>
            <Block $gold>
                <EnhTitle>Enhancements</EnhTitle>
                {enhLines.map((line, i) => <Row key={`enh-${i}`}>{line}</Row>)}
            </Block>
        </EnhArea>
    );
};

const splitHtmlLines = (text) =>
    (text || '')
        .split(/<br\s*\/?>/i)
        .map(line => line.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim())
        .filter(Boolean);

class ShipNotesPanel extends React.Component {

    render() {
        //hideEnhancements: the lobby grid window shows enhancements in the standalone
        //bottom-right EnhancementsPanel instead (feedback round 3)
        const { ship, full, grid, hideEnhancements } = this.props;

        const complement = buildComplement(ship);
        const noteLines = splitHtmlLines(ship.notes);
        const enhLines = hideEnhancements ? [] : splitHtmlLines(ship.enhancementTooltip);

        const meta = [];
        if (ship.limited && ship.limited != 0) meta.push("Limited: " + ship.limited + "%");
        if (ship.variantOf) {
            const occ = ship.occurence
                ? ship.occurence.charAt(0).toUpperCase() + ship.occurence.slice(1) + " "
                : "";
            meta.push(occ + "variant of " + ship.variantOf);
        }
        if (ship.isd) meta.push("In-Service (ISD): " + ship.isd);

        let customFlag = null;
        if (ship.unofficial === 'S') customFlag = "Semi-Custom";
        else if (ship.unofficial) customFlag = "Custom";

        const hasNotesBlock = noteLines.length > 0 || meta.length > 0 || customFlag;

        return (
            <Rail $full={full} $grid={grid}>
                {ship.flight && (
                    <Block>
                        <BlockTitle>Flight Stats</BlockTitle>
                        <StatRow><StatLabel>Armor F/S/A</StatLabel><StatValue>{shipManager.systems.getFlightArmour(ship)}</StatValue></StatRow>
                        <StatRow><StatLabel>Off. bonus</StatLabel><StatValue>{ship.offensivebonus * 5}</StatValue></StatRow>
                        {/*flights carry forwardDefense/sideDefense exactly like ships (the
                           lobby resets them from the blueprint on edit, and FtrPetals-style
                           systems mutate them live), so the profile reads the same way as
                           ManoeuvreStats' - user request 2026-07-23*/}
                        <StatRow><StatLabel>Profile - Front / Side</StatLabel><StatValue>{ship.forwardDefense * 5}/{ship.sideDefense * 5}</StatValue></StatRow>
                        <StatRow><StatLabel>Thrust</StatLabel><StatValue>{ship.freethrust}</StatValue></StatRow>
                        <StatRow><StatLabel>Initiative</StatLabel><StatValue>{ship.iniativebonus}</StatValue></StatRow>
                    </Block>
                )}

                {complement.length > 0 && (
                    <Block>
                        <BlockTitle>Hangar Capacity</BlockTitle>
                        {complement.map((line, i) => <Row key={`comp-${i}`}>{line}</Row>)}
                    </Block>
                )}

                {hasNotesBlock && (
                    <Block>
                        <BlockTitle>Notes</BlockTitle>
                        {noteLines.map((line, i) => <Row key={`note-${i}`}>{line}</Row>)}
                        {meta.map((line, i) => <Row key={`meta-${i}`}>{line}</Row>)}
                        {customFlag && <CustomFlag>{customFlag}</CustomFlag>}
                    </Block>
                )}

                {enhLines.length > 0 && (
                    <Block $gold>
                        <EnhTitle>Enhancements</EnhTitle>
                        {enhLines.map((line, i) => <Row key={`enh-${i}`}>{line}</Row>)}
                    </Block>
                )}
            </Rail>
        );
    }
}

export default ShipNotesPanel;
