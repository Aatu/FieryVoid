window.webglScene = (function(){

    var ZOOM_MAX = 8;
    var ZOOM_MIN = 0.2;

    function webglScene() {
        this.scene = null;
        this.camera = null;
        this.hexGridRenderer = null;

        this.element = null;

        this.initialized = false;
        this.zoom = 1;
        this.zoomTarget = this.zoom;
        this.draggingStartPosition = null;
        this.lastDraggingPosition = null;
        this.dragging = false;
    }

    webglScene.prototype.init = function (canvasId, element, hexGridRenderer) {
        this.element = element;
        this.hexGridRenderer = hexGridRenderer;

        this.scene = new THREE.Scene();

        this.width = window.innerWidth;
        this.height = window.innerHeight;

        this.camera = new THREE.OrthographicCamera(
            this.zoom*this.width / - 2,
            this.zoom*this.width / 2,
            this.zoom*this.height / 2,
            this.zoom*this.height / - 2,
            -500,
            1000
        );

        var geometry = new THREE.PlaneGeometry( 20, 20, 1, 1 );
        var material = new THREE.MeshBasicMaterial( { color: 0x00ff00, transparent: true, opacity: 0.5 } );
        var cube = new THREE.Mesh( geometry, material );
        this.scene.add( cube );





        this.scene.add(new THREE.AmbientLight(0xffffff));
        this.renderer = new THREE.WebGLRenderer({ alpha: true });
        this.renderer.setSize( this.width, this.height );
        this.renderer.context.getExtension('OES_standard_derivatives');

        jQuery(this.renderer.domElement)
            .addClass("webglCanvas").appendTo(canvasId);

        this.initEventListeners();

        this.initialized = true;

        this.hexGridRenderer.renderHexGrid(this.scene, ZOOM_MIN, ZOOM_MAX);
        this.render();
    };

    webglScene.prototype.moveCamera = function(position) {
        if (!this.initialized) {
            return;
        }

        this.camera.position.x -= position.x*this.zoom*this.zoom;
        this.camera.position.y += position.y*this.zoom*this.zoom;
    };

    webglScene.prototype.moveCameraTo = function(position) {
        if (!this.initialized) {
            return;
        }

        this.camera.position.x = position.x;
        this.camera.position.y = position.y;
    };

    webglScene.prototype.zoomCamera = function(zoom)
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

        this.hexGridRenderer.onZoom(this.zoom);
    };

    webglScene.prototype.render = function () {
        requestAnimationFrame(this.render.bind(this));
        this.renderer.render( this.scene, this.camera );
    };


    webglScene.prototype.initEventListeners = function()
    {
        //this.zooming.init(element);

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

        this.renderer.setSize( window.innerWidth, window.innerHeight );
    };

    webglScene.prototype.mouseDown = function(event)
    {
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.fromViewPortToGame(pos);

        this.draggingStartPosition = getViewPortAndGameObject(pos, gamePos);
        this.lastDraggingPosition = getViewPortAndGameObject(pos, gamePos);

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
        var gamePos = this.fromViewPortToGame(pos);

        this.fireEvent(
            'MouseMoveEvent',
            getViewPortAndGameObject(pos, gamePos)
        );

    };

    webglScene.prototype.drag = function(event)
    {
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.fromViewPortToGame(pos);
        var current = getViewPortAndGameObject(pos, gamePos);

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
            delta: getViewPortAndGameObject(deltaView, deltaGame)
        };

        this.distanceDragged += mathlib.distance({x:0, y:0}, deltaView);
        this.lastDraggingPosition = current;
        this.dragging(payload);
    };

    webglScene.prototype.click = function(event)
    {
        var pos = getMousePositionInObservedElement.call(this, event);
        var gamePos = this.fromViewPortToGame(pos);

        var payload = this.getViewPortAndGameObject(pos, gamePos);

        this.fireEvent(
            'ClickEvent',
            payload
        );
    };

    webglScene.prototype.fireEvent = function(eventName, payload)
    {

    };

    webglScene.prototype.fromViewPortToGame = function(pos)
    {
        var cameraPos = this.camera.position;
        var zoom = this.zoom;
        var windowDimensions = {width:this.width, height: this.height};
        var positionFromCenterOfScreen = {x: pos.x - windowDimensions.width/2, y: windowDimensions.height/2 - pos.y };
        var withZoom = {x: positionFromCenterOfScreen.x / zoom, y: positionFromCenterOfScreen.y / zoom};

        var positionFromCamera = {x:withZoom.x + cameraPos.x, y:withZoom.y + cameraPos.y}
        return positionFromCamera;
    };

    webglScene.prototype.fromGameToViewPort = function(pos)
    {
        var cameraPos = this.camera.position;
        var zoom = this.zoom;
        var windowDimensions = {width:this.width, height: this.height};


        var positionFromCamera = {x: pos.x - cameraPos.x, y:pos.y - cameraPos.y};
        var withZoom = {x: positionFromCamera.x * zoom, y: positionFromCamera.y * zoom};
        var positionFromCenterOfScreen = {x: withZoom.x + windowDimensions.width/2, y: windowDimensions.height/2 - withZoom.y};

        return positionFromCenterOfScreen;
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
        requestAnimationFrame( animateZoom.bind(this) );
        if ( this.zoomTarget && this.zoomTarget != this.zoom)
        {
            var change = (this.zoomTarget - this.zoom);
            if (Math.abs(change) < 0.00001 || (this.zoomTarget == 1 && Math.abs(change) < 0.01))
            {
                this.zoomCamera(this.zoomTarget);
            }
            else
            {
                this.zoomCamera(this.zoom + change*0.1);
            }
        }
    };

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
    };

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
    };

    function bindMouseWheelEvent(element)
    {
        element = element.get(0);
        if(element.addEventListener)
        {
            element.addEventListener('DOMMouseScroll', mouseWheel.bind(this), false);
            element.addEventListener('mousewheel', mouseWheel.bind(this), false);
        }
    };

    function cancelMouseWheelEvent(e)
    {
        e = e ? e : window.event;
        if(e.stopPropagation)
            e.stopPropagation();
        if(e.preventDefault)
            e.preventDefault();
        e.cancelBubble = true;
        e.cancel = true;
        e.returnValue = false;
        return false;
    };



    function getMousePositionInObservedElement(event)
    {
        return {
            x: event.pageX - this.element.offset().left,
            y: event.pageY - this.element.offset().top
        };
    }


    function getViewPortAndGameObject(v, g)
    {
        return {
            view: v,
            game: g
        };
    }


    return new webglScene();
})();
