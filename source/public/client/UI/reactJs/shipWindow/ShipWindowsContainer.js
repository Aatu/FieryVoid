import * as React from "react";
import styled from "styled-components";
import ShipWindow from "./ShipWindow";

const ShipWindows = styled.div``;

class ShipWindowsContainer extends React.Component {
  render() {
    const { ships, movementService } = this.props;

    return (
      <ShipWindows>
        {ships.map(ship => (
          <ShipWindow
            key={`shipwindow-${ship.id}`}
            ship={ship}
            movementService={movementService}
          />
        ))}
      </ShipWindows>
    );
  }
}

export default ShipWindowsContainer;
