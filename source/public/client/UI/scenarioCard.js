"use strict";

/*
 * scenarioCard.js - a game's structured Scenario Description: the definition of its JSON, and the
 * read-only fact grid it renders as. CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §3.3 / §4.1.
 *
 * Loaded by BOTH creategame.php (the wizard's Summary step is a preview of exactly what the
 * opponent will see) and gamelobby.php (its Scenario Description panel). One renderer, so the two
 * screens cannot drift apart. Styles: styles/scenarioCard.css.
 *
 * WHY: the scenario used to travel only as one "LABEL: value\n" string in tac_game.description,
 * which gamelobby.php recovers by splitting each line on its first colon - so a value containing
 * a colon misparses, and neither screen can show more structure than that string carries. It now
 * travels as JSON in tac_game.scenario (db/createGameRedesign.sql) as well.
 *
 * ⚠️ FIELDS IS A STORAGE CONTRACT. Its keys (key / otherKey / pointsKey) are what gets written to
 * tac_game.scenario and kept for the life of the game - rename one and every stored game loses
 * that fact. The `options` strings are copied VERBATIM from creategame.php's <option value>s and
 * stored as-is, so they are compared, not just displayed: do not paraphrase them either.
 * Called Shots is deliberately absent (dropped from the redesign, plan §3.3).
 * The server keeps its own copy of the key list (Manager::$scenarioKeys) - change both together.
 *
 * `help` is the Create Game form's "?" text. Stage 1 builds that form from this list; Stages 2
 * and 4 render the stored JSON with render().
 */
window.scenarioCard = {

    //Bump only when an existing key changes MEANING. Adding a field needs no bump: a game stored
    //before the field existed simply has no value for it, and render() skips empty facts.
    VERSION: 1,

    FIELDS: [
        {
            key: "tier", label: "Expected Power Level", factLabel: "Tier",
            options: ["Any", "Tier 1", "Tier 2", "Tier 3", "Ancient", "Other"],
            otherKey: "tierCustom",
            help: "The power level fleets are expected to be built to. Factions & Tiers lists where each faction sits."
        },
        {
            key: "fleetRequirements", label: "Fleet Requirements", factLabel: "Fleet Requirements",
            options: ["Pass the fleet checker", "Other"],
            otherKey: "fleetRequirementsCustom",
            help: "What every fleet must satisfy before the game starts - normally, passing the lobby's Fleet Checker."
        },
        {
            key: "customFactions", label: "Custom Factions / Units", factLabel: "Custom Factions",
            options: ["Allowed", "Custom factions allowed", "Custom ships in official factions only", "Not allowed"],
            help: "Whether fan-made factions, and custom ships added to official factions, may be used."
        },
        {
            //free text, "None" by default - a fact that says nothing is left out of the grid
            key: "forbiddenFactions", label: "Forbidden Factions", factLabel: "Forbidden Factions",
            omitNone: true,
            help: "Factions that may not be used in this game, whatever their tier."
        },
        {
            key: "enhancements", label: "Enhancements", factLabel: "Enhancements",
            options: ["Allowed", "Up to X points", "Not allowed"],
            pointsKey: "enhancementsPoints",
            help: "Whether units may buy enhancements such as Elite Crew, and optionally a points limit on them."
        },
        {
            key: "mapBorders", label: "Map Borders", factLabel: "Map Borders",
            options: ["Can't end movement outside map", "Cannot leave map at all"],
            help: "What happens to a unit that goes off the edge of the map."
        },
        {
            key: "victoryConditions", label: "Victory Conditions", factLabel: "Victory Conditions",
            options: ["Most Points after Turn 12", "Last unit on map", "Last ship on map", "Other"],
            otherKey: "victoryCustom",
            help: "How the game is won."
        },
        {
            key: "additionalInfo", label: "Additional Info", factLabel: "Additional Info",
            multiline: true, wide: true
        }
    ],

    //Every key a stored scenario may carry, in FIELDS order.
    storedKeys: function storedKeys() {
        var keys = [];
        scenarioCard.FIELDS.forEach(function (field) {
            keys.push(field.key);
            if (field.otherKey) keys.push(field.otherKey);
            if (field.pointsKey) keys.push(field.pointsKey);
        });
        return keys;
    },

    /*
     * Raw scenario (a JSON string, a parsed object, or nothing) -> a clean object holding only
     * known keys, every value a trimmed string; or null when there is no usable scenario.
     *
     * Every value goes through String() because the lobby payload is encoded with
     * JSON_NUMERIC_CHECK, which hands a numeric-looking string back as a NUMBER - an
     * enhancementsPoints of "20" arrives as 20. (That flag can also CHANGE a value - "0012"
     * becomes 12 - which no amount of client-side care undoes. The server side's answer is to
     * publish the column as raw JSON text; see db/createGameRedesign.sql.)
     */
    normalise: function normalise(raw) {
        var data = raw;
        if (typeof data === "string") {
            if (data.trim() === "") return null;
            try {
                data = JSON.parse(data);
            } catch (e) {
                return null;
            }
        }
        if (!data || typeof data !== "object" || Array.isArray(data)) return null;

        var clean = { v: Number(data.v) || scenarioCard.VERSION };
        scenarioCard.storedKeys().forEach(function (key) {
            if (data[key] != null) clean[key] = String(data[key]).trim();
        });
        return clean;
    },

    //The text a fact card shows for one field: an "Other" or "Up to X points" choice is replaced
    //by what the creator typed for it, and left as the bare option when they typed nothing.
    factValue: function factValue(field, data) {
        var value = data[field.key] || "";
        if (field.otherKey && value === "Other" && data[field.otherKey]) {
            return data[field.otherKey];
        }
        if (field.pointsKey && value === "Up to X points" && data[field.pointsKey]) {
            return "Up to " + data[field.pointsKey] + " pts";
        }
        return value;
    },

    /*
     * The fact grid as an HTML string, or "" when there is nothing to show - the caller owns the
     * panel/card around it and its heading. Every value is escaped: the free-text fields are
     * player-typed and this output goes straight into .html().
     *
     * options.plain: drop each fact's own card chrome, for a grid that already sits INSIDE a card
     * (the wizard's Summary step). The Gamelobby panel uses the default carded grid.
     */
    render: function render(raw, options) {
        var data = scenarioCard.normalise(raw);
        if (!data) return "";

        var facts = "";
        scenarioCard.FIELDS.forEach(function (field) {
            var value = scenarioCard.factValue(field, data);
            if (value === "" || (field.omitNone && /^none$/i.test(value))) return;

            facts += '<div class="fv-scn-fact' + (field.wide ? " fv-scn-fact--wide" : "") + '">' +
                '<dt class="fv-scn-label">' + scenarioCard.escapeHtml(field.factLabel) + "</dt>" +
                '<dd class="fv-scn-value' + (field.multiline ? " fv-scn-value--multiline" : "") + '">' +
                scenarioCard.escapeHtml(value) + "</dd>" +
                "</div>";
        });
        if (facts === "") return "";

        var plain = options && options.plain;
        return '<dl class="fv-scn-grid' + (plain ? " fv-scn-grid--plain" : "") + '">' + facts + "</dl>";
    },

    /*
     * The game's optional rules, one chip each: the lobby's Game Rules row (under its Map Preview)
     * and the wizard's Summary step, which previews it - one list, so the two can never
     * disagree. `rules` is the rules object as the server publishes it (gamedata.rules) or as the
     * wizard is about to post it (createGame.rules); the two share every key.
     * options.unlimitedPoints: the slots are unlimited - a slot setting, not a rule, so the caller
     * says so.
     * options.inServiceDate: the In-Service Date cutoff year, or null - a column of its own
     * (tac_game.in_service_date), not a rule, so likewise.
     * options.isPrivate: the game has a password (tac_game.password_hash, Stage 8) - likewise. It
     * leads the row: it decides who can be in the game at all.
     *
     * Every count goes through parseInt: the lobby payload is JSON_NUMERIC_CHECKed, the wizard's is
     * not, so a count can arrive as either. The terrain map's name is the creator's text (the
     * template's name, but it came from a POST) - renderRuleChips escapes it.
     *
     * `kind` is the chip's colour (user, 2026-09-25): ladder gold, terrain white, simultaneous
     * movement green, reinforcements cyan, mines purple, private yellow (the Load a Fleet menu's
     * private padlock), anything else the page's blue.
     */
    ruleChips: function ruleChips(rules, options) {
        var r = rules || {};
        var chips = [];
        var add = function (kind, text) { chips.push({ kind: kind, text: text }); };
        var count = function (value) { return Math.max(0, parseInt(value, 10) || 0); };

        if (options && options.isPrivate) add("private", "Private Game");
        if (r.ladder) add("ladder", "Ladder Game");
        var brackets = count(r.initiativeCategories);
        if (brackets > 0) add("simmove", "Simultaneous Movement (" + brackets + (brackets === 1 ? " bracket)" : " brackets)"));
        if (r.allowMines) add("mines", "Mines Allowed");
        if (r.allowReinforcements) add("reinforcements", "Reinforcements Allowed");
        if (r.desperate != null) {
            var teams = parseInt(r.desperate, 10);
            add("rule", "Desperate Scenario (" + (teams === 1 ? "Team 1" : teams === 2 ? "Team 2" : "Both teams") + ")");
        }
        if (r.friendlyFire) add("rule", "Friendly Fire");
        if (options && options.unlimitedPoints) add("rule", "Unlimited Points");
        var inServiceDate = count(options && options.inServiceDate);
        if (inServiceDate > 0) add("rule", "In-Service Date: " + inServiceDate);

        //The random terrain (Create Game's Terrain Features rows) as ONE chip, then the terrain map
        //as its own - the Map Preview shows what the map places.
        var terrain = [];
        if (count(r.asteroids) > 0) terrain.push("Asteroids (" + count(r.asteroids) + ")");
        if (r.moons) {
            var moons = [["small", "Small"], ["medium", "Medium"], ["large", "Large"]]
                .filter(function (size) { return count(r.moons[size[0]]) > 0; })
                .map(function (size) { return count(r.moons[size[0]]) + " " + size[1]; });
            if (moons.length) terrain.push("Moons (" + moons.join(", ") + ")");
        }
        if (r.dustAndMeteors) {
            if (count(r.dustAndMeteors.dust) > 0) terrain.push("Dust (" + count(r.dustAndMeteors.dust) + ")");
            if (count(r.dustAndMeteors.meteors) > 0) terrain.push("Meteor Swarms (" + count(r.dustAndMeteors.meteors) + ")");
        }
        //No-break spaces inside each type, and before each dot, so a long chip wraps only between
        //types, the dot ending the line.
        if (terrain.length) {
            add("terrain", "Random Terrain: " + terrain.map(function (type) {
                return type.replace(/ /g, " ");
            }).join(" · "));
        }
        var layout = r.terrainLayout;
        if (layout && layout.units && layout.units.length) {
            var name = String(layout.name == null ? "" : layout.name).trim() || "Pre-placed";
            add("terrain", "Terrain Map: " + name);
        }

        return chips;
    },

    //ruleChips() as the <li>s of a .fv-rule-chips list; one dashed "No optional rules" when empty.
    renderRuleChips: function renderRuleChips(chips) {
        if (!chips || !chips.length) {
            return '<li class="fv-rule-chip fv-rule-chip--none">No optional rules</li>';
        }
        return chips.map(function (chip) {
            return '<li class="fv-rule-chip fv-rule-chip--' + chip.kind + '">' + scenarioCard.escapeHtml(chip.text) + "</li>";
        }).join("");
    },

    //Same as games.js's escapeHtml - that file is not loaded on either page that uses this one.
    escapeHtml: function escapeHtml(value) {
        return String(value == null ? "" : value).replace(/[&<>"']/g, function (c) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
        });
    }
};
