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

    <!-- `no-print` from the very first paint, because #LogActual ships with an inline
         display:none and so the filters have nothing to act on yet. Setting it here rather
         than waiting for the first updateTurnControls keeps them from flashing on at load
         the way the MINE chip used to. -->
    <div id="combatLogButtons" class="fv-log-bar no-print">
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
                onclick="window.combatLog.showCurrent();" style="display:none;">CURRENT</button>

        <!-- The bar reads as three runs now: WHICH TURN, HOW IT IS ORDERED, WHAT IS IN IT.
             A 6px flex gap alone did not separate them - every control is the same chip,
             so eight of them in a row read as one undifferentiated strip. A hairline costs
             5px and does the grouping the gap could not. #currentTurnButton is display:none
             on the live turn, so this simply falls in behind the stepper then. -->
        <span class="fv-log-divider fv-log-filter" aria-hidden="true"></span>

        <!-- TWO CONTROLS, ONE STATE (user, 2026-08-31). Chips read better than a select
             wherever there is room for them and match the rest of the bar; the select is
             kept for narrow viewports, where three more chips would push the bar into a
             sideways scroll. Only one is ever on screen - styles/logPanel.css swaps them
             at the same breakpoints the tab strip uses - and combatLog.syncControls paints
             both from combatLog.sortMode, so whichever is visible is always right.
             DAMAGE was cut (user, 2026-08-31), which is what lets combatLog.sortGroups
             stay a pure name comparison with no damage index behind it. -->
        <span class="fv-log-seg fv-log-wide-only fv-log-filter" id="combatLogSortSeg" role="group" aria-label="Sort fire groups">
            <button type="button" class="fv-log-chip" data-sort="resolution" aria-pressed="true">Resolution</button>
            <button type="button" class="fv-log-chip" data-sort="attacker" aria-pressed="false">Attacker</button>
            <button type="button" class="fv-log-chip" data-sort="target" aria-pressed="false">Target</button>
        </span>
        <select id="combatLogSort" class="fv-log-select fv-log-narrow-only fv-log-filter" title="Sort fire groups">
            <option value="resolution">Resolution</option>
            <option value="attacker">Attacker</option>
            <option value="target">Target</option>
        </select>

        <span class="fv-log-divider fv-log-filter" aria-hidden="true"></span>

        <!-- WHAT IS IN THE LOG: three peer toggles, in the same shape, doing the same kind
             of job. Any one of them narrows the turn; none of them reorders it.

             THESE REPLACED AN `All | Mine | Enemy` SEGMENT (user, 2026-08-31), and ENEMY
             THEN CAME BACK AS A TOGGLE OF ITS OWN (user, 2026-09-02). What was wrong with
             the segment was its SHAPE, not its third position: it cost ~118px of a bar that
             must not wrap and it did not read as the same kind of control as the Hits switch
             sitting next to it. Two plain toggles do the same work in the shape the bar
             already speaks.

             MINE AND ENEMY ARE STILL ONE FILTER, not two. They are the two ends of
             combatLog.sideFilter (all / mine / enemy) and are mutually exclusive by
             construction: pressing one releases the other, and pressing the lit one lands
             back on `all`. Nothing anywhere can hold "mine AND enemy" - which would be an
             empty log every time, since no group is fired by both sides.

             The COLOURS carry which is which, because two identical green chips could only
             be told apart by reading their labels: MINE lights green like every other lone
             switch on this bar, ENEMY lights the ladder gold (user, 2026-09-02). See
             .fv-log-chip--enemy in styles/logPanel.css. -->
        <button type="button" id="combatLogHitsOnly" class="fv-log-chip fv-log-chip--toggle fv-log-filter" aria-pressed="false"
                title="Only show fire that scored at least one hit">Hits</button>
        <button type="button" id="combatLogMineOnly" class="fv-log-chip fv-log-chip--toggle fv-log-filter" aria-pressed="false"
                title="Only show fire by my own side">Mine</button>
        <button type="button" id="combatLogEnemyOnly" class="fv-log-chip fv-log-chip--toggle fv-log-chip--enemy fv-log-filter" aria-pressed="false"
                title="Only show fire by the other side">Enemy</button>

        <span class="fv-log-bar-spacer"></span>
        <!--<span class="fv-log-bar-meta" data-log-meta="log"></span>-->
        <input type="search" id="combatLogFind" class="fv-log-find fv-log-filter" placeholder="Find" autocomplete="off"
               title="Filter by ship or weapon name">
    </div>

    <!-- The LIVE replay stream. Deliberately written with no whitespace inside. -->
    <div id="logLive"></div>

    <div id="LogActual" class="LogActual" style="display:none;"> <!-- actual Combat Log, filled on button press -->
        <!-- the printed combat log will go here -->
    </div>
</div>
