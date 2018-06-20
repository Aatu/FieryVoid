import * as React from "react";
import styled from "styled-components"
import SystemIcon from "./SystemIcon"


const WeaponListContainer = styled.div`
    display:none;
    z-index: 2;
    position:fixed;
    left: 805px;
    width: calc(100%-805px);
    bottom: 0;
`;

class WeaponList extends React.Component{

    constructor(props) {
        super(props);
    }

    getWeapons(ship, gamePhase) {
        return ship.systems
            .filter(system => system.weapon)
            .filter(weapon => (gamePhase === 1 && weapon.ballistic) || (gamePhase === 3 && !weapon.ballistic))
    }

    render(){
        const {ship, gamePhase} = this.props;

        if (!ship) {
            return null;
        }

        const weapons = this.getWeapons(ship, gamePhase)

        return (
            <WeaponListContainer>
                {
                    weapons.map((weapon, index) => (<SystemIcon key={`system-${index}`} system={weapon}/>))
                }
            </WeaponListContainer>
        )
    }
}


export default WeaponList;
