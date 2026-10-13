/*
 * docViewer.js - the Fiery Void DATA ARCHIVE: the Starter Guide, FAQ, Factions & Tiers,
 * Ship Options and Fleet Checker documents shown as one window over the current screen,
 * instead of each opening as a new web page. DOCUMENT_VIEWER_PLAN.md is the record.
 *
 * HOW A PAGE GETS IT
 * ------------------
 * Link styles/docViewer.css and load this file (game.php and gamelobby.php bundle it from their
 * debug script list; games.php, creategame.php and profile.php load it directly). Nothing else:
 * a plain left-click on any link to one of the five document pages - faq.php, faq.php#ladder,
 * ./factions-tiers.php and so on - opens the window at that entry instead of leaving the page.
 * An element with no href (game.php's USEFUL LINKS buttons) says which document it opens with
 * data-fvdoc="faq" or data-fvdoc="faq#interception". Ctrl/Shift/middle-click still open the page
 * in a new tab, and the five pages themselves still exist: each is now this same viewer filling
 * the page (PAGE MODE, docs/docPage.php), so an old bookmark or a link pasted in Discord still
 * lands on the right entry.
 *
 * WHERE THE WORDS LIVE
 * --------------------
 * docs/*.html, one file per document. Each <section data-key data-title data-group> is one entry
 * in the list on the left; the file headers explain the rest. The window fetches a document the
 * first time it is needed (cache: no-cache, so an edit is picked up on the next page load without
 * a version string), and PAGE MODE reads it from a <template> the PHP page embeds instead.
 *
 * WHY IT IS BUILT THE WAY IT IS
 * -----------------------------
 * - Plain DOM, no jQuery and no React: it has to run on five pages that load different stacks.
 * - Only the open entry is ever in the page. The documents are parsed into inert <template>s, and
 *   an entry is cloned in when it is shown - so no id inside a document can collide with the host
 *   page's own ids (game.php looks a great many up by id), and nothing in a hidden entry loads.
 *   For the same reason the documents use data-anchor, never id, for the places a link can land.
 * - While the window is open it owns the keyboard: key events are stopped at its edge, so typing
 *   in the search box cannot fire game.php's map hotkeys, and Escape closes it rather than a lobby
 *   window underneath. It also pushes one history entry, so a phone's Back button closes the
 *   window instead of leaving the game.
 */
(function (window, document) {
    "use strict";

    if (window.fvDocs) return; // loaded twice (a bundle and a tag) - the first copy wins

    /* Image placeholders: the grey "image pending" boxes that mark where a picture is wanted. They
       show by default so the gaps are easy to find; set this to false to hide any that have not
       been filled yet (a deploy that should not show them), without touching the documents. */
    var SHOW_IMAGE_PLACEHOLDERS = true;

    /* The five documents, in tab order. `page` is the standalone page (and the name a link uses),
       `src` the content file. */
    var DOCS = [
        { key: "starter",  page: "starterGuide.php",              src: "docs/starter-guide.html",  title: "Starter Guide",    heading: "How to Play Fiery Void" },
        { key: "faq",      page: "faq.php",                       src: "docs/faq.html",            title: "FAQ",              heading: "Fiery Void FAQ" },
        { key: "factions", page: "factions-tiers.php",            src: "docs/factions-tiers.html", title: "Factions & Tiers", heading: "Factions & Tiers" },
        { key: "ammo",     page: "ammo-options-enhancements.php", src: "docs/ammo-options.html",   title: "Ship Options",     heading: "Ship Options" },
        { key: "fleet",    page: "fleetchecker.php",              src: "docs/fleet-checker.html",  title: "Fleet Checker",    heading: "Fleet Checker Rules" }
    ];
    var DOC = {};
    var PAGE_DOC = {};
    DOCS.forEach(function (d) {
        DOC[d.key] = d;
        PAGE_DOC[d.page.toLowerCase()] = d.key;
    });

    // A link to one of the document pages, from any folder depth: faq.php, ./faq.php#x, /faq.php.
    var PAGE_RX = /(?:^|\/)([\w-]+\.php)(?:\?[^#]*)?(?:#(.*))?$/i;

    var STORE_KEY = "fvDocs.last"; // sessionStorage: the entry last read in each document

    var ui = null;              // the window's parts, once built
    var mode = null;            // "overlay" or "page"
    var isOpen = false;
    var cur = { doc: null, key: null };
    var docs = {};              // doc key -> { status, promise, sections, byKey, anchors }
    var last = readLast();      // doc key -> entry key
    var collapsed = {};         // doc key -> { group label: true } for folded list groups
    var query = "";             // the live search, normalised; "" when the list is showing
    var navToken = 0;           // drops the reply of a navigation that has been overtaken
    var searchTimer = 0;
    var spyFrame = 0;
    var historyPushed = false;
    var returnFocus = null;
    var fontsAsked = false;

    /* ══ Public ══════════════════════════════════════════════════════════════════════════════ */

    window.fvDocs = {
        /* open("faq"), open("faq", "ladder"), open("faq#ladder"). In PAGE MODE it navigates the
           page's own viewer instead of opening a second one. */
        open: function (doc, anchor) {
            var t = splitTarget(doc, anchor);
            if (mode === "page") return go(t.doc, t.anchor, { resume: !t.anchor });
            openOverlay(t.doc, t.anchor);
        },
        close: function () { close(false); },
        isOpen: function () { return isOpen; },
        docs: DOCS
    };

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
    else init();

    function init() {
        // Capture phase: a modal that stops click propagation (or a jQuery handler bound to the
        // link) must not get between a document link and the window.
        document.addEventListener("click", onDocumentClick, true);
        window.addEventListener("popstate", function () {
            if (isOpen && mode === "overlay" && historyPushed) {
                historyPushed = false;
                close(true);
            }
        });

        var mount = document.getElementById("fvdPage");
        if (mount) startPageMode(mount);
    }

    /* ══ Opening and closing ═════════════════════════════════════════════════════════════════ */

    function openOverlay(docKey, anchor) {
        if (!ui) build("overlay", document.body);
        if (!isOpen) {
            isOpen = true;
            returnFocus = document.activeElement;
            ui.root.hidden = false;
            // Two frames, so the opening transition runs from the hidden state.
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { if (isOpen) ui.root.classList.add("is-open"); });
            });
            document.documentElement.classList.add("fvd-lock");
            window.addEventListener("keydown", onWindowKey, true);
            window.addEventListener("keyup", onWindowKey, true);
            try {
                history.pushState({ fvdoc: 1 }, "");
                historyPushed = true;
            } catch (e) { historyPushed = false; }
            loadFonts();
        }
        go(docKey, anchor, { resume: !anchor });
        ui.panel.focus({ preventScroll: true });
    }

    function close(fromHistory) {
        if (!isOpen || mode !== "overlay") return;
        isOpen = false;
        ui.root.classList.remove("is-open", "is-toc-open");
        ui.root.hidden = true;
        document.documentElement.classList.remove("fvd-lock");
        window.removeEventListener("keydown", onWindowKey, true);
        window.removeEventListener("keyup", onWindowKey, true);
        if (!fromHistory && historyPushed) {
            historyPushed = false;
            try { history.back(); } catch (e) { }
        }
        if (returnFocus && returnFocus.focus && document.contains(returnFocus)) {
            try { returnFocus.focus({ preventScroll: true }); } catch (e) { }
        }
        returnFocus = null;
    }

    function startPageMode(mount) {
        var docKey = DOC[mount.getAttribute("data-doc")] ? mount.getAttribute("data-doc") : "faq";
        build("page", mount);
        isOpen = true;
        ui.root.hidden = false;
        ui.root.classList.add("is-open");
        window.addEventListener("hashchange", function () {
            var a = decodeHash(location.hash);
            if (a) go(cur.doc, a, {});
        });
        go(docKey, decodeHash(location.hash), { resume: false });
    }

    /* The page's own fonts: Orbitron and Bruno Ace SC come in with gamesNew.css, which game.php
       does not link (logPanel.css deliberately avoids them there). Asked for once, the first time
       the window opens on such a page. */
    function loadFonts() {
        if (fontsAsked) return;
        fontsAsked = true;
        if (document.querySelector('link[href*="gamesNew.css"]')) return;
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Orbitron&display=swap";
        document.head.appendChild(link);
    }

    /* ══ Links from the host page ════════════════════════════════════════════════════════════ */

    function onDocumentClick(e) {
        if (e.defaultPrevented || e.button !== 0) return;
        var el = e.target && e.target.closest ? e.target.closest("[data-fvdoc], a[href]") : null;
        if (!el || (ui && ui.root.contains(el))) return; // the window handles its own links
        var target = null;
        if (el.hasAttribute("data-fvdoc")) {
            target = splitTarget(el.getAttribute("data-fvdoc"));
        } else {
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return; // a new tab: the page
            target = parseDocHref(el.getAttribute("href"));
        }
        if (!target) return;
        e.preventDefault();
        window.fvDocs.open(target.doc, target.anchor);
    }

    // "faq.php#x" -> {doc: "faq", anchor: "x"}, or null if the link is not to a document page.
    function parseDocHref(href) {
        var m = PAGE_RX.exec(href || "");
        if (!m) return null;
        var docKey = PAGE_DOC[m[1].toLowerCase()];
        return docKey ? { doc: docKey, anchor: m[2] ? decodeHash(m[2]) : null } : null;
    }

    // ("faq#x") or ("faq", "x") -> {doc, anchor}
    function splitTarget(doc, anchor) {
        doc = String(doc || "faq");
        var i = doc.indexOf("#");
        if (i >= 0) {
            anchor = anchor || doc.slice(i + 1);
            doc = doc.slice(0, i);
        }
        return { doc: DOC[doc] ? doc : "faq", anchor: anchor || null };
    }

    function decodeHash(h) {
        h = String(h || "").replace(/^#/, "");
        try { h = decodeURIComponent(h); } catch (e) { }
        return h || null;
    }

    /* ══ Loading a document ══════════════════════════════════════════════════════════════════ */

    function loadDoc(docKey) {
        var rec = docs[docKey];
        if (rec && rec.status !== "error") return rec.promise;
        rec = docs[docKey] = { status: "loading" };
        var embedded = document.getElementById("fvdSrc-" + docKey);
        var got;
        if (embedded && embedded.content) {
            got = Promise.resolve(embedded.content.cloneNode(true));
        } else {
            got = fetch(DOC[docKey].src, { cache: "no-cache", credentials: "same-origin" })
                .then(function (res) {
                    if (!res.ok) throw new Error("HTTP " + res.status);
                    return res.text();
                })
                .then(function (html) {
                    var tpl = document.createElement("template"); // inert: nothing in it loads or runs
                    tpl.innerHTML = html;
                    return tpl.content;
                });
        }
        rec.promise = got.then(function (frag) {
            indexDoc(rec, frag);
            rec.status = "ready";
            return rec;
        }, function (err) {
            rec.status = "error";
            throw err;
        });
        return rec.promise;
    }

    function indexDoc(rec, frag) {
        rec.sections = [];
        rec.byKey = {};
        rec.anchors = {};
        var nodes = frag.querySelectorAll("section[data-key]");
        for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            var s = {
                key: n.getAttribute("data-key"),
                title: n.getAttribute("data-title") || n.getAttribute("data-key"),
                group: n.getAttribute("data-group") || "",
                node: n,
                index: i,
                raw: null,
                lower: null
            };
            rec.sections.push(s);
            rec.byKey[s.key] = s;
            rec.anchors[s.key.toLowerCase()] = { s: s, a: null };
            (n.getAttribute("data-alias") || "").split(/[\s,]+/).forEach(function (a) {
                if (a) rec.anchors[a.toLowerCase()] = { s: s, a: null };
            });
            var marks = n.querySelectorAll("[data-anchor]");
            for (var j = 0; j < marks.length; j++) {
                var a = marks[j].getAttribute("data-anchor");
                if (a && !rec.anchors[a.toLowerCase()]) rec.anchors[a.toLowerCase()] = { s: s, a: a };
            }
        }
    }

    // An entry's text, for searching - without the image-placeholder captions.
    function sectionText(s) {
        if (s.raw === null) {
            var copy = s.node.cloneNode(true);
            var ph = copy.querySelectorAll(".fvd-fig--ph .fvd-ph");
            for (var i = 0; i < ph.length; i++) ph[i].parentNode.removeChild(ph[i]);
            s.raw = (copy.textContent || "").replace(/\s+/g, " ").trim();
            s.lower = s.raw.toLowerCase();
        }
        return s;
    }

    /* ══ Building the window ═════════════════════════════════════════════════════════════════ */

    var ICON_SEARCH = '<svg viewBox="0 0 16 16" aria-hidden="true"><circle cx="6.8" cy="6.8" r="4.6" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M10.3 10.3l3.9 3.9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
    var ICON_MENU = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2 4h12M2 8h12M2 12h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
    var ICON_LINK = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M6.6 9.4l2.8-2.8M5.2 7.4L3.8 8.8a2.4 2.4 0 003.4 3.4l1.4-1.4M10.8 8.6l1.4-1.4a2.4 2.4 0 00-3.4-3.4L7.4 5.2" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
    var EMBLEM = '<svg class="fvd-emblem" viewBox="0 0 32 32" aria-hidden="true"><path d="M16 2.5L27.7 9.25v13.5L16 29.5 4.3 22.75V9.25z" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M16 9l6.1 3.5v7L16 23l-6.1-3.5v-7z" fill="currentColor" opacity=".28"/><circle cx="16" cy="16" r="2.1" fill="currentColor"/></svg>';

    function build(asMode, host) {
        mode = asMode;
        var root = document.createElement("div");
        root.className = "fvd";
        root.setAttribute("data-mode", mode);
        root.hidden = true;

        var tabs = DOCS.map(function (d) {
            return '<button type="button" class="fvd-tab" role="tab" aria-selected="false" data-doc="' + d.key + '">' + esc(d.title) + '</button>';
        }).join("");

        root.innerHTML =
            (mode === "overlay" ? '<div class="fvd-scrim" data-fvd-close></div>' : '') +
            '<section class="fvd-panel" tabindex="-1"' + (mode === "overlay" ? ' role="dialog" aria-modal="true"' : '') + ' aria-labelledby="fvdDocTitle">' +
              '<i class="fvd-bk fvd-bk--tl"></i><i class="fvd-bk fvd-bk--tr"></i><i class="fvd-bk fvd-bk--bl"></i><i class="fvd-bk fvd-bk--br"></i>' +
              '<header class="fvd-head">' +
                '<div class="fvd-brand">' + EMBLEM +
                  '<div class="fvd-brand-text"><span class="fvd-kicker">Fiery Void <b>//</b> Data Archive</span>' +
                  '<h2 class="fvd-doc-title" id="fvdDocTitle"></h2></div>' +
                '</div>' +
                '<nav class="fvd-tabs" role="tablist" aria-label="Documents">' + tabs + '</nav>' +
                (mode === "overlay" ? '<button type="button" class="fvd-close" data-fvd-close aria-label="Close" title="Close (Esc)">&times;</button>' : '') +
              '</header>' +
              '<div class="fvd-body">' +
                '<aside class="fvd-side" aria-label="Contents">' +
                  '<div class="fvd-search">' + ICON_SEARCH +
                    '<input type="search" class="fvd-search-input" placeholder="Search all documents" aria-label="Search all documents" autocomplete="off" spellcheck="false">' +
                  '</div>' +
                  '<nav class="fvd-toc" aria-label="Entries"></nav>' +
                  '<div class="fvd-results" hidden></div>' +
                  '<div class="fvd-side-foot"><span class="fvd-side-count"></span><span class="fvd-side-hint">Esc closes</span></div>' +
                '</aside>' +
                '<div class="fvd-drawer-scrim" data-fvd-drawer></div>' +
                '<main class="fvd-main">' +
                  '<div class="fvd-mainbar">' +
                    '<button type="button" class="fvd-contents-btn" data-fvd-drawer aria-label="Contents">' + ICON_MENU + '<span>Contents</span></button>' +
                    '<span class="fvd-mainbar-title"></span>' +
                  '</div>' +
                  '<div class="fvd-scroll">' +
                    '<p class="fvd-sr" aria-live="polite"></p>' +
                    '<article class="fvd-article">' +
                      '<header class="fvd-article-head">' +
                        '<div class="fvd-crumbs"></div>' +
                        '<h1 class="fvd-title"></h1>' +
                        '<div class="fvd-article-meta"><span class="fvd-index"></span>' +
                          '<button type="button" class="fvd-copylink" title="Copy a link to this entry">' + ICON_LINK + '<span>Copy link</span></button>' +
                        '</div>' +
                      '</header>' +
                      '<div class="fvd-content"></div>' +
                      '<footer class="fvd-pager"></footer>' +
                    '</article>' +
                  '</div>' +
                '</main>' +
              '</div>' +
            '</section>';

        host.appendChild(root);
        ui = {
            root: root,
            panel: root.querySelector(".fvd-panel"),
            docTitle: root.querySelector(".fvd-doc-title"),
            tabs: root.querySelectorAll(".fvd-tab"),
            search: root.querySelector(".fvd-search-input"),
            toc: root.querySelector(".fvd-toc"),
            results: root.querySelector(".fvd-results"),
            count: root.querySelector(".fvd-side-count"),
            live: root.querySelector(".fvd-sr"),
            barTitle: root.querySelector(".fvd-mainbar-title"),
            scroll: root.querySelector(".fvd-scroll"),
            article: root.querySelector(".fvd-article"),
            crumbs: root.querySelector(".fvd-crumbs"),
            title: root.querySelector(".fvd-title"),
            index: root.querySelector(".fvd-index"),
            copy: root.querySelector(".fvd-copylink"),
            content: root.querySelector(".fvd-content"),
            pager: root.querySelector(".fvd-pager")
        };
        if (mode === "page") ui.root.querySelector(".fvd-side-hint").textContent = "";

        root.addEventListener("click", onClick);
        root.addEventListener("keydown", onKey);
        if (mode === "overlay") {
            // Past the window's edge, no key reaches the host page: the map's hotkeys, the combat
            // log's arrows and the lobby's own Escape all stay quiet while it is open.
            root.addEventListener("keyup", stop);
            root.addEventListener("keypress", stop);
        }
        ui.search.addEventListener("input", function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(runSearch, 140);
        });
        ui.scroll.addEventListener("scroll", function () {
            if (!spyFrame) spyFrame = requestAnimationFrame(spy);
        }, { passive: true });
    }

    function stop(e) { e.stopPropagation(); }

    /* ══ Input ═══════════════════════════════════════════════════════════════════════════════ */

    function onClick(e) {
        var t = e.target;
        var el;

        if ((el = t.closest("[data-fvd-close]"))) return close(false);
        if ((el = t.closest("[data-fvd-drawer]"))) return toggleDrawer();

        if ((el = t.closest(".fvd-tab"))) {
            if (el.getAttribute("data-doc") !== cur.doc) go(el.getAttribute("data-doc"), null, { resume: true });
            return;
        }
        if ((el = t.closest(".fvd-toc-grouphead"))) return toggleGroup(el);
        if ((el = t.closest(".fvd-toc-sub"))) {
            scrollToSub(+el.getAttribute("data-sub"));
            closeDrawer();
            return;
        }
        if ((el = t.closest(".fvd-toc-item"))) return go(cur.doc, el.getAttribute("data-key"), {});
        if ((el = t.closest(".fvd-result"))) return go(el.getAttribute("data-doc"), el.getAttribute("data-key"), { hit: true });
        if ((el = t.closest(".fvd-pager-btn"))) return go(cur.doc, el.getAttribute("data-key"), {});
        if ((el = t.closest(".fvd-copylink"))) return copyLink();
        if ((el = t.closest("[data-fvd-retry]"))) {
            var doc = cur.doc;
            delete docs[doc];
            return go(doc, null, { resume: true });
        }

        if ((el = t.closest(".fvd-content a[href]"))) {
            var href = el.getAttribute("href");
            if (href.charAt(0) === "#") {
                e.preventDefault();
                return go(cur.doc, decodeHash(href), {});
            }
            var target = parseDocHref(href);
            if (target && !(e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0)) {
                e.preventDefault();
                go(target.doc, target.anchor, { resume: !target.anchor });
            }
        }
    }

    function onKey(e) {
        if (e.key === "Escape") {
            if (ui.root.classList.contains("is-toc-open")) {
                closeDrawer();
                e.preventDefault();
            } else if (e.target === ui.search && ui.search.value) {
                ui.search.value = "";
                runSearch();
                e.preventDefault();
            } else if (mode === "overlay") {
                close(false);
                e.preventDefault();
            }
        } else if (e.key === "Tab" && mode === "overlay") {
            trapTab(e);
        } else if ((e.key === "ArrowDown" || e.key === "ArrowUp") && e.target.closest &&
            e.target.closest(".fvd-toc, .fvd-results")) {
            moveInList(e);
        } else if (e.key === "ArrowDown" && e.target === ui.search) {
            var first = ui.root.querySelector(ui.results.hidden ? ".fvd-toc-item" : ".fvd-result");
            if (first) {
                first.focus();
                e.preventDefault();
            }
        }
        if (mode === "overlay") e.stopPropagation();
    }

    // While open, focus can still sit outside the window (on <body>, say, after a click on the
    // scrim) - such keys are dropped here, and Escape still closes.
    function onWindowKey(e) {
        if (!isOpen || ui.root.contains(e.target)) return;
        e.stopPropagation();
        if (e.type === "keydown" && e.key === "Escape") {
            e.preventDefault();
            close(false);
        }
    }

    function trapTab(e) {
        var f = focusables();
        if (!f.length) return;
        var first = f[0];
        var lastEl = f[f.length - 1];
        var active = document.activeElement;
        if (e.shiftKey && (active === first || active === ui.panel)) {
            lastEl.focus();
            e.preventDefault();
        } else if (!e.shiftKey && active === lastEl) {
            first.focus();
            e.preventDefault();
        }
    }

    function focusables() {
        var all = ui.panel.querySelectorAll('a[href], button:not([disabled]), input, [tabindex]:not([tabindex="-1"])');
        var out = [];
        for (var i = 0; i < all.length; i++) {
            var n = all[i];
            if (n.offsetParent !== null && getComputedStyle(n).visibility !== "hidden") out.push(n);
        }
        return out;
    }

    function moveInList(e) {
        var list = e.target.closest(".fvd-toc, .fvd-results");
        var items = [].slice.call(list.querySelectorAll(".fvd-toc-grouphead, .fvd-toc-item, .fvd-toc-sub, .fvd-result"))
            .filter(function (n) { return n.offsetParent !== null; });
        var i = items.indexOf(document.activeElement);
        if (i < 0) return;
        var next = items[i + (e.key === "ArrowDown" ? 1 : -1)];
        if (next) {
            next.focus();
            e.preventDefault();
        } else if (e.key === "ArrowUp") {
            ui.search.focus();
            e.preventDefault();
        }
    }

    /* ══ Navigation ══════════════════════════════════════════════════════════════════════════ */

    /* Show an entry: go("faq", "ladder"), or go("faq", null, {resume: true}) for where the reader
       last was. opts.hit scrolls to the first search match. */
    function go(docKey, anchor, opts) {
        opts = opts || {};
        var token = ++navToken;
        var docChanged = docKey !== cur.doc;
        cur.doc = docKey;
        paintDoc(docKey);
        if (docChanged) {
            cur.key = null;
            ui.toc.innerHTML = "";
            renderStatus("loading");
        }
        loadDoc(docKey).then(function (rec) {
            if (token !== navToken) return;
            var hit = anchor ? rec.anchors[String(anchor).toLowerCase()] : null;
            if (!hit && opts.resume && rec.byKey[last[docKey]]) hit = { s: rec.byKey[last[docKey]], a: null };
            if (!hit) hit = { s: rec.sections[0], a: null };
            if (!hit.s) return renderStatus("empty");
            if (docChanged || !ui.toc.firstChild) renderToc(rec);
            renderEntry(rec, hit.s, hit.a, opts);
        }, function () {
            if (token === navToken) renderStatus("error");
        });
    }

    function paintDoc(docKey) {
        var d = DOC[docKey];
        ui.docTitle.textContent = d.heading;
        for (var i = 0; i < ui.tabs.length; i++) {
            var on = ui.tabs[i].getAttribute("data-doc") === docKey;
            ui.tabs[i].setAttribute("aria-selected", on ? "true" : "false");
            ui.tabs[i].classList.toggle("is-active", on);
            ui.tabs[i].tabIndex = on ? 0 : -1;
        }
        var active = ui.root.querySelector(".fvd-tab.is-active");
        if (active && active.scrollIntoView) active.scrollIntoView({ block: "nearest", inline: "nearest" });
        if (mode === "page") document.title = "Fiery Void - " + d.title;
    }

    function renderStatus(what) {
        ui.crumbs.textContent = DOC[cur.doc].title;
        ui.index.textContent = "";
        ui.copy.hidden = true;
        ui.pager.innerHTML = "";
        ui.barTitle.textContent = "";
        if (what === "loading") {
            ui.title.textContent = "Retrieving records";
            ui.content.innerHTML = '<p class="fvd-status"><span class="fvd-cursor"></span>Accessing archive&hellip;</p>';
        } else if (what === "error") {
            ui.title.textContent = "Signal lost";
            ui.content.innerHTML = '<p class="fvd-status fvd-status--error">This document could not be loaded.</p>' +
                '<p><button type="button" class="fvd-btn" data-fvd-retry>Try again</button> ' +
                '<a class="fvd-btn fvd-btn--ghost" target="_blank" rel="noopener" href="' + DOC[cur.doc].page + '">Open it as a page</a></p>';
        } else {
            ui.title.textContent = "No entries";
            ui.content.innerHTML = "";
        }
    }

    function renderToc(rec) {
        var html = "";
        var group = null;
        var fold = collapsed[cur.doc] || {};
        var counts = {};
        rec.sections.forEach(function (s) { counts[s.group] = (counts[s.group] || 0) + 1; });
        rec.sections.forEach(function (s, i) {
            if (s.group !== group) {
                if (group !== null) html += "</ol></div>";
                group = s.group;
                var shut = !!fold[group];
                html += '<div class="fvd-toc-group' + (shut ? " is-collapsed" : "") + '" data-group="' + esc(group) + '">';
                if (group) {
                    html += '<button type="button" class="fvd-toc-grouphead" aria-expanded="' + (!shut) + '">' +
                        '<span class="fvd-toc-grouplabel">' + esc(group) + '</span><span class="fvd-toc-count">' + counts[group] + '</span></button>';
                }
                html += '<ol class="fvd-toc-list">';
            }
            html += '<li class="fvd-toc-entry" data-key="' + esc(s.key) + '"><button type="button" class="fvd-toc-item" data-key="' + esc(s.key) + '">' +
                '<span class="fvd-toc-num">' + pad(i + 1) + '</span><span class="fvd-toc-label">' + esc(s.title) + '</span></button></li>';
        });
        if (group !== null) html += "</ol></div>";
        ui.toc.innerHTML = html;
        setCount(rec.sections.length, "entry", "entries");
    }

    function setCount(n, one, many) {
        ui.count.textContent = n + " " + (n === 1 ? one : many);
    }

    function toggleGroup(head) {
        var box = head.parentNode;
        var shut = !box.classList.contains("is-collapsed");
        box.classList.toggle("is-collapsed", shut);
        head.setAttribute("aria-expanded", String(!shut));
        var fold = collapsed[cur.doc] = collapsed[cur.doc] || {};
        fold[box.getAttribute("data-group")] = shut;
    }

    function renderEntry(rec, s, anchor, opts) {
        cur.key = s.key;
        last[cur.doc] = s.key;
        writeLast();

        var d = DOC[cur.doc];
        ui.crumbs.innerHTML = esc(d.title) + (s.group ? ' <b>//</b> ' + esc(s.group) : "");
        ui.title.textContent = s.title;
        ui.index.textContent = "Entry " + pad(s.index + 1) + " / " + pad(rec.sections.length);
        ui.barTitle.textContent = s.title;
        ui.live.textContent = d.title + ": " + s.title;
        ui.copy.hidden = false;
        ui.copy.classList.remove("is-done");

        // The entry itself: a clone of the inert original, dressed for display.
        var copy = s.node.cloneNode(true);
        var frag = document.createDocumentFragment();
        while (copy.firstChild) frag.appendChild(copy.firstChild);
        ui.content.textContent = "";
        ui.content.appendChild(frag);
        dress(ui.content);

        // Previous / next, across groups, in document order.
        var prev = rec.sections[s.index - 1];
        var next = rec.sections[s.index + 1];
        ui.pager.innerHTML =
            (prev ? '<button type="button" class="fvd-pager-btn fvd-pager-btn--prev" data-key="' + esc(prev.key) + '"><span class="fvd-pager-dir">&#9664; Previous</span><span class="fvd-pager-name">' + esc(prev.title) + '</span></button>' : '<span></span>') +
            (next ? '<button type="button" class="fvd-pager-btn fvd-pager-btn--next" data-key="' + esc(next.key) + '"><span class="fvd-pager-dir">Next &#9654;</span><span class="fvd-pager-name">' + esc(next.title) + '</span></button>' : '<span></span>');

        markToc(s);
        ui.article.classList.remove("is-entering");
        void ui.article.offsetWidth; // restart the fade
        ui.article.classList.add("is-entering");

        ui.scroll.scrollTop = 0;
        var hits = query ? highlight(ui.content, query) : 0;
        var landing = anchor ? ui.content.querySelector('[data-anchor="' + cssEscape(anchor) + '"]') : null;
        if (landing) {
            scrollToEl(landing);
        } else if (hits && opts.hit) {
            scrollToEl(ui.content.querySelector("mark.fvd-hit"), true);
        }
        spy();
        closeDrawer();
        if (mode === "page") {
            try { history.replaceState(null, "", d.page + "#" + encodeURIComponent(anchor || s.key)); } catch (e) { }
        }
    }

    // Display-only touches on a freshly cloned entry.
    function dress(box) {
        var i, n;
        var figs = box.querySelectorAll(".fvd-fig--ph");
        for (i = 0; i < figs.length; i++) {
            n = figs[i];
            if (!SHOW_IMAGE_PLACEHOLDERS) {
                n.parentNode.removeChild(n);
                continue;
            }
            var ph = n.querySelector(".fvd-ph");
            if (ph && !ph.querySelector(".fvd-ph-tag")) {
                var tag = document.createElement("span");
                tag.className = "fvd-ph-tag";
                tag.textContent = "Image pending";
                ph.insertBefore(tag, ph.firstChild);
                if (n.getAttribute("data-img")) {
                    var file = document.createElement("code");
                    file.className = "fvd-ph-file";
                    file.textContent = n.getAttribute("data-img");
                    ph.appendChild(file);
                }
            }
            var ratio = n.getAttribute("data-ratio");
            if (ratio && ph) ph.style.aspectRatio = ratio.replace(":", " / ");
        }
        var tables = box.querySelectorAll("table");
        for (i = 0; i < tables.length; i++) {
            n = tables[i];
            if (n.parentNode.classList && n.parentNode.classList.contains("fvd-table-wrap")) continue;
            var wrap = document.createElement("div");
            wrap.className = "fvd-table-wrap";
            n.parentNode.insertBefore(wrap, n);
            wrap.appendChild(n);
        }
        var links = box.querySelectorAll('a[href^="http"]');
        for (i = 0; i < links.length; i++) {
            links[i].target = "_blank";
            links[i].rel = "noopener noreferrer";
        }
        var imgs = box.querySelectorAll("img");
        for (i = 0; i < imgs.length; i++) {
            if (!imgs[i].hasAttribute("loading")) imgs[i].setAttribute("loading", "lazy");
            imgs[i].setAttribute("decoding", "async");
        }
    }

    /* The open entry in the list: highlighted, its group unfolded, its sub-headings listed under
       it (the h3s of the entry - the second level of the old tables of contents). */
    function markToc(s) {
        var old = ui.toc.querySelectorAll(".fvd-toc-item.is-active, .fvd-toc-subs");
        for (var i = 0; i < old.length; i++) {
            if (old[i].classList.contains("fvd-toc-subs")) old[i].parentNode.removeChild(old[i]);
            else {
                old[i].classList.remove("is-active");
                old[i].removeAttribute("aria-current");
            }
        }
        var entry = ui.toc.querySelector('.fvd-toc-entry[data-key="' + cssEscape(s.key) + '"]');
        if (!entry) return;
        var btn = entry.querySelector(".fvd-toc-item");
        btn.classList.add("is-active");
        btn.setAttribute("aria-current", "true");
        var group = entry.closest(".fvd-toc-group");
        if (group && group.classList.contains("is-collapsed")) {
            var head = group.querySelector(".fvd-toc-grouphead");
            if (head) toggleGroup(head);
        }
        var heads = ui.content.querySelectorAll("h3");
        if (heads.length > 1) {
            var html = '<ol class="fvd-toc-subs">';
            for (var j = 0; j < heads.length; j++) {
                html += '<li><button type="button" class="fvd-toc-sub" data-sub="' + j + '">' + esc(heads[j].textContent.replace(/\s+/g, " ").trim()) + '</button></li>';
            }
            entry.insertAdjacentHTML("beforeend", html + "</ol>");
        }
        if (!query) keepInView(ui.toc, btn);
    }

    function scrollToSub(i) {
        var h = ui.content.querySelectorAll("h3")[i];
        if (h) scrollToEl(h);
    }

    function scrollToEl(el, centre) {
        if (!el) return;
        var box = ui.scroll.getBoundingClientRect();
        var r = el.getBoundingClientRect();
        var y = ui.scroll.scrollTop + r.top - box.top - (centre ? box.height / 3 : 14);
        ui.scroll.scrollTo({ top: Math.max(0, y), behavior: reducedMotion() ? "auto" : "smooth" });
    }

    // Scroll-spy: the sub-heading the reader has reached lights up in the list.
    function spy() {
        spyFrame = 0;
        if (!ui) return;
        var subs = ui.toc.querySelectorAll(".fvd-toc-sub");
        if (!subs.length) return;
        var heads = ui.content.querySelectorAll("h3");
        var line = ui.scroll.getBoundingClientRect().top + 90;
        var at = -1;
        for (var i = 0; i < heads.length; i++) {
            if (heads[i].getBoundingClientRect().top <= line) at = i;
            else break;
        }
        if (ui.scroll.scrollTop + ui.scroll.clientHeight >= ui.scroll.scrollHeight - 4 && heads.length) at = heads.length - 1;
        for (var j = 0; j < subs.length; j++) subs[j].classList.toggle("is-current", j === at);
    }

    function keepInView(scroller, el) {
        var a = scroller.getBoundingClientRect();
        var b = el.getBoundingClientRect();
        if (b.top < a.top + 8 || b.bottom > a.bottom - 8) {
            scroller.scrollTop += b.top - a.top - a.height / 3;
        }
    }

    function copyLink() {
        var url = new URL(DOC[cur.doc].page + "#" + encodeURIComponent(cur.key), location.href).href;
        var done = function () {
            ui.copy.classList.add("is-done");
            ui.copy.querySelector("span").textContent = "Copied";
            setTimeout(function () {
                if (!ui) return;
                ui.copy.classList.remove("is-done");
                ui.copy.querySelector("span").textContent = "Copy link";
            }, 1600);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(done, function () { window.prompt("Link to this entry:", url); });
        } else {
            window.prompt("Link to this entry:", url);
        }
    }

    /* ══ Phones: the list is a drawer ════════════════════════════════════════════════════════ */

    function toggleDrawer() {
        var opening = !ui.root.classList.contains("is-toc-open");
        ui.root.classList.toggle("is-toc-open", opening);
        if (opening) {
            var active = ui.toc.querySelector(".fvd-toc-item.is-active");
            if (active) keepInView(ui.toc, active);
        }
    }

    function closeDrawer() {
        if (ui) ui.root.classList.remove("is-toc-open");
    }

    /* ══ Search ══════════════════════════════════════════════════════════════════════════════ */

    function runSearch() {
        var q = ui.search.value.toLowerCase().replace(/\s+/g, " ").trim();
        query = q.length >= 2 ? q : "";
        clearHighlight(ui.content);
        if (!query) {
            ui.results.hidden = true;
            ui.toc.hidden = false;
            var rec = docs[cur.doc];
            if (rec && rec.sections) setCount(rec.sections.length, "entry", "entries");
            return;
        }
        ui.toc.hidden = true;
        ui.results.hidden = false;
        ui.results.innerHTML = '<p class="fvd-results-empty"><span class="fvd-cursor"></span>Searching&hellip;</p>';
        var asked = query;
        Promise.all(DOCS.map(function (d) {
            return loadDoc(d.key).catch(function () { return null; });
        })).then(function () {
            if (asked !== query) return;
            renderResults(asked);
            highlight(ui.content, asked);
        });
    }

    function renderResults(q) {
        var terms = q.split(" ");
        var order = [cur.doc].concat(DOCS.map(function (d) { return d.key; }).filter(function (k) { return k !== cur.doc; }));
        var html = "";
        var total = 0;
        order.forEach(function (docKey) {
            var rec = docs[docKey];
            if (!rec || rec.status !== "ready") return;
            var found = [];
            rec.sections.forEach(function (s) {
                sectionText(s);
                var title = s.title.toLowerCase();
                var score = 0;
                for (var i = 0; i < terms.length; i++) {
                    var inTitle = title.indexOf(terms[i]) >= 0;
                    var n = count(s.lower, terms[i]);
                    if (!inTitle && !n) return; // every term must appear
                    score += (inTitle ? 50 : 0) + Math.min(n, 25);
                }
                found.push({ s: s, score: score });
            });
            if (!found.length) return;
            found.sort(function (a, b) { return b.score - a.score || a.s.index - b.s.index; });
            total += found.length;
            html += '<div class="fvd-results-doc"><span>' + esc(DOC[docKey].title) + '</span><span class="fvd-toc-count">' + found.length + '</span></div>';
            found.slice(0, 40).forEach(function (f) {
                html += '<button type="button" class="fvd-result" data-doc="' + docKey + '" data-key="' + esc(f.s.key) + '">' +
                    '<span class="fvd-result-title">' + marked(f.s.title, terms) + '</span>' +
                    '<span class="fvd-result-snip">' + snippet(f.s, terms) + '</span></button>';
            });
        });
        ui.results.innerHTML = html || '<p class="fvd-results-empty">No entry mentions &ldquo;' + esc(q) + '&rdquo;.</p>';
        setCount(total, "match", "matches");
    }

    function count(hay, needle) {
        var n = 0;
        var i = hay.indexOf(needle);
        while (i >= 0 && n < 99) {
            n++;
            i = hay.indexOf(needle, i + needle.length);
        }
        return n;
    }

    function snippet(s, terms) {
        var at = -1;
        for (var i = 0; i < terms.length && at < 0; i++) at = s.lower.indexOf(terms[i]);
        if (at < 0) return esc(s.raw.slice(0, 110)) + (s.raw.length > 110 ? "&hellip;" : "");
        var from = Math.max(0, at - 45);
        var to = Math.min(s.raw.length, at + 85);
        if (from > 0) {
            var sp = s.raw.indexOf(" ", from);
            if (sp > 0 && sp < at) from = sp + 1;
        }
        return (from > 0 ? "&hellip;" : "") + marked(s.raw.slice(from, to), terms) + (to < s.raw.length ? "&hellip;" : "");
    }

    // Plain text in, escaped HTML out, with the search terms wrapped in <mark>.
    function marked(text, terms) {
        var rx = termsRx(terms);
        var out = "";
        var at = 0;
        var m;
        while ((m = rx.exec(text))) {
            out += esc(text.slice(at, m.index)) + "<mark>" + esc(m[0]) + "</mark>";
            at = m.index + m[0].length;
            if (!m[0].length) rx.lastIndex++;
        }
        return out + esc(text.slice(at));
    }

    function termsRx(terms) {
        return new RegExp(terms.map(function (t) {
            return t.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        }).join("|"), "gi");
    }

    // Wrap the search terms in the shown entry; returns how many were found.
    function highlight(box, q) {
        clearHighlight(box);
        var rx = termsRx(q.split(" "));
        var walker = document.createTreeWalker(box, NodeFilter.SHOW_TEXT, {
            acceptNode: function (n) {
                if (!n.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                return n.parentNode.closest(".fvd-ph, mark") ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }
        });
        var nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);
        var hits = 0;
        nodes.forEach(function (node) {
            var text = node.nodeValue;
            rx.lastIndex = 0;
            if (!rx.test(text)) return;
            rx.lastIndex = 0;
            var frag = document.createDocumentFragment();
            var at = 0;
            var m;
            while ((m = rx.exec(text))) {
                if (m.index > at) frag.appendChild(document.createTextNode(text.slice(at, m.index)));
                var mark = document.createElement("mark");
                mark.className = "fvd-hit";
                mark.textContent = m[0];
                frag.appendChild(mark);
                hits++;
                at = m.index + m[0].length;
            }
            if (at < text.length) frag.appendChild(document.createTextNode(text.slice(at)));
            node.parentNode.replaceChild(frag, node);
        });
        return hits;
    }

    function clearHighlight(box) {
        var marks = box.querySelectorAll("mark.fvd-hit");
        for (var i = 0; i < marks.length; i++) {
            var m = marks[i];
            var parent = m.parentNode;
            parent.replaceChild(document.createTextNode(m.textContent), m);
            parent.normalize();
        }
    }

    /* ══ Helpers ═════════════════════════════════════════════════════════════════════════════ */

    function esc(s) {
        return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function cssEscape(s) {
        return window.CSS && CSS.escape ? CSS.escape(s) : String(s).replace(/["\\\]]/g, "\\$&");
    }

    function pad(n) {
        return n < 10 ? "0" + n : String(n);
    }

    function reducedMotion() {
        return !!(window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches);
    }

    function readLast() {
        try {
            return JSON.parse(sessionStorage.getItem(STORE_KEY)) || {};
        } catch (e) {
            return {};
        }
    }

    function writeLast() {
        try { sessionStorage.setItem(STORE_KEY, JSON.stringify(last)); } catch (e) { }
    }
})(window, document);
