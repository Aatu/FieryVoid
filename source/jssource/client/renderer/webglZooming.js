window.webglZooming = (function(){

    function Zooming()
    {
        this.wheeltimer = null;
        this.wheelzoom = 0;
        this.zoominprogress = false;
        this.zoom = 1;
        this.zoomTarget = null;

        this.element = null;
    };

    Zooming.prototype.getCurrentZoom = function()
    {
        return this.zoom;
    };

    Zooming.prototype.init = function(element)
    {
        this.element = element;
        this.bindEvent(element);
        this.animate();
    };

    Zooming.prototype.animate = function()
    {
        requestAnimationFrame(this.animate.bind(this));
        if ( this.zoomTarget && this.zoomTarget != this.zoom)
        {
            var change = (this.zoomTarget - this.zoom);
            if (Math.abs(change) < 0.00001 || (this.zoomTarget == 1 && Math.abs(change) < 0.01))
            {
                this.dispatchZoom(this.zoomTarget);
            }
            else
            {
                this.dispatchZoom(this.zoom + change*0.1);
            }
        }
    };

    Zooming.prototype.mouseWheel = function(e)
    {
        e = e ? e : window.event;
        var wheelData = e.detail ? e.detail * -1 : e.wheelDelta / 40;

        var step = 0;
        if ( wheelData < 0)
            step = -1;
        else
            step = 1;

        var offsetLeft = this.element[0].offsetLeft;
        var offsetTop = this.element[0].offsetTop;

        var x = e.pageX - offsetLeft;
        var y = e.pageY - offsetTop;

        this.changeZoom(step);
        return this.cancelEvent(e);
    };

    Zooming.prototype.changeZoom = function(zoom)
    {
        zoom *= 0.5;
        if (zoom < -0.5)
            zoom = -0.5;

        this.zoominprogress = false;
        var newzoom = this.zoom + (this.zoom * zoom);

        //console.log(zoom);


        if (newzoom < 0.01)
            newzoom = 0.01;

        if (newzoom > 1)
            newzoom = 1;

        //newzoom = parseFloat(newzoom.toFixed(2));
        //console.log("zoom to: " + newzoom);
        this.zoomTarget = newzoom;
    };

    Zooming.prototype.dispatchZoom = function(zoom)
    {
        var oldZoom = this.zoom;
        this.zoom = zoom;

        this.dispatcher.dispatch({
            name: "ZoomEvent",
            zoom: zoom,
            oldZoom: oldZoom
        });
    };

    Zooming.prototype.bindEvent = function(element)
    {
        element = element.get(0);

        var o = this;
        var callback = function(event){o.mouseWheel.call(o, event);};

        if(element.addEventListener)
        {
            element.addEventListener('DOMMouseScroll', callback, false);
            element.addEventListener('mousewheel', callback, false);
        }
    };

    Zooming.prototype.cancelEvent = function(e)
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

    return new Zooming();
})();