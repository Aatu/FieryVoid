//Design tokens for the React UI ("digital SCS" redesign, SHIPWINDOW_REDESIGN_PLAN.md
//Stage 1d). Single source for the established game palette - the seed for roadmap
//item 6 (visual unification of the remaining jQuery windows). Import values from here
//rather than hard-coding hex colours in components.
//
//This is the styled-components half of the palette. The CSS half is
//styles/tokens.css, linked first on every page. They are two files on purpose - a
//build step generating one from the other is not worth a new pipeline dependency for
//~30 values - and are kept honest by cross-reference comments on both sides. Retune a
//value that names an --fv-* token here and you must retune it there too.
const theme = {
    colors: {
        windowBg: "#152029de",                    //window body / backdrop - dark + desaturated so the grayscale watermark pops
        panelBg: "#04161c",                     //darker panel + header fill
        panelBgGlass: "rgba(4, 22, 28, 0.22)",  //panel over the watermark - translucent so the hull art reads through
        line: "#496791",                        //standard border / divider
        linePrimary: "#6089c1",                 //primary-section emphasis border
        text: "#ffffff",
        textAccent: "#C6E2FF",                  //bluish-white value text
        textDim: "#7f9bb8",                     //secondary labels
        healthOk: "#427231",                    //structure bar fill, healthy
        healthCrit: "#ed6738",                  //structure bar fill when criticals present
        warning: "#e1b000",
        warningSoft: "#e6b400",                     //amber status (e.g. ROLLED banner)
        statusOk: "limegreen",                  //green status banners - matches the map tooltip (Undetected / Attached)
        statusAlert: "#e1b000",                  //orange alert banners - matches the map tooltip (boarding)
        statusBad: "red",                       //red status banners - matches the map tooltip (Detected)
        /* Cyan "not here yet / changes next turn" status. Same #00b8e6 the fleet list already
           uses for its "[Deploys on Turn N]" fleet header and FighterIcon uses for DOCKED, so a
           pending arrival reads the same wherever it is shown. */
        statusPending: "#00b8e6",
        /* The system tooltip card (system/SystemCard.js, user choice 2026-11-08) needed three values that
           tokens.css already had but this file did not - added as their twins, not as new colours. */
        card: "#0a1c26",                        //--fv-card   - the card's header band, a shade above panelBg
        rule: "#1e3a4a",                        //--fv-rule   - hairlines inside a panel (cells, footer)
        accent: "#8bcaf2",                      //--fv-accent - section labels
        /* THE GOLD SET - "this was bought, it is not standard equipment".
           enhText was already here; the other three were hex literals repeated in
           ShipNotesPanel.js (Block $gold and EnhTitle). They were extracted when the
           system-enhancement menu needed the same look (WEAPON_ENHANCEMENTS_PLAN.md §1), because
           a second file copying the literals is exactly how two gold surfaces drift apart -
           see [[project_visual_unification]]. Any new gold surface reads these four.
           ⚠️ enhBg is a FILL with its own alpha, never element opacity: element opacity would
           fade the text as well, and stacking the two compounds.
           SYNC CONTRACT: styles/tokens.css carries the CSS twins (--fv-enh-text / -title / -bg /
           -line), which the buy dialog's Enhancements head paints with. Retune a pair together. */
        enhText: "#d8be86",                     //Enhancements list body text
        enhTitle: "#e8cf93",                    //Enhancements section-bar title - a shade brighter
        enhBg: "rgba(169, 128, 56, 0.30)",      //Enhancements section-bar fill
        enhLine: "#8a6d3b",                     //Enhancements border / divider - bronze
        /* THE GREEN SET - the control-menu look the Gravitic Augmenter menu introduced, and the
           thrust panel (shipThrust/ShipThrust.js) took on by user request 2026-10-01. Extracted
           from GraviticAugmenterMenu.js's literals when the second surface needed it, for the same
           reason as the gold set above: two files copying hex values is how two surfaces drift.
           ⚠️ greenBg is a FILL with its own alpha. Never add element opacity on top of it - it
           would fade the text as well, and the two compound. */
        greenBg: "rgba(8, 28, 12, 0.92)",       //panel body
        greenLine: "#3f8a3f",                   //panel frame, title border, dividers
        greenTitleBg: "#16401b",                //title bar fill
        greenText: "#e6ffe6",                   //title and button text
        greenLabel: "#bdf0bd",                  //row labels, sub-labels
        greenBtnBg: "#1b5e20",                  //button fill
        greenBtnLine: "#2e7d32",                //button border; also the hover / active FILL
        greenBtnLineHover: "#66bb6a",           //button border on hover / active
        greenGlow: "#4caf50",                   //active button glow
        custom: "#cccc00",

        //HISTORICAL - the 2011 map-overlay skin. Stage 4 converged every surface that wore
        //it, so nothing paints with these two any more. They are kept rather than deleted
        //because they are the documented revert path: putting the old look back means
        //pointing styled/Container.js at chromeBg/chromeLine and theme.radii.pill back to
        //"30px", rather than re-deriving the values from git history. Mirrors --fv-chrome
        //and --fv-line-chrome in styles/tokens.css. Do not reach for them in new code.
        chromeBg: "#0a3340",
        chromeLine: "#587e8d",
        chromeText: "#deebff",                  //--fv-text - chrome body text. NOT legacy-only:
                                                //the converted surfaces kept this text colour, so it
                                                //is still the right value to reach for on chrome.
        overlayBg: "black",                     //--fv-overlay     - map tooltips + .confirm
        overlayBgSoft: "rgba(0, 0, 0, 0.65)",   //--fv-overlay-soft - the same, see-through as a FILL.
        //                                        Element opacity fades TEXT as well as panel; putting the
        //                                        translucency here keeps tooltip text at full strength.
        //                                        Never combine with element opacity (alpha compounding).
    },
    fonts: {
        body: "arial",
        mono: 'Consolas, "Lucida Console", monospace', //numeric readouts - SCS datasheet feel
        //the platform's own UI face (Segoe UI / San Francisco / Roboto) - the system tooltip card's text
        //(user choice 2026-11-08: crisper than Arial at 12-13px, and no download). No tokens.css twin.
        ui: 'system-ui, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        //--fv-display - the lobby's panel and section titles. Only pages that @import it
        //(gamesNew.css) have it: game.php does NOT, so reach for it only on lobby surfaces.
        display: '"Orbitron", sans-serif',
    },
    /* The system card's class chips (user request 2026-11-08). A WEAPON CLASS keeps one colour everywhere: the
       animation colour its family fires with - the family base class's animationColor, or the commonest one across
       the ship files where there is no base class. Two are nudged so they do not read as a neighbour (Gravitic, Psychic),
       and Ballistic, whose own colour is a near-black grey, takes gunmetal (--fv-gunmetal, the Ammo & Ordnance bar).
       A class not listed - and the black ones: Ramming, JumpPoint, Standard, Boarding - draws in `neutral`.
       DAMAGE TYPES have a set of their own, drawn as OUTLINES (SystemCard Chip) so the two kinds of chip never read as one.
       Signals, like the allegiance set: retune them as a set, never one alone. No tokens.css twins - nothing in CSS paints them. */
    chips: {
        neutral: "#8c9bab",
        weaponClass: {
            Laser: "#e60000",           //Laser (230, 0, 0)
            Particle: "#ff6600",        //Particle (255, 102, 0)
            Matter: "#fafabe",          //Matter (250, 250, 190)
            Plasma: "#4bfa5a",          //Plasma (75, 250, 90)
            Molecular: "#afe1af",       //Molecular (175, 225, 175)
            Antimatter: "#00b8e6",      //AntimatterWeapon (0, 184, 230)
            Gravitic: "#c8ff00",        //Gravitic (99, 255, 0), pushed toward chartreuse - it read as Plasma's green
            Ion: "#7f00ff",             //commonest (127, 0, 255)
            Electromagnetic: "#a5a5ff", //commonest (165, 165, 255)
            SWIon: "#5096fa",           //(80, 150, 250)
            Projectile: "#ff0b0b",      //(255, 11, 11)
            Psychic: "#b01e3c",         //(128, 0, 0), lifted toward crimson - the raw value read as a dim Laser
            Pulse: "#c0c0c0",           //(192, 192, 192)
            Support: "#63ff00",         //(99, 255, 0)
            Ballistic: "#8e9aa6",       //--fv-gunmetal
        },
        damageType: {
            Standard: "#deebff",
            Raking: "#e60000",
            Pulse: "#f5a524",
            Flash: "#ffe45c",
            Piercing: "#d17cff",
            Special: "#3fd0c9",
            Plasma: "#5fe07a",
            Matter: "#e8dca0",
        },
    },
    //Corner radii (roadmap item 6). THE tell that a surface belongs to the old world or
    //the new one: the SCS ship windows are square, the chrome around them used to be
    //rounded. Mirrors --fv-radius-* in styles/tokens.css.
    //
    //There is no `pill` entry any more. It existed to square the three ContainerRounded*
    //variants at once; those collapsed into a single ContainerShadowed (see Container.js),
    //which declares no border-radius at all because square is the default. A token pinned
    //at 0 with nothing reading it was just noise.
    radii: {
        modal: "0px",                           //--fv-radius-modal   - .confirm + PlayerSettingsForm (was 5px/8px)
        tooltip: "7px",                         //--fv-radius-tooltip - both map tooltips, deliberately still rounded
    },
    //Geometry for the map-edge HUD buttons: the EW strip, FullScreen and PlayerSettings.
    //
    //These used to be sized independently and had drifted - 45x45 / 50x45 / 26x28 boxes,
    //with content scaled by three different font-sizes - which read as sloppy where they
    //sit near each other, worst at phone sizes (user report 2026-07-30). One definition
    //now, so they cannot drift again.
    //
    //THE CONTENT SIZES ARE THE TUNING KNOBS. `icon` and `glyph` differ on purpose: an SVG
    //fills its viewBox almost completely, whereas a text glyph leaves leading inside its em
    //box, so the same number would render the gear noticeably smaller than the ⛶ icon. Both
    //are pitched to sit alongside the EW buttons, whose PNG art fills its tile edge to edge
    //(background-size: cover) - so if anything they err generous. Nudge these two values,
    //not the individual components.
    //
    //THESE TWO BOX SIZES ALSO SET THE HEIGHT OF THE WHOLE TOP BAR. #phaseheader and
    //#replayUI sit in the same row and are matched to these buttons through
    //--fv-hud-bar / --fv-hud-bar-small in styles/tokens.css (btn + the 1px bottom border
    //these keep, the top one being dropped for the flush-to-edge look). The three used to
    //be sized independently and came out 35 / 35 / 46. Change btn/btnSmall and you must
    //change those tokens - and --fv-hud-strip, which reserves the row's width so the
    //header cannot grow underneath these buttons.
    //
    //`gap` is the space between neighbouring buttons in the top-right row; every `right:`
    //offset in FullScreen / BackToLobby / Surrender is arithmetic on btn + gap. The small
    //gap is tighter than the desktop one because Back to Lobby made the row four buttons
    //wide, which is a squeeze beside the phase header on a portrait phone. Change either
    //gap and --fv-hud-strip / --fv-hud-strip-small must follow.
    hud: {
        btn: "36px",                            //button box, desktop
        btnSmall: "25px",                       //button box, phones + short landscape
        gap: "10px",                            //space between top-right row buttons, desktop
        gapSmall: "5px",                        //the same, phones + short landscape
        icon: "26px",                           //inline SVG inside the box (~58%)
        iconSmall: "17px",
        glyph: "30px",                          //text glyph inside the box (~71% - see above)
        glyphSmall: "20px",
    },
};

export default theme;
