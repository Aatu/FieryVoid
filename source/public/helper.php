
<link href="styles/helper.css" rel="stylesheet" type="text/css">

<?php
    $ret=HelpManager::getHelpMessage($messagelocation);
	if (($ingame!=null) && ($ingame!=false)) {
	    print('<div id="ingamehelpcontainer" class="ingamehelpcontainer" style="background-image:url('.$ret['helpimg'].');">');
        print('<div id="helpmanual" class="helpmanual">');
        print('<a href="http://www.tesarta.com/b5wars/aogwarskitchensink.pdf" target="_blank"><img id="manualimg" src="img/manual.jpg" height="30" width="30">	</a>');
        print('</div>');
	    print('<div id="ingamehelpMessages" class="ingamehelpMessages">');
 	} else {
     	print('<div class="helpcontainer" style="background-image:url('.$ret['helpimg'].');">');
	    print('<div class="helpMessages">');
 	}
     print($ret['message']);
?>

    </div>
</div>