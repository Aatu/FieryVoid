import * as React from "react";
import styled from "styled-components";
import ThrustButton from "./ThrustButton";
import RevertButton from "./RevertButton";
import CancelButton from "./CancelButton";
import PivotButton from "./PivotButton";
import RollButton from "./RollButton";
import EvadeButton from "./EvadeButton";

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
  render() {
    const { ship, movementService } = this.props;

    return (
      <Container id="shipMovementActual">
        <ThrustButton
          ship={ship}
          movementService={movementService}
          direction={0}
        />

        <ThrustButton
          ship={ship}
          movementService={movementService}
          direction={1}
        />

        <ThrustButton
          ship={ship}
          movementService={movementService}
          direction={2}
        />

        <ThrustButton
          ship={ship}
          movementService={movementService}
          direction={3}
        />

        <ThrustButton
          ship={ship}
          movementService={movementService}
          direction={4}
        />

        <ThrustButton
          ship={ship}
          movementService={movementService}
          direction={5}
        />

        <RevertButton ship={ship} movementService={movementService} />

        <CancelButton ship={ship} movementService={movementService} />

        <PivotButton
          ship={ship}
          movementService={movementService}
          pivotDirection={-1}
        />

        <PivotButton
          ship={ship}
          movementService={movementService}
          pivotDirection={1}
        />

        <RollButton ship={ship} movementService={movementService} />

        <EvadeButton ship={ship} movementService={movementService} />
      </Container>
    );
  }
}

export default Movement;
