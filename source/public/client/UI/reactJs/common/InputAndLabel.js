import * as React from "react";
import styled from 'styled-components';


class InputAndLabel extends React.Component {
    render() {
        return (<Container>
            <Label>{this.props.label}</Label>
            <Input
                type={this.props.type || "text"}
                value={this.props.value}
                placeholder={this.props.placeholder}
                onKeyDown={this.props.onKeydown}
                onChange={this.props.onChange}
                tabIndex="0"
            />
        </Container>)
    }
}

const Container = styled.div`
    width: 100%;
    display: flex;
    padding: 10px;

    @media (max-width: 765px) {
        padding: 4px 10px;
    }
`;

const Label = styled.span`
    width: calc(50% - 10px);

    @media (max-width: 765px) {
        font-size: 12px;
    }
`;

const Input = styled.input`
    width: calc(50% - 10px);

    @media (max-width: 765px) {
        font-size: 12px;
        padding: 2px;
    }
`;



export { InputAndLabel };