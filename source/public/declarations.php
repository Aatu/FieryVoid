<?php 
/*
window showing current declarations (fire/EW)
*/
?>

<link href="styles/chat.css" rel="stylesheet" type="text/css">

<script src="client/declarations.js"></script>




<div class="chatcontainer">
    <div class = "chatMessages"> <!-- buttons directing what's to be displayed -->
      Side: <input type="button" value="Own" onclick="window.declarations.callOwn();"> <input type="button" value="Enemy" onclick="declarations.callEnemy();"> 
      <br>Content: <input type="button" value="EW" onclick="declarations.callEW();"> <input type="button" value="Fire" onclick="declarations.callFire();"> 
      <br>Display by: <input type="button" value="Source" onclick="declarations.callSource();"> <input type="button" value="Target" onclick="declarations.callTarget();"> 
    </div>
  
    <div id="declarationsActual" class="chatMessages"> <!-- actual declarations tab, filled on button press -->
    </div>
  
</div>
