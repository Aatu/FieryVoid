//Design tokens for the React UI ("digital SCS" redesign, SHIPWINDOW_REDESIGN_PLAN.md
//Stage 1d). Single source for the established game palette - the seed for roadmap
//item 6 (visual unification of the remaining jQuery windows). Import values from here
//rather than hard-coding hex colours in components.
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
        enhText: "#d8be86",                     //Enhancements list body text - gold, matches the block's bronze border/title
        custom: "#cccc00",    
    },
    fonts: {
        body: "arial",
        mono: 'Consolas, "Lucida Console", monospace', //numeric readouts - SCS datasheet feel
    },
};

export default theme;
