window.ajaxInterface = {

    poll: null,
    pollcount: 0,
	submiting: false,

    submitGamedata: function(){
	
		if ( ajaxInterface.submiting )
			return;
			
		ajaxInterface.submiting = true;
	
        var gd = ajaxInterface.construcGamedata();
        
        $.ajax({
            type : 'POST',
            url : 'gamedata.php',
            dataType : 'json',
            data: gd,
            success : ajaxInterface.successSubmit,
            error : ajaxInterface.errorSubmit
        });
        
    },
    
    construcGamedata: function(){
    
        var tidyships = jQuery.extend(true, {}, gamedata.ships);
        
        for (var i in tidyships){
            var ship = tidyships[i];
            ship.htmlContainer = null;
            ship.shipclickableContainer = null;
            ship.shipStatusWindow = null;
        }

        var gd = {
            turn: gamedata.turn,
            phase: gamedata.gamephase,
            activeship: gamedata.activeship,
            gameid: gamedata.gameid,
            playerid: gamedata.thisplayer,
            ships: JSON.stringify(tidyships)
        };
        
        
        
        
        return gd;
    },
    
    successSubmit: function(data){
		ajaxInterface.submiting = false;
        //console.log("success submit" + $(data));
		if (data.error){
			ajaxInterface.errorAjax(data);
		}else{
			gamedata.parseServerData(data);
		}
    },
    
    errorAjax: function(data){
        console.log("error !");
        if (data.error == "Not logged in."){
			$(".notlogged").show();
			$(".waiting").hide();
			gamedata.waiting = false;
		}
    },
	
	errorSubmit: function(data){
		ajaxInterface.submiting = false;
        console.log("error " + data.error);
        if (data.error = "Not logged in."){
			$(".notlogged").show();
			$(".waiting").hide();
			gamedata.waiting = false;
		}
    },
    
    startPollingGamedata: function(){
        
        if (gamedata.poll != null){
			console.log("starting to poll, but poll is not null");
            return;
			}
           
        ajaxInterface.pollcount = 0;
            
        ajaxInterface.pollGamedata();
    },
    
    pollGamedata: function(){
        if (gamedata.waiting == false){
            return;
			
			ajaxInterface.poll = null;
			ajaxInterface.pollcount  = 0;
		}          
        if (!gamedata.animating){
            //console.log("polling for gamedata...");
            animation.animateWaiting();
        
            if (!ajaxInterface.submiting)
                ajaxInterface.requestGamedata();
			
        }
        
        ajaxInterface.pollcount++;
        
        var time = 3000;
        
        if (ajaxInterface.pollcount > 100){
            time = 30000;
        }
        
        if (ajaxInterface.pollcount > 200){
            time = 300000;
        }
        
        if (ajaxInterface.pollcount > 300){
            return;
        }   
       
		ajaxInterface.poll = setTimeout(ajaxInterface.pollGamedata, time);
    },
    
    startPollingGames: function(){
        ajaxInterface.pollGames();
    },
    
    pollGames: function(){
        if (gamedata.waiting == false)
            return;
        
        if (!gamedata.animating){

            animation.animateWaiting();
        
            ajaxInterface.requestAllGames();
        }
        
    },
    
    requestGamedata: function(){
        
        ajaxInterface.submiting = true;
        
        $.ajax({
            type : 'GET',
            url : 'gamedata.php',
            dataType : 'json',
            data: {
                turn: gamedata.turn,
                phase: gamedata.gamephase,
                activeship: gamedata.activeship,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer,
                time: new Date().getTime()
            },
            success : ajaxInterface.successRequest,
            error : ajaxInterface.errorAjax
        });
    },
    
    requestAllGames: function(){
        
        ajaxInterface.submiting = true;
        
        $.ajax({
            type : 'GET',
            url : 'allgames.php',
            dataType : 'json',
            data: {},
            success : ajaxInterface.successRequest,
            error : ajaxInterface.errorAjax
        });
    },
    
    successRequest: function(data){
        ajaxInterface.submiting = false;
        //console.log("success request" + $(data));
        gamedata.parseServerData(data);
    }
}







