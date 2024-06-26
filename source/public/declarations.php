<?php 
/*
window showing current declarations (fire/EW)
*/
?>

<link href="styles/chat.css" rel="stylesheet" type="text/css">

<script src="client/declarations.js"></script>




<div id="declarationsContainer" class="chatcontainer">
    <div id="declarationsButtons" class = ""> <!-- buttons directing what's to be displayed -->
      Side: <input type="button" value="Own" onclick="window.declarations.callOwn();"> <input type="button" value="Enemy" onclick="declarations.callEnemy();"> 
      <br>Content: <input type="button" value="EW" onclick="declarations.callEW();"> <input type="button" value="Fire" onclick="declarations.callFire();"> 
      <br>Display by: <input type="button" value="Source" onclick="declarations.callSource();"> <input type="button" value="Target" onclick="declarations.callTarget();"> 
	  <br>Game description: <input type="button" value="Show description" onclick="declarations.callGameDescriptionActual();"> 
    </div>
  
    <div id="declarationsActual" class=""> <!-- actual declarations tab, filled on button press -->
    </div>
  
</div>
