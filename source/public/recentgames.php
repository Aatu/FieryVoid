<?php
// Recent Games window markup, included by games.php next to ladder.php.
// Behaviour: client/recentGames.js. Data: recentgameslist.php.
// Same include-a-partial pattern as ladder.php / ladderstandings.php.
?>
<div class="fv-modal" id="rgModal" role="dialog" aria-modal="true" aria-labelledby="rgTitle">
  <div class="fv-modal-panel">
    <button class="fv-modal-close" type="button" id="rgClose" aria-label="Close recent games">&times;</button>
    <h2 class="fv-modal-title" id="rgTitle">Recent Games</h2>
    <p class="fv-modal-sub">Every game across Fiery Void with activity in the last 7 days.</p>

    <div class="fv-controls">
      <input class="fv-search" id="rgSearch" type="search" placeholder="Search games or players" aria-label="Search games or players">
      <div class="fv-chips" role="group" aria-label="Filters">
        <button class="fv-chip" type="button" data-filter="mine" aria-pressed="false">Mine</button>
        <button class="fv-chip" type="button" data-filter="ladder" aria-pressed="false">Ladder</button>
        <button class="fv-chip" type="button" data-filter="active" aria-pressed="false">Active</button>
      </div>
      <label class="fv-sort" for="rgSort">Sort
        <select class="fv-select" id="rgSort">
          <option value="recent">Most recent activity</option>
          <option value="turn">Highest turn</option>
          <option value="name">Game name A&ndash;Z</option>
        </select>
      </label>
    </div>

    <p class="fv-count">
      <span id="rgCount"></span>
      <button class="fv-clear" type="button" id="rgClear" hidden>Clear filters</button>
    </p>

    <div class="fv-modal-body" id="rgBody"></div>
  </div>
</div>
