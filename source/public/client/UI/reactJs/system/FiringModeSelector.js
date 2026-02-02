import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";

const SelectorContainer = styled.div`
    display: grid;
    grid-template-columns: repeat(6, min-content);
    gap: 2px;
`;

// Reuse Button style but add selected state
const ModeButton = styled.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${props => props.img});
    background-size: cover;
    align-items: center;
    justify-content: center;
    ${Clickable}
    border: 1px solid ${props => props.selected ? 'yellow' : 'transparent'}; 
    position: relative;
    box-shadow: ${props => props.selected ? '0 0 5px #0cf' : 'none'};
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

        // Current firing mode index
        const currentModeIndex = parseInt(system.firingMode);

        // Icon path logic
        let iconPath = '';
        if (system.iconPath) {
            iconPath = `./img/systemicons/${system.iconPath}`;
        } else {
            iconPath = `./img/systemicons/${system.name}.png`;
        }

        const modeList = [];
        for (const key in system.firingModes) {
            if (system.firingModes.hasOwnProperty(key)) {
                // Ensure key is parsed as int if needed, usually keys are 1, 2, 3...
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
            <SelectorContainer>
                {modeList}
            </SelectorContainer>
        );
    }
}
