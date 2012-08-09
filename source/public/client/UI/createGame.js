jQuery(function($){
    $("#mapselect").on("change", createGame.mapSelect);
    createGame.mapSelect();

    $("input").on("change", createGame.inputChange);
    $("select").on("change", createGame.inputChange);
    $("input").on("focus", createGame.inputFocus);
    $(".addslotbutton").on("click", createGame.createNewSlot);
    $(".close").on("click", createGame.removeSlot);
    $("#createGameForm").on("submit", createGame.setData);

    createGame.createSlotsFromArray();
});

window.createGame = {
    
    slots: Array(
        {id:1, team:1, name:"BLUE", points:1000, depx:-20, depy:0, deptype:"box", depwidth:10, depheight:40, depavailable:0},
        {id:2, team:2, name:"RED",  points:1000, depx:20, depy:0, deptype:"box", depwidth:10, depheight:40, depavailable:0}
    ),
    slotid: 2,

    mapSelect: function(){

        $("#default_option").remove();
        var val = $("#mapselect").val();
        $("body").css("background-image", "url(img/maps/"+val+")");

    },
    
    inputFocus: function(e){
        var input = $(this);
        var value = input.val();
        input.data("oldvalue", value);
    },
    
    inputChange: function(e){
        
        var input = $(this);
        var value = input.val();
        var inputname = input.attr("name");
            
        if (input.data("validation")){
        var patt=new RegExp(input.data("validation"));
            if (value.lenght == 0 || !patt.test(value)){
                input.val(input.data("oldvalue"));
                return;
            }
        }
        
        var slot = $(".slot").has($(this));
        var data = createGame.getSlotData(slot.data("slotid"));
        if (!slot || !data)
            return;
        
        data[inputname] = value;

        if (inputname == "deptype"){
            var width = $(".depwidthheader", slot);
            var height = $(".depheightheader", slot);
            var inputHeight = $(".depwidth", slot);
            
            if (value == "box"){
                width.html("Width:");
                height.html("Height:");
                inputHeight.show();
                height.show();
            }
            
            if (value == "circle"){
                width.html("Radius:");
                inputHeight.hide();
                height.hide();
            }
            
            if (value == "distance"){
                width.html("From:");
                height.html("To:");
                inputHeight.show();
                height.show();
            }
        }

    },
    
    createSlotsFromArray: function(){
        for (var i in createGame.slots){
            createGame.createSlot(createGame.slots[i]);
        }
    },
    
    createSlot: function(data){
        var template = $("#slottemplatecontainer .slot");
        var target = $("#team"+data.team + ".slotcontainer");
        var actual = template.clone(true).appendTo(target);
        
        actual.data("slotid", data.id);
        actual.addClass("slotid_"+data.id);
        createGame.setSlotData(data);
    },
    
    setSlotData: function(data){
        var slot = $(".slot.slotid_"+data.id);
        $(".name",slot).val(data.name);
        $(".points",slot).val(data.points);
        
        $(".depx",slot).val(data.depx);
        $(".depy",slot).val(data.depy);
        $(".deptype",slot).val(data.deptype);
        $(".depwidth",slot).val(data.depwidth);
        $(".depheight",slot).val(data.depheight);
        $(".depavailable",slot).val(data.depavailable);
        
    },
    
    createNewSlot: function(e){
        var team = ($(this).hasClass("team1")) ? 1 : 2;
        createGame.slotid++;
        var data = {id:createGame.slotid, team:team, name:"RED",  points:1000, depx:0, depy:0, deptype:"box", depwidth:0, depheight:0, depavailable:0}
        createGame.slots.push(data);
        createGame.createSlot(data);
    },
    
    getSlotData: function(id){
        for (var i in createGame.slots){
            var slot = createGame.slots[i];
            if (slot.id == id)
                return slot;
        }
    },

    removeSlotData: function(id){
        for(var i = createGame.slots-1; i >= 0; i--){  
            if(createGame.slots[i].id == id){              
                createGame.slots.splice(i,1);                 
            }
        }  
    },
    
    removeSlot: function(e){
        var slot = $(".slot").has($(this));
        var data = createGame.getSlotData(slot.data("slotid"));
        
        if (data.id < 3){
            window.confirm.error("You cant delete the first slot", function(){});
            return false;
        }
        createGame.removeSlotData(data.id);
        slot.remove();
    },
    
    setData: function(){
        var gamename = $("#gamename").val();
        var background = $("#mapselect").val();
        
        var data = ({gamename:gamename, background:background, slots:createGame.slots});
        data = JSON.stringify(data);
        $("#createGameData").val(data);
    }
}