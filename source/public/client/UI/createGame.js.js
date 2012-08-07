jQuery(function($){
    $("#mapselect").on("change", createGame.mapSelect);
    createGame.mapSelect();

});

window.createGame = {

    mapSelect: function(){

        $("#default_option").remove();
        var val = $("#mapselect").val();
        $("body").css("background-image", "url(img/maps/"+val+")");

    }
}