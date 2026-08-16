import styled from 'styled-components';
import theme from '../styled/theme';

/* Shared ticker controls for the system menus (WEAPON_ENHANCEMENTS_PLAN.md §6.1).
 *
 * These lived in ApplyDamageMenu.js until SystemEnhancementsSection needed the same
 * [-] [ n ] [+] row. They were lifted here rather than forked, because two copies of a
 * ticker are two places to fix the next time one of them is nudged.
 *
 * ⚠️ THERE IS NO $gold TICKER ANY MORE (2026-08-16). ActionButton and ValueInput carried a
 * bronze variant so the enhancement rows would not have blue buttons sitting in them - but
 * the reason blue buttons fought those rows was that the chrome was a SATURATED blue. Once
 * the chassis was drained to near-neutral (see MENU_CHROME below) a chassis ticker sits
 * inside a bronze row without arguing with it, and the variant was the last thing keeping
 * those rows half-gold: bronze buttons either side of a cold navy number well.
 * $gold survives on MenuRow and MenuHint, which tint TEXT. The rule the menu now follows is
 * that section ink paints bars, rails and body text, and never a control.
 */

/* ⚠️ Every consumer of these sits inside SystemInfoMenu's absolutely-positioned tooltip,
   which is SHRINK-TO-FIT capped at 500px - so a max-content width anywhere in here feeds
   straight back into how wide the menu draws. Keep the fixed flex bases below fixed. */

/* ⭐ THE CHASSIS - frame, body fill, title bar, every ticker, every number well, every dim
 * label. Shared by all three lobby damage editors (ApplyDamageMenu, FighterDamageMenu,
 * MineDamageMenu). Defined here at the top because the controls below read it.
 *
 * ⚠️ WHY IT IS NOT BLUE ANY MORE (user request 2026-08-16). The chrome used to be
 * theme.colors.line #496791, which is H215 S33 - and the Damage section bar is #23506b,
 * H202 S51. Thirteen degrees apart. Two of the menu's four surfaces were the same colour at
 * two strengths, so the window read as "a blue menu" and the Damage section had no identity
 * of its own; it was just the chassis, slightly greener. That was the unintended cost of the
 * 2026-08-15 fix which moved the title bar onto the 202°/225° hue midpoint - correct in
 * itself, but it parked the frame right on top of a section's hue.
 *
 * The fix is NOT to re-hue any section. It is to drain the chrome and leave all three
 * section colours exactly as they are: frame and title fall from S33 to S21/S25, the tickers
 * from S38 to S23. Nothing else moves, and the sections get louder by comparison without
 * being touched. The rule this encodes: ONLY THE SECTIONS ARE ALLOWED TO BE SATURATED.
 *
 * ⚠️ Deliberately NOT a retune of theme.colors.line / chromeText, which have ~100 usages
 * across the React UI - that is a token-level change with a whole-app blast radius and wants
 * its own pass. These three menus therefore sit a step apart from the rest of the chrome on
 * purpose. If the neutral reads better everywhere, promote it; do not copy the literals.
 *
 * ⚠️ NO element `opacity` anywhere that reads these - the translucency is entirely in bg's
 * alpha. See the note on theme.colors.overlayBgSoft: element opacity fades TEXT as well, and
 * stacking the two compounds. */
export const MENU_CHROME = {
    bg: 'rgba(8, 12, 16, 0.96)',    //body fill. Alpha only - never pair with element opacity
    line: '#33414f',                //frame, section-break rules, control borders
    titleBg: '#1b242e',             //MenuHeader fill (the fighter and mine menus; see below)
    text: '#c7d3de',                //body text - the chassis answer to theme.colors.chromeText
    dim: '#6c7a87',                 //secondary labels - the chassis answer to theme.colors.textDim
    btnBg: '#161d25',               //ticker fill
    btnText: '#aebac6',             //ticker glyph, a shade under `text` so ± never outshouts a label
    well: '#05080b',                //number-input fill
    focus: '#4d6070',               //focused input border
};

export const MenuRow = styled.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${props => props.$gold ? theme.colors.enhText : MENU_CHROME.text};
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
    color: ${props => props.$gold ? theme.colors.enhText : MENU_CHROME.dim};
    font-size: 10px;
    opacity: ${props => props.$gold ? 0.75 : 1};
`;

/* THE one ticker button in these menus. It used to have a twin - CriticalEffectsSection's
   own TickerButton, 18x16, identical colours, differing only in being smaller for no reason
   anyone could name. Folded in here at 24x18: one component, and bigger touch targets on the
   phones the lobby damage menus actually get used on. The crit rows lose 12px of label width
   to it, which they were already wrapping inside. */
export const ActionButton = styled.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: ${MENU_CHROME.btnBg};
    border: 1px solid ${MENU_CHROME.line};
    color: ${MENU_CHROME.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${MENU_CHROME.line};
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${MENU_CHROME.btnBg};
            color: ${MENU_CHROME.btnText};
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
    /*White in every section, gold included: the NUMBERS are chassis, so they read the same
      whether the row above them is bronze, teal or rust. Red is reserved for one state.*/
    color: ${props => props.$destroyed ? '#ff8a80' : '#ffffff'};
    background-color: ${MENU_CHROME.well};
    border: 1px solid ${MENU_CHROME.line};
    outline: none;

    &:focus {
        border-color: ${MENU_CHROME.focus};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`;

/* The window's name. $sticky for the two menus whose body scrolls (max-height + overflow),
   so the title stays put while the fighter/mine rows run past underneath.

   ⚠️ ApplyDamageMenu NO LONGER RENDERS ONE (user request 2026-08-16). Its title was only ever
   its own section names joined by an ampersand - "Apply Damage & Critical Effects" over a
   Damage bar and a Critical Effects bar - so once the three bars were doing the labelling the
   title was pure restatement. FighterDamageMenu and MineDamageMenu keep theirs: they have ONE
   section each, so the title is the only thing naming the window.
   That is also why SectionBar drops its top hairline when it is first (see below): with no
   title above it, the topmost bar butts straight onto the container's own border. */
export const MenuHeader = styled.div`
    padding: 3px;
    background-color: ${MENU_CHROME.titleBg};
    border-bottom: 1px solid ${MENU_CHROME.line};
    color: ${MENU_CHROME.text};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    ${props => props.$sticky ? 'position: sticky; top: 0;' : ''}
`;

/* ⭐ THE THREE SECTION BARS, in one place so they cannot drift.
 *
 * The menu stacks three editors in one shrink-to-fit tooltip, and the player needs to know at
 * a glance which one they are typing into. One bar per section, identical geometry, three
 * inks:
 *
 *   ✦ ENHANCEMENTS    bronze   - "this was BOUGHT; it is not standard equipment"
 *   Damage            teal     - the hull itself
 *   Critical Effects  rust     - what the hull is SUFFERING
 *
 * ⚠️ Criticals used to be INDIGO, which was the fault the bronze bar was being blamed for.
 * Orange is the game's critical colour everywhere else - SystemIcon and the structure bar
 * both go theme.colors.healthCrit #ed6738 the moment a system carries one - and the crit rows
 * in this menu have always been warm. So the bar was naming one thing and its body another.
 * It is rust now: the same lightness and chroma as the Damage bar, mirrored across the wheel,
 * so the two are unmistakably one geometry in two inks (Damage 202°, Criticals 14°).
 *
 * Two warm sections is fine, and the layout is what makes it fine: Damage sits between bronze
 * and rust, and in the commonest case (no enhancements offered) the bronze bar is not drawn at
 * all. They are also different KINDS of warm - bronze is dull and metallic at S40, rust is hot
 * at S52. Bought equipment versus a burning system.
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

    /*ApplyDamageMenu has no title bar any more, so whichever bar comes first butts straight
      onto the container's own 1px border - two hairlines in two colours, which reads as a
      rendering fault rather than as a frame. Self-maintaining: it is always whichever section
      happens to be on top, and Enhancements is absent more often than not.*/
    &:first-child {
        border-top: none;
    }
`;

/* ⭐ THE RAIL. A 2px stripe of the section's own ink down the left of its ROWS, so the
 * section colour does not stop dead at the bar.
 *
 * Before this, each section was identified by a 14px stripe and nothing else - every row
 * underneath was chassis-coloured - so the menu read as one panel with three stripes laid on
 * top, and each bar had to shout to do its job. Carrying the ink down the body means identity
 * comes from how far it REACHES rather than from how loud the bar is, which is what lets the
 * bars stay exactly as tuned while the chrome around them goes quiet.
 *
 * ⚠️ box-shadow, not border-left, and not a tinted background:
 *   - a border would shift every row 2px right and mean compensating padding in five
 *     different components; an inset shadow paints over padding the rows already have.
 *   - a background wash would carry its own alpha on top of MENU_CHROME.bg's 0.96, and
 *     alpha compounding is the trap theme.colors.overlayBgSoft exists to warn about.
 */
export const SECTION_INK = {
    enh: theme.colors.enhLine,   //#8a6d3b - bronze, the shared gold set
    damage: '#3d7a9c',           //the Damage bar's own hairline
    crit: '#a85c33',             //the Critical Effects bar's own hairline
};

export const SectionBody = styled.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 2px 0 0 ${props => props.$ink};
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
   head. Untouched by the 2026-08-16 pass: it now reads as a section rather than as a
   slightly greener chassis purely because the chassis stopped being blue. */
export const DamageSectionHeader = styled(SectionBar)`
    background-color: #23506b;
    border-top: 1px solid ${SECTION_INK.damage};
    border-bottom: 1px solid ${SECTION_INK.damage};
    color: #e8f2ff;
`;

/* Malfunctions. Rust - the Damage bar's own L28/S51 taken across the wheel to 14°, so the
   two bars are one geometry in two inks. It is the dark end of the game's critical orange
   (theme.colors.healthCrit #ed6738), which is what the crit rows beneath it are already
   painted in, so the bar and its body finally name the same thing.
   The title goes warm-white for the same reason the fill does: two signals of one difference
   read more clearly than one. */
export const CritSectionHeader = styled(SectionBar)`
    background-color: #6d3823;
    border-top: 1px solid ${SECTION_INK.crit};
    border-bottom: 1px solid ${SECTION_INK.crit};
    color: #ffece2;
`;

/* The hard visual break between two editors sharing one menu - they must not read as one
   list (§6.1). Three editors means two breaks, so there are two tones:

     default   bronze, the boundary of the section that COSTS POINTS
     $chrome   the standard chrome line, for a break between two peer sections

   Each sits directly above the next section's own top hairline, which is what gives the
   break its weight - a 2px rule alone would read as one more hairline among three. */
export const SectionDivider = styled.div`
    height: 2px;
    background-color: ${props => props.$chrome ? MENU_CHROME.line : theme.colors.enhLine};
    opacity: 0.8;
`;
