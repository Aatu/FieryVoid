import styled from "styled-components";
import { Clickable } from "../styled";

const Container = styled.div`
  position: absolute;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  svg #svg-path {
    fill: white;
    ${props => props.overChannel && "fill:#ffad3a;"}
  }
  ${Clickable}
`;

export default Container;
