<?php 

?>

<link href="styles/chat.css" rel="stylesheet" type="text/css">


<div id="combatLogContainer" class="chatcontainer">
    <div id="combatLogButtons" class = ""> <!-- buttons directing what's to be displayed -->
      Display Combat Logs:  
      <input type="button" id="previousTurnButton" value="Previous Turn" onclick="window.combatLog.showPrevious();">
      <input type="button" id="nextTurnButton" value="Next Turn" onclick="window.combatLog.showNext();" style="display:none;"> <!-- Hidden initially -->
      <input type="button" id="currentTurnButton" value="Back to Current Turn" onclick="window.combatLog.showCurrent();" style="display:none;"> <!-- Hidden initially -->
    </div>
  
    <div id="LogActual" class="" style="display:none;"> <!-- actual Combat Log, filled on button press -->
        <!-- the printed combat log will go here -->
    </div>
</div>
