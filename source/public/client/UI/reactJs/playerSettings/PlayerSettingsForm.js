import * as React from "react";
import styled from 'styled-components';
import { Container, Title, SubTitle, Backdrop, ContainerRoundedRightBottom, Clickable } from "../styled";
import { InputAndLabel } from "../common";


class PlayerSettingsForm extends React.Component {

    getOnChange(key) {
        return (e) => {
            this.props.set(key, e.target.value);
            this.props.save();
            this.forceUpdate();
        };
    }

    getOnKeyDown(key) {
        return (e) => {

            console.log("keydown");
            e.preventDefault();
            e.stopPropagation();

            if (!keyCodes[e.keyCode]) {
                return;
            }

            const keyDefinition = {
                keyCode: e.keyCode,
                shiftKey: e.shiftKey,
                altKey: e.altKey,
                ctrlKey: e.ctrlKey,
                metaKey: e.metaKey
            };

            this.props.set(key, keyDefinition);
            this.props.save();
            this.forceUpdate();
        };
    }

    getKey(key) {
        return keyToString(this.props.settings[key]);
    }

    get(key) {
        return this.props.settings[key];
    }

    render() {
        return (<Backdrop>
            <SubContainer>
                <CloseButton onClick={this.props.close}>✕</CloseButton>
                <Title>Player settings</Title>
                <Paragraph>Change settings for currently used browser and device. Reload the page to apply changed settings.</Paragraph>
                <SubTitle>Keys</SubTitle>
                <InputAndLabel label={"Key to display all EW"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowAllEW")} value={this.getKey.call(this, "ShowAllEW")} />

                <SubTitle>Visual</SubTitle>
                <InputAndLabel placeholder="0" type="number" label={"Zoom level to change to strategic view"} onChange={this.getOnChange.call(this, "ZoomLevelToStrategic")} value={this.get.call(this, "ZoomLevelToStrategic")} />

            </SubContainer>
        </Backdrop>)
    }
}

const keyToString = (key) => {
    let value = keyCodes[key.keyCode];

    value = value.toUpperCase();

    if (key.shiftKey) {
        value += " + shift";
    }

    if (key.altKey) {
        value += " + alt";
    }

    if (key.ctrlKey) {
        value += " + ctrl";
    }

    if (key.metaKey) {
        value += " + cmd";
    }

    return value;
};

const CloseButton = styled.div`
    width: 50px;
    height: 50px;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 9999999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
    padding-left: 5px;
    ${Clickable}
`;

const SubContainer = Container.extend`
    position: relative;
    max-width: 100%;
    width: 800px;
    margin: 30px auto;
    display: flex;
    flex-direction: column;
`;

const Paragraph = styled.p`
    padding: 0 10px 0 10px;
    margin: 5px 0 5px 0;
    color: #6689ba;
`;

const keyCodes = {
    48: "0",
    49: "1",
    50: "2",
    51: "3",
    52: "4",
    53: "5",
    54: "6",
    55: "7",
    56: "8",
    57: "9",
    58: ":",
    65: "a",
    66: "b",
    67: "c",
    68: "d",
    69: "e",
    70: "f",
    71: "g",
    72: "h",
    73: "i",
    74: "j",
    75: "k",
    76: "l",
    77: "m",
    78: "n",
    79: "o",
    80: "p",
    81: "q",
    82: "r",
    83: "s",
    84: "t",
    85: "u",
    86: "v",
    87: "w",
    88: "x",
    89: "y",
    90: "z",
};
export default PlayerSettingsForm;