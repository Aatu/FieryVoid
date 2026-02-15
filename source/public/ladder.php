<div id="ladderModal" class="modal">
  <div class="modal-content ladder-modal-content">
    <span class="close-ladder">&times;</span>
    <h2>Online Ladder</h2>
    
    <div class="ladder-flex-container">
        <div id="ladderStandingsPane" class="ladder-standings">
            <h3>Standings <div style="float:right;"><button id="btnRegisterLadder" class="btn-register-ladder" style="float:none;">Register for Ladder</button> <button id="btnRemoveAccount" class="btn-remove-account" style="float:none; margin-left: 10px; display: none;">Remove Account</button></div></h3>
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
            <h3>Match History: <span id="historyPlayerName" class="history-player-name"></span> <button id="btnHistoryBack" class="btn-ladder-inline" style="float:right;">Back</button></h3>
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
            <h3>Calculate Points Difference</h3>
            <div class="ladder-input-group">
                <label class="ladder-label">Your Rating:</label>
                <span id="calcMyRating" class="ladder-static-value">Loading...</span>
            </div>
            <div class="ladder-input-group">
                <label class="ladder-label">Opponent's Rating:</label>
                <input type="number" id="calcOppRating" value="100" class="ladder-input">
            </div>
            <div class="ladder-input-group">
                <label class="ladder-label">Base Points Value:</label>
                <input type="text" id="calcGamePoints" value="3500" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="ladder-input">
            </div>
            <button id="btnCalculate">Calculate</button>
            <div id="calcResult" class="ladder-calc-result"></div>
            <button id="btnPopulateSlots" style="display:none; margin-top:10px;">Populate Slots</button>
            <div class="ladder-disclaimer">
                * Lower rated player receives a point bonus equal to the rating difference percentage.<br>
                * Winner gets +1 Rating, Loser gets -1 Rating.
            </div>
        </div>
    </div>
  </div>
</div>
