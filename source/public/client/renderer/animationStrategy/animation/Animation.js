window.Animation = (function() {
    function Animation(doneCallback) {
        this.active = false;
        this.onDoneCallback = doneCallback;
    }

    Animation.prototype.start = function () {
        this.active = true;
    };

    Animation.prototype.stop = function () {
        this.active = false;
    };

    Animation.prototype.reset = function () {

    };

    Animation.prototype.cleanUp = function () {

    };

    Animation.prototype.update = function (gameData) {

    };

    Animation.prototype.render = function (now, total, last, delta) {

    };

    Animation.prototype.done = function() {
        if (this.onDoneCallback) {
            this.onDoneCallback();
        }
    };

    return Animation;
})();