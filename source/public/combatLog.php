<?php

?>

<link href="<?php echo AssetLoader::getAssetUrl('styles/chat.css'); ?>" rel="stylesheet" type="text/css">

<!-- THE COMBAT LOG (LOG_PANEL_REDESIGN_PLAN.md Stage 2).

     THREE ROWS IN A FLEX COLUMN, which is the fix for the reported overlap bug: the
     control bar used to be position:absolute inside the scrolling container, so log
     text flowed underneath it AND it scrolled out of reach on a long turn. It is now a
     sticky, opaque row of its own and the two bodies below it are what scroll.

     TWO BODIES, not one. #logLive holds the replay stream (entries appended live by
     combatLog.logFireOrders / logDestroyedShip / logMoves); #LogActual holds the printed
     turn log. They used to be an overflow:visible box with a second absolutely-positioned
     scroller sitting on top of it, which is why $("#log").scrollTop() was a no-op and live
     entries spilled out of the 150px panel instead of scrolling inside it.
     #logLive:empty is display:none, so whichever one has content gets the whole body -
     keep its markup free of whitespace or :empty stops matching.

     ONE ROW IS A HARD CONSTRAINT for the bar (user, 2026-08-31): at the default panel
     height there are only ~120px of body left underneath it, so a second row of controls
     would cost a fifth of the log. The group labels (SORT / SHOW) are gone with it - the
     controls name themselves. -->
<div id="combatLogContainer" class="chatcontainer">

    <div id="combatLogButtons" class="fv-log-bar">
        <!-- Turn stepper. The three cells share one line-height and collapse their
             borders into a single control (margin-left:-1px) - sized only by padding
             they came out at different heights and read as overlapping. -->
        <span class="fv-log-stepper">
            <button type="button" id="previousTurnButton" class="fv-log-step" title="Previous turn"
                    onclick="window.combatLog.showPrevious();">&#9664;</button>
            <span class="fv-log-step-value" id="combatLogTurnLabel">TURN &ndash;</span>
            <button type="button" id="nextTurnButton" class="fv-log-step" title="Next turn"
                    onclick="window.combatLog.showNext();">&#9654;</button>
        </span>
        <button type="button" id="currentTurnButton" class="fv-log-chip" title="Back to the current turn"
                onclick="window.combatLog.showCurrent();" style="display:none;">Live</button>

        <!-- TWO CONTROLS, ONE STATE (user, 2026-08-31). Chips read better than a select
             wherever there is room for them and match the rest of the bar; the select is
             kept for narrow viewports, where three more chips would push the bar into a
             sideways scroll. Only one is ever on screen - styles/logPanel.css swaps them
             at the same breakpoints the tab strip uses - and combatLog.syncControls paints
             both from combatLog.sortMode, so whichever is visible is always right.
             DAMAGE was cut (user, 2026-08-31), which is what lets combatLog.sortGroups
             stay a pure name comparison with no damage index behind it. -->
        <span class="fv-log-seg fv-log-wide-only" id="combatLogSortSeg" role="group" aria-label="Sort fire groups">
            <button type="button" class="fv-log-chip" data-sort="resolution" aria-pressed="true">Resolution</button>
            <button type="button" class="fv-log-chip" data-sort="attacker" aria-pressed="false">Attacker</button>
            <button type="button" class="fv-log-chip" data-sort="target" aria-pressed="false">Target</button>
        </span>
        <select id="combatLogSort" class="fv-log-select fv-log-narrow-only" title="Sort fire groups">
            <option value="resolution">Resolution</option>
            <option value="attacker">Attacker</option>
            <option value="target">Target</option>
        </select>

        <span class="fv-log-seg" id="combatLogSide" role="group" aria-label="Whose fire">
            <button type="button" class="fv-log-chip" data-side="all" aria-pressed="true">All</button>
            <button type="button" class="fv-log-chip" data-side="mine" aria-pressed="false">Mine</button>
            <button type="button" class="fv-log-chip" data-side="enemy" aria-pressed="false">Enemy</button>
        </span>

        <button type="button" id="combatLogHitsOnly" class="fv-log-chip" aria-pressed="false"
                title="Only show fire that scored at least one hit">Hits</button>

        <span class="fv-log-bar-spacer"></span>
        <span class="fv-log-bar-meta" data-log-meta="log"></span>
        <input type="search" id="combatLogFind" class="fv-log-find" placeholder="Find" autocomplete="off"
               title="Filter by ship or weapon name">
    </div>

    <!-- The LIVE replay stream. Deliberately written with no whitespace inside. -->
    <div id="logLive"></div>

    <div id="LogActual" class="LogActual" style="display:none;"> <!-- actual Combat Log, filled on button press -->
        <!-- the printed combat log will go here -->
    </div>
</div>
