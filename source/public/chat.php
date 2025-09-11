<?php 

if (! isset($chatgameid ))
    $chatgameid = 0;

if (! isset($chatelement))
    throw new Exception("\$chatelement is missing!");

?>
<link href="styles/chat.css" rel="stylesheet" type="text/css">
<script>
(function(){
    jQuery(function(){
        chat.initChat();
    });

    var chat = {

        polling: false,
        requesting: false,
        lastid: 0,
        lastTimeChecked: "",
        lastTimeStamp: "",
        focus: false,
        message: "",
        gameid: <?php print($chatgameid) ?>,
        playerid: <?php print(isset($_SESSION["user"]) ? $_SESSION["user"] : '""') ?>,
        chatElement: <?php print("'$chatelement'") ?>,

        initChat: function(){
            $(chat.chatElement + " .chatinput").on("keydown", chat.onKeyUp);
            $(chat.chatElement + " .chatinput").on("focus", chat.onFocus);
            $(chat.chatElement + " .chatinput").on("blur", chat.onBlur);
            $(chat.chatElement).on('onshow', chat.resizeChat);

            var h = $(chat.chatElement + " .chatcontainer").height();
            $(chat.chatElement + " .chatMessages").css("height", (h-20)+"px");
            var c = $(chat.chatElement + " .chatMessages");
            c.scrollTop(c[0].scrollHeight);

            chat.startPolling();
            chat.getLastTimeChecked();
        },

        resizeChat: function(){
            chat.setLastTimeChecked();
            chat.removeNewMessageTag();
            var h = $(chat.chatElement + " .chatcontainer").height();
            $(chat.chatElement + " .chatMessages").css("height", (h-20)+"px");
            var c = $(chat.chatElement + " .chatMessages");
            c.scrollTop(c[0].scrollHeight);
            chat.getLastTimeChecked();
        },

        onFocus: function(){
            if (window.windowEvents) windowEvents.chatfocus = true;
        },

        onBlur: function(){
            if (window.windowEvents) windowEvents.chatfocus = false;
        },

        onKeyUp: function(e){
            e.stopPropagation();
            if (e.keyCode == 13){
                var input = $(this);
                var value = input.val();
                if (value.length === 0) return;

                input.val("");
                chat.submitChatMessage(value);
            }
        },

        parseChatData: function(data){
            var c = $(chat.chatElement + " .chatMessages");
            var scroll = false;

            for (var i in data){
                var message = data[i];
                var mine = message.userid == chat.playerid ? " mine" : "";
                var ingame = chat.gameid == 0 ? '<span class="chatglobal"></span>' : '<span class="chatingame"></span>';

                if(message.userid != chat.playerid){
                    chat.lastTimeStamp = message.time;
                }

                var e = $('<div class="chatmessage">'+ingame+
                          '<span class="chattime">('+message.time+')</span> '+
                          '<span class="chatuser'+mine+'">'+message.username+': </span>'+
                          '<span class="chattext">'+message.message+'</span></div>');
                e.appendTo(c);
                chat.lastid = message.id;
                scroll = true;
            }

            if(chat.checkTimesForLightup(chat.lastTimeStamp, chat.lastTimeChecked)){
                var thisChat = chat.gameid == 0 ? "globalChatTab" : "chatTab";
                if(document.getElementById(thisChat) && 
                   !document.getElementById(thisChat).classList.contains("selected")){
                    document.getElementById(thisChat).classList.add("newMessage");
                }
            }

            if(scroll) c.scrollTop(c[0].scrollHeight);
        },

        checkTimesForLightup: function(timeStamp, lastChecked){
            if(!timeStamp || !lastChecked) return false;
            return chat.compareTimes(chat.parseTime(timeStamp), chat.parseTime(lastChecked)) > 0;
        },

        parseTime: function(timeString){
            return [
                parseInt(timeString.substring(0,4)),
                parseInt(timeString.substring(5,7)),
                parseInt(timeString.substring(8,10)),
                parseInt(timeString.substring(11,13)),
                parseInt(timeString.substring(14,16)),
                parseInt(timeString.substring(17))
            ];
        },

        compareTimes: function(timeArray1, timeArray2){
            for(var i in timeArray1){
                if(timeArray1[i] > timeArray2[i]) return 1;
                if(timeArray1[i] < timeArray2[i]) return -1;
            }
            return 0;
        },

        startPolling: function(){
            if(chat.polling) return;
            chat.polling = true;
            setTimeout(chat.requestChatdata, 1000); //Set initial polling to 1 sec to load chat, then it'll go to 8secs.
        },

        removeNewMessageTag: function(){
            var el = chat.gameid == 0 ? "globalChatTab" : "chatTab";
            document.getElementById(el)?.classList.remove("newMessage");
        },

        setLastTimeChecked: function(){
            $.ajax({
                type: 'POST',
                url: 'playerChatInfo.php',
                dataType: 'json',
                data: { gameid: chat.gameid },
                success: chat.successSetLastTimeChecked,
                error: function(){ setTimeout(chat.setLastTimeChecked, 8000); }
            });
        },

        getLastTimeChecked: function(){
            $.ajax({
                type: 'GET',
                url: 'playerChatInfo.php',
                dataType: 'json',
                data: { gameid: chat.gameid },
                success: chat.successGetLastTimeChecked,
                error: function(){ setTimeout(chat.getLastTimeChecked, 8000); }
            });
        },

        successSetLastTimeChecked: function(data){
            if(data.error) window.confirm.exception(data, function(){});
        },

        successGetLastTimeChecked: function(data){
            if(data.error) window.confirm.exception(data, function(){});
            else chat.lastTimeChecked = data.lastCheckGame;
        },

        submitChatMessage: function(message){
            chat.message = message;
            $.ajax({
                type: 'POST',
                url: 'chatdata.php',
                dataType: 'json',
                data: { gameid: chat.gameid, message: message },
                success: chat.successSubmit,
                error: function(){ $.ajax(this); }
            });
            chat.setLastTimeChecked();
        },

        requestChatdata: function(){
            if(chat.requesting) return;
            chat.requesting = true;
            $.ajax({
                type: 'GET',
                url: 'chatdata.php',
                dataType: 'json',
                data: { gameid: chat.gameid, lastid: chat.lastid },
                success: chat.successRequest,
                error: function(){ setTimeout(chat.requestChatdata, 8000); }
            });
        },

        successRequest: function(data){
            chat.requesting = false;
            if(data.error){
                window.confirm.exception(data, function(){});
                chat.requesting = true;
            }else{
                chat.parseChatData(data);
                setTimeout(chat.requestChatdata, 8000);
            }
        },

        successSubmit: function(data){
            if(data.error) window.confirm.exception(data, function(){});
        },

    }
})();
</script>

<div class="chatcontainer">
    <div class="chatMessages"></div>
    <div class="chatinputTd">
        <input class="chatinput" value="" name="chatinput">
    </div>
</div>




<!--
<link href="styles/chat.css" rel="stylesheet" type="text/css">
<script>
   
    
    (function(){
        jQuery(function(){
            chat.initChat();
        });
    
        var chat = {

            polling: false,
            requesting: false,
            lastid: 0,
            lastTimeChecked: "",
            lastTimeStamp: "",
            focus: false,
            message: "",
            gameid:<?php print($chatgameid) ?>,
            playerid:<?php print(isset($_SESSION["user"]) ? $_SESSION["user"] : '""') ?>,
            chatElement: <?php print("'$chatelement'") ?>,
            
            initChat: function(){
                $(chat.chatElement+ " .chatinput").on("keydown", chat.onKeyUp);
                $(chat.chatElement+ " .chatinput").on("focus", chat.onFocus);
                $(chat.chatElement+ " .chatinput").on("blur", chat.onBlur);
                $(chat.chatElement).on('onshow', chat.resizeChat);
                
                var h = $(chat.chatElement+ " .chatcontainer").height();
                $(chat.chatElement+ " .chatMessages").css("height", (h-20)+"px");
                var c = $(chat.chatElement+ " .chatMessages");
                c.scrollTop(c[0].scrollHeight);

                chat.startPolling();

                chat.getLastTimeChecked();
            },
            
            resizeChat: function(){
                chat.setLastTimeChecked();

                chat.removeNewMessageTag();
                
                var h = $(chat.chatElement+ " .chatcontainer").height();
                $(chat.chatElement+ " .chatMessages").css("height", (h-20)+"px");
                var c = $(chat.chatElement+ " .chatMessages");
                c.scrollTop(c[0].scrollHeight);
                
                chat.getLastTimeChecked();
            },
            
            onFocus: function(){
                if (window.windowEvents)
                    windowEvents.chatfocus = true;
            },

            onBlur: function(){
                if (window.windowEvents)
                    windowEvents.chatfocus = false;
            },

            onKeyUp: function(e){
                e.stopPropagation();
                if (e.keyCode == 13){
                    var input = $(this);
                    var value = input.val();
                    if (value.lenght == 0)
                        return;

                    input.val("");
                    chat.submitChatMessage(value);
                }
            },

            parseChatData: function(data){
                var c = $(chat.chatElement+ " .chatMessages");
                var scroll = false;

                for (var i in data){
                    var message = data[i];
                    
                    var mine = "";
                    if (message.userid == chat.playerid)
                        mine = " mine";
                    
                    var ingame;
					
					/* NOT NECESSARY, player sees where (s)he is!*/
                    if (chat.gameid == 0){
                        //ingame = '<span class="chatglobal">GLOBAL: </span>';
						ingame = '<span class="chatglobal"></span>';
                    }
                    else{
                        //ingame = '<span class="chatingame">IN GAME: </span>';
                        ingame = '<span class="chatingame"></span>';
                    }

                    if(message.userid != chat.playerid){
                        chat.lastTimeStamp = message.time;
                    }
					

                    var e = $('<div class="chatmessage">'+ingame+'<span class="chattime">('+message.time+')</span> <span class="chatuser'+mine+'">'+message.username+': </span><span class="chattext">'+message.message+'</span></div></div>');
                    e.appendTo(c);
                    chat.lastid = message.id;
                    scroll = true;
                }

                if(chat.checkTimesForLightup(chat.lastTimeStamp, chat.lastTimeChecked )){
                    var thisChat = "chatTab";
                    
                    if (chat.gameid == 0){
                        thisChat = "globalChatTab";
                    }
                    if(!document.getElementById(thisChat)){
                        return;
                    }
                    
                    if(!document.getElementById(thisChat).classList.contains("selected")){
                        document.getElementById(thisChat).classList.add("newMessage");
                    }
                }
                
                if (scroll)
                    c.scrollTop(c[0].scrollHeight);

            },
            
            checkTimesForLightup: function(timeStamp, lastChecked){
                if(!timeStamp || !lastChecked){
                    return false;
                }
                
                if(chat.compareTimes(chat.parseTime(timeStamp), chat.parseTime(lastChecked))>0){
                    return true;
                }
                
                return false;
            },
            
            parseTime: function(timeString){
                var yearString = timeString.substring(0, 4);
                var monthString = timeString.substring(5,7);
                var dayString = timeString.substring(8,10);
                var hoursString = timeString.substring(11, 13);
                var minutesString = timeString.substring(14,16);
                var secondsString = timeString.substring(17);
                
                var timeArray = new Array();
                
                timeArray[0] = parseInt(yearString);
                timeArray[1] = parseInt(monthString);
                timeArray[2] = parseInt(dayString);
                timeArray[3] = parseInt(hoursString);
                timeArray[4] = parseInt(minutesString);
                timeArray[5] = parseInt(secondsString);
                
                return timeArray;
            },
            
            compareTimes: function(timeArray1, timeArray2){
                // returns 1 if time in timeArray1 is later than timeArray2
                // returns -1 if time in timeArray1 is later than timeArray2
                // returns 0 if times are equal
                for(var i in timeArray1){
                   var time1 = timeArray1[i];
                   var time2 = timeArray2[i];
                   
                   if(time1 > time2){
                       return 1;
                   }else if(time1 < time2){
                       return -1;
                   }
                }
                
                return 0;
            },

            startPolling: function(){
                if (chat.polling)
                    return;

                setTimeout(chat.requestChatdata, 3000);
            },

            removeNewMessageTag:function(){
                if (chat.gameid == 0){
                    document.getElementById("globalChatTab").classList.remove("newMessage");
                }
                else{
                    document.getElementById("chatTab").classList.remove("newMessage");
                }
            },
            
            setLastTimeChecked: function(){
                $.ajax({
                    type : 'POST',
                    url : 'playerChatInfo.php',
                    dataType : 'json',
                    data: {
                        gameid:chat.gameid
                    },
                    success : chat.successSetLastTimeChecked,
                    error : chat.retrySetTimeChecked
                });
                
            },
            
            retrySetTimeChecked: function(){
                setTimeout(chat.setLastTimeChecked(), 3000);
            },


            getLastTimeChecked: function(){
                $.ajax({
                    type : 'GET',
                    url : 'playerChatInfo.php',
                    dataType : 'json',
                    data: {
                        gameid:chat.gameid
                    },
                    success : chat.successGetLastTimeChecked,
                    error : chat.retryGetTimeChecked
                });
                
            },

            retryGetTimeChecked: function(){
                setTimeout(chat.getLastTimeChecked(), 3000);
            },

            successSetLastTimeChecked: function(data){
                if (data.error){
                    window.confirm.exception(data , function(){});
                }
            },

            successGetLastTimeChecked: function(data){
                if (data.error){
                    window.confirm.exception(data , function(){});
                }else{
                    chat.lastTimeChecked = data.lastCheckGame;
                }
            },

            submitChatMessage: function(message){
                chat.message = message;
                
                $.ajax({
                    type : 'POST',
                    url : 'chatdata.php',
                    dataType : 'json',
                    data: {
                        gameid:chat.gameid,
                        message:message
                    },
                    success : chat.successSubmit,
                    error: function(){
                        $.ajax(this);
                    }
                });

                chat.setLastTimeChecked();
            },

            requestChatdata: function(){

                if (chat.requesting)
                    return;

                chat.requesting = true;

                $.ajax({
                    type : 'GET',
                    url : 'chatdata.php',
                    dataType : 'json',
                    data: {
                        gameid:chat.gameid,
                        lastid:chat.lastid
                    },
                    success : chat.successRequest,
                    error: function(){
                        setTimeout(chat.requestChatdata, 3000);
                    }
                });
            },
            
            successRequest: function(data){
                chat.requesting = false;
                if (data.error){
                    window.confirm.exception(data , function(){});
                    chat.requesting = true;
                }else{
                    setTimeout(chat.requestChatdata, 3000);
                    chat.parseChatData(data);
                }

            },

            successSubmit: function(data){
                if (data.error){
                    window.confirm.exception(data , function(){});
                }
            },

        }
    })();


</script>
<div class="chatcontainer">
    <div class="chatMessages">
        
    </div>
    <div class="chatinputTd">
        <input class="chatinput" value="" name="chatinput">
    </div>
</div>
-->
