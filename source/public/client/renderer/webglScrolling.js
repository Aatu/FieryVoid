"use strict";

window.webglSrcolling = function () {

    function Scrolling() {
        this.position = { x: 0, y: 0 };
        this.scrollingSpeed = 1;
        this.zoom = 1;
    };

    Scrolling.prototype.getCurrentPosition = function () {
        return this.position;
    };

    Scrolling.prototype.onZoom = function (event) {
        if (event.zoom) this.zoom = event.zoom;
    };

    Scrolling.prototype.getScrollingSpeed = function () {
        return this.scrollingSpeed * (1 / this.zoom);
    };

    Scrolling.prototype.scroll = function (payload) {
        if (payload.stopped) return;

        if (payload.capture) {
            payload.capture(this.scroll.bind(this));
            return;
        }

        if (payload.release) return;

        this.position.x -= payload.delta.game.x;
        this.position.y += payload.delta.game.y;
    };

    Scrolling.prototype.scrollTo = function (pos) {
        this.position.x = pos.x;
        this.position.y = pos.y;

        this.dispatch(this.position);
    };

    return new Scrolling();
}();