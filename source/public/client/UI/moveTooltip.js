'use strict';

//Styled by .fv-movetooltip in tactical.css (EXTENDED_TURNS_PLAN.md §4.6); only the position is set here.
var moveTooltipHTML = `
    <div id="movetooltip" class="movementTypeContainer fv-movetooltip">
        <span class="movementType">Roll</span>
    </div>
`;

(function() {
    // Movement tooltip (movetooltip) functionality
    jQuery(function() {
        jQuery(".movement-icon").hover(function() {
            // On hover, display the tooltip with the movement name
            var movementType = jQuery(this).data('movement-type');
            var tooltip = jQuery(moveTooltipHTML);
    		var iconId = jQuery(this).attr("id");
            tooltip.find(".movementType").text(movementType);

			// Check if either of the active pivot icons (Stop Pivoting) is visible
			if ((jQuery("#pivotLeftActive").is(":visible") || jQuery("#pivotRightActive").is(":visible")) &&  (iconId === "pivotleft" || iconId === "pivotright")) {
			    // If either active pivot icon is visible, show "Stop Pivoting"
			    tooltip.find(".movementType").text("Stop Pivoting");
			}else if ((jQuery("#rollActive").is(":visible")) &&  (iconId === "roll")) {
			    // If either active roll icon is visible, show "Stop Rolling"
			    tooltip.find(".movementType").text("Stop Rolling");
			}else {
			    // Otherwise, show the regular movement type
			    tooltip.find(".movementType").text(movementType); // Default movement type
			}

            // Set tooltip position
            tooltip.css({
                "top": jQuery(this).offset().top - 30 + "px",  // Positioning slightly above the icon
                "left": jQuery(this).offset().left + "px"
            }).appendTo('body').fadeIn();

        }, function() {
            // Remove the tooltip when mouse leaves
            jQuery("#movetooltip").remove();
        });

        // Optional: Adjust tooltip position dynamically when moving mouse over the icon
        jQuery(".movement-icon").mousemove(function(event) {
            jQuery("#movetooltip").css({
                "top": event.pageY - 30 + "px",
                "left": event.pageX + "px"
            });
        });
    });
})();
