import * as React from "react";
import styled from "styled-components";
import { X } from "../icon";
import Container from "./Container";

const ButtonContainer = styled(Container)`
  left: -175px;
  top: -153px;
`;

class CancelButton extends React.Component {
  canCancel() {
    const { movementService, ship } = this.props;
    return movementService.canCancel(ship);
  }

  cancel() {
    const { movementService, ship } = this.props;
    return movementService.cancel(ship);
  }

  render() {
    if (!this.canCancel()) {
      return null;
    }

    return (
      <ButtonContainer onClick={this.cancel.bind(this)}>
        <X />
      </ButtonContainer>
    );
  }
}

export default CancelButton;
