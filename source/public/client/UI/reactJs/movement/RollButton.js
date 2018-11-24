import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";
import { Roll } from "../icon";

const Container = styled.div`
  position: absolute;
  width: 50px;
  height: 50px;
  left: -25px;
  top: -153px;
  ${Clickable}
`;

class RollButton extends React.Component {
  canRoll() {
    const { movementService, ship } = this.props;
    return movementService.canRoll(ship);
  }

  roll() {
    const { movementService, ship } = this.props;
    return movementService.roll(ship);
  }

  render() {
    if (!this.canRoll()) {
      return null;
    }

    return (
      <Container onClick={this.roll.bind(this)}>
        <Roll />
      </Container>
    );
  }
}

export default RollButton;
