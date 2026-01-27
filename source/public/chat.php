<?php 

if (! isset($chatgameid ))
    $chatgameid = 0;

if (! isset($chatelement))
    throw new Exception("\$chatelement is missing!");

?>
<link href="styles/chat.css" rel="stylesheet" type="text/css">
<script>
(function(){

    const POLL_INTERVAL = 8000;
    const REQUEST_TIMEOUT = 7000;    
    // Define chat first
    var chat = {

        polling: false,
        requesting: false,
        lastid: 0,
        lastTimeChecked: "",
        lastTimeStamp: "",
        focus: false,
        message: "",
        gameid: <?php print((int)$chatgameid); ?>,
        playerid: <?php print(isset($_SESSION["user"]) ? (int)$_SESSION["user"] : 0); ?>,
        chatElement: <?php print("'$chatelement'") ?>,

        // place to store current jqXhr so we can abort on unload
        _currentXhr: null,

        initInterface: function(){
            $(chat.chatElement + " .chatinput").on("keydown", function(e){
                chat.onKeyUp.call(this, e);
            });
            $(chat.chatElement + " .chatinput").on("focus", function(e){
                chat.onFocus.call(this, e);
            });
            $(chat.chatElement + " .chatinput").on("blur", function(e){
                chat.onBlur.call(this, e);
            });
            $(chat.chatElement).on('onshow', chat.resizeChat);

            var h = $(chat.chatElement + " .chatcontainer").height();
            $(chat.chatElement + " .chatMessages").css("height", (h-20)+"px");
            var c = $(chat.chatElement + " .chatMessages");
            c.scrollTop(c[0].scrollHeight);

            // abort outstanding requests on navigation away
            $(window).on('beforeunload.chat', function(){
                if(chat._currentXhr && typeof chat._currentXhr.abort === "function"){
                    try { chat._currentXhr.abort(); } catch(e){}
                }
                chat.polling = false;
                chat.requesting = false;
            });
        },

        startNetworkOp: function(){
            // start polling only once
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
    // 🧹 Keep only last 100 messages to prevent DOM bloat
    if (c.children().length > 100) {
        c.children().slice(0, c.children().length - 100).remove();
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
            setTimeout(chat.requestChatdata, 3000); //Set initial polling to 1 sec to load chat, then it'll go to 8secs.
        },

        removeNewMessageTag: function(){
            var el = chat.gameid == 0 ? "globalChatTab" : "chatTab";
            document.getElementById(el)?.classList.remove("newMessage");
        },

        setLastTimeChecked: function(){
            if (!chat.polling) return;
            chat._currentXhr = ajaxInterface.ajaxWithRetry({
                type: 'POST',
                url: 'playerChatInfo.php',
                dataType: 'json',
                data: { gameid: chat.gameid },
                success: chat.successSetLastTimeChecked,
                error: function(){ setTimeout(chat.setLastTimeChecked, POLL_INTERVAL * 2); }
            }).fail(() => {});
        },

        getLastTimeChecked: function(){
            if (!chat.polling) return;
            chat._currentXhr = ajaxInterface.ajaxWithRetry({
                type: 'GET',
                url: 'playerChatInfo.php',
                dataType: 'json',
                data: { gameid: chat.gameid },
                success: function(data){
                    if(!data || data.error){
                        if(data?.error) window.confirm.exception(data, function(){});
                        setTimeout(chat.getLastTimeChecked, 8000);
                        return;
                    }
                    chat.lastTimeChecked = data.lastCheckGame;
                },
                error: function(){ setTimeout(chat.getLastTimeChecked, 8000); }
            }).fail(() => {});
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

            // small retry limit and exponential backoff
            var attempt = 0;
            var maxAttempts = 4;

            function doSend(delay){
                if (!chat.polling) return; // avoid when shutting down
                chat._currentXhr = ajaxInterface.ajaxWithRetry({
                    type: 'POST',
                    url: 'chatdata.php',
                    dataType: 'json',
                    data: { gameid: chat.gameid, message: message },
                    success: function(data){
                        chat.successSubmit(data);
                    },
                    error: function(jqXHR, textStatus){
                        attempt++;
                        if (attempt <= maxAttempts){
                            // backoff: 500ms, 1000ms, 2000ms, 4000ms ...
                            setTimeout(function(){ doSend(); }, 500 * Math.pow(2, attempt-1));
                        } else {
                            console.error("Failed to submit chat message after " + maxAttempts + " attempts: " + textStatus);
                        }
                    }
                }).fail(() => {});
            }
            doSend();

            chat.setLastTimeChecked();
        },


requestChatdata: function(){
  if(chat.requesting || !chat.polling) return;
  chat.requesting = true;

  if(chat._currentXhr && chat._currentXhr.readyState !== 4){
    try { chat._currentXhr.abort(); } catch(e){}
  }

  chat._currentXhr = ajaxInterface.ajaxWithRetry({
    type: 'GET',
    url: 'chatdata.php',
    dataType: 'json',
    timeout: REQUEST_TIMEOUT,
    data: { gameid: chat.gameid, lastid: chat.lastid },
    success: function(data){
      chat.requesting = false;
      if(!chat.polling) return;
      if(!data.error) chat.parseChatData(data);
      setTimeout(chat.requestChatdata, POLL_INTERVAL);
    },
    error: function(){
      chat.requesting = false;
      if(chat.polling) setTimeout(chat.requestChatdata, POLL_INTERVAL * 2);
    }
  }).fail(() => {});
},

        successRequest: function(data){
            chat.requesting = false;
            if(data.error){
                window.confirm.exception(data, function(){});
                // don't block further polling; schedule next attempt
                setTimeout(chat.requestChatdata, 8000);
            }else{
                chat.parseChatData(data);
                setTimeout(chat.requestChatdata, 8000);
            }
        },


        successSubmit: function(data){
            if(data.error) window.confirm.exception(data, function(){});
        } // no comma here

    }//endof Chat

// Pause/resume polling when the tab visibility changes
document.addEventListener("visibilitychange", function() {
    if (document.hidden) {
        chat.polling = false; // stop polling while tab is hidden
    } else {
        if (!chat.polling) {
            chat.polling = true; // resume polling
            chat.requestChatdata();
        }
    }
});


    // register DOM ready AFTER chat is defined
    jQuery(function(){
        chat.initInterface();
        setTimeout(function(){
            chat.startNetworkOp();
        }, 2000);
    });

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
