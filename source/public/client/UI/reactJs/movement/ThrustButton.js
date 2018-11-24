import * as React from "react";
import styled from "styled-components";
import { Arrow } from "../icon";

import Container from "./Container";

const ButtonContainer = styled(Container)`

  ${props => `transform: rotate(${props.direction + 90}deg);`}

  ${props => `left: calc(${props.x}px - 25px);`}
  ${props => `top: calc(${props.y}px - 25px);`}
`;

class ThrustButton extends React.Component {
  getPosition(direction) {
    return mathlib.getPointInDirection(
      150,
      -mathlib.hexFacingToAngle(direction),
      0,
      0
    );
  }

  canThrust() {
    const { ship, movementService, direction } = this.props;
    return movementService.canThrust(ship, direction);
  }

  thrust() {
    const { ship, movementService, direction } = this.props;
    return movementService.thrust(ship, direction);
  }

  render() {
    const { direction } = this.props;

    const can = this.canThrust();
    const { overChannel } = can;

    if (!can) {
      return null;
    }

    return (
      <ButtonContainer
        overChannel={overChannel}
        direction={mathlib.hexFacingToAngle(direction)}
        onClick={this.thrust.bind(this)}
        {...this.getPosition(direction)}
      >
        <Arrow />
      </ButtonContainer>
    );
  }
}

export default ThrustButton;
