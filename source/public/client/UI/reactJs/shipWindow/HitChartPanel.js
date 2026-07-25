import * as React from "react";
import styled from "styled-components";
import theme from "../styled/theme";
import buildHitChart from "../helpers/buildHitChart";

/*Hit-chart popup body (SHIPWINDOW_REDESIGN_PLAN.md Stage 1b, reworked after the
  2026-07-16 feedback rounds): section tables arranged geographically like the ship
  itself - Port column | Front/Primary/Aft column | Starboard column, with the side
  columns vertically centred against the middle one. Rows sorted by hit chance
  descending like the legacy hitChartSetup tables. Content only - the popup box
  (position, background, close-on-outside-click) is ShipWindow's PopupHolder.*/

//hit-chart locations per popup column, top-to-bottom in ship order
const LEFT_LOCATIONS = [3, 31, 32];
const CENTRE_LOCATIONS = [1, 0, 2]; //Front / Primary / Aft (base "Sections" is location 1)
const RIGHT_LOCATIONS = [4, 41, 42];

const Columns = styled.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`;

const Column = styled.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${props => props.$side ? 'center' : 'flex-start'};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`;

const SectionTable = styled.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${theme.colors.line};
    padding: 3px 5px;
`;

/*shaded header bar filling the table edge-to-edge (negative margins cancel
  SectionTable's padding) so section names stand out from the rows*/
const SectionName = styled.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${theme.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${theme.colors.line};
`;

const Row = styled.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${theme.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`;

const Pct = styled.span`
    font-family: ${theme.fonts.mono};
    color: ${theme.colors.text};
    flex-shrink: 0;
`;

const renderTable = (ship, section) => (
    <SectionTable key={`hitchart-${ship.id}-${section.location}`}>
        <SectionName>{section.name}</SectionName>
        {[...section.entries]
            .sort((a, b) => b.chance - a.chance)
            .map((entry, i) => (
                <Row key={`hitchart-${ship.id}-${section.location}-${i}`}>
                    <span>{entry.name}</span>
                    <Pct>{entry.chance}%</Pct>
                </Row>
            ))}
    </SectionTable>
);

class HitChartPanel extends React.Component {
    render() {
        const { ship } = this.props;
        const chart = buildHitChart(ship);

        if (chart.length === 0) return null;

        const byLocation = {};
        chart.forEach(section => { byLocation[section.location] = section; });

        const pickColumn = locations => locations
            .filter(location => byLocation[location])
            .map(location => renderTable(ship, byLocation[location]));

        const left = pickColumn(LEFT_LOCATIONS);
        const centre = pickColumn(CENTRE_LOCATIONS);
        const right = pickColumn(RIGHT_LOCATIONS);

        return (
            <Columns>
                {left.length > 0 && <Column $side>{left}</Column>}
                {centre.length > 0 && <Column>{centre}</Column>}
                {right.length > 0 && <Column $side>{right}</Column>}
            </Columns>
        );
    }
}

export default HitChartPanel;
