import styled from "styled-components";

const Title = styled.span`
    font-family: arial;
    font-size: 16px;
    text-transform: uppercase;
    color: #deebff;
    padding: 10px;
    font-weight: bold;
`;

const SubTitle = Title.extend`
    font-weight: normal;
`;

export {Title, SubTitle};