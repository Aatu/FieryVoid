import * as React from "react";
import styled from 'styled-components';
import { Backdrop, Clickable } from "../styled";
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
        return (<Overlay onClick={this.props.close}>
            <Panel onClick={(e) => e.stopPropagation()}>
                <Header>
                    <HeaderTitle>Player Settings</HeaderTitle>
                    <CloseButton onClick={this.props.close} title="Close">✕</CloseButton>
                </Header>
                <Body>
                    <Intro>Settings apply to this browser and device only. Reload the page for changes to take effect.</Intro>

                    <SectionLabel>Keys</SectionLabel>
                    <InputAndLabel label={"Display ALL Electronic Warfare (EW)"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowAllEW")} value={this.getKey.call(this, "ShowAllEW")} />
                    <InputAndLabel label={"Display FRIENDLY Electronic Warfare (EW)"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowFriendlyEW")} value={this.getKey.call(this, "ShowFriendlyEW")} />
                    <InputAndLabel label={"Display ENEMY Electronic Warfare (EW)"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowEnemyEW")} value={this.getKey.call(this, "ShowEnemyEW")} />
                    <InputAndLabel label={"Display ALL Ballistics"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowAllBallistics")} value={this.getKey.call(this, "ShowAllBallistics")} />
                    <InputAndLabel label={"Display FRIENDLY Ballistics"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowFriendlyBallistics")} value={this.getKey.call(this, "ShowFriendlyBallistics")} />
                    <InputAndLabel label={"Display ENEMY Ballistics"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ShowEnemyBallistics")} value={this.getKey.call(this, "ShowEnemyBallistics")} />
                    <InputAndLabel label={"Toggle RULER tool"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleLoS")} value={this.getKey.call(this, "ToggleLoS")} />
                    <InputAndLabel label={"Toggle HEX numbers"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleHexNumbers")} value={this.getKey.call(this, "ToggleHexNumbers")} />
                    <InputAndLabel label={"Toggle MAP background"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleBackground")} value={this.getKey.call(this, "ToggleBackground")} />

                    <SectionLabel>Replay</SectionLabel>
                    <InputAndLabel label={"Play / pause Replay"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "TogglePlayPause")} value={this.getKey.call(this, "TogglePlayPause")} />

                    <SectionLabel>Sound</SectionLabel>
                    <InputAndLabel label={"Toggle sound in Replay"} onChange={() => { }} onKeydown={this.getOnKeyDown.call(this, "ToggleSound")} value={this.getKey.call(this, "ToggleSound")} />

                    <SectionLabel>Visual</SectionLabel>
                    <InputAndLabel placeholder="0" type="number" label={"Zoom level to switch to strategic view"} onChange={this.getOnChange.call(this, "ZoomLevelToStrategic")} value={this.get.call(this, "ZoomLevelToStrategic")} />

                    <Disclaimer>Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners.</Disclaimer>
                </Body>
            </Panel>
        </Overlay>)
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

/* Full-screen dim + centring layer. Extends the shared Backdrop (which handles
   the fixed cover + z-index) and adds flex centring for the compact Panel plus a
   light blur so the board reads as pushed back. Click the dim area to close. */
const Overlay = styled(Backdrop)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`;

/* Compact modal card. Sits centred over the Backdrop; the panel itself scrolls
   internally (Body) so it never overflows a short viewport. Palette + hairline
   borders match the phase header / replay UI HUD. */
const Panel = styled.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: #071f26;
    border: 1px solid #587e8d;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: #deebff;
    font-family: arial;
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`;

/* Fixed title bar; stays put while the Body scrolls beneath it. */
const Header = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: #0a3340;
    border-bottom: 1px solid #587e8d;
`;

const HeaderTitle = styled.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`;

const CloseButton = styled.div`
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    line-height: 1;
    color: #7ba2ea;
    border: 1px solid transparent;
    border-radius: 4px;
    transition: color 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
    ${Clickable}

    &:hover {
        color: #d6f7fd;
        border-color: #49c4d4;
        background-color: rgba(73, 196, 212, 0.12);
    }
`;

/* Scrollable content region. */
const Body = styled.div`
    overflow-y: auto;
    overflow-x: hidden; /* rows are box-sized to fit; never scroll sideways */
    padding: 4px 0 12px;

    &::-webkit-scrollbar {
        width: 10px;
    }
    &::-webkit-scrollbar-track {
        background: #0d1620;
    }
    &::-webkit-scrollbar-thumb {
        background: #3c5574;
        border-radius: 6px;
    }
    &::-webkit-scrollbar-thumb:hover {
        background: #5a7ea8;
    }
`;

const Intro = styled.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`;

/* Section divider: small uppercase cyan label with a hairline rule trailing off
   to the right, echoing the replay UI accent colour. */
const SectionLabel = styled.div`
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 14px 14px 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #49c4d4;

    &::after {
        content: "";
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, rgba(73, 196, 212, 0.4), transparent);
    }

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        margin: 10px 10px 2px;
    }
`;

const Disclaimer = styled.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`;

const keyCodes = {
    32: "space",
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