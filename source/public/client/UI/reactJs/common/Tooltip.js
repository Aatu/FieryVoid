import * as React from "react";
import styled from 'styled-components';
import { Transform } from "stream";


class Component extends React.Component{
    render(){
        const {children, className} = this.props
        return (
            <div className={className}>
                {children}
            </div>
        )
    }
}

const Tooltip = styled(Component)`
    z-index:7001;
    opacity:0.65;
    position:absolute;
    text-align:center;
    font-family:arial;
    font-size:12px;
    color:white;
    background-color:black;
    border-radius: 7px;
    -moz-border-radius: 7px;
    -webkit-border-radius: 7px;
    padding:3px 15px 3px 15px;
    padding-bottom: 8px;
`;


const TooltipHeader = styled.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`

const TooltipEntry = styled.div`
    color: ${props => {
        if (props.type == 'good') {
            return '#6fc126;';
        } else if (props.type == 'bad') {
            return '#ff7b3f;';
        } else {
            return 'white;'
        }
    }}
    font-weight: ${props => props.important ? 'bold' : 'inherit'};
    font-size: ${props => props.important ? '14px' : '12px'};
    margin-top: ${props => props.space ? '14px' : '0'};
`

export {Tooltip, TooltipHeader, TooltipEntry}