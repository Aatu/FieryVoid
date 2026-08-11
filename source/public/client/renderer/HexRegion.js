'use strict';

/* Grid-exact overlays on the battle map: a patch of hexes becomes ONE blanket polygon whose
   outline follows the grid and whose interior is a single flat fill.

   Extracted from ShipIcon.js so the ballistic/terrain hex overlays can use it too - the arcs, the
   EW blankets, the terrain footprints and the weapon splash areas are all the same problem, and
   the geometry here is subtle enough (winding, holes, corner rounding) that a second copy of it
   would be a liability. ShipIcon.js now delegates to this module; see also
   BallisticIconContainer.js.

   THE POINT: hexes in a radius-N patch grow with N SQUARED while the patch's boundary grows with
   N. A radius-5 splash is 91 hexes but only 66 boundary points, and it draws in TWO calls (fill +
   outline) instead of 91. Do not go back to a mesh per hex. */
window.HexRegion = function () {

    /* Corners of a pointy-top hex as unit offsets from its centre - the same 30/90/.../330 degree
       corners mathlib.getHexCorners uses to build the grid, so a hexagon drawn from these lands
       exactly on a grid hex.

       The grid-aligned overlays (the EW blankets, terrain footprints, splash areas) use them as
       they are, on an unrotated mesh. The weapon arcs emit them in the icon's LOCAL space and turn
       the finished mesh to the ship's facing, which is safe without any per-facing correction
       because a pointy-top hexagon maps onto itself under any 60 degree rotation and a ship's
       facing is always a multiple of 60 (mathlib.hexFacingToAngle) - the hexes stay grid-aligned
       however the ship is pointing. */
    var HEX_CORNERS = [30, 90, 150, 210, 270, 330].map(function (degrees) {
        var radians = degrees * Math.PI / 180;
        return { x: Math.cos(radians), y: Math.sin(radians) };
    });

    /* Axial neighbour steps, in the same order as the six hex directions: a steps along direction 0
       (due East), b along direction 1. The rest fall out of n[d-1] + n[d+1] = n[d], which is why
       direction 2 is b-a and direction 5 is a-b. */
    var AXIAL_NEIGHBOURS = [[1, 0], [0, 1], [-1, 1], [-1, 0], [0, -1], [1, -1]];

    var DEFAULT_BORDER_COLOUR = 0x08485e; //the straight arcs' cyan, brighter than their fill
    var MAX_MITER = 4;                    //see buildRibbon - hex corners never come near this

    /* The OUTLINE of a patch of grid hexes, as closed loops of points in game units relative to the
       centre hex - everything the overlays need, because a region is drawn as one blanket polygon
       with its boundary traced, not as a heap of individual hexagons.

       That is the whole point of working in loops. The hexes in a range-N patch grow with N SQUARED
       - ShipIcon's 60-hex arc cap is 10,981 of them - while its boundary grows with N, 726 edges at
       that same cap. Filling hex by hex meant a 790KB vertex buffer and 44,000 triangles for one
       hovered weapon; the loops are a couple of hundred triangles and a few tens of KB, and the
       player sees exactly the same thing: hex-true edges, flat colour inside.

       `accept(x, y, a, b)` decides membership - the arc tests and footprint sets live there - and is
       given both the centre in game units and its axial coordinates. Members go into a flat axial
       grid so the boundary sweep can ask "is my neighbour in?" with an array read rather than a hash
       lookup.

       An edge of a member hex is on the boundary exactly when the neighbour across it is not a
       member, which is a direct test - no counting every edge and keeping the singletons. Emitted
       from corner (5-d) to corner (6-d) for direction d, which walks the region counter-clockwise
       with the inside on the left, so outer boundaries come out counter-clockwise and any hole comes
       out clockwise. buildFill leans on that to tell one from the other. */
    function buildRegion(range, hexDistance, accept) {
        var stride = 2 * range + 1;
        var members = new Uint8Array(stride * stride);
        //bearings are clockwise, game space is counter-clockwise, so direction 1 sits at math angle -60
        var stepX = hexDistance * 0.5;
        var stepY = -hexDistance * Math.sqrt(3) / 2;
        var radius = hexDistance / Math.sqrt(3); //hexDistance is centre-to-centre; this is centre-to-corner
        var a, b;

        /* Cube coordinates in disguise: a steps in direction 0 plus b steps in direction 1 is the
           cube (a+b, -a, -b), whose distance from the origin is max(|a+b|, |a|, |b|). Clamping b to
           [-range-a, range-a] as well as to [-range, range] is therefore exactly "within range". */
        for (a = -range; a <= range; a++) {
            var last = Math.min(range, -a + range);

            for (b = Math.max(-range, -a - range); b <= last; b++) {
                if (accept(a * hexDistance + b * stepX, b * stepY, a, b)) members[(a + range) * stride + (b + range)] = 1;
            }
        }

        var edges = [];

        for (a = -range; a <= range; a++) {
            for (b = -range; b <= range; b++) {
                if (!members[(a + range) * stride + (b + range)]) continue;

                var centreX = a * hexDistance + b * stepX;
                var centreY = b * stepY;

                for (var d = 0; d < 6; d++) {
                    var na = a + AXIAL_NEIGHBOURS[d][0];
                    var nb = b + AXIAL_NEIGHBOURS[d][1];

                    //outside the grid is outside the region, so those edges are boundary too
                    if (na >= -range && na <= range && nb >= -range && nb <= range
                        && members[(na + range) * stride + (nb + range)]) continue; //interior seam

                    var from = HEX_CORNERS[(5 - d) % 6];
                    var to = HEX_CORNERS[(6 - d) % 6];

                    edges.push({
                        x1: centreX + from.x * radius, y1: centreY + from.y * radius,
                        x2: centreX + to.x * radius, y2: centreY + to.y * radius
                    });
                }
            }
        }

        return chainLoops(edges);
    }

    /* Offset (odd-r) to cube, byte-identical to hexagon.Offset.prototype.toCube but taking any plain
       {q, r} - mathlib.getNeighbouringHexes and mathlib.getRotatedHex both hand back bare objects,
       not Offsets. (r & 1) is deliberate rather than r % 2: it gives 1 for negative odd rows, which
       is what the game's own conversion does. */
    function offsetToCube(hex) {
        var x = hex.q - ((hex.r + (hex.r & 1)) / 2);

        return { x: x, y: -x - hex.r, z: hex.r };
    }

    /* An explicit LIST of grid hexes as a region, for the overlays whose shape is a footprint rather
       than a formula - a terrain unit's occupied hexes, a weapon's splash area.

       buildRegion works in the axial frame where the cube coordinate of (a, b) is (a+b, -a, -b), so
       inverting that gives a = -y and b = -z of the hex's cube offset from the centre. The region's
       local coordinates then come out as plain game-space deltas from the centre hex: no rotation,
       no axis flip, so the caller only has to place the mesh at fromHexToGame(centreHex).

       Verified against fromHexToGame over 48,841 centre/member pairs covering both row parities:
       zero mismatches, worst error 2.3e-13 game units. */
    function buildRegionFromHexes(centreHex, hexes, hexDistance) {
        if (!hexes || !hexes.length) return [];

        var centre = offsetToCube(centreHex);
        var members = new Set();
        var range = 0;

        hexes.forEach(function (hex) {
            var cube = offsetToCube(hex);
            var a = -(cube.y - centre.y);
            var b = -(cube.z - centre.z);

            members.add(a + ',' + b);
            //max(|a|, |b|, |a+b|) is this hex's distance from the centre in hexes
            range = Math.max(range, Math.abs(a), Math.abs(b), Math.abs(a + b));
        });

        return buildRegion(range, hexDistance, function (x, y, a, b) {
            return members.has(a + ',' + b);
        });
    }

    /* Boundary edges into closed loops, by following each edge's end vertex to the edge that starts
       there. Corner positions are rounded to whole game units to match them up: two hexes sharing a
       corner compute it from different centres and land a rounding error apart, while genuinely
       different corners are a hex side - 50 units - apart, so there is a wide margin either way.

       A vertex normally has one outgoing edge, but where two parts of a region meet at a single
       corner it has two; keeping a list per vertex and taking whichever is still unused walks such a
       pinch as two loops that touch, which fills correctly. */
    function chainLoops(edges) {
        var outgoing = new Map();
        var used = new Uint8Array(edges.length);
        var loops = [];

        function vertexKey(x, y) {
            return (Math.round(x) + 32768) * 65536 + Math.round(y) + 32768;
        }

        edges.forEach(function (edge, index) {
            var key = vertexKey(edge.x1, edge.y1);
            var starting = outgoing.get(key);

            if (starting) starting.push(index);
            else outgoing.set(key, [index]);
        });

        function takeEdgeFrom(x, y) {
            var starting = outgoing.get(vertexKey(x, y));

            while (starting && starting.length) {
                var next = starting.pop();

                if (!used[next]) return next;
            }

            return -1;
        }

        edges.forEach(function (edge, index) {
            if (used[index]) return;

            var loop = [];
            var current = index;

            while (current !== -1 && !used[current]) {
                used[current] = 1;
                loop.push({ x: edges[current].x1, y: edges[current].y1 });
                current = takeEdgeFrom(edges[current].x2, edges[current].y2);
            }

            if (loop.length > 2) loops.push(loop);
        });

        return loops;
    }

    //Shoelace. Positive is counter-clockwise, which buildRegion gives an outer boundary; a hole
    //is wound the other way and comes out negative.
    function getSignedArea(loop) {
        var area = 0;

        for (var i = 0, j = loop.length - 1; i < loop.length; j = i++) {
            area += (loop[j].x - loop[i].x) * (loop[j].y + loop[i].y);
        }

        return area / 2;
    }

    //Ray cast along +x, counting crossings.
    function isPointInLoop(point, loop) {
        var inside = false;

        for (var i = 0, j = loop.length - 1; i < loop.length; j = i++) {
            if ((loop[i].y > point.y) === (loop[j].y > point.y)) continue;

            if (point.x < (loop[j].x - loop[i].x) * (point.y - loop[i].y) / (loop[j].y - loop[i].y) + loop[i].x) inside = !inside;
        }

        return inside;
    }

    function traceLoop(path, loop) {
        loop.forEach(function (point, index) {
            if (index === 0) path.moveTo(point.x, point.y);
            else path.lineTo(point.x, point.y);
        });

        path.closePath();

        return path;
    }

    /* The blanket: one flat polygon per outer loop, holes punched where the region has them (the
       straight arcs' six-pointed star has one, around the ship's own hex). Triangulated once by
       ShapeGeometry into a single buffer - a few hundred triangles for an arc that would have been
       tens of thousands drawn hexagon by hexagon.

       DoubleSide because the triangulator's winding is its own business and this is a flat unlit
       overlay: there is no cost to being visible from both sides and no way to be caught out. */
    function buildFill(loops, colour, opacity) {
        var shapes = [];

        loops.forEach(function (loop) {
            if (getSignedArea(loop) > 0) shapes.push({ loop: loop, shape: traceLoop(new THREE.Shape(), loop) });
        });

        loops.forEach(function (loop) {
            if (getSignedArea(loop) > 0) return;

            //a hole sits inside exactly one outer loop, so any of its corners identifies the owner
            var owner = shapes.find(function (candidate) { return isPointInLoop(loop[0], candidate.loop); });

            if (owner) owner.shape.holes.push(traceLoop(new THREE.Path(), loop));
        });

        var material = new THREE.MeshBasicMaterial({
            color: colour,
            opacity: opacity,
            transparent: true,
            side: THREE.DoubleSide
        });

        return new THREE.Mesh(new THREE.ShapeGeometry(shapes.map(function (entry) { return entry.shape; })), material);
    }

    /* A closed loop as a miter-joined RIBBON of `width` game units, centred on the boundary - the
       thick-border form of buildOutline, for overlays that sit next to something whose own edge is
       drawn in world units.

       Miters are safe here without special cases: a hex-boundary loop turns 60 degrees one way or
       the other at every vertex (interior angle 120 for a convex corner, 240 for a notch), so the
       miter factor is a flat 1/sin(60) = 1.155 whichever way it turns - there are no near-parallel
       corners to throw a spike. The clamp and the reversal fallback are belt and braces.

       Two triangles per boundary point: 132 for a radius-5 disc, still one draw call. Joining at the
       shared miter vertices rather than overlapping quads matters for a TRANSPARENT ribbon, where an
       overlap would show as a darker blob on every corner. */
    function buildRibbon(loops, colour, opacity, width) {
        var positions = [];
        var half = width / 2;

        loops.forEach(function (loop) {
            var count = loop.length;
            if (count < 3) return;

            var outer = [];
            var inner = [];
            var i;

            for (i = 0; i < count; i++) {
                var previous = loop[(i + count - 1) % count];
                var point = loop[i];
                var next = loop[(i + 1) % count];

                var inX = point.x - previous.x, inY = point.y - previous.y;
                var outX = next.x - point.x, outY = next.y - point.y;
                var inLength = Math.sqrt(inX * inX + inY * inY) || 1;
                var outLength = Math.sqrt(outX * outX + outY * outY) || 1;

                //left-hand normals of the incoming and outgoing edges
                var n1x = -inY / inLength, n1y = inX / inLength;
                var n2x = -outY / outLength, n2y = outX / outLength;

                var mx = n1x + n2x, my = n1y + n2y;
                var mLength = Math.sqrt(mx * mx + my * my);
                var offsetX, offsetY;

                if (mLength < 1e-6) { //the path doubles back - no miter exists, use the edge normal
                    offsetX = n1x * half;
                    offsetY = n1y * half;
                } else {
                    mx /= mLength;
                    my /= mLength;

                    var miter = half / Math.max(mx * n1x + my * n1y, 1 / MAX_MITER);

                    offsetX = mx * miter;
                    offsetY = my * miter;
                }

                outer.push({ x: point.x + offsetX, y: point.y + offsetY });
                inner.push({ x: point.x - offsetX, y: point.y - offsetY });
            }

            for (i = 0; i < count; i++) {
                var j = (i + 1) % count;

                positions.push(
                    outer[i].x, outer[i].y, 0, inner[i].x, inner[i].y, 0, inner[j].x, inner[j].y, 0,
                    outer[i].x, outer[i].y, 0, inner[j].x, inner[j].y, 0, outer[j].x, outer[j].y, 0
                );
            }
        });

        var geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));

        //DoubleSide for the same reason buildFill needs it - the winding here is whatever the loop's was
        return new THREE.Mesh(geometry, new THREE.MeshBasicMaterial({
            color: colour === undefined ? DEFAULT_BORDER_COLOUR : colour,
            opacity: opacity === undefined ? 0.9 : opacity,
            transparent: true,
            side: THREE.DoubleSide
        }));
    }

    /* The region's boundary. Two forms, and which one is right depends on what the overlay sits
       next to:

       NO width - all loops in a single LineSegments. LineBasicMaterial.linewidth is ignored by the
       WebGL renderer, which here is the point: the border is one device pixel at every zoom level,
       so it neither thickens as you zoom in nor thins away as you zoom out. This is the ship arcs'
       convention (see also ShipIcon's buildArcOutline) and they keep it.

       A width in GAME UNITS - a ribbon of that width instead, so the border scales with the map.
       That is what an overlay drawn alongside a BallisticSprite needs: the sprite's own rim is a
       10px stroke baked into a 512px texture, i.e. ~3.4 game units, and a 1px line beside it reads
       as a different kind of edge entirely at every zoom but one.

       Colour and opacity default to the straight arcs' cobalt; every other overlay passes its own so
       it keeps the colour it is recognised by. */
    function buildOutline(loops, colour, opacity, width) {
        if (width > 0) {
            var ribbon = buildRibbon(loops, colour, opacity, width);
            ribbon.position.z = 0.01; //clear of the coplanar fill, still behind the ship sprite

            return ribbon;
        }

        var positions = [];

        loops.forEach(function (loop) {
            loop.forEach(function (point, index) {
                var next = loop[(index + 1) % loop.length];

                positions.push(point.x, point.y, 0, next.x, next.y, 0);
            });
        });

        var geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));

        var outlines = new THREE.LineSegments(
            geometry,
            new THREE.LineBasicMaterial({
                color: colour === undefined ? DEFAULT_BORDER_COLOUR : colour,
                opacity: opacity === undefined ? 0.9 : opacity,
                transparent: true
            })
        );
        outlines.position.z = 0.01; //clear of the coplanar fill, still behind the ship sprite

        return outlines;
    }

    /* Fill plus outline as one mesh, ready to be positioned and added. The outline is a CHILD of the
       fill so it inherits its rotation, its grid-lock correction and its removal.

       borderWidth is in game units and optional - omit it for the one-device-pixel line. */
    function buildOverlay(loops, colour, fillOpacity, borderColour, borderOpacity, borderWidth) {
        var fill = buildFill(loops, colour, fillOpacity);

        fill.add(buildOutline(loops, borderColour, borderOpacity, borderWidth));
        fill.position.z = -1;

        return fill;
    }

    /* Every overlay is rebuilt from scratch when what it depicts changes, so once one leaves the
       scene its buffers are dead - but THREE frees a geometry's GPU memory on dispose(), not on
       remove(). Children (the outline) go with their parent. Materials only ever reference shared
       textures, and Material.dispose() leaves those alone. */
    function dispose(overlay) {
        if (!overlay) return;

        overlay.children.forEach(dispose);

        if (overlay.geometry) overlay.geometry.dispose();
        if (overlay.material) overlay.material.dispose();
    }

    return {
        buildRegion: buildRegion,
        buildRegionFromHexes: buildRegionFromHexes,
        buildFill: buildFill,
        buildOutline: buildOutline,
        buildRibbon: buildRibbon,
        buildOverlay: buildOverlay,
        getSignedArea: getSignedArea,
        isPointInLoop: isPointInLoop,
        offsetToCube: offsetToCube,
        dispose: dispose
    };
}();
