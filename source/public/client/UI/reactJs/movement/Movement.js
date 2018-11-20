import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";
import ThrustButton from "./ThrustButton";

const Container = styled.div`
  position: absolute;
  width: 0px;
  height: 0px;
  left: 1000px;
  top: 500px;
  z-index: 99999;
  opacity: 0.85;
`;

class Movement extends React.Component {
  canThrust(direction) {
    return true;
  }

  thrust(direction) {
    const { movementService, ship } = this.props;
    movementService.thrust(ship, direction);
  }

  render() {
    const { ship } = this.props;

    return (
      <Container id="shipMovementActual">
        {this.canThrust(0) && (
          <ThrustButton direction={0} clicked={this.thrust.bind(this, 0)} />
        )}
        {this.canThrust(1) && (
          <ThrustButton direction={1} clicked={this.thrust.bind(this, 1)} />
        )}
        {this.canThrust(2) && (
          <ThrustButton direction={2} clicked={this.thrust.bind(this, 2)} />
        )}
        {this.canThrust(3) && (
          <ThrustButton direction={3} clicked={this.thrust.bind(this, 3)} />
        )}
        {this.canThrust(4) && (
          <ThrustButton direction={4} clicked={this.thrust.bind(this, 4)} />
        )}
        {this.canThrust(5) && (
          <ThrustButton direction={5} clicked={this.thrust.bind(this, 5)} />
        )}
      </Container>
    );
  }
}

export default Movement;
