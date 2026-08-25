import styled from 'styled-components';
import theme from '../styled/theme';

/* The shared chassis for the small LABELLED ON/OFF menus that hang off a system icon.
 *
 * These started life inside PowerCapacitor.js as that one menu's private styling. They were lifted
 * here when the Jump Engine's Maintain control (JUMP_POINTS_PLAN.md Stage 5) needed the same
 * thing: a titled panel of "<what it does>   [OFF] [ON]" rows. Two copies would have been two
 * places to fix the next time one of them is nudged, and these two menus are deliberately meant to
 * read as the same control.
 *
 * ⚠️ NOT the same chassis as menuControls.js's MENU_CHROME. That one is the drained, near-neutral
 * frame the three lobby damage editors use; this is the older saturated #215a7a header the
 * activation menus have always had. They are different families on purpose - don't merge them
 * without a look at both on screen.
 *
 * $active paints the side of the pair that is currently in force: amber for OFF, green for ON
 * ($variant="activate"), so a glance at the row says which way the switch is thrown.
 */

export const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${theme.colors.line};
`;

export const Header = styled.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${theme.colors.line};
    border-bottom: 1px solid ${theme.colors.line};
    color: ${theme.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`;

export const Row = styled.div`
    display: flex;
    align-items: center;
    padding: 3px 8px;
    border-bottom: 1px solid #496791;
    font-size: 11px;
    color: #deebff;
    justify-content: space-between;

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background-color: rgba(73, 103, 145, 0.4);
    }
`;

export const Label = styled.div`
    flex: 1;
`;

export const Controls = styled.div`
    display: flex;
    align-items: center;
    gap: 5px;
`;

/* A note under a row, for saying WHY a control is doing something. Nothing in PowerCapacitor uses
   it; the Maintain row does, because "this shuts your whole ship down" is not something a player
   should have to discover by clicking. */
export const Note = styled.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${theme.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`;

export const ActionButton = styled.div`
    width: 24px;
    height: 18px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #496791;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${props => props.$active && props.$variant !== 'activate' && `
        background: #806c00;
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;
    `}

    ${props => props.$active && props.$variant === 'activate' && `
        background: #1b5e20;
        color: white;
        border: 1px solid #4caf50;
        opacity: 1;

        &:hover {
            background: #2e7d32;
            border: 1px solid #66bb6a;
            color: #ffffff;
            opacity: 1;
        }
    `}
`;
