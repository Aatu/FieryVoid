import * as React from "react";
import styled from "styled-components"
import FighterIcon from './FighterIcon';


const FighterListContainer = styled.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;

class FighterList extends React.Component{

    render() {
        const {ship} = this.props;

        return (
            <FighterListContainer>
                {getFighters(ship)}
            </FighterListContainer>
        );
    }

}

const getFighters = (ship) => {
    return ship.systems.map((fighter, i) => {
        return (
            <FighterIcon key={`flight-${ship.id}-${i}`} fighter={fighter} ship={ship}/>
        );
    })  
}

export default FighterList;