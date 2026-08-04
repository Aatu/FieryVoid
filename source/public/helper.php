
<!-- styles/helper.css was DELETED (roadmap item 6, Stage 5) as dead code: this file's only
     include site, in game.php, has been sitting inside an HTML comment, so neither this
     partial nor its stylesheet has rendered in a long time. The link is left here as a
     marker rather than silently pointing at a missing file - if the in-game help panel is
     ever revived, restore styles/helper.css from git history or restyle it against
     styles/tokens.css, which is where the site's design tokens now live.
<link href="styles/helper.css" rel="stylesheet" type="text/css">
-->


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