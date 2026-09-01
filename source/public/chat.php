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

    /* Consecutive "database unreachable" replies (code 300) tolerated in silence before
       the panel admits to it. Every one of these used to raise a MODAL dialog — one per
       chat, per poll — so during the 2026-09-01 connection bursts players were buried in
       dialogs they could not dismiss faster than they arrived, for as long as the outage
       lasted.

       Four is deliberately past the point where a blip would have cleared: at the hot
       2s interval that is ~8 seconds, and further up the ladder longer still. Most
       connection shortages are over inside one poll and the player should never learn
       they happened. See CHAT_DB_RESILIENCE_PLAN.md item 7. */
    const DB_DOWN_NOTICE_AFTER = 4;

    /* ── Emoji ────────────────────────────────────────────────────────────────
       The picker's contents. Four short groups rather than one long grid: the
       panel is scrollable, and a labelled group is findable in a way that row 6
       of an undifferentiated wall of faces is not. Kept to what people actually
       reach for in a game chat — this is a wargame's message bar, not a keyboard.

       Each entry is [glyph, name]. The name is the hover tooltip and the key's
       aria-label; a PAIR rather than a parallel glyph->name map so that adding a
       glyph cannot leave it nameless, and so nothing depends on two copies of a
       variation-selector sequence ("⚔️" is two code points) matching by eye.

       ⚠️ Nothing newer than Emoji 12 (2019). Windows 10's Segoe UI Emoji stops
       there, so an Emoji 14 glyph — the U+1FAxx faces and hands — renders as a
       tofu box for a large share of the players. A saluting face (U+1FAE1) sat
       in Gestures until Aug 2026 doing exactly that; "o7" resolves to 🖖 now.

       Anything in here is stored as an HTML numeric entity by the server (see
       ChatManager::submitChatMessage), so it survives a 3-byte utf8 column. */
    const EMOJI_GROUPS = [
        { label: "Reactions", emoji: [
            ["🙂", "Slight smile"],    ["😀", "Grinning"],         ["😄", "Grinning, big eyes"],
            ["😆", "Squinting laugh"], ["🤣", "Rolling on floor"], ["😉", "Wink"],
            ["😍", "Heart eyes"],      ["😎", "Sunglasses"],       ["😛", "Tongue out"],
            ["🤔", "Thinking"],        ["😮", "Surprised"],        ["😐", "Neutral"],
            ["🙄", "Rolling eyes"],    ["😏", "Smirk"],            ["😴", "Sleeping"],
            ["🙁", "Slight frown"],    ["😢", "Crying"],           ["😭", "Sobbing"],
            ["😤", "Steam from nose"], ["😡", "Enraged"],          ["🤯", "Mind blown"],
            ["😱", "Screaming"],       ["🥳", "Partying"]
        ] },
        { label: "Gestures", emoji: [
            ["👍", "Thumbs up"],       ["👎", "Thumbs down"],      ["👌", "OK hand"],
            ["✌️", "Victory"],          ["🤞", "Fingers crossed"],  ["👏", "Clapping"],
            ["🙏", "Folded hands"],    ["💪", "Flexed biceps"],    ["🙌", "Raising hands"],
            ["🖖", "Vulcan salute"],   ["👀", "Eyes"],             ["🤷", "Shrug"],
            ["🤦", "Facepalm"],        ["👋", "Waving"],           ["🤝", "Handshake"]
        ] },
        { label: "Battle", emoji: [
            ["🚀", "Rocket"],          ["🛸", "Flying saucer"],    ["🛰️", "Satellite"],
            ["🌌", "Milky Way"],       ["⭐", "Star"],              ["💫", "Dizzy"],
            ["☄️", "Comet"],            ["🔥", "Fire"],             ["💥", "Explosion"],
            ["⚡", "High voltage"],     ["🎯", "Direct hit"],       ["🛡️", "Shield"],
            ["⚔️", "Crossed swords"],   ["💣", "Bomb"],             ["☠️", "Skull and bones"],
            ["💀", "Skull"],           ["🔧", "Wrench"],           ["⚙️", "Gear"],
            ["📡", "Dish antenna"],    ["🔋", "Battery"]
        ] },
        { label: "Signals", emoji: [
            ["✅", "Check mark"],       ["❌", "Cross mark"],        ["❓", "Question"],
            ["❗", "Exclamation"],      ["⚠️", "Warning"],           ["🎲", "Die"],
            ["🏆", "Trophy"],          ["🥇", "Gold medal"],       ["❤️", "Red heart"],
            ["💔", "Broken heart"],    ["🍺", "Beer"],             ["🎉", "Party popper"],
            ["⏳", "Hourglass"],        ["🆘", "SOS"]
        ] }
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
        "o7": "🖖"
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

    /* The same table read backwards: glyph -> the token a player would type for it.
       The picker's tooltips are the only place the emoticons are advertised, so this
       is how anyone finds out ":)" works at all.

       Several tokens share a glyph (":-)" and ":)" are both 🙂). The SHORTEST wins,
       and ties keep the one declared first — which is what makes 😛 show ":P" rather
       than ":p". Derived rather than written out so the two can never disagree. */
    const EMOTICON_FOR = (function(){
        var out = {};
        Object.keys(EMOTICONS).forEach(function(token){
            var glyph = EMOTICONS[token];
            if (!out[glyph] || token.length < out[glyph].length) out[glyph] = token;
        });
        return out;
    })();

    /* ── The key tooltip ──────────────────────────────────────────────────────
       Hovering a key names the glyph and, where one exists, shows the text token
       that produces it — the picker is the only place the emoticon table is ever
       advertised, so without this nobody finds out ":)" works.

       ONE element, on <body>, shared by both of game.php's includes and positioned
       `fixed` from the hovered key's own rect. It cannot live inside the picker:
       .chatEmojiPanel is overflow-y: auto, and a box that clips one axis clips
       both, so a tooltip drawn over the top row of keys lost its head — the same
       clipping that put the picker in the flex flow rather than in a popup. */
    var CAN_HOVER = !window.matchMedia || window.matchMedia("(hover: hover)").matches;

    function emojiTipEl(){
        if (!window.fvChatEmojiTip){
            window.fvChatEmojiTip = $(
                '<div class="fvChatEmojiTip" hidden>' +
                    '<span class="fvChatEmojiTipName"></span>' +
                    '<span class="fvChatEmojiTipCode" hidden></span>' +
                '</div>'
            ).appendTo(document.body);
        }
        return window.fvChatEmojiTip;
    }

    function showEmojiTip(key){
        var code = key.attr("data-emoji-code") || "";
        var tip  = emojiTipEl();

        tip.find(".fvChatEmojiTipName").text(key.attr("data-emoji-name") || "");
        tip.find(".fvChatEmojiTipCode").text(code).prop("hidden", !code);
        tip.prop("hidden", false);

        /* Measured only once it is displayable, then clamped to the window. Every
           host docks the chat to an edge: the top row of a picker at the foot of
           the page has room above it, but the first key of a narrow in-game panel
           is close enough to the left edge that a centred tooltip would hang off. */
        var r = key[0].getBoundingClientRect();
        var w = tip.outerWidth();
        var h = tip.outerHeight();

        var left = Math.min(r.left + r.width / 2 - w / 2, window.innerWidth - w - 4);
        var top  = r.top - h - 6;
        if (top < 4) top = r.bottom + 6;            // no room above: sit under the key

        tip.css({ left: Math.round(Math.max(4, left)) + "px", top: Math.round(top) + "px" });
    }

    function hideEmojiTip(){
        if (window.fvChatEmojiTip) window.fvChatEmojiTip.prop("hidden", true);
    }

    /* ── Shared poll coordinator ──────────────────────────────────────────────
       game.php includes this file TWICE — global chat and this game's chat — and each
       include is its own IIFE with its own `chat`. They used to poll independently:
       two requests every interval, two slots in ajaxInterface's single global request
       queue, asking two questions that fit comfortably in one. Both are answered out
       of the same APCu segment by the same process, so the second request bought
       nothing but a round trip.

       The loop therefore lives on `window`, not in the closure. Whichever include
       starts first builds it; the second joins. One timer, one request carrying every
       member's (gameid, lastid), one response fanned back out by gameid.

       games.php and gamelobby.php include this file once, so there the coordinator has
       a single member and behaves exactly as the old per-chat loop did.

       Pacing is shared because the request is: interval() takes the MINIMUM over the
       members, so a hot chat pulls its quiet neighbour along with it. That neighbour
       costs nothing extra — it is the same request either way — and it keeps its own
       quietPolls ladder, so it returns to its own rhythm as soon as the hot one cools. */
    var coordinator = window.fvChatPoll;
    if (!coordinator) {
        coordinator = window.fvChatPoll = {
            members: [],
            timer: null,
            requesting: false,

            add: function(c){
                if (coordinator.members.indexOf(c) === -1) coordinator.members.push(c);
            },

            live: function(){
                return coordinator.members.filter(function(c){ return c.polling; });
            },

            interval: function(){
                var live = coordinator.live();
                var best = null;
                for (var i = 0; i < live.length; i++){
                    var v = live[i].pollInterval();
                    if (best === null || v < best) best = v;
                }
                return best === null ? POLL_HIDDEN : best;
            },

            /* THE ONLY PLACE A POLL IS SCHEDULED, and it clears the pending timer
               before setting a new one. That is deliberate: it makes two concurrent
               poll chains impossible by construction.

               They used to be very possible, and the cost was not a one-off doubling.
               The visibilitychange handler cleared chat.polling on hide and then set
               it and called requestChatdata() directly on show, while the previous
               timeout was still pending — so both went on to schedule their own
               successors. The `requesting` guard collapsed them only when they fired
               at the same instant; a switch partway through the interval leaves them
               staggered, and both chains survive. Measured against the old file: three
               staggered tab switches left FOUR live chains, i.e. one extra permanent
               chain per switch, growing for as long as the tab stays open, with no
               symptom but the request count. */
            schedule: function(delay){
                if (coordinator.timer){
                    clearTimeout(coordinator.timer);
                    coordinator.timer = null;
                }
                if (!coordinator.live().length) return;
                coordinator.timer = setTimeout(function(){
                    coordinator.timer = null;
                    coordinator.poll();
                }, delay === undefined ? coordinator.interval() : delay);
            },

            /* A freshness report that cost us no request of our own.
               ─────────────────────────────────────────────────────────
               game.php polls gamedata.php every 4-8s while a game is live, and that
               file's APCu fast-poll reply now carries the same chat watermarks
               chatdata.php would have returned (see gamedata.php). Called from
               ajaxInterface.successRequest with that map.

               Three outcomes:
                 - a chat is BEHIND     -> real messages exist; fetch them right now
                 - every chat CONFIRMED unchanged -> bank it as a quiet poll and push
                   our own request out. This is what takes steady-state chat traffic on
                   an active game to zero: as long as gamedata reports in more often
                   than our own interval, our timer never reaches zero.
                 - anything unknown     -> learn nothing, change nothing

               Our own timer deliberately remains the floor. gamedata's interval decays
               to 30s, then 30 minutes, then stops; chat must not inherit that, so when
               the reports become rarer than our ladder, our timer simply fires first
               and we are back to polling normally. */
            observe: function(chatIds){
                if (!chatIds) return;
                var live = coordinator.live();
                if (!live.length) return;

                var behind = false, covered = 0;
                live.forEach(function(c){
                    var id = chatIds[c.gameid];
                    if (id === undefined || id === null) return;   // nothing said about this chat
                    covered++;
                    if (c.lastid < id){
                        behind = true;
                        c.markHot();
                    }
                });

                if (!covered) return;
                if (behind){ coordinator.schedule(0); return; }

                // Only a partial report — cannot conclude the page is up to date.
                if (covered < live.length) return;

                // Counted exactly as receive() counts an empty reply, so the ladder
                // backs off the same way whether the news came free or was paid for.
                live.forEach(function(c){ c.quietPolls++; });
                coordinator.schedule();
            },

            poll: function(){
                /* A request is already in flight — drop this tick rather than stack a
                   second one. Note this returns WITHOUT rescheduling, which is correct
                   but worth being explicit about: while a request is outstanding, the
                   next poll is owned by that request's success/error handler, both of
                   which call schedule(). ajaxWithRetry always ends in one or the other
                   (and REQUEST_TIMEOUT bounds the wait), so the chain cannot be lost. */
                if (coordinator.requesting) return;
                var live = coordinator.live();
                if (!live.length) return;       // everyone has unloaded; let the loop die
                coordinator.requesting = true;

                var spec = live.map(function(c){ return c.gameid + ":" + c.lastid; }).join(",");

                ajaxInterface.ajaxWithRetry({
                    type: 'GET',
                    url: 'chatdata.php',
                    dataType: 'json',
                    timeout: REQUEST_TIMEOUT,
                    data: { chats: spec },
                    success: function(data){
                        coordinator.requesting = false;
                        live.forEach(function(c){
                            // Numeric gameid indexes the string key fine — JS coerces.
                            c.receive(data ? data[c.gameid] : null);
                        });
                        coordinator.schedule();
                    },
                    error: function(){
                        coordinator.requesting = false;
                        // Treat a failed poll as a quiet one AND double the wait, so a
                        // server having a bad minute is not asked about it at the hot
                        // interval by every chat on the page.
                        live.forEach(function(c){ c.quietPolls++; });
                        coordinator.schedule(coordinator.interval() * 2);
                    }
                }).fail(() => {});
            }
        };
    }

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

        /* The timer and the in-flight request both belong to the coordinator now, not
           to this chat. Nor is there a stored jqXHR to abort: what
           ajaxInterface.ajaxWithRetry hands back is a plain $.Deferred().promise(),
           which has neither .abort() nor .readyState, so the abort this file used to
           attempt threw on every single poll and was swallowed by an empty catch. */

        hotUntil: 0,      // Date.now() before which we poll at POLL_HOT
        quietPolls: 0,    // consecutive polls that returned nothing; indexes POLL_LADDER

        /* Bounded because the endpoint behind it, playerChatInfo.php, has no APCu fast
           path — every call is two real queries. An unbounded retry there is a tab
           quietly generating database load for as long as it stays open. */
        timeCheckFails: 0,

        dbDownPolls: 0,        // consecutive code-300 replies; see DB_DOWN_NOTICE_AFTER
        dbNoticeShown: false,  // the one in-panel notice line is up (never more than one)

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

            // Stand down on navigation away. In-flight requests cannot be aborted (see
            // the note on the coordinator's timer), but dropping out of the members
            // list is enough: the shared loop stops of its own accord once nothing is
            // left polling, and re-times itself if the other chat on the page is.
            $(window).on('beforeunload.chat', function(){
                chat.polling = false;
                coordinator.schedule();
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
                group.emoji.forEach(function(entry){
                    var glyph = entry[0];
                    var name  = entry[1];
                    var code  = EMOTICON_FOR[glyph] || "";

                    /* Name and token ride on the button as data attributes. The hover
                       handler below is delegated, so the alternative is finding the
                       entry again by glyph on every mouseenter — the variation-selector
                       string comparison that the [glyph, name] pairs exist to avoid.

                       The aria-label carries the same two facts for a screen reader,
                       which gets nothing from a tooltip and nothing useful from the
                       glyph it used to be given. */
                    $('<button type="button" class="chatEmojiKey" tabindex="-1"></button>')
                        .attr("aria-label", "Insert " + name + (code ? ", typed as " + code : ""))
                        .attr("data-emoji-name", name)
                        .attr("data-emoji-code", code)
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

            /* Mouse only. A tap fires mouseenter with nothing to fire the matching
               mouseleave, so on a touch screen the tooltip would just stick over the
               keys; those players get the label through the aria-label instead. */
            if (CAN_HOVER){
                panel.on("mouseenter", ".chatEmojiKey", function(){ showEmojiTip($(this)); });
                panel.on("mouseleave", ".chatEmojiKey", hideEmojiTip);

                // The tooltip is placed from the key's VIEWPORT rect, so scrolling the
                // picker slides the key out from under a tooltip that stays put.
                panel.on("scroll", hideEmojiTip);
            }

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

            // Closing the picker leaves no key to mouseleave off.
            hideEmojiTip();

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

            // Reported so receive() can tell a live conversation from a quiet one and
            // pace the next poll accordingly.
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
            coordinator.add(chat);
            coordinator.schedule(0);   // first load: fill the panel immediately
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

        /* This chat's slice of a batched response — see coordinator.poll(). `slice` is
           whatever chatdata.php returned under this gameid: [] for "nothing new" (which
           is also what the APCu fast path returns without touching the database), a map
           of messages keyed by id, or that one chat's own error. */
        receive: function(slice){
            if(!chat.polling) return;

            var arrived = false;
            if(slice && slice.error){
                /* A database that is briefly unreachable is NOT a fault to interrupt the
                   player about — it is a condition to wait out, and the poll ladder is
                   already backing off on its own. Anything else still raises the dialog,
                   because anything else is a real fault the player should hear about. */
                if(chat.isDbUnavailable(slice)) chat.dbUnavailable();
                else {
                    chat.dbRecovered();
                    window.confirm.exception(slice, function(){});
                }
            }
            else if(slice){
                chat.dbRecovered();
                arrived = chat.parseChatData(slice);
            }

            if(arrived) chat.markHot();
            else chat.quietPolls++;
        },

        /* chatdata.php could not reach the database. Code 300 is the marker restored by
           ChatManager::initDBManager — before that the failure arrived as code 2
           (E_WARNING) from inside mysqli_connect and was indistinguishable from any other
           error, which is why every one of them became a dialog.

           Compared loosely: ChatManager stringifies the code into its hand-built JSON
           while chatdata.php's outer catch emits it as a number. */
        isDbUnavailable: function(slice){
            return !!slice && !!slice.error && slice.code == 300;
        },

        /* Stay completely silent for the first few, then say it once — in the panel, not
           in a dialog — and leave that single line alone until the chat recovers. The
           player can carry on reading, and nothing steals focus. Modelled on
           timeCheckFailed: bounded, non-modal, and it never interrupts. */
        dbUnavailable: function(){
            if(++chat.dbDownPolls < DB_DOWN_NOTICE_AFTER) return;
            if(chat.dbNoticeShown) return;

            chat.dbNoticeShown = true;
            var c = $(chat.chatElement + " .chatMessages");
            $('<div class="chatmessage chatSystemNotice">' +
              '<span class="chattext">Chat cannot reach the server. Still trying — ' +
              'messages will reappear on their own.</span></div>').appendTo(c);

            /* Only scroll if the player is already at the bottom; yanking the panel down
               while they are reading back is exactly the interruption this replaced. */
            if(c.length && c[0].scrollHeight - c.scrollTop() - c.outerHeight() < 40){
                c.scrollTop(c[0].scrollHeight);
            }
        },

        /* Anything that comes back normally clears the condition — including an empty
           "[]", which is a perfectly good sign the endpoint is answering again. */
        dbRecovered: function(){
            chat.dbDownPolls = 0;
            if(!chat.dbNoticeShown) return;
            chat.dbNoticeShown = false;
            $(chat.chatElement + " .chatSystemNotice").remove();
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
                        coordinator.schedule(300);
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


        successSubmit: function(data){
            if(data.error) window.confirm.exception(data, function(){});
        } // no comma here

    }//endof Chat

/* Re-pace on tab visibility rather than stopping and restarting.

   This handler is where the poll-chain leak lived — see coordinator.schedule for what
   it cost. chat.polling now means "this chat is alive" and is cleared only on unload;
   hiding and showing simply re-times the one shared chain through that method, which
   clears the pending timer before setting the next and so cannot leave a second behind.

   Both includes on game.php register a listener and both fire. That is harmless and
   intended: each marks its OWN chat hot, and the two schedule() calls collapse onto
   the single shared timer. */
document.addEventListener("visibilitychange", function() {
    if (!chat.polling) return;
    if (document.hidden) {
        coordinator.schedule(POLL_HIDDEN);
    } else {
        // Coming back is a sign of life, and the player wants to see what they missed.
        chat.markHot();
        coordinator.schedule(0);
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
