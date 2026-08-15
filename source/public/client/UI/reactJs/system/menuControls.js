import styled from 'styled-components';
import theme from '../styled/theme';

/* Shared ticker controls for the system menus (WEAPON_ENHANCEMENTS_PLAN.md §6.1).
 *
 * These lived in ApplyDamageMenu.js until SystemEnhancementsSection needed the same
 * [-] [ n ] [+] row in gold. They were lifted here rather than forked, because a gold
 * variant is one prop - and two copies of a ticker are two places to fix the next time
 * one of them is nudged.
 *
 * $gold selects the enhancement palette (theme.colors.enh*) over the blue chrome. It is a
 * TRANSIENT prop ($-prefixed) so styled-components does not forward it to the DOM.
 */

/* ⚠️ Every consumer of these sits inside SystemInfoMenu's absolutely-positioned tooltip,
   which is SHRINK-TO-FIT capped at 500px - so a max-content width anywhere in here feeds
   straight back into how wide the menu draws. Keep the fixed flex bases below fixed. */

export const MenuRow = styled.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${props => props.$gold ? theme.colors.enhText : theme.colors.chromeText};
`;

export const MenuRowLabel = styled.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`;

/* A long label ("Advanced Defensive Targeting", "Antimatter Shredder") must not push the
   ticker off the menu, so it ellipsises rather than widening the container. Consumers pair
   it with a `title` so the full text is still readable on hover. */
export const MenuLabelText = styled.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;

export const MenuHint = styled.span`
    flex: 0 0 auto;
    color: ${props => props.$gold ? theme.colors.enhText : theme.colors.textDim};
    font-size: 10px;
    opacity: ${props => props.$gold ? 0.75 : 1};
`;

export const ActionButton = styled.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: ${props => props.$gold ? theme.colors.enhBg : '#203348'};
    border: 1px solid ${props => props.$gold ? theme.colors.enhLine : '#496791'};
    color: ${props => props.$gold ? theme.colors.enhTitle : '#deebff'};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${props => props.$gold ? theme.colors.enhLine : '#496791'};
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${props.$gold ? theme.colors.enhBg : '#203348'};
            color: ${props.$gold ? theme.colors.enhTitle : '#deebff'};
        }
    `}
`;

export const ValueInput = styled.input`
    flex: 0 0 44px;
    width: 44px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${theme.fonts.mono};
    font-size: 12px;
    color: ${props => props.$destroyed ? '#ff8a80' : (props.$gold ? theme.colors.enhTitle : '#ffffff')};
    background-color: #101a26;
    border: 1px solid ${props => props.$gold ? theme.colors.enhLine : '#496791'};
    outline: none;

    &:focus {
        border-color: ${props => props.$gold ? theme.colors.enhTitle : '#6089c1'};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`;

/* The gold twin of CriticalEffectsSection's SectionHeader. Same geometry, enhancement
   palette, plus the ✦ that marks the whole feature. */
export const EnhSectionHeader = styled.div`
    padding: 3px;
    background-color: ${theme.colors.enhBg};
    border-top: 1px solid ${theme.colors.enhLine};
    border-bottom: 1px solid ${theme.colors.enhLine};
    color: ${theme.colors.enhTitle};
    text-align: center;
    font-size: 10px;
    letter-spacing: 0.5px;
    user-select: none;
`;

/* The hard visual break between the gold enhancement half and the blue damage half. Two
   editors share one menu and they must not read as one list (§6.1). */
export const SectionDivider = styled.div`
    height: 2px;
    background-color: ${theme.colors.enhLine};
    opacity: 0.8;
`;
