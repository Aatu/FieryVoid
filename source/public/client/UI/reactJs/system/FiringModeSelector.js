import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #571616;
    border: 1px solid #b43131;
    border-bottom: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`;

const SelectorContainer = styled.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`;

const ModeButton = styled.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${props => props.img});
    background-size: cover;
    align-items: center;
    opacity: 1 !important;    
    justify-content: center;
    ${Clickable}
    border: 1px solid ${props => props.selected ? '#ef4444' : 'transparent'};
    position: relative;
    box-shadow: ${props => props.selected ? '0 0 5px #b43131' : 'none'};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;

export default class FiringModeSelector extends React.Component {

    selectMode(e, mode) {
        e.stopPropagation();
        e.preventDefault();
        const { ship, system } = this.props;
        weaponManager.onSetModeClicked(ship, system, mode);
    }

    selectAllMode(e, mode) {
        e.stopPropagation();
        e.preventDefault();
        const { ship, system } = this.props;
        weaponManager.onSetModeAllClicked(ship, system, mode);
    }

    render() {
        const { ship, system } = this.props;

        // showModes lets callers host other controls (e.g. self-intercept) inside
        // this styled box without the firing-mode grid when there's nothing to pick.
        const showModes = this.props.showModes !== false;

        const currentModeIndex = parseInt(system.firingMode);

        let iconPath = '';
        if (system.iconPath) {
            iconPath = `./img/systemicons/${system.iconPath}`;
        } else {
            iconPath = `./img/systemicons/${system.name}.png`;
        }

        const modeList = [];
        for (const key in system.firingModes) {
            if (system.firingModes.hasOwnProperty(key)) {
                const modeIdx = parseInt(key);
                const modeName = system.firingModes[key];
                const isSelected = (modeIdx === currentModeIndex);

                let numLetters = system.modeLetters || 1;
                if (system.modeLettersArray && system.modeLettersArray[modeIdx]) {
                    numLetters = system.modeLettersArray[modeIdx];
                }

                modeList.push(
                    <ModeButton
                        key={modeIdx}
                        img={iconPath}
                        selected={isSelected}
                        onClick={(e) => this.selectMode(e, modeIdx)}
                        onContextMenu={(e) => this.selectAllMode(e, modeIdx)}
                        title={`Set mode: ${modeName} ${isSelected ? '(Current)' : ''} (Right click to set all)`}
                    >
                        {modeName.substring(0, numLetters)}
                    </ModeButton>
                );
            }
        }

        return (
            <Container>
                {showModes && <Header>Select Firing Mode</Header>}
                <SelectorContainer>
                    {showModes && modeList}
                    {this.props.children}
                </SelectorContainer>
            </Container>
        );
    }
}
