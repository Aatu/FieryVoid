"use strict";

/*
 * mapPreview.js - the deployment-zone map preview, drawn onto a canvas.
 * CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §3.4 / §4.2.
 *
 * Loaded by BOTH creategame.php (the Teams & Map step, and the Summary step's copy of it) and
 * gamelobby.php (its Map Preview panel), so the creator and the players see one map. Each page
 * decides WHAT is drawn - the map's size, the zones and their colours, the pre-placed terrain -
 * and this file decides how.
 *
 * The look is the mockup's Teams & Map artboard (plan §11): the map a dark well under its faint
 * hex grid, each deployment zone a light wash of its colour behind a dashed edge, labelled in the
 * corner nearest the map's rim, and terrain as grey discs of its real size. Colours are
 * tokens.css values written out, because a canvas cannot read var().
 *
 * Drawn in the game's own proportions (final refinements, plan §12.12): pointy-top hexes, a row
 * ROW x a hex's width below the last, as coordinateConverter.fromHexToGame lays them out and
 * webglHexGridRenderer / DeploymentPhaseStrategy size the map and the zones - so a 42 x 30 map is
 * the wide box the game draws, not 42:30.
 */
window.mapPreview = {

    COLORS: {
        well: "#04161c",        //--fv-well
        hex: "#0f2c3a",         //the hex grid: a shade above --fv-card, under everything else
        rim: "#215a7a",         //--fv-line
        terrain: "90, 106, 118", //#5a6a76, as rgb for the alpha
        dust: "196, 160, 112",  //sand - the game's dust texture (BallisticIconContainer), as rgb for the alpha
        nebula: "150, 100, 215" //violet - the game's nebula texture (BallisticIconContainer), as rgb for the alpha
    },

    //The Dust Zone's wash, under the zones and terrain.
    DUST_ALPHA: 0.3,

    /* The Nebula's wash (NebulaRule) - the whole map, under the hex grid - and a few soft banks of brighter
       gas over it, always in the same places, so it reads as a cloud rather than a flat tint. A bank's x / y
       are fractions of the map's half-size from its centre, r a fraction of its longer side. */
    NEBULA_ALPHA: 0.2,
    NEBULA_BANKS: [
        { x: -0.55, y: -0.35, r: 0.38, rgb: "190, 110, 230", a: 0.22 },
        { x: 0.5, y: 0.3, r: 0.42, rgb: "70, 185, 200", a: 0.17 },
        { x: 0.15, y: -0.65, r: 0.28, rgb: "225, 120, 200", a: 0.14 },
        { x: -0.25, y: 0.6, r: 0.32, rgb: "115, 120, 240", a: 0.15 }
    ],

    //Logical width of the drawing; its height follows the map. Drawn at PIXEL_RATIO times that,
    //so the thin lines stay sharp however CSS scales the canvas (width 100%, height auto).
    WIDTH: 545,
    PIXEL_RATIO: 2,

    //Row pitch over a hex's width: 1.5 x the hex size against sqrt(3) x it.
    ROW: Math.sqrt(3) / 2,

    /* Team colours, as the game itself picks them (plan §11.4; gamedata.js, which neither page
       loads - ⚠️ keep in step with its teamBaseColors / teamBaseColorsMultiTeam):
         RELATIVE  - a participant in a 2-team game: own green, a team-mate's slot ally blue, the
                     other team enemy red.
         teamColor - everyone else, by team NUMBER: with 2 teams green / red (teamBaseColors), with
                     3+ the multi-team palette (teamBaseColorsMultiTeam). */
    RELATIVE: {
        own: [50, 205, 50],
        ally: [51, 173, 255],
        enemy: [255, 80, 80]
    },

    TEAM_COLORS_MULTI: [
        [50, 205, 50], [255, 150, 40], [40, 230, 230], [170, 90, 230],
        [240, 230, 60], [51, 173, 255], [230, 40, 230], [255, 80, 80]
    ],

    teamColor: function teamColor(team, teamCount) {
        if (teamCount <= 2) return (team == 1) ? mapPreview.RELATIVE.own : mapPreview.RELATIVE.enemy;
        var palette = mapPreview.TEAM_COLORS_MULTI;
        return palette[(team - 1) % palette.length];
    },

    /* ── Pre-placed terrain (Maps with Terrain) ────────────────────────────────────────────
       A map template with pre-placed terrain travels as rules.terrainLayout (server:
       TerrainLayoutRule, placed by BuyingGamePhase::advance before any random terrain). `type` is a
       TerrainLayoutRule::$types key - keep the two lists in step. Here each type only needs its
       footprint, to be drawn: `huge` is the disc radius (0 = one hex), `offsets` an irregular shape
       turned by the unit's facing `h`, and `field` marks the fainter Dust / Meteor Swarm markers. */
    TERRAIN_TYPES: {
        asteroidS: { huge: 0 },
        asteroidM: { huge: 0 },
        asteroidL: { huge: 0 },
        asteroid2: { offsets: [{ q: 1, r: 0 }] },
        asteroid3: { offsets: [{ q: 0, r: 1 }, { q: -1, r: 0 }] },
        moonS: { huge: 1, moon: true },
        moonM: { huge: 2, moon: true },
        moonL: { huge: 3, moon: true },
        dust: { huge: 0, field: true },
        meteors: { huge: 0, field: true }
    },

    //Marker opacity: solid terrain, and the fainter dust / meteor fields.
    TERRAIN_ALPHA: 1,
    FIELD_TERRAIN_ALPHA: 0.55,

    /* Which kinds of marker a layout draws, for a legend: {solid, fields, dust}. A template's dust is drawn
       as dust (paintDust) - it joins the Dust Zone when the game is created - so `fields` is the Meteor
       Swarms alone. */
    terrainKinds: function terrainKinds(layout) {
        var units = (layout && layout.units) || [];
        var types = mapPreview.TERRAIN_TYPES;
        return {
            solid: units.some(function (unit) { return types[unit.type] && !types[unit.type].field; }),
            fields: units.some(function (unit) { return types[unit.type] && types[unit.type].field && unit.type !== 'dust'; }),
            dust: units.some(function (unit) { return unit.type === 'dust'; })
        };
    },

    //A rules.dustZone's coverage: 100, 50, 25, or 0 for none (or only a template's dust hexes).
    dustCoverage: function dustCoverage(zone) {
        return zone ? (parseInt(zone.coverage, 10) || 0) : 0;
    },

    /* Mathlib::getRotatedHex (server), which BuyingGamePhase uses for the same footprint: hex ->
       pixel, turn the offset by facing * -60 degrees, pixel -> nearest hex (odd-r offset).
       Verified identical to the server on all 32 irregular units of the terrain maps (plan §12.3). */
    rotatedHex: function rotatedHex(center, offset, facing) {
        var s3 = Math.sqrt(3);
        var toPx = function (q, r) { return { x: s3 * (q - 0.5 * (r & 1)), y: 1.5 * r }; };
        var c = toPx(center.q, center.r), o = toPx(offset.q, offset.r), z = toPx(0, 0);
        var vx = o.x - z.x, vy = o.y - z.y;
        var a = -facing * Math.PI / 3;
        var px = c.x + vx * Math.cos(a) - vy * Math.sin(a);
        var py = c.y + vx * Math.sin(a) + vy * Math.cos(a);

        //axial, cube-rounded, then back to odd-r offset
        var fq = (s3 / 3) * px - py / 3, fr = (2 / 3) * py, fs = -fq - fr;
        var rq = Math.round(fq), rr = Math.round(fr), rs = Math.round(fs);
        var dq = Math.abs(rq - fq), dr = Math.abs(rr - fr), ds = Math.abs(rs - fs);
        if (dq > dr && dq > ds) rq = -rr - rs;
        else if (ds <= dr) rr = -rq - rs;
        return { q: rq + (rr + (rr & 1)) / 2, r: rr };
    },

    /*
     * Draw a map onto `canvas`:
     *   map.width, map.height - in hexes (an open map is passed as the 84 x 60 it is drawn at)
     *   map.zones   - [{x, y, w, h, rgb: [r, g, b], label}], (x, y) the zone's CENTRE in hexes, the
     *                 later ones drawn over the earlier; `label` optional ("TEAM 1")
     *   map.terrain - a rules.terrainLayout, or nothing
     *   map.dust    - a rules.dustZone, or nothing
     *   map.nebula  - true when the game is in a nebula (rules.nebula)
     * The canvas takes the map's own proportions (height/width clamped 0.45-1.0, letterboxed
     * beyond), so a caller sizes it with CSS width alone.
     */
    paint: function paint(canvas, map) {
        if (!canvas) return;
        var ctx = canvas.getContext("2d");
        if (!ctx) return;

        var colors = mapPreview.COLORS;
        var mapWidth = map.width || 1;
        var mapHeight = map.height || 1;
        var row = mapPreview.ROW;

        var width = mapPreview.WIDTH;
        var height = Math.round(width * Math.min(1, Math.max(0.45, mapHeight * row / mapWidth)));
        var ratio = mapPreview.PIXEL_RATIO;
        if (canvas.width !== width * ratio || canvas.height !== height * ratio) {
            canvas.width = width * ratio; //resizing also clears it
            canvas.height = height * ratio;
        }
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        //scale = one hex's width; a row is row x that.
        var margin = 1; //room for the rim's own line
        var scale = Math.min((width - margin * 2) / mapWidth, (height - margin * 2) / (mapHeight * row));
        var mapW = mapWidth * scale, mapH = mapHeight * row * scale;
        var left = (width - mapW) / 2, top = (height - mapH) / 2;
        var centerX = left + mapW / 2, centerY = top + mapH / 2;
        var snap = function (v) { return Math.round(v * ratio) / ratio; }; //a 1px line (ratio device px) centred here is sharp

        //Hex column x -> canvas. The map's true centre is half a hex left of x = 0 (plan §12.3), so
        //x sits half a hex right of where the box would put it; y runs up the map, in rows.
        var toX = function (x) { return centerX + (x + 0.5) * scale; };
        var toY = function (y) { return centerY - y * row * scale; };
        //Hex (q, r) -> canvas: an odd row sits half a hex left (fromHexToGame).
        var hexToCanvas = function (q, r) {
            return { x: toX(q - 0.5 * (r & 1)), y: toY(r) };
        };

        ctx.save();
        ctx.fillStyle = colors.well;
        ctx.fillRect(left, top, mapW, mapH);
        ctx.beginPath();
        ctx.rect(left, top, mapW, mapH);
        ctx.clip(); //zones and terrain past the rim are cut there, as the game cuts them

        if (map.nebula) mapPreview.paintNebula(ctx, { x: centerX, y: centerY, w: mapW, h: mapH });
        mapPreview.paintHexGrid(ctx, scale, Math.ceil(mapW / 2 / scale) + 2, Math.ceil(mapH / 2 / (row * scale)) + 1, hexToCanvas);
        mapPreview.paintDust(ctx, scale, map.dust, map.terrain, { x: centerX, y: centerY, w: mapW, h: mapH }, hexToCanvas);

        var labels = [];
        (map.zones || []).forEach(function (zone) {
            var zxHex = parseInt(zone.x, 10) || 0;
            var zyHex = parseInt(zone.y, 10) || 0;
            var w = parseInt(zone.w, 10) || 0;
            var h = parseInt(zone.h, 10) || 0;
            if (w <= 0 || h <= 0) return;

            var rgb = zone.rgb.join(",");
            var zx = toX(zxHex - w / 2), zy = toY(zyHex + h / 2);
            var zw = w * scale, zh = h * row * scale;

            ctx.fillStyle = "rgba(" + rgb + ", 0.14)";
            ctx.fillRect(zx, zy, zw, zh);

            //Dashed on the sides facing into the map; a side lying on the rim is left to the rim,
            //as in the mockup. Inset by half the line, which is 1.5px.
            var x0 = zx + 0.75, y0 = zy + 0.75, x1 = zx + zw - 0.75, y1 = zy + zh - 0.75;
            var sides = [
                [x0, y0, x1, y0, zy <= top + 0.5],
                [x1, y0, x1, y1, zx + zw >= left + mapW - 0.5],
                [x0, y1, x1, y1, zy + zh >= top + mapH - 0.5],
                [x0, y0, x0, y1, zx <= left + 0.5]
            ];
            ctx.save();
            ctx.strokeStyle = "rgb(" + rgb + ")";
            ctx.lineWidth = 1.5;
            ctx.setLineDash([6, 4]);
            sides.forEach(function (side) {
                if (side[4]) return;
                ctx.beginPath();
                ctx.moveTo(side[0], side[1]);
                ctx.lineTo(side[2], side[3]);
                ctx.stroke();
            });
            ctx.restore();

            if (zone.label) {
                labels.push({ text: zone.label, rgb: rgb, zx: zx, zy: zy, zw: zw, zh: zh, right: zxHex + 0.5 > 1, bottom: zyHex < 0 });
            }
        });

        mapPreview.paintTerrain(ctx, scale, map.terrain, hexToCanvas);

        //Labels last, over the terrain: in the zone's corner nearest the map's rim, running on
        //into the map when the zone is narrower than the label. Sized to read about 10px as SHOWN:
        //CSS scales the canvas down to under 400px in the lobby's third-width column or on a
        //phone, where a fixed 11px label shrank to 7. A canvas not on screen yet (a hidden wizard
        //step) keeps 11, which is also what a desktop Create Game works out to.
        var shown = canvas.getBoundingClientRect().width;
        var labelSize = shown > 0 ? Math.min(18, Math.max(11, Math.round(10 * width / shown))) : 11;
        var pad = Math.round(labelSize * 0.55);
        ctx.font = labelSize + "px Consolas, 'Lucida Console', monospace";
        ctx.textBaseline = "alphabetic";
        ctx.shadowColor = colors.well; //lifts it off a dashed edge or a marker it crosses
        ctx.shadowBlur = 3;

        //Teams sharing a zone - or zones whose corners meet - would print their labels over each
        //other, so a label that would touch one already placed moves a line further from the rim
        //(down from a top corner, up from a bottom one) until it is clear: a list, in team order
        //read top to bottom either way. A label touching one with the SAME text is left out -
        //Create Game labels every slot, and two slots of one team often share a zone.
        var byName = function (a, b) { return a.text.localeCompare(b.text, undefined, { numeric: true }); };
        labels.sort(function (a, b) {
            if (a.bottom !== b.bottom) return a.bottom ? 1 : -1;
            return a.bottom ? byName(b, a) : byName(a, b);
        });
        var placed = [];
        var lineHeight = Math.round(labelSize * 1.25);
        labels.forEach(function (label) {
            var textWidth = ctx.measureText(label.text).width;
            var x = label.right ? label.zx + label.zw - pad : label.zx + pad;
            var y = label.bottom ? label.zy + label.zh - pad : label.zy + pad + Math.round(labelSize * 0.82);
            var box = function () {
                var l = label.right ? x - textWidth : x;
                return { text: label.text, l: l, r: l + textWidth, t: y - labelSize * 0.85, b: y + labelSize * 0.25 };
            };
            var clashes = function (other) {
                var own = box();
                return own.l < other.r && other.l < own.r && own.t < other.b && other.t < own.b;
            };
            for (var tries = 0; tries <= placed.length; tries++) {
                var hits = placed.filter(clashes);
                if (!hits.length) break;
                if (hits.some(function (other) { return other.text === label.text; })) return;
                y += label.bottom ? -lineHeight : lineHeight;
            }
            placed.push(box());

            ctx.textAlign = label.right ? "right" : "left";
            ctx.fillStyle = "rgb(" + label.rgb + ")";
            ctx.fillText(label.text, x, y);
        });
        ctx.restore(); //the clip

        ctx.strokeStyle = colors.rim;
        ctx.lineWidth = 1;
        ctx.strokeRect(snap(left) + 0.5, snap(top) + 0.5, snap(mapW) - 1, snap(mapH) - 1); //just inside the map
    },

    /* The hex grid under everything: every hex within `cols` columns and `rows` rows of the centre,
       as one path and one stroke, so an edge two hexes share is no darker than the rest. Pointy-top
       hexes `scale` wide, a corner at the top and bottom. The lines thin as the hexes shrink on the
       biggest maps, so the grid stays a texture under the zones rather than a mesh over them. */
    paintHexGrid: function paintHexGrid(ctx, scale, cols, rows, hexToCanvas) {
        var radius = scale / Math.sqrt(3); //centre to corner
        var corners = [];
        for (var k = 0; k < 6; k++) {
            var angle = (60 * k + 30) * Math.PI / 180;
            corners.push({ x: radius * Math.cos(angle), y: radius * Math.sin(angle) });
        }

        ctx.save();
        ctx.beginPath();
        for (var r = -rows; r <= rows; r++) {
            for (var q = -cols; q <= cols; q++) {
                var c = hexToCanvas(q, r);
                ctx.moveTo(c.x + corners[5].x, c.y + corners[5].y);
                for (k = 0; k < 6; k++) ctx.lineTo(c.x + corners[k].x, c.y + corners[k].y);
            }
        }
        ctx.strokeStyle = mapPreview.COLORS.hex;
        ctx.lineWidth = Math.min(1, scale / 12);
        ctx.stroke();
        ctx.restore();
    },

    /* The Dust Zone (server: DustZoneRule) as a sand wash, the game's dust colour: the whole map at
       100%, otherwise its hexes - the clump's, which the server grows when the game is created, and a
       map template's dust, which joins it then. Before that (Create Game) a 50% / 25% clump has no hexes
       yet, so it is shown by its size and place alone: an ellipse of that share of the map round the
       centre, dashed because the real edge will wander. `box` is the map's centre and size on the canvas. */
    paintDust: function paintDust(ctx, scale, zone, layout, box, toCanvas) {
        var coverage = mapPreview.dustCoverage(zone);
        var fill = "rgba(" + mapPreview.COLORS.dust + ", " + mapPreview.DUST_ALPHA + ")";

        ctx.save();
        ctx.fillStyle = fill;
        if (coverage === 100) {
            ctx.fillRect(box.x - box.w / 2, box.y - box.h / 2, box.w, box.h);
            ctx.restore();
            return;
        }

        var hexes = [];
        ((zone && zone.spans) || []).forEach(function (span) {
            for (var q = span[1]; q <= span[2]; q++) hexes.push({ q: q, r: span[0] });
        });
        ((layout && layout.units) || []).forEach(function (unit) {
            if (unit.type === 'dust') hexes.push({ q: unit.q, r: unit.r });
        });

        if (coverage > 0 && !(zone.spans && zone.spans.length)) {
            var k = Math.sqrt(4 * (coverage / 100) / Math.PI); //an ellipse inscribed in the map has pi/4 of its area
            ctx.beginPath();
            ctx.ellipse(box.x, box.y, box.w / 2 * k, box.h / 2 * k, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = "rgba(" + mapPreview.COLORS.dust + ", 0.8)";
            ctx.lineWidth = 1.5;
            ctx.setLineDash([6, 4]);
            ctx.stroke();
            ctx.setLineDash([]);
        }

        //Every hex in one path and one fill, so neighbours meet without a seam.
        if (hexes.length) {
            var radius = scale / Math.sqrt(3);
            var corners = [];
            for (var c = 0; c < 6; c++) {
                var angle = (60 * c + 30) * Math.PI / 180;
                corners.push({ x: radius * Math.cos(angle), y: radius * Math.sin(angle) });
            }
            ctx.beginPath();
            hexes.forEach(function (hex) {
                var p = toCanvas(hex.q, hex.r);
                ctx.moveTo(p.x + corners[5].x, p.y + corners[5].y);
                for (var i = 0; i < 6; i++) ctx.lineTo(p.x + corners[i].x, p.y + corners[i].y);
            });
            ctx.fill("nonzero");
        }
        ctx.restore();
    },

    //The Nebula: the whole map in its wash, then the banks. `box` is the map's centre and size on the canvas.
    paintNebula: function paintNebula(ctx, box) {
        ctx.save();
        ctx.fillStyle = "rgba(" + mapPreview.COLORS.nebula + ", " + mapPreview.NEBULA_ALPHA + ")";
        ctx.fillRect(box.x - box.w / 2, box.y - box.h / 2, box.w, box.h);
        mapPreview.NEBULA_BANKS.forEach(function (bank) {
            var x = box.x + bank.x * box.w / 2, y = box.y + bank.y * box.h / 2;
            var radius = bank.r * Math.max(box.w, box.h);
            var gradient = ctx.createRadialGradient(x, y, 0, x, y, radius);
            gradient.addColorStop(0, "rgba(" + bank.rgb + ", " + bank.a + ")");
            gradient.addColorStop(1, "rgba(" + bank.rgb + ", 0)");
            ctx.fillStyle = gradient;
            ctx.fillRect(x - radius, y - radius, radius * 2, radius * 2);
        });
        ctx.restore();
    },

    /* Grey markers of each unit's real size: a disc for a moon, a dot per hex for the rest. A
       marker big enough to carry one gets the mockup's halo - a fainter ring INSIDE the unit's
       footprint, so a moon still reads at its true size. Meteor fields: fainter, no halo. A
       template's dust is not a marker: paintDust draws it as dust. */
    paintTerrain: function paintTerrain(ctx, scale, layout, toCanvas) {
        if (!layout || !layout.units) return;

        var grey = mapPreview.COLORS.terrain;
        ctx.save();
        layout.units.forEach(function (unit) {
            var type = mapPreview.TERRAIN_TYPES[unit.type];
            if (!type || unit.type === 'dust') return;

            var alpha = type.field ? mapPreview.FIELD_TERRAIN_ALPHA : mapPreview.TERRAIN_ALPHA;
            var hexes = [{ q: unit.q, r: unit.r }];
            if (type.offsets) {
                type.offsets.forEach(function (offset) {
                    hexes.push(mapPreview.rotatedHex(unit, offset, unit.h || 0));
                });
            }
            //A moon's disc has the AREA of its hexes (3n² + 3n + 1 of them, each sqrt(3)/2 x scale²):
            //one circle cannot also match both the flat-sided width and the pointed height of a hex
            //disc. Anything else is a dot inside each of its hexes.
            var n = type.huge || 0;
            var radius = type.moon
                ? Math.sqrt((3 * n * n + 3 * n + 1) * Math.sqrt(3) / (2 * Math.PI)) * scale
                : Math.max(0.4 * scale, 1.5);
            var halo = (type.field || radius < 6) ? 0 : Math.min(6, radius * 0.3);

            hexes.forEach(function (hex) {
                var p = toCanvas(hex.q, hex.r);
                if (halo) {
                    ctx.fillStyle = "rgba(" + grey + ", " + (alpha * 0.3) + ")";
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, radius, 0, Math.PI * 2);
                    ctx.fill();
                }
                ctx.fillStyle = "rgba(" + grey + ", " + alpha + ")";
                ctx.beginPath();
                ctx.arc(p.x, p.y, radius - halo, 0, Math.PI * 2);
                ctx.fill();
            });
        });
        ctx.restore();
    }
};
