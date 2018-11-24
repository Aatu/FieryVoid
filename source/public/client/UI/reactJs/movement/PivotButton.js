import * as React from "react";
import styled from "styled-components";
import { Pivot } from "../icon";
import Container from "./Container";

const ButtonContainer = styled(Container)`
  left: 125px;
  top: 103px;

  ${props =>
    props.pivotDirection === 1
      ? "left: -175px; transform: rotate(-90deg);"
      : "transform: rotate(-90deg) scaleY(-1);"}
`;

class PivotButton extends React.Component {
  canPivot() {
    const { ship, movementService, pivotDirection } = this.props;
    return movementService.canPivot(ship, pivotDirection);
  }

  pivot() {
    const { ship, movementService, pivotDirection } = this.props;
    return movementService.pivot(ship, pivotDirection);
  }

  render() {
    const { pivotDirection } = this.props;

    const can = this.canPivot();
    const { overChannel } = can;

    if (!can) {
      return null;
    }

    return (
      <ButtonContainer
        overChannel={overChannel}
        pivotDirection={pivotDirection}
        onClick={this.pivot.bind(this)}
      >
        <Pivot />
      </ButtonContainer>
    );
  }
}

export default PivotButton;
