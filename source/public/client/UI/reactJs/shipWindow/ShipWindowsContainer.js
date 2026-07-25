import * as React from "react";
import styled from "styled-components"
import ShipWindow from './ShipWindow'
import theme from "../styled/theme";

const ShipWindows = styled.div`

`

/*Per-window error boundary: a render error in ONE ship window must not unmount the
  whole React root (feedback round 4: a crashing bought-flight window also killed
  every window opened afterwards). The fallback is a minimal frame naming the unit
  with a working ✕, so the broken window can be closed and replaced; closing/
  reopening remounts the boundary with fresh state.*/
const FallbackFrame = styled.div`
    position: absolute;
    top: 50px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10001;
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 10px;
    border: 1px solid ${theme.colors.warning};
    background-color: ${theme.colors.windowBg};
    color: ${theme.colors.text};
    font-family: ${theme.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`;

const FallbackClose = styled.span`
    cursor: pointer;
    color: ${theme.colors.line};
    font-size: 16px;

    &:hover {
        color: ${theme.colors.text};
    }
`;

class ShipWindowErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { error: null };
    }

    static getDerivedStateFromError(error) {
        return { error };
    }

    componentDidCatch(error, info) {
        const ship = this.props.ship;
        console.error("Ship window render failed for", ship && (ship.name || ship.shipClass), error, info);
    }

    render() {
        if (this.state.error) {
            const ship = this.props.ship;
            return (
                <FallbackFrame>
                    <span>{(ship && (ship.name || ship.shipClass)) || "Ship"} — window failed to render (see console)</span>
                    <FallbackClose onClick={() => window.uiEvents.relay('CloseShipWindow', { ship })}>✕</FallbackClose>
                </FallbackFrame>
            );
        }
        return this.props.children;
    }
}

class ShipWindowsContainer extends React.Component{

    render() {
        const {ships} = this.props;

        return (<ShipWindows>
            {/*key includes userid: in the lobby a store blueprint (userid 0) and a
               fleet ship can be open side-by-side with the same numeric id*/}
            {ships.map(ship => (
                <ShipWindowErrorBoundary key={`shipwindow-${ship.userid}-${ship.id}`} ship={ship}>
                    <ShipWindow ship={ship}/>
                </ShipWindowErrorBoundary>
            ))}
        </ShipWindows>)
    }
}

export default ShipWindowsContainer;
