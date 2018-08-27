<?php 
/*
window showing current declarations (fire/EW)
*/
?>

<link href="styles/chat.css" rel="stylesheet" type="text/css">






<div class="chatcontainer">
    <div class = "chatMessages"> <!-- buttons directing what's to be displayed -->
      Side: <input type="button" value="Own" onclick="callOwn();"> <input type="button" value="Enemy" onclick="callEnemy();"> 
      <br>Content: <input type="button" value="EW" onclick="callEW();"> <input type="button" value="Fire" onclick="callFire();"> 
      <br>Display by: <input type="button" value="Source" onclick="callSource();"> <input type="button" value="Target" onclick="callTarget();"> 
    </div>
  
    <div id="declarationsActual" class="chatMessages"> <!-- actual declarations tab, filled on button press -->
    </div>
  
</div>
