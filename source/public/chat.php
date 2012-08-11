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
            focus: false,
            gameid:<?php print($chatgameid) ?>,
            playerid:<?php print($_SESSION["user"]) ?>,
            chatElement: <?php print("'$chatelement'") ?>,
            
            initChat: function(){
                $(chat.chatElement+ " .chatinput").on("keydown", chat.onKeyUp);
                $(chat.chatElement+ " .chatinput").on("focus", chat.onFocus);
                $(chat.chatElement+ " .chatinput").on("blur", chat.onBlur);
                $(chat.chatElement).on('onshow', chat.resizeChat);
                
                chat.resizeChat();
                chat.startPolling();
            },
            
            resizeChat: function(){
                var h = $(chat.chatElement+ " .chatcontainer").height();
                $(chat.chatElement+ " .chatMessages").css("height", (h-20)+"px");
                var c = $(chat.chatElement+ " .chatMessages");
                c.scrollTop(c[0].scrollHeight);
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
                    
                    var ingame = '<span class="chatingame">IN GAME: </span>';
                    if (chat.gameid == 0)
                        ingame = '<span class="chatglobal">GLOBAL: </span>';
                    
                    var e = $('<div class="chatmessage">'+ingame+'<span class="chattime">('+message.time+')</span> <span class="chatuser'+mine+'">'+message.username+': </span><span class="chattext">'+message.message+'</span></div></div>');
                    e.appendTo(c);
                    chat.lastid = message.id;
                    scroll = true;
                }

                if (scroll)
                    c.scrollTop(c[0].scrollHeight);

            },

            startPolling: function(){
                if (chat.polling)
                    return;

                setTimeout(chat.requestChatdata, 3000);
            },

            submitChatMessage: function(message){
                $.ajax({
                    type : 'POST',
                    url : 'chatdata.php',
                    dataType : 'json',
                    data: {
                        gameid:chat.gameid,
                        message:message
                    },
                    success : chat.successSubmit,
                    error : chat.errorAjax
                });
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
                    error : chat.errorAjax
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

            errorAjax: function(jqXHR, textStatus, errorThrown){
                console.log("CHAT ERROR");
                console.dir(jqXHR);
                console.dir(errorThrown);
                window.confirm.exception({error:"CHAT AJAX error: " +textStatus} , function(){});
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