import styled from "styled-components";

const Title = styled.span`
    font-family: arial;
    font-size: 16px;
    text-transform: uppercase;
    color: #deebff;
    padding: 10px;
    font-weight: bold;

    @media (max-width: 765px) {
        padding: 5px 10px;
        font-size: 14px;
    }
`;

const SubTitle = styled(Title)`
    font-weight: normal;
`;

export { Title, SubTitle };