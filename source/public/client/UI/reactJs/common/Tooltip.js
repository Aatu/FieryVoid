import * as React from "react";
import styled from 'styled-components';
import theme from '../styled/theme';



class Component extends React.Component {
    render() {
        const { children, className } = this.props
        return (
            <div className={className}>
                {children}
            </div>
        )
    }
}

/* The hover tooltip for systems, weapons and thrusters.

   Its twin is .shipNameContainer in tactical.css - the jQuery map tooltip - which
   declares the identical design a second time: same 65%-black fill, same 7px radius,
   same arial 12px, same z-index 7001. Two stacks, one look, and until roadmap item 6
   Stage 3 they agreed only by luck. Both now read the same tokens (theme.radii.tooltip
   here, var(--fv-radius-tooltip) there), so a retune of one is a retune of both. If you
   change anything here, change it there.

   ⚠️ The translucency is in the FILL (overlayBgSoft), not in element `opacity`. Opacity
   fades the TEXT along with the panel; the jQuery twin's ship names were coming out
   visibly duller than the identical allegiance tokens in the hex picker beside them, and
   this surface was changed with it to keep the two in step. The panel is exactly as
   see-through as before. Do not reintroduce `opacity` - it compounds with the alpha. */
const Tooltip = styled(Component)`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${theme.fonts.body};
    font-size:12px;
    color:${theme.colors.text};
    background-color:${theme.colors.overlayBgSoft};
    border-radius: ${theme.radii.tooltip};
    -moz-border-radius: ${theme.radii.tooltip};
    -webkit-border-radius: ${theme.radii.tooltip};
    padding:3px 3px 3px 3px;
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
        if (props.$type == 'good') {
            return '#6fc126;';
        } else if (props.$type == 'bad') {
            return '#ff7b3f;';
        } else {
            return 'white;'
        }
    }}
    font-weight: ${props => props.$important ? 'bold' : 'inherit'};
    font-size: ${props => props.$important ? '14px' : '12px'};
    margin-top: ${props => props.$space ? '14px' : '0'};
`

export { Tooltip, TooltipHeader, TooltipEntry }