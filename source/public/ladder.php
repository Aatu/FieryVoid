<?php
// Online Ladder window. Included by games.php and by creategame.php.
// Behaviour: client/ladder.js. Data: ladderstandings.php + Manager::getLadderStandings.
//
// The shell deliberately mirrors the Recent Games window (recentgames.php): overlay,
// panel, Orbitron title, dim subtitle, control row, monospace count line, then a
// divided scrolling body. Styling lives in styles/ladder.css rather than
// gamesPanel.css, because creategame.php includes this partial but does not link
// gamesPanel.css.
//
// Every id and class client/ladder.js binds to is unchanged. The inline display:none
// on #btnRemoveAccount / #ladderHistoryPane / #btnPopulateSlots is functional --
// jQuery .show()/.hide() toggles it -- so it stays in the markup.
?>
<div id="ladderModal" class="modal">
  <div class="modal-content ladder-modal-content">
    <button class="close-ladder" type="button" aria-label="Close ladder">&times;</button>
    <h2 class="ladder-modal-title">Online Ladder</h2>
    <p class="ladder-modal-sub ladder-faq-link">Ranked play across Fiery Void. You can learn about how the Online Ladder works <a href="/faq.php#ladder">here</a>.</p>

    <div class="ladder-controls">
      <button id="btnRegisterLadder" class="btn-register-ladder" type="button">Register for Ladder</button>
      <button id="btnRemoveAccount" class="btn-remove-account" type="button" style="display:none;">Remove Account</button>
    </div>

    <p class="ladder-count"><span id="ladderCount"></span></p>

    <div class="ladder-modal-body">
      <div class="ladder-flex-container">
        <div id="ladderStandingsPane" class="ladder-standings">
            <h3 class="ladder-section-head"><span>Standings</span></h3>
            <div class="ladder-table-scroll">
                <table id="ladderTable">
                    <thead>
                        <tr>
                            <th class="text-left">Player</th>
                            <th class="text-center">Rating</th>
                            <th class="text-center">Wins</th>
                            <th class="text-center">Losses</th>
                            <th class="text-center">Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div id="ladderHistoryPane" class="ladder-standings" style="display:none;">
            <h3 class="ladder-section-head">
                <span>Match History: <span id="historyPlayerName" class="history-player-name"></span></span>
                <button id="btnHistoryBack" class="btn-ladder-inline" type="button">Back</button>
            </h3>
            <div class="ladder-table-scroll">
                <table id="ladderHistoryTable">
                    <thead>
                        <tr>
                            <th class="text-left">Game</th>
                            <th class="text-left">Opponent</th>
                            <th class="text-center">Result</th>
                            <th class="text-center">Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ladder-calculator">
            <h3 class="ladder-section-head"><span>Calculate Points Difference</span></h3>
            <div class="ladder-input-group">
                <span class="ladder-label">Your Rating</span>
                <span id="calcMyRating" class="ladder-static-value">Loading...</span>
            </div>
            <div class="ladder-input-group">
                <label class="ladder-label" for="calcOppRating">Opponent's Rating</label>
                <input type="number" id="calcOppRating" value="100" class="ladder-input">
            </div>
            <div class="ladder-input-group">
                <label class="ladder-label" for="calcGamePoints">Base Points Value</label>
                <input type="text" id="calcGamePoints" value="3500" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="ladder-input">
            </div>
            <button id="btnCalculate" type="button">Calculate</button>
            <div id="calcResult" class="ladder-calc-result"></div>
            <button id="btnPopulateSlots" type="button" style="display:none;">Populate Slots</button>
            <div class="ladder-disclaimer">
                * Lower rated player receives a point bonus equal to the rating difference percentage.<br>
                * Winner gets +1 Rating, Loser gets -1 Rating.
            </div>
        </div>
      </div>
    </div>

    <p class="ladder-disclaimer ladder-foot">Games older than three months will no longer be available to view.</p>
  </div>
</div>
