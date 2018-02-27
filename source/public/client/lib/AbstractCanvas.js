'use strict';

window.AbstractCanvas = function () {

    function AbstractCanvas() {}

    AbstractCanvas.prototype.create = function (width, height, debug) {
        var canvas = $('<canvas width="' + width + '" height="' + height + '"></canvas>').css({
            position: 'absolute',
            top: '100px',
            right: '100px',
            border: '1px solid red',
            zIndex: 1000,
            backgroundColor: 'black' //'transparent'
            //,display: 'none'
        });
        if (debug) {
            $(document.body).append(canvas);
        }

        canvas = canvas[0];
        return canvas;
    };

    return new AbstractCanvas();
}();