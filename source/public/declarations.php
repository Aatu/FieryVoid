<?php 
/*
window showing current declarations (fire/EW)
*/
?>

<link href="styles/chat.css" rel="stylesheet" type="text/css">


<script>
  
  
</script>



<div class="chatcontainer">
    <div class = "chatMessages"> <!-- buttons directing what's to be displayed -->
      Side: <input type="button" value="Own" onclick="Declarations.callOwn();"> <input type="button" value="Enemy" onclick="Declarations.callEnemy();"> 
      <br>Content: <input type="button" value="EW" onclick="Declarations.callEW();"> <input type="button" value="Fire" onclick="Declarations.callFire();"> 
      <br>Display by: <input type="button" value="Source" onclick="Declarations.callSource();"> <input type="button" value="Target" onclick="Declarations.callTarget();"> 
      <br>
    </div>
    <div id="declarationsActual" class="chatMessages"> <!-- actual declarations tab, filled on button press -->
        
    </div>
</div>
