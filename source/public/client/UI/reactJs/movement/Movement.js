import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";

const Container = styled.div`
  position: absolute;
`;

class Movement extends React.Component {
  constructor(props) {
    super(props);
    const { movementSevice, ship } = this.props;
    this.movementSevice = movementSevice;
    this.ship = ship;
  }

  render() {
    const { ship } = this.props;

    return <Container />;
  }
}

export default Movement;
