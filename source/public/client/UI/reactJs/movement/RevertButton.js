import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";
import { Cancel } from "../icon";

const Container = styled.div`
  position: absolute;
  width: 50px;
  height: 50px;
  left: 125px;
  top: -153px;
  ${Clickable}
`;

class RevertButton extends React.Component {
  canRevert() {
    const { movementService, ship } = this.props;
    return movementService.canRevert(ship);
  }

  revert() {
    const { movementService, ship } = this.props;
    return movementService.revert(ship);
  }

  render() {
    if (!this.canRevert()) {
      return null;
    }

    return (
      <Container onClick={this.revert.bind(this)}>
        <Cancel />
      </Container>
    );
  }
}

export default RevertButton;
