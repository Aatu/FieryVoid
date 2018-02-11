window.webglScene = (function(){

    var ZOOM_MAX = 7;
    var ZOOM_MIN = 0.1;

    function webglScene() {
        this.scene = null;
        this.camera = null;
        this.hexGridRenderer = null;
        this.phaseDirector = null;
        this.coordinateConverter = null;

        this.element = null;

        this.initialized = false;

        this.zoom = 1;
        this.zoomTarget = this.zoom;

        this.draggingStartPosition = null;
        this.lastDraggingPosition = null;
        this.dragging = false;
        this.draggingDistanceTreshold = 20;
        this.distanceDragged = 0;
        this.stats = null;
    }

    webglScene.prototype.init = function (canvasId, element, hexGridRenderer, animationTimeline, gamedata, coordinateConverter) {
        this.element = element;
        this.hexGridRenderer = hexGridRenderer;
        this.phaseDirector = animationTimeline;
        this.coordinateConverter = coordinateConverter;

        this.scene = new THREE.Scene();

        this.width = window.innerWidth;
        this.height = window.innerHeight;
        this.coordinateConverter.init(this.width, this.height);
        this.phaseDirector.init(this.coordinateConverter, this.scene);

        this.camera = new THREE.OrthographicCamera(
            this.zoom*this.width / - 2,
            this.zoom*this.width / 2,
            this.zoom*this.height / 2,
            this.zoom*this.height / - 2,
            -1000,
            1000
        );

        this.camera.setPosition = new THREE.Vector3(0, 0, 200);

        /*
        var geometry = new THREE.PlaneGeometry( 20, 20, 1, 1 );
        var material = new THREE.MeshBasicMaterial( { color: 0x00ff00, transparent: true, opacity: 0.5 } );
        var cube = new THREE.Mesh( geometry, material );
        this.scene.add( cube );


        var sprite = new BallisticSprite({x:0, y:0}, 'launch');
        sprite.show();
        this.scene.add(sprite.mesh);
         */


        this.scene.add(new THREE.AmbientLight(0xffffff));
        this.renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        this.renderer.setSize( this.width, this.height );
        this.renderer.context.getExtension('OES_standard_derivatives');

        jQuery(this.renderer.domElement)
            .addClass("webglCanvas").appendTo(canvasId);

        this.initEventListeners();

        this.stats = new Stats();
        this.stats.showPanel( 0 ); // 0: fps, 1: ms, 2: mb, 3+: custom
        document.body.appendChild( this.stats.dom );

        this.initialized = true;
        this.hexGridRenderer.renderHexGrid(this.scene, ZOOM_MIN, ZOOM_MAX);
        this.phaseDirector.receiveGamedata(gamedata, this);
        this.render();
    };

    webglScene.prototype.receiveGamedata = function(gamedata) {
        if (!this.phaseDirector) {
            return;
        }

        this.phaseDirector.receiveGamedata(gamedata, this);
    };

    webglScene.prototype.customEvent = function(name, payload) {
        if (!this.initialized) {
            return;
        }

        this.phaseDirector.relayEvent(
            name,
            payload
        );
    };

    webglScene.prototype.moveCamera = function(position) {
        if (!this.initialized) {
            return;
        }

        this.camera.position.x -= position.x*this.zoom*this.zoom;
        this.camera.position.y += position.y*this.zoom*this.zoom;


        this.coordinateConverter.onCameraMoved(this.camera.position);
        this.phaseDirector.relayEvent(
            'ScrollEvent',
            this.camera.position
        );
    };

    webglScene.prototype.moveCameraTo = function(position) {
        if (!this.initialized) {
            return;
        }

        this.camera.position.x = position.x;
        this.camera.position.y = position.y;

        this.coordinateConverter.onCameraMoved(this.camera.position);
        this.phaseDirector.relayEvent(
            'ScrollEvent',
            this.camera.position
        );
    };

    webglScene.prototype.zoomCamera = function(zoom, animationReady)
    {
        this.zoom = zoom;

        if (! this.initialized) {
            return;
        }

        this.camera.left = this.zoom * this.width / -2;
        this.camera.right = this.zoom * this.width / 2;
        this.camera.top = this.zoom * this.height / 2;
        this.camera.bottom = this.zoom * this.height / -2;

        this.camera.updateProjectionMatrix();

        this.coordinateConverter.onZoom(this.zoom);
        this.hexGridRenderer.onZoom(this.zoom);

        this.phaseDirector.relayEvent(
            'ZoomEvent',
            {
                zoom: this.zoom,
                animationReady: Boolean(animationReady)
            }
        );

        var alpha = zoom > 6 ? zoom - 6 : 0;
        if (alpha > 1) {
            alpha = 1;
        }

        jQuery('#background').css({opacity: 1-alpha});
    };

    webglScene.prototype.render = function () {
        this.stats.begin();
        this.phaseDirector.render(this.scene, this.coordinateConverter, this.zoom);
        this.renderer.render( this.scene, this.camera);
        animateZoom.call(this);
        this.stats.end();

        requestAnimationFrame(this.render.bind(this));
    };


    webglScene.prototype.initEventListeners = function()
    {
        bindMouseWheelEvent.call(this, this.element);
        animateZoom.call(this);

        jQuery(window).resize(this.onWindowResize.bind(this));
        this.element.on("mousedown",  this.mouseDown.bind(this));
        this.element.on("mouseup",    this.mouseUp.bind(this));
        this.element.on("mouseout",   this.mouseOut.bind(this));
        this.element.on("mouseover",   this.mouseOver.bind(this));
        this.element.on("mousemove",  this.mouseMove.bind(this));

        return this;
    };

    webglScene.prototype.onWindowResize = function() {
        this.width = window.innerWidth;
        this.height = window.innerHeight;

        this.zoomCamera(this.zoom);

        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.coordinateConverter.onResize(window.innerWidth, window.innerHeight);
    };

    webglScene.prototype.mouseDown = function(event)
    {
        event.stopPropagation();
        event.preventDefault();
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.coordinateConverter.fromViewPortToGame(pos);

        this.draggingStartPosition = getPositionObject(pos, gamePos);
        this.lastDraggingPosition = getPositionObject(pos, gamePos);

        var self = this;
        var payload = {
            start: this.draggingStartPosition,
            ctrlKey: event.ctrlKey,
            altKey: event.altKey,
            shiftKey: event.shiftKey,
            metaKey: event.metaKey,
            capture: function(callback){
                self.dragging = callback;
                payload.stopped = true;
            }
        };

        scroll.call(this, payload);
    };

    webglScene.prototype.mouseUp = function(event)
    {
        event.stopPropagation();
        event.preventDefault();

        if (this.distanceDragged < this.draggingDistanceTreshold)
            this.click(event);

        if (this.dragging)
            this.dragging({release:true});

        this.distanceDragged = 0;
        this.dragging = false;
    };

    webglScene.prototype.mouseOut = function(e)
    {
        if (this.dragging)
            this.fireEvent('DragEvent', {release:true});

        this.fireEvent('MouseOutEvent', {});

        this.distanceDragged = 0;
        this.dragging = false;
    };

    webglScene.prototype.mouseOver = function(e)
    {

    };

    webglScene.prototype.mouseMove = function(event)
    {
        if (this.dragging)
            this.drag(event);
        else
            this.doMouseMove(event);
    };

    webglScene.prototype.doMouseMove = function(event)
    {
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.coordinateConverter.fromViewPortToGame(pos);
        var hexPos = this.coordinateConverter.fromGameToHex(gamePos);

        this.phaseDirector.relayEvent(
            'MouseMoveEvent',
            getPositionObject(pos, gamePos, hexPos)
        );

    };

    webglScene.prototype.drag = function(event)
    {
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.coordinateConverter.fromViewPortToGame(pos);
        var current = getPositionObject(pos, gamePos);

        var deltaView = {
            x: pos.x - this.lastDraggingPosition.view.x,
            y: pos.y - this.lastDraggingPosition.view.y
        };

        var deltaGame = {
            x: (pos.x - this.lastDraggingPosition.view.x) * (1/this.zoom),
            y: (pos.y - this.lastDraggingPosition.view.y) * (1/this.zoom)
        };

        var payload = {
            start: this.draggingStartPosition,
            previous: this.lastDraggingPosition,
            current: current,
            delta: getPositionObject(deltaView, deltaGame)
        };

        this.distanceDragged += mathlib.distance({x:0, y:0}, deltaView);
        this.lastDraggingPosition = current;
        this.dragging(payload);
    };

    webglScene.prototype.click = function(event)
    {
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.coordinateConverter.fromViewPortToGame(pos);
        var hexPos = this.coordinateConverter.fromGameToHex(gamePos, true);
        var payload = getPositionObject.call(this, pos, gamePos, hexPos);
        payload.button = event.button;

        console.log(pos, hexPos);
        if (this.lastPositionClicked) {
            //console.log("direction", mathlib.getCompassHeadingOfPoint(hexPos, this.lastPositionClicked));
        }


        this.phaseDirector.relayEvent(
            'ClickEvent',
            payload
        );

        if (! this.lastPositionClicked) {
            //console.log("set last position clicked");
            this.lastPositionClicked = hexPos;
        }
    };

    webglScene.prototype.fireEvent = function(eventName, payload)
    {

    };

    function scroll(payload)
    {
        if (payload.stopped)
            return;

        if (payload.capture)
        {
            payload.capture(scroll.bind(this));
            return;
        }

        if (payload.release)
            return;

        var position = {
            x: payload.delta.game.x,
            y: payload.delta.game.y
        };

        this.moveCamera(position);
    }

    function animateZoom()
    {
        if ( this.zoomTarget && this.zoomTarget != this.zoom)
        {
            var change = (this.zoomTarget - this.zoom)*0.1;
            if (Math.abs(change) < 0.0001 || (this.zoomTarget == 1 && Math.abs(change) < 0.0001))
            {
                this.zoomCamera(this.zoomTarget, true);
            }
            else
            {
                this.zoomCamera(this.zoom + change);
            }
        }
    }

    function mouseWheel(e)
    {
        e = e ? e : window.event;
        var wheelData = e.detail ? e.detail * -1 : e.wheelDelta / 40;

        var step = 0;
        if ( wheelData < 0)
            step = 1;
        else
            step = -1;

        changeZoom.call(this, step);
        return cancelEvent(e);
    }

    function changeZoom(zoom)
    {
        zoom *= 0.5;
        if (zoom < -0.5)
            zoom = -0.5;

        this.zoominprogress = false;
        var newzoom = this.zoom + (this.zoom * zoom);

        if (newzoom < ZOOM_MIN)
            newzoom = ZOOM_MIN;

        if (newzoom > ZOOM_MAX)
            newzoom = ZOOM_MAX;

        this.zoomTarget = newzoom;
    }

    function bindMouseWheelEvent(element)
    {
        element = element.get(0);
        if(element.addEventListener)
        {
            element.addEventListener('DOMMouseScroll', mouseWheel.bind(this), false);
            element.addEventListener('mousewheel', mouseWheel.bind(this), false);
        }
    }

    function getMousePositionInObservedElement(event)
    {
        return {
            x: event.pageX - this.element.offset().left,
            y: event.pageY - this.element.offset().top
        };
    }


    function getPositionObject(view, game, hex)
    {
        var o = {
            view: view,
            game: game
        };

        if (hex) {
            o.hex = hex;
        }

        return o;
    }


    return new webglScene();
})();
