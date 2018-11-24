import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";
import { Evade } from "../icon";

const Container = styled.div`
  position: absolute;
  width: 40px;
  height: 40px;
  left: -20px;
  top: 103px;
  ${Clickable}
`;

const RotatedContainer = styled(Container)`
  transform: rotate(180deg);
  top: 153px;
`;

class EvadeButton extends React.Component {
  canEvade() {
    const { movementService, ship } = this.props;
    return movementService.canEvade(ship);
  }

  evade() {
    const { movementService, ship } = this.props;
    return movementService.roll(ship);
  }

  render() {
    return [
      <Container key="evade-more" onClick={this.evade(this)}>
        <Evade />
      </Container>,
      <RotatedContainer key="evade-less" onClick={this.evade(this)}>
        <Evade />
      </RotatedContainer>
    ];
  }
}

export default EvadeButton;
