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

const TextContainer = styled.div`
    position: absolute
    color: white;
    font-family: arial;
    margin: 0;
    font-size: 16px;
    text-transform: uppercase;
    height: 20px;
    padding: 2px 0;
    overflow: hidden;
     /* background-color: rgba(0, 0, 0, 0.5); */
    display: flex;
    justify-content: center;
    align-items: center;
    text-shadow: black 0px 0px 13px, black 0px 0px 13px, black 0px 0px 13px;
    cursor: default;
`;

const EnginePower = styled(TextContainer)`
  top: -100px;
  left: -120px;
  width: 240px;
`;

const Evasion = styled(TextContainer)`
  top: 76px;
  left: -180px;
  width: 360px;
  justify-content: space-around;
`;

const Stats = styled(TextContainer)`
  top: -76px;
  left: -120px;
  width: 240px;
  font-size: 14px;
  text-transform: none;
  justify-content: space-around;
  flex-wrap: wrap;
`;

const OverChannel = styled(Stats)`
  top: 52px;
  left: -100px;
  width: 200px;
`;

class Movement extends React.Component {
  render() {
    const { ship, movementService } = this.props;

    return (
      <Container id="shipMovementActual">
        <EnginePower>
          {`Engine power: ${movementService.getRemainingEngineThrust(
            ship
          )} / ${movementService.getTotalProducedThrust(ship)}`}
        </EnginePower>
        {/*
        <Stats>
          <div>{`Acceleration cost: ${ship.accelcost}`}</div>
          <div>{`Pivot cost: ${ship.pivotcost}`}</div>
        </Stats>
       */}
        <Evasion>
          <div>{`Over: ${movementService.getOverChannel(ship)}`}</div>
          <div>{`Evasion: ${movementService.getEvasion(ship)}`}</div>
          <div>{`Rolling: ${
            movementService.getRollMove(ship) ? "yes" : "no"
          }`}</div>
        </Evasion>
        {/* 
        <OverChannel>
          <div>{`Evasion cost: ${ship.evasioncost}`}</div>
          <div>{`Roll cost: ${ship.rollcost}`}</div>
        </OverChannel>
        */}
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
