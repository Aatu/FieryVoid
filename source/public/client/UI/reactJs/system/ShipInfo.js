import * as React from "react";
import styled from "styled-components"
import {Header, Entry} from './SystemInfo';

const InfoContainer = styled.div``;

class ShipInfo extends React.Component {

    render() {
        const {ship} = this.props;

         return (
            <InfoContainer>
                {ship.flight && <Entry><Header>Offensive bonus: </Header>{ship.offensivebonus * 5}</Entry>}
                {ship.flight && <Entry><Header>Armor (F/S/A): </Header>{shipManager.systems.getFlightArmour(ship)}</Entry>}
                {ship.flight && <Entry><Header>Thrust per turn: </Header>{ship.freethrust}</Entry>}
            </InfoContainer>
         );
    }
}

export default ShipInfo;