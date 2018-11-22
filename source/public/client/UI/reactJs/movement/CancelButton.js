import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";
import { X } from "../icon";

const Container = styled.div`
  position: absolute;
  width: 50px;
  height: 50px;
  left: -175px;
  top: -153px;
  ${Clickable}
`;

class CancelButton extends React.Component {
  render() {
    const { clicked, direction } = this.props;

    return (
      <Container onClick={clicked}>
        <X />
      </Container>
    );
  }
}

export default CancelButton;
