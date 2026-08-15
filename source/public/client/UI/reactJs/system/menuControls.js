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

/* ⭐ THE FRAME the section bars sit inside - title bar, body fill and border, shared by all
 * three lobby damage editors (ApplyDamageMenu, FighterDamageMenu, MineDamageMenu). They were
 * three copies of the same four literals; they are one now, because the frame's whole job is
 * to be the one thing in the menu that is NOT a section.
 *
 * ⚠️ The title used to be #215a7a, and the section bars used to be much darker. Once those
 * were brightened the title was within 12° of hue and a hair of luminance of the Damage bar
 * (both ~202°) and read as a fourth section rather than as the window's name (user report,
 * 2026-08-15). It now sits at ~214°, which is:
 *   - the exact HUE MIDPOINT of its two blue children (Damage 202°, Criticals 225°), so it
 *     parents both rather than duplicating either;
 *   - low chroma beside them - the sections are the saturated things, the frame is not;
 *   - brighter than all three bars, so the eye still lands on the title first;
 *   - and roughly the COMPLEMENT of the bronze bar, which is what makes the gold pop.
 * It is also the family theme.colors.line already belongs to (#496791 is 215°), so the
 * border and the title bar are siblings for free.
 *
 * ⚠️ NO element `opacity` here - the translucency is entirely in the fill's alpha. The three
 * menus each carried BOTH (0.95/0.97 element opacity over a 0.90/0.95 fill), which compounds
 * and, worse, fades the TEXT; ApplyDamageMenu's header even carried an `opacity: 1 !important`
 * to fight it, which cannot work - a child cannot opt out of an ancestor's group opacity.
 * See the note on theme.colors.overlayBgSoft, which says exactly this. */
const MENU_HEADER_BG = '#3f5a7d';

export const MENU_CHROME = {
    bg: 'rgba(13, 20, 30, 0.94)',   //body fill. Alpha only - never pair with element opacity
    line: theme.colors.line,        //frame + the rule under the title
};

/* The window's name. $sticky for the two menus whose body scrolls (max-height + overflow),
   so the title stays put while the fighter/mine rows run past underneath. */
export const MenuHeader = styled.div`
    padding: 3px;
    background-color: ${MENU_HEADER_BG};
    border-bottom: 1px solid ${MENU_CHROME.line};
    color: ${theme.colors.chromeText};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    ${props => props.$sticky ? 'position: sticky; top: 0;' : ''}
`;

/* ⭐ THE THREE SECTION BARS, in one place so they cannot drift.
 *
 * "Add Enhancements & Damage" stacks three editors in one shrink-to-fit tooltip, and the
 * player needs to know at a glance which one they are typing into. One bar per section,
 * identical geometry, three tints:
 *
 *   ✦ ENHANCEMENTS    bronze   - "this was BOUGHT; it is not standard equipment"
 *   Damage            teal     - the hull. The menu's own chrome blue
 *   Critical Effects  indigo   - what the hull is SUFFERING
 *
 * The two blue bars are a deliberately SMALL step apart (user request 2026-08-15): they
 * share their red and blue channels and differ almost entirely in green, so side by side
 * they read as two sections and never as two different windows. The gold bar is the one
 * that is meant to jump out - it is the only section that costs points.
 *
 * ⚠️ Kept as literals here rather than promoted into styled/theme.js. The GOLD set is in
 * the theme because two files paint gold surfaces and copied literals are how they drift
 * ([[project_visual_unification]]); these three are painted here and nowhere else, and a
 * token nothing else reads is just indirection. If a fourth section bar ever appears in
 * another file, move all three at once.
 */
const SectionBar = styled.div`
    padding: 3px;
    text-align: center;
    font-size: 10px;
    letter-spacing: 0.5px;
    user-select: none;
`;

/* Bought equipment. Bronze, and the ✦ that marks the whole feature. */
export const EnhSectionHeader = styled(SectionBar)`
    background-color: ${theme.colors.enhBg};
    border-top: 1px solid ${theme.colors.enhLine};
    border-bottom: 1px solid ${theme.colors.enhLine};
    color: ${theme.colors.enhTitle};
`;

/* Structure. The teal-slate this menu has always worn, lifted a stop (round 1b) - at
   #1b3b50 the bar sank into the menu body and read as a caption rather than a section
   head. Still deliberately below the menu's own #215a7a title bar, which stays the
   brightest thing in the window. */
export const DamageSectionHeader = styled(SectionBar)`
    background-color: #23506b;
    border-top: 1px solid #3d7a9c;
    border-bottom: 1px solid #3d7a9c;
    color: #e8f2ff;
`;

/* Malfunctions. Indigo: the same R and B as the damage bar with the green pulled out, so
   it is unmistakably the same kind of bar one step round the wheel - and nowhere near the
   bronze above it. The text is nudged a shade cooler for the same reason the fill is: two
   signals of one small difference read more clearly than one large one. */
export const CritSectionHeader = styled(SectionBar)`
    background-color: #23386b;
    border-top: 1px solid #4c5da8;
    border-bottom: 1px solid #4c5da8;
    color: #dfe5ff;
`;

/* The hard visual break between two editors sharing one menu - they must not read as one
   list (§6.1). Three editors means two breaks, so there are two tones:

     default   bronze, the boundary of the section that COSTS POINTS
     $chrome   the standard chrome line, for a break between two peer sections

   Each sits directly above the next section's own top hairline, which is what gives the
   break its weight - a 2px rule alone would read as one more hairline among three. */
export const SectionDivider = styled.div`
    height: 2px;
    background-color: ${props => props.$chrome ? theme.colors.line : theme.colors.enhLine};
    opacity: 0.8;
`;
