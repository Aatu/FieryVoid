import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";
import { Arrow } from "../icon";

const Container = styled.div`
  position: absolute;
  width: 50px;
  height: 50px;
  ${Clickable}

  ${props => `transform: rotate(${props.direction}deg);`}

  ${props => `left: calc(${props.x}px - 25px);`}
  ${props => `top: calc(${props.y}px - 25px);`}
`;

class ThrustButton extends React.Component {
  canThrust(direction) {}

  thrust(direction) {}

  getPosition(direction) {
    return mathlib.getPointInDirection(
      150,
      -mathlib.hexFacingToAngle(direction),
      0,
      0
    );
  }

  render() {
    const { clicked, direction } = this.props;

    return (
      <Container
        direction={mathlib.hexFacingToAngle(direction)}
        onClick={clicked}
        {...this.getPosition(direction)}
      >
        <Arrow />
      </Container>
    );
  }
}

export default ThrustButton;
