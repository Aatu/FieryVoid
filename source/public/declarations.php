<?php 
/*
window showing current declarations (fire/EW)
*/
?>

<link href="styles/chat.css" rel="stylesheet" type="text/css">


<script>
  var GlobalSide = '';
  var GlobalContent = '';
  var GlobalDisplay = '';

  //writes actual content to declarationsActual div
  function fillDeclarationsActual() {
    //fix data (if not done yet)
    if(GlobalSide=='') GlobalSide = 'Own';
    if(GlobalContent=='') GlobalContent = 'EW';
    if(GlobalDisplay=='') GlobalDisplay = 'Source';
    
    //prepare data
    
    
    //prepare text
    
    
    //display text
    

  }

  function callOwn() {
    GlobalSide = 'Own';
    fillDeclarationsActual();
  }  
  function callEnemy() {
    GlobalSide = 'Enemy';
    fillDeclarationsActual();
  }    
  function callEW() {
    GlobalContent = 'EW';
    fillDeclarationsActual();
  }      
  function callFire() {
    GlobalContent = 'Fire';
    fillDeclarationsActual();
  }  
  function callSource() {
    GlobalDisplay = 'Source';
    fillDeclarationsActual();
  }    
  function callTarget() {
    GlobalDisplay = 'Target';
    fillDeclarationsActual();
  }    
  
</script>



<div class="chatcontainer">
    <div class = "chatMessages"> <!-- buttons directing what's to be displayed -->
      Side: <input type="button" value="Own" onclick="callOwn();"> <input type="button" value="Enemy" onclick="callEnemy();"> 
      <br>Content: <input type="button" value="EW" onclick="callEW();"> <input type="button" value="Fire" onclick="callFire();"> 
      <br>Display by: <input type="button" value="Source" onclick="callSource();"> <input type="button" value="Target" onclick="callTarget();"> 
    </div>
  
    <div id="declarationsActual" class="chatMessages"> <!-- actual declarations tab, filled on button press -->
    </div>
  
</div>
