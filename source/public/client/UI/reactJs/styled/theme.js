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
        warning: "#e1b000",                     //amber status (e.g. ROLLED banner)
        statusOk: "limegreen",                  //green status banners - matches the map tooltip (Undetected / Attached)
        statusAlert: "#e1b000",                  //orange alert banners - matches the map tooltip (boarding)
        statusBad: "red",                       //red status banners - matches the map tooltip (Detected)
        enhText: "#d8be86",                     //Enhancements list body text - gold, matches the block's bronze border/title
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
    },
    fonts: {
        body: "arial",
        mono: 'Consolas, "Lucida Console", monospace', //numeric readouts - SCS datasheet feel
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
    hud: {
        btn: "36px",                            //button box, desktop
        btnSmall: "25px",                       //button box, phones + short landscape
        icon: "26px",                           //inline SVG inside the box (~58%)
        iconSmall: "17px",
        glyph: "32px",                          //text glyph inside the box (~71% - see above)
        glyphSmall: "21px",
    },
};

export default theme;
