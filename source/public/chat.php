<?php

if (! isset($chatgameid ))
    $chatgameid = 0;

if (! isset($chatelement))
    throw new Exception("\$chatelement is missing!");

/*
 * OPTIONAL PANEL HEAD
 * -------------------
 * Set $chattitle before the include and this file renders its own head bar (see
 * .fv-chat-head in chat.css); $chatmeta is the smaller readout on its right. Leave
 * $chattitle unset and no bar is rendered at all — game.php wants that, because its
 * chat already sits behind a labelled tab in a 150px panel with no room to spare, and
 * games.php wants it because it supplies its own .fv-panel-head to match the news and
 * games panels beside it.
 *
 * Both are consumed and unset at the end of this file: game.php includes chat.php
 * TWICE, and a title left set by the first include would leak into the second.
 */
$chattitle = isset($chattitle) ? (string)$chattitle : null;
$chatmeta  = isset($chatmeta)  ? (string)$chatmeta  : null;

/*
 * The composer's placeholder. Derived from $chatgameid rather than hard-coded in the
 * markup, because "Message all players" is a lie in game.php's GAME CHAT tab — which
 * is why games.php used to patch this attribute from its own ready handler. It does
 * not need to any more; this file knows which chat it is.
 */
$chatplaceholder = isset($chatplaceholder)
    ? (string)$chatplaceholder
    : ((int)$chatgameid === 0 ? "Message all players" : "Message this game");

/*
 * Set $chatcompact for a chat that has to live somewhere tight — game.php's log panel
 * is 150px until the player expands it, and there every pixel the composer takes is a
 * line of message it takes away. See .fv-chat-compact in chat.css.
 */
$chatcompact = !empty($chatcompact);
?>
<link href="<?php echo AssetLoader::getAssetUrl('styles/chat.css'); ?>" rel="stylesheet" type="text/css">
<script>
(function(){

    /* ── Poll pacing ──────────────────────────────────────────────────────────
       Chat used to poll on a flat 6s timer that never decayed. That is 600 requests
       an hour per chat for as long as the tab is open, and game.php runs TWO of them
       (global + this game), so an abandoned tab was 1200 requests an hour forever.

       The server side of an unchanged chat is now free of the database — chatdata.php
       answers it straight out of APCu (see ChatManager::submitChatMessage, which keeps
       the last message id there) — but the request itself is not free. Every call made
       through ajaxInterface.ajaxWithRetry is chained onto ONE global request queue, so
       each chat poll is a slot the gamedata poll cannot have.

       So spend the requests where they buy something: poll fast while a conversation
       is actually happening, and stand down when it is not. HOT is entered when a
       message arrives, when the player sends one, and when they focus the composer —
       which between them cover every moment somebody is waiting on a reply. Otherwise
       the interval walks up the ladder below, one rung per poll that came back empty.

       Against the old flat 6s that is roughly 3x faster in conversation and roughly
       3x fewer requests when nobody is talking. */
    const POLL_HOT     = 2000;
    const HOT_DURATION = 60000;   // stay hot this long after the last sign of life

    /* Rungs are [consecutive empty polls, interval]. Read top-down, first match wins;
       the last entry is the floor. Deliberately gentler than gamedata's decay (which
       reaches 30 MINUTES) — gamedata has the whole game screen to tell a player that
       something happened, whereas an unpolled chat is simply silent, so 15s is as far
       as this is allowed to drift. */
    const POLL_LADDER = [[3, 4000], [8, 6000], [20, 10000], [Infinity, 15000]];

    /* Backgrounded tabs keep polling rather than stopping dead, so the CHAT tab can
       still light up while the player is reading a rules PDF in another tab. Browsers
       clamp background timers hard (and freeze them outright when the tab is fully
       discarded), so this costs less than the arithmetic suggests. */
    const POLL_HIDDEN = 60000;

    const REQUEST_TIMEOUT = 5000;

    /* Retry ceiling for playerChatInfo.php. That endpoint has no APCu fast path — every
       call is two real queries — so its retries have to terminate. See getLastTimeChecked. */
    const TIME_CHECK_MAX_FAILS = 3;

    /* ── Emoji ────────────────────────────────────────────────────────────────
       The picker's contents. Four short groups rather than one long grid: the
       panel is scrollable, and a labelled group is findable in a way that row 6
       of an undifferentiated wall of faces is not. Kept to what people actually
       reach for in a game chat — this is a wargame's message bar, not a keyboard.

       Anything in here is stored as an HTML numeric entity by the server (see
       ChatManager::submitChatMessage), so it survives a 3-byte utf8 column. */
    const EMOJI_GROUPS = [
        { label: "Reactions", emoji: ["🙂","😀","😄","😆","🤣","😉","😍","😎","🤔","😐","🙄","😏","😴","😢","😭","😤","😡","🤯","😱","🥳"] },
        { label: "Gestures",  emoji: ["👍","👎","👌","✌️","🤞","👏","🙏","💪","🫡","🖖","👀","🤷","🤦","👋","🤝"] },
        { label: "Battle",    emoji: ["🚀","🛸","🛰️","🌌","⭐","💫","☄️","🔥","💥","⚡","🎯","🛡️","⚔️","💣","☠️","💀","🔧","⚙️","📡","🔋"] },
        { label: "Signals",   emoji: ["✅","❌","❓","❗","⚠️","🎲","🏆","🥇","❤️","💔","🍺","🎉","⏳","🆘"] }
    ];

    /* Classic text smileys, swapped for the real thing as the message is sent (so
       what is stored is what everyone sees, including anyone who never types them).
       Longest token first — the alternation below is ordered, so ":-)" has to get a
       look before ":)" or it would never match. */
    const EMOTICONS = {
        ":-)": "🙂",  ":)": "🙂",
        ":-D": "😄",  ":D": "😄",
        ";-)": "😉",  ";)": "😉",
        ":-(": "🙁",  ":(": "🙁",
        ":-P": "😛",  ":P": "😛",  ":p": "😛",
        ":-O": "😮",  ":O": "😮",  ":o": "😮",
        ":'(": "😢",
        "xD": "😆",   "XD": "😆",
        "<3": "❤️",
        "\\o/": "🙌",
        "o7": "🫡"
    };

    /* Only matched when the token stands alone between spaces, so "10:00" and a
       smiley at the end of a URL are both left alone. The leading (^|\s) is a
       capture rather than a lookbehind for Safari's sake — lookbehind is recent
       there, and this file has to run on whatever the player brought. */
    const EMOTICON_RE = new RegExp(
        "(^|\\s)(" +
        Object.keys(EMOTICONS)
              .sort(function(a, b){ return b.length - a.length; })
              .map(function(t){ return t.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); })
              .join("|") +
        ")(?=\\s|$)", "g"
    );

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

        /* The one live poll timer. Every schedule goes through schedulePoll(), which
           clears this first — that is what makes "exactly one poll chain" an invariant
           rather than a hope. There is no stored jqXHR to abort alongside it: what
           ajaxInterface.ajaxWithRetry hands back is a plain $.Deferred().promise(),
           which has neither .abort() nor .readyState, so the abort this file used to
           attempt threw on every single poll and was swallowed by an empty catch. */
        _pollTimer: null,

        hotUntil: 0,      // Date.now() before which we poll at POLL_HOT
        quietPolls: 0,    // consecutive polls that returned nothing; indexes POLL_LADDER

        /* Bounded because the endpoint behind it, playerChatInfo.php, has no APCu fast
           path — every call is two real queries. An unbounded retry there is a tab
           quietly generating database load for as long as it stays open. */
        timeCheckFails: 0,

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

            chat.initEmoji();
            chat.scrollToBottom();

            // Stand down on navigation away. In-flight requests cannot be aborted from
            // here (see _pollTimer), but clearing the flag stops anything that lands
            // from scheduling another round.
            $(window).on('beforeunload.chat', function(){
                chat.polling = false;
                chat.requesting = false;
                if(chat._pollTimer){
                    clearTimeout(chat._pollTimer);
                    chat._pollTimer = null;
                }
            });
        },

        /* The message list used to be sized from JS — `.chatMessages` got an inline
           height of (container height - 20). chat.css lays the panel out as a flex
           column now, so that measurement is both unnecessary and wrong (it fought
           the flex row and needed an !important to beat). All that is left of it is
           pinning the scroll to the newest message. */
        scrollToBottom: function(){
            var c = $(chat.chatElement + " .chatMessages");
            if (c.length) c.scrollTop(c[0].scrollHeight);
        },

        /* ── Emoji picker ─────────────────────────────────────────────────────
           Built here rather than printed as markup because chat.php is included
           twice on game.php and this keeps the emitted HTML to one empty div. */
        initEmoji: function(){
            var panel  = $(chat.chatElement + " .chatEmojiPanel");
            var button = $(chat.chatElement + " .chatEmojiButton");
            if (!panel.length || !button.length) return;

            EMOJI_GROUPS.forEach(function(group){
                var g = $('<div class="chatEmojiGroup"></div>');
                $('<div class="chatEmojiGroupLabel"></div>').text(group.label).appendTo(g);
                var grid = $('<div class="chatEmojiGrid"></div>').appendTo(g);
                group.emoji.forEach(function(glyph){
                    $('<button type="button" class="chatEmojiKey" tabindex="-1"></button>')
                        .attr("aria-label", "Insert " + glyph)
                        .text(glyph)
                        .appendTo(grid);
                });
                g.appendTo(panel);
            });

            // mousedown default = "move focus here", which would blur the input and,
            // in game.php, hand the map's key handlers back the keyboard mid-message.
            // Suppressing it on both the key and the panel keeps the caret where it is.
            button.on("mousedown", function(e){ e.preventDefault(); });
            panel.on("mousedown", function(e){ e.preventDefault(); });

            button.on("click", function(e){
                e.preventDefault();
                e.stopPropagation();
                chat.toggleEmojiPanel();
            });

            panel.on("click", ".chatEmojiKey", function(e){
                e.preventDefault();
                chat.insertAtCaret($(this).text());
            });

            // Anything outside this chat's own composer or picker closes its own panel.
            // Each include registers its own handler; they do not fight, because each
            // only ever looks at (and closes) its own element.
            $(document).on("mousedown", function(e){
                if (panel.prop("hidden")) return;
                var target = $(e.target);
                if (target.closest(chat.chatElement + " .chatinputTd").length) return;
                if (target.closest(chat.chatElement + " .chatEmojiPanel").length) return;
                chat.toggleEmojiPanel(false);
            });
        },

        toggleEmojiPanel: function(show){
            var panel  = $(chat.chatElement + " .chatEmojiPanel");
            var button = $(chat.chatElement + " .chatEmojiButton");
            if (!panel.length) return;

            if (show === undefined) show = panel.prop("hidden");
            if (show === !panel.prop("hidden")) return;      // already in that state

            panel.prop("hidden", !show);
            button.attr("aria-expanded", show ? "true" : "false");

            // The picker takes its height out of the message list, so the list is a
            // different size either side of this — without re-pinning it, opening the
            // picker scrolls the newest messages out of sight.
            chat.scrollToBottom();

            if (show) $(chat.chatElement + " .chatinput").trigger("focus");
        },

        insertAtCaret: function(text){
            var input = $(chat.chatElement + " .chatinput");
            var el = input[0];
            if (!el) return;

            var start = el.selectionStart;
            var end   = el.selectionEnd;

            if (typeof start === "number" && typeof end === "number"){
                el.value = el.value.slice(0, start) + text + el.value.slice(end);
                var caret = start + text.length;
                el.setSelectionRange(caret, caret);
            }else{
                el.value += text;
            }
            el.focus();
        },

        // ":)" -> "🙂", but only where the token stands on its own. See EMOTICON_RE.
        applyEmoticons: function(text){
            return text.replace(EMOTICON_RE, function(match, lead, token){
                return lead + EMOTICONS[token];
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
            chat.scrollToBottom();
            chat.getLastTimeChecked();
        },

        onFocus: function(){
            if (window.windowEvents) windowEvents.chatfocus = true;
            // A player with the caret in the composer is in the conversation, whether
            // or not they have sent anything yet — go hot before they hit Enter, so
            // the reply they are about to provoke does not arrive on a 15s timer.
            chat.markHot();
        },

        onBlur: function(){
            if (window.windowEvents) windowEvents.chatfocus = false;
        },

        onKeyUp: function(e){
            e.stopPropagation();

            // Escape closes the picker rather than the message, so a player who opened
            // it by accident is not made to reach for the mouse to get out again.
            if (e.keyCode == 27){
                chat.toggleEmojiPanel(false);
                return;
            }

            if (e.keyCode == 13){
                var input = $(this);
                var value = chat.applyEmoticons(input.val());
                if (value.length === 0) return;

                input.val("");
                chat.toggleEmojiPanel(false);
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
                /* Only the in-game tab lights up. Global traffic is read in the lobby,
                   where the player already sees it, so highlighting #globalChatTab in
                   game.php just nagged about messages that were not about this game.
                   var thisChat = chat.gameid == 0 ? "globalChatTab" : "chatTab"; */
                var thisChat = chat.gameid == 0 ? null : "chatTab";
                if(thisChat && document.getElementById(thisChat) &&
                   !document.getElementById(thisChat).classList.contains("selected")){
                    document.getElementById(thisChat).classList.add("newMessage");
                }
            }

            if(scroll) c.scrollTop(c[0].scrollHeight);

            // Reported so requestChatdata can tell a live conversation from a quiet
            // one and pace the next poll accordingly.
            return scroll;
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
            chat.schedulePoll(0);   // first load: fill the panel immediately
        },

        // Something is happening in this chat — poll fast for the next HOT_DURATION.
        markHot: function(){
            chat.hotUntil = Date.now() + HOT_DURATION;
            chat.quietPolls = 0;
        },

        // The delay to use for the next poll, given how the last few went.
        pollInterval: function(){
            if(document.hidden) return POLL_HIDDEN;
            if(Date.now() < chat.hotUntil) return POLL_HOT;
            for(var i = 0; i < POLL_LADDER.length; i++){
                if(chat.quietPolls <= POLL_LADDER[i][0]) return POLL_LADDER[i][1];
            }
            return POLL_LADDER[POLL_LADDER.length - 1][1];
        },

        /* THE ONLY PLACE A POLL IS SCHEDULED, and it clears the pending timer before
           setting a new one. That is deliberate: it makes two concurrent poll chains
           impossible by construction.

           They used to be very possible, and the cost was not a one-off doubling. The
           visibilitychange handler cleared chat.polling on hide and then set it and
           called requestChatdata() directly on show, while the previous timeout was
           still pending — so both went on to schedule their own successors. The
           `requesting` guard collapsed them only when they fired at the same instant;
           a switch partway through the interval leaves them staggered, and both chains
           survive. Measured against the old file: three staggered tab switches left
           FOUR live chains, i.e. one extra permanent chain per switch, growing for as
           long as the tab stays open, with no symptom but the request count. */
        schedulePoll: function(delay){
            if(chat._pollTimer){
                clearTimeout(chat._pollTimer);
                chat._pollTimer = null;
            }
            if(!chat.polling) return;
            chat._pollTimer = setTimeout(function(){
                chat._pollTimer = null;
                chat.requestChatdata();
            }, delay === undefined ? chat.pollInterval() : delay);
        },

        removeNewMessageTag: function(){
            var el = chat.gameid == 0 ? "globalChatTab" : "chatTab";
            document.getElementById(el)?.classList.remove("newMessage");
        },

        /* ── playerChatInfo.php ───────────────────────────────────────────────
           The read/write mark for "when did this player last look at this chat",
           which drives the CHAT tab highlight. NOT on the poll loop — these fire on
           init, on tab switch and on send — but every call is two real queries, with
           no APCu fast path in front of them, so their retries must terminate.

           They did not. An expired session used to be answered with a redirect to
           index.php; jQuery followed it, failed to parse the HTML as JSON, and landed
           in the error handler, which retried on a fixed timer — forever, at two
           queries a time, for as long as the tab stayed open. playerChatInfo.php
           returns 401 JSON now, and the counter below caps every other failure mode
           the same way. A chat that gives up here still polls messages normally; all
           that is lost is the unread highlight. */
        timeCheckFailed: function(xhr, retry){
            // 401 is terminal, not transient — retrying cannot produce a session.
            if(xhr && xhr.status === 401) return;
            if(++chat.timeCheckFails > TIME_CHECK_MAX_FAILS) return;
            setTimeout(retry, 6000 * chat.timeCheckFails);
        },

        setLastTimeChecked: function(){
            if (!chat.polling) return;
            ajaxInterface.ajaxWithRetry({
                type: 'POST',
                url: 'playerChatInfo.php',
                dataType: 'json',
                data: { gameid: chat.gameid },
                success: chat.successSetLastTimeChecked,
                error: function(xhr){ chat.timeCheckFailed(xhr, chat.setLastTimeChecked); }
            }).fail(() => {});
        },

        getLastTimeChecked: function(){
            if (!chat.polling) return;
            ajaxInterface.ajaxWithRetry({
                type: 'GET',
                url: 'playerChatInfo.php',
                dataType: 'json',
                data: { gameid: chat.gameid },
                success: function(data){
                    if(!data || data.error){
                        if(data?.error) window.confirm.exception(data, function(){});
                        chat.timeCheckFailed(null, chat.getLastTimeChecked);
                        return;
                    }
                    chat.timeCheckFails = 0;
                    chat.lastTimeChecked = data.lastCheckGame;
                },
                error: function(xhr){ chat.timeCheckFailed(xhr, chat.getLastTimeChecked); }
            }).fail(() => {});
        },

        successSetLastTimeChecked: function(data){
            if(data.error) window.confirm.exception(data, function(){});
            else chat.timeCheckFails = 0;
        },

        // (successGetLastTimeChecked removed — getLastTimeChecked handles its own
        //  response inline, and the orphan copy here was never wired to anything.)

        submitChatMessage: function(message){
            chat.message = message;

            // Sending is the strongest possible signal that this chat is live.
            chat.markHot();

            // small retry limit and exponential backoff
            var attempt = 0;
            var maxAttempts = 4;

            function doSend(){
                if (!chat.polling) return; // avoid when shutting down
                ajaxInterface.ajaxWithRetry({
                    type: 'POST',
                    url: 'chatdata.php',
                    dataType: 'json',
                    data: { gameid: chat.gameid, message: message },
                    success: function(data){
                        chat.successSubmit(data);
                        /* Pull it straight back rather than waiting out the timer: the
                           sender seeing their own message land is what makes the chat
                           feel responsive, and this is the one poll guaranteed to miss
                           the APCu fast path (submitChatMessage has just raised the
                           cached id), so it is also the only one that costs a query. */
                        chat.schedulePoll(300);
                    },
                    error: function(jqXHR, textStatus){
                        attempt++;
                        if (attempt <= maxAttempts){
                            // backoff: 500ms, 1000ms, 2000ms, 4000ms ...
                            setTimeout(doSend, 500 * Math.pow(2, attempt-1));
                        } else {
                            console.error("Failed to submit chat message after " + maxAttempts + " attempts: " + textStatus);
                        }
                    }
                }).fail(() => {});
            }
            doSend();

            chat.setLastTimeChecked();
        },


        /* On the server this is the cheap one: chatdata.php answers an unchanged chat
           out of APCu and exits before it opens a database connection or takes the
           session lock, so the steady state here costs no queries at all. What it does
           still cost is a slot in ajaxInterface's single global request queue, which is
           shared with the gamedata poll — hence the pacing at the top of this file. */
        requestChatdata: function(){
            if(chat.requesting || !chat.polling) return;
            chat.requesting = true;

            ajaxInterface.ajaxWithRetry({
                type: 'GET',
                url: 'chatdata.php',
                dataType: 'json',
                timeout: REQUEST_TIMEOUT,
                data: { gameid: chat.gameid, lastid: chat.lastid },
                success: function(data){
                    chat.requesting = false;
                    if(!chat.polling) return;

                    var arrived = false;
                    if(data && data.error) window.confirm.exception(data, function(){});
                    else if(data) arrived = chat.parseChatData(data);

                    if(arrived) chat.markHot();
                    else chat.quietPolls++;

                    chat.schedulePoll();
                },
                error: function(){
                    chat.requesting = false;
                    // Treat a failed poll as a quiet one AND double the wait, so a server
                    // having a bad minute is not asked about it at the hot interval.
                    chat.quietPolls++;
                    chat.schedulePoll(chat.pollInterval() * 2);
                }
            }).fail(() => {});
        },

        successSubmit: function(data){
            if(data.error) window.confirm.exception(data, function(){});
        } // no comma here

    }//endof Chat

/* Re-pace on tab visibility rather than stopping and restarting.

   This handler is where the poll-chain leak lived — see schedulePoll for what it cost.
   chat.polling now means "this chat is alive" and is cleared only on unload; hiding
   and showing simply re-times the one chain through schedulePoll(), which clears the
   pending timer before setting the next and so cannot leave a second one behind. */
document.addEventListener("visibilitychange", function() {
    if (!chat.polling) return;
    if (document.hidden) {
        chat.schedulePoll(POLL_HIDDEN);
    } else {
        // Coming back is a sign of life, and the player wants to see what they missed.
        chat.markHot();
        chat.schedulePoll(0);
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

<?php // .fv-chat carries the panel styling; .chatcontainer is kept because combatLog.php
      // and declarations.php share it and chat.css still holds their base rules. ?>
<div class="chatcontainer fv-chat<?php echo $chatcompact ? ' fv-chat-compact' : ''; ?>">
<?php if ($chattitle !== null): ?>
    <div class="fv-chat-head">
        <span><?php echo htmlspecialchars($chattitle, ENT_QUOTES, 'UTF-8'); ?></span>
<?php   if ($chatmeta !== null): ?>
        <span class="fv-chat-meta"><?php echo htmlspecialchars($chatmeta, ENT_QUOTES, 'UTF-8'); ?></span>
<?php   endif; ?>
    </div>
<?php endif; ?>
    <div class="chatMessages"></div>
    <?php // A row of the flex column, between the log and the composer — NOT a popup
          // floating over them. Every one of the four hosts wraps the chat in a panel
          // that clips (`.chat-panel` needs it for its rounded corners), so an absolutely
          // positioned picker got its top cut off; in the flow it simply takes its space
          // from the message list for as long as it is open, and cannot escape anything.
          //
          // Filled in by chat.initEmoji(). Empty in the markup so the two includes on
          // game.php do not each ship the same ~70 glyphs down the wire. ?>
    <div class="chatEmojiPanel" hidden></div>
    <div class="chatinputTd">
        <div class="chatcomposer">
            <input class="chatinput" value="" name="chatinput" autocomplete="off"
                   placeholder="<?php echo htmlspecialchars($chatplaceholder, ENT_QUOTES, 'UTF-8'); ?>">
            <?php // A line-art face in currentColor rather than a 🙂 glyph: the colour
                  // emoji was the one saturated yellow object on an otherwise blue-grey
                  // bar, and it read as a message someone had already sent rather than as
                  // a control. Inline SVG so it inherits the accent and can brighten on
                  // hover — an <img> could do neither. ?>
            <button type="button" class="chatEmojiButton" aria-expanded="false" aria-label="Insert emoji">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="9.2" fill="none" stroke="currentColor" stroke-width="1.6"/>
                    <circle cx="9" cy="9.8" r="1.2" fill="currentColor"/>
                    <circle cx="15" cy="9.8" r="1.2" fill="currentColor"/>
                    <?php // Quadratic with the control point BELOW both ends (y grows
                          // downward), so the curve bulges into a smile. Its ends sit at
                          // x 7.8/16.2 to line up under the outer edge of each eye. ?>
                    <path d="M7.8 13.6 Q12 17.6 16.2 13.6" fill="none" stroke="currentColor"
                          stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>
</div>
<?php
// Consumed — game.php includes this file twice and the first include's title must not
// leak into the second.
unset($chattitle, $chatmeta, $chatplaceholder, $chatcompact);
?>




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
