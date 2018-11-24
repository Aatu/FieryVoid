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
  canEvade(step) {
    const { movementService, ship } = this.props;
    return movementService.canEvade(ship, step);
  }

  evade(step) {
    const { movementService, ship } = this.props;
    return movementService.evade(ship, step);
  }

  render() {
    return [
      this.canEvade(1) && (
        <Container key="evade-more" onClick={this.evade.bind(this, 1)}>
          <Evade />
        </Container>
      ),
      this.canEvade(-1) && (
        <RotatedContainer key="evade-less" onClick={this.evade.bind(this, -1)}>
          <Evade />
        </RotatedContainer>
      )
    ];
  }
}

export default EvadeButton;
