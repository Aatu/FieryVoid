'use strict';

var moveTooltipHTML = `
    <div id="movetooltip" class="movementTypeContainer" style="background-color: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 5px; font-size: 16px; font-weight: bold;">
        <span class="movementType" style="color: #cccccc;">Roll</span>
    </div>
`;

(function() {
    // Movement tooltip (movetooltip) functionality
    jQuery(function() {
        jQuery(".movement-icon").hover(function() {
            // On hover, display the tooltip with the movement name
            var movementType = jQuery(this).data('movement-type');
            var tooltip = jQuery(moveTooltipHTML);
            tooltip.find(".movementType").text(movementType);

            // Set tooltip position and styling
            tooltip.css({
                "position": "absolute",
                "background": "#333",
                "color": "#fff",
                "padding": "4px",
                "border-radius": "4px",
                "font-size": "12px",
                "z-index": 1000,
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