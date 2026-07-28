import * as React from "react";
import styled from "styled-components"
import SystemIcon from "../system/SystemIcon"
import theme from "../styled/theme";

/*SCS-style section panel (SHIPWINDOW_REDESIGN_PLAN.md Stage 1a): dotted panel with a
  header line reading "PORT 54/60 A6" whose background fill IS the structure health bar
  (same colour semantics as the old bottom bar: green, orange when criticals). The panel
  background is translucent so the window's watermark hull art reads through the gaps.

  Grid placement (grid-area / vertical alignment / width class) is decided by ShipWindow
  and passed in via props; outside the grid (terrain / mine compact windows) those props
  are simply omitted. `displayLocation` is the location whose *visual side* this section
  is drawn on - it differs from `location` only for rolled ships (port/starboard swap)
  and only affects the icon mirroring, never the section name.*/

const SECTION_NAMES = {
    0: "Primary",
    1: "Forward",
    2: "Aft",
    3: "Port",
    4: "Starboard",
    5: "",
    31: "Port Fwd",
    32: "Port Aft",
    41: "Stbd Fwd",
    42: "Stbd Aft"
};

const ShipSectionContainer = styled.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${theme.colors.panelBgGlass};
    ${props => props.$area ? `grid-area: ${props.$area};` : ''}
    ${props => props.$valign ? `align-self: ${props.$valign};` : ''}
    ${props => props.$justify ? `justify-self: ${props.$justify};` : ''}
    width: ${props => {
        if (props.$isTerrain) return '125px';
        //fixed widths keep the icon rows at 4-wide (centre column) and 3-wide (side
        //columns) - the pick/pickOuter symmetric grouping assumes those row lengths.
        //Side columns are 128px, not the bare 120px the three 32px icons need: the extra
        //room lets the header line fit 3-figure structure text ("STARBOARD 540/600 A8")
        //without clipping. 128 is the ceiling that still holds 3-wide rows - at 130px a
        //4th 32px icon (zero horizontal margin) would fit and break the symmetric layout.
        return props.$wide ? '156px' : '128px';
    }};
    ${props => props.$minHeight ? `min-height: ${props.$minHeight}px;` : ''}
    /*Ship Art toggle (item 3, 2026-07-22): hide the whole section (header + icons) but
      keep its grid footprint so the window/watermark never resize while the art shows*/
    ${props => props.$hidden ? 'visibility: hidden;' : ''}
    margin: ${props => props.$area ? '0' : '2px'};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;

    border: ${props => {
        switch (props.$location) {
            case 0:
                //Primary keeps the heavier solid border for emphasis, in the shared line colour
                return `1px solid ${theme.colors.line}`;
            default:
                return `1px dotted ${theme.colors.line}`;
        }
    }};
`;

const SectionHeader = styled.div`
    position: relative;
    height: 15px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    padding: 0 4px;
    box-sizing: border-box;
    background-color: black;
    border-bottom: 1px solid ${theme.colors.healthOk};
    overflow: hidden;

    /*structure health fill - the header line doubles as the section's health bar*/
    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: ${props => props.$health}%;
        background-color: ${props => props.$criticals ? theme.colors.healthCrit : theme.colors.healthOk};
    }
`;

/*The structure readout keeps the mono (SCS datasheet) font the user prefers (2026-07-22
  revert). Mono (Consolas) and the arial name centre at slightly different heights under
  the header's align-items:center, so the name read a touch high against the readout (the
  "top-aligned label vs centred value" report). Rather than force one font, the NAME gets
  a small downward nudge to sit level with the readout, which stays the visual reference
  (it already looked centred). Tweak the name's `top` if it needs a hair more/less.*/
const SectionName = styled.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${theme.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
`;

const StructureText = styled.span`
    position: relative;
    z-index: 1;
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${props => props.$destroyed ? 'transparent' : theme.colors.text};
    filter: ${props => props.$destroyed ? 'blur(1px)' : 'none'};
    text-shadow: black 0 0 6px, black 0 0 6px;
`;

const IconArea = styled.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;

/*Structure arc indicator (STRUCTURE_ARCS_PLAN.md): hovering (or long-pressing) a section's
  health bar draws that section's facing wedge on the 3D icon, so a player can see at a glance
  which bearings feed damage into the bar they are looking at. The header raises its OWN
  StructureMouseOver/Out event rather than SystemMouseOver: the arc is all that's wanted here,
  with no system-info tooltip and no interference with the weapon-arc show/hide. PhaseStrategy
  handles it on game.php; the lobby's uiEvents handler ignores it (no map to draw on).

  Touch mirrors SystemIcon: 400ms hold, 10px move cancels, plus the shared
  window.lastTouchActiveTime guard against the ghost mouse events a tap emits afterwards.*/
class ShipSection extends React.Component {

    constructor(props) {
        super(props);
        this.longPressTimer = null;
        this.touchActive = false;
        this.arcShown = false; //so only a section that actually raised a wedge asks for the sweep
        this.onStructureMouseOver = this.onStructureMouseOver.bind(this);
        this.onStructureMouseOut = this.onStructureMouseOut.bind(this);
        this.onStructureTouchStart = this.onStructureTouchStart.bind(this);
        this.onStructureTouchMove = this.onStructureTouchMove.bind(this);
        this.onStructureTouchEnd = this.onStructureTouchEnd.bind(this);
        this.onStructureTouchCancel = this.onStructureTouchCancel.bind(this);
    }

    componentWillUnmount() {
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
        //a section unmounting under the cursor/finger (window closed, turn refresh) never fires
        //mouse-out, so its wedge would be left stranded on the map
        this.hideStructureArc();
    }

    showStructureArc() {
        const { ship, systems } = this.props;
        this.arcShown = true;
        window.uiEvents.relay('StructureMouseOver', { ship: ship, structure: getStructure(systems) });
    }

    hideStructureArc() {
        if (!this.arcShown) return;
        this.arcShown = false;
        window.uiEvents.relay('StructureMouseOut');
    }

    onStructureMouseOver(event) {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return; //ghost mouseover after a tap

        event.stopPropagation();
        this.showStructureArc();
    }

    onStructureMouseOut(event) {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        event.stopPropagation();
        this.hideStructureArc();
    }

    onStructureTouchStart(event) {
        event.stopPropagation();
        this.touchActive = true;
        window.lastTouchActiveTime = Date.now();

        if (this.longPressTimer) clearTimeout(this.longPressTimer);

        const touch = event.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartY = touch.clientY;

        this.longPressTimer = setTimeout(() => {
            this.showStructureArc();
            this.longPressTimer = null;
        }, 400);
    }

    onStructureTouchMove(event) {
        event.stopPropagation();
        if (!this.longPressTimer) return;

        const touch = event.touches[0];
        if (Math.abs(touch.clientX - this.touchStartX) > 10 || Math.abs(touch.clientY - this.touchStartY) > 10) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
    }

    onStructureTouchEnd(event) {
        event.stopPropagation();
        if (this.longPressTimer) { //short tap - the hold never completed, so no arc was shown
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        } else {
            this.hideStructureArc();
        }

        setTimeout(() => {
            this.touchActive = false;
        }, 300);
    }

    onStructureTouchCancel(event) {
        event.stopPropagation();
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
        this.touchActive = false;
        this.hideStructureArc();
    }

    render() {
        const { ship, systems, location, displayLocation, area, valign, justify, wide, isTerrain, minHeight, nameOverride, hidden } = this.props;

        const structure = getStructure(systems);
        const health = structure ? getStructureLeft(ship, structure) : 0;
        const orderLocation = displayLocation !== undefined ? displayLocation : location;
        //rolled ship: this section is drawn on the opposite side - flip the icon art
        //horizontally so weapon/thruster facings match the drawn side
        const mirrored = displayLocation !== undefined && displayLocation !== location;

        return (
            <ShipSectionContainer $location={location} $area={area} $valign={valign} $justify={justify} $wide={wide} $isTerrain={isTerrain} $minHeight={minHeight} $hidden={hidden}>
                {/*sections without structure exist purely for icon placement - no header*/}
                {/*nameOverride: ships with a single side structure spread over both
                   quarter sections read plain "Port"/"Starboard" (set by ShipWindow)*/}
                {structure && <SectionHeader
                    $health={health}
                    $criticals={hasCriticals(structure)}
                    onMouseOver={this.onStructureMouseOver}
                    onMouseOut={this.onStructureMouseOut}
                    onTouchStart={this.onStructureTouchStart}
                    onTouchMove={this.onStructureTouchMove}
                    onTouchEnd={this.onStructureTouchEnd}
                    onTouchCancel={this.onStructureTouchCancel}>
                    <SectionName>{nameOverride || SECTION_NAMES[location] || ""}</SectionName>
                    <StructureText $destroyed={health === 0}>
                        {structure.maxhealth - damageManager.getDamage(ship, structure)}/{structure.maxhealth} A{shipManager.systems.getArmour(ship, structure)}
                    </StructureText>
                </SectionHeader>}
                <IconArea>
                    {orderSystems(systems, orderLocation, wide).map(system => (<SystemIcon scs mirror={mirrored} key={`system-scs-${location}-${ship.id}-${system.id}`} system={system} ship={ship} />))}
                </IconArea>
            </ShipSectionContainer>
        )
    }
}

const getStructureLeft = (ship, system) => (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100;

const hasCriticals = (system) => shipManager.criticals.hasCriticals(system)

/*name check, NOT instanceof (matches systems.js getStructureSystem): lobby fleet
  ships are jQuery.extend clones of the blueprint whose systems lose their prototype
  chain - instanceof Structure was false for them, so bought-ship sections rendered
  headerless (no health bar) and the structure leaked into the icon grid as a "0".*/
const isStructure = system => system.name === 'structure';

const getStructure = systems => systems.find(isStructure)

const filterStructure = systems => systems.filter(system => !isStructure(system))

const orderSystems = (systems, location, wide) => {
    systems = filterStructure(systems);

    //a section rendered at centre-column width (156px) fits 4 icons per row - used for
    //big-base quarter sections so their many systems spread wide instead of stacking
    //tall. pickOuter rows are symmetric by construction, so no mirroring is needed.
    if (wide) {
        return orderSystemsFourWide(systems);
    }

    if ([4, 41, 42].includes(location)) {
        return orderSystemsThreeWide(systems);
    } else if ([3, 31, 32].includes(location)) {
        return reverseRowsOfThree(orderSystemsThreeWide(systems));
    } else if ([1, 2, 0].includes(location)) {
        return orderSystemsFourWide(systems);
    } else {
        return orderWide(systems)
    }
}

const reverseRowsOfThree = (systems) => {
    let list = [];

    systems.forEach((system, i) => {
        const j = i % 3;
        if (j === 0) {
            list[i + 2] = system;
        } else if (j === 1) {
            list[i] = system;
        } else {
            list[i - 2] = system;
        }
    })

    return list;
}

const orderWide = (systems) => {
    systems = filterStructure(systems);

    let list = [];

    if (systems.length === 3) {
        return orderSystemsThreeWide(systems);
    } else if (systems.length === 4) {
        return orderSystemsFourWide(systems);
    } else {
        return systems;
    }
}

const orderSystemsFourWide = (systems) => {
    systems = filterStructure(systems);

    let list = [];

    //4 equal systems
    while (true) {

        const { picked, remaining } = pickOuter(systems, 4);

        if (picked.length === 0) {
            break;
        }

        systems = remaining;

        list = list.concat(picked);
    }


    //2 systems, plus optionally 2 other systems in the middle
    while (true) {

        const { picked, remaining } = pickOuter(systems, 2);

        if (picked.length === 0) {
            break;
        }


        systems = remaining;

        const secondPick = pickOuter(systems, 2);

        if (secondPick.picked.length > 0) {
            systems = secondPick.remaining;
            list = list.concat([picked[0], secondPick.picked[0], secondPick.picked[1], picked[1]]);
        } else {
            //list = list.concat([picked[0], systems.pop(), systems.pop(), picked[1]]) //use shift so system order is not reversed
            list = list.concat([picked[0], systems.shift(), systems.shift(), picked[1]]);
            list = list.filter(system => system);
        }
    }

    list = list.concat(systems);

    return list;
}

const orderSystemsThreeWide = (systems) => {
    systems = filterStructure(systems);

    let list = [];

    while (true) {

        const { picked, remaining } = pick(systems, 3);

        if (picked.length === 0) {
            break;
        }

        systems = remaining;

        list = list.concat(picked);
    }

    while (true) {

        const { picked, remaining } = pick(systems, 2);

        if (picked.length === 0) {
            break;
        }

        const { three, remainingSystems } = findFriendForTwo(picked, remaining);

        systems = remainingSystems;

        list = list.concat(three);
    }

    list = list.concat(systems);

    return list;
}

const findFriendForTwo = (two, systems) => {

    const onePick = pick(systems, 1);

    /* singleton in the middle - does not look that good on the sides! changing to singleton on the inside
    if (onePick.picked.length === 1) {
        return {three: [two[0], onePick.picked[0], two[1]], remainingSystems: onePick.remaining}
    }

    if (systems.length > 0) {
        return {three: [two[0], systems.pop(), two[1]], remainingSystems: systems}
    }
    */
    if (onePick.picked.length === 1) {
        return { three: [onePick.picked[0], two[0], two[1]], remainingSystems: onePick.remaining }
    }

    if (systems.length > 0) {
        /*return {three: [systems.pop(), two[0], two[1] ], remainingSystems: systems}*/ //use shift so system order is not reversed
        return { three: [systems.shift(), two[0], two[1]], remainingSystems: systems }
    }

    return { three: [two[0], two[1]], remainingSystems: systems }
}

const pick = (systems, amount = 3) => {
    const one = systems.find(system => {
        const count = systems.reduce((all, otherSystem) => {
            if (otherSystem.name === system.name) {
                return all + 1;
            }

            return all;
        }, 0)

        if (amount === 1) {
            return count === amount;
        } else {
            return count >= amount;
        }
    });

    if (!one) {
        return { picked: [], remaining: systems };
    }

    let picked = [];
    // this gets first X...
    const remaining = systems.filter(otherSystem => {
        if (otherSystem.name === one.name && amount > 0) {
            amount--;
            picked.push(otherSystem);
            return false;
        }

        return true;
    })

    return { picked, remaining };
}


//like pick(), but instead of picking first X elements - picks outer X elements
const pickOuter = (systems, amount = 3) => {
    const one = systems.find(system => {
        const count = systems.reduce((all, otherSystem) => {
            if (otherSystem.name === system.name) {
                return all + 1;
            }

            return all;
        }, 0)

        if (amount === 1) {
            return count === amount;
        } else {
            return count >= amount;
        }
    });

    if (!one) {
        return { picked: [], remaining: systems };
    }

    let picked = [];
    let picked2 = [];
    // this gets outer X...
    const remaining = systems.filter(otherSystem => {
        if (otherSystem.name === one.name /*&& amount > 0*/) {
            //amount--;
            picked2.push(otherSystem);
            return false;
        }

        return true;
    })

    var fromBeginning = Math.ceil(amount / 2);
    var fromEnding = Math.floor(amount / 2);
    for (var i = 0; i < picked2.length; i++) {
        if ((i < fromBeginning) || (i >= (picked2.length - fromEnding))) { //elements from beginning and end get picked
            picked.push(picked2[i]);
        } else {//remaining elements (from the middle) get returned to the pool
            remaining.unshift(picked2[i]); //return to the beginning - so they're picked first in next row!
        }
    }

    return { picked, remaining };
}


export default ShipSection;
