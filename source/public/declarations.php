<?php
/*
window showing current declarations (fire/EW)
*/
?>

<link href="<?php echo AssetLoader::getAssetUrl('styles/chat.css'); ?>" rel="stylesheet" type="text/css">

<!-- DECLARATIONS (LOG_PANEL_REDESIGN_PLAN.md Stage 4).

     Same information, same reads out of gamedata - restructured. Three segmented chip
     groups on ONE row, in the combat log's grammar, replacing four rows of <input
     type="button"> and their <br>s.

     BRIEFING IS ITS OWN CONTROL, on the far right, and it is still a MODE rather than the
     old fire-and-forget button. It used to be a "Show description" button that overwrote
     #declarationsActual while the other controls carried on claiming the panel showed,
     say, "Own EW by Source" - the chips lied about what was on screen. It now reports its
     own state through aria-pressed and declarations.js dims SIDE, SHOW and BY while it is
     on, because none of the three means anything for a briefing. It sits apart from the
     three filter groups because it is not a filter: it replaces the whole panel. -->
<div id="declarationsContainer" class="chatcontainer">

    <div id="declarationsButtons" class="fv-log-bar">
        <span class="fv-log-group" id="declSide">
            <span class="fv-log-label">Side</span>
            <span class="fv-log-seg" role="group" aria-label="Side">
                <button type="button" class="fv-log-chip" data-side="Own" aria-pressed="true">Own</button>
                <button type="button" class="fv-log-chip" data-side="Enemy" aria-pressed="false">Enemy</button>
            </span>
        </span>

        <span class="fv-log-group" id="declContent">
            <span class="fv-log-label">Show</span>
            <span class="fv-log-seg" role="group" aria-label="Content">
                <button type="button" class="fv-log-chip" data-content="EW" aria-pressed="true">EW</button>
                <button type="button" class="fv-log-chip" data-content="Fire" aria-pressed="false">Fire</button>
            </span>
        </span>

        <span class="fv-log-group" id="declDisplay">
            <span class="fv-log-label">By</span>
            <span class="fv-log-seg" role="group" aria-label="Grouped by">
                <button type="button" class="fv-log-chip" data-display="Source" aria-pressed="true">Source</button>
                <button type="button" class="fv-log-chip" data-display="Target" aria-pressed="false">Target</button>
            </span>
        </span>

        <!-- THE READOUT SITS WITH THE FILTERS, NOT WITH BRIEFING (user, 2026-08-31).
             It restates what the three groups to its left add up to ("OWN EW BY SOURCE"),
             so it belongs at the end of that run - beside the Source/Target control that
             finishes the sentence. Parked after the spacer it read as a caption on the
             Briefing button, which is the one control on this bar it says nothing about.
             The spacer moves below it, so Briefing stays pinned to the right-hand end. -->
        <span class="fv-log-bar-meta" data-log-meta="declarations"></span>
        <span class="fv-log-bar-spacer"></span>
        <button type="button" id="declBriefing" class="fv-log-chip fv-log-chip--toggle" aria-pressed="false"
                title="Game name, rules of engagement and the scenario briefing">Briefing</button>
    </div>

    <div id="declarationsActual" class=""> <!-- actual declarations tab, filled on button press -->
    </div>

</div>
