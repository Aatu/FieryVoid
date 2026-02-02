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
        console.log(this.props.settings)
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
                <InputAndLabel label={"Key to display ALL Electronic Warfare (EW)"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowAllEW")} value={this.getKey.call(this, "ShowAllEW")} />
                <InputAndLabel label={"Key to display FRIENDLY Electronic Warfare (EW)"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowFriendlyEW")} value={this.getKey.call(this, "ShowFriendlyEW")} />
                <InputAndLabel label={"Key to display ENEMY Electronic Warfare (EW)"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowEnemyEW")} value={this.getKey.call(this, "ShowEnemyEW")} />
                <InputAndLabel label={"Key to display ALL Ballistics"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowAllBallistics")} value={this.getKey.call(this, "ShowAllBallistics")} />
                <InputAndLabel label={"Key to display FRIENDLY Ballistics"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowFriendlyBallistics")} value={this.getKey.call(this, "ShowFriendlyBallistics")} />
                <InputAndLabel label={"Key to display ENEMY Ballistics"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowEnemyBallistics")} value={this.getKey.call(this, "ShowEnemyBallistics")} />
                <InputAndLabel label={"Key to toggle RULER tool"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleLoS")} value={this.getKey.call(this, "ToggleLoS")} />
                <InputAndLabel label={"Key to toggle HEX numbers"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleHexNumbers")} value={this.getKey.call(this, "ToggleHexNumbers")} />
                <SubTitle>Sound</SubTitle>
                <InputAndLabel label={"Toggle sound in Replay"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleSound")} value={this.getKey.call(this, "ToggleSound")} />
                <SubTitle>Visual</SubTitle>
                <InputAndLabel placeholder="0" type="number" label={"Zoom level to change to strategic view"} onChange={this.getOnChange.call(this, "ZoomLevelToStrategic")} value={this.get.call(this, "ZoomLevelToStrategic")} />
                <Disclaimer>Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners.</Disclaimer>
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

const SubContainer = styled(Container)`
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

const Disclaimer = styled.p`
    font-size: 0.9rem;
    opacity: 0.7;
    margin-top: 20px;
    text-align: center;
    line-height: 1.3;
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