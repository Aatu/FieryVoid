window.Animation = (function() {
    function Animation() {
        this.active = false;
    }

    Animation.prototype.start = function () {
        this.active = true;
    };

    Animation.prototype.stop = function () {
        this.active = false;
    };

    Animation.prototype.update = function (gameData) {

    };

    Animation.prototype.render = function (now, total, last, delta) {

    };

    return Animation;
})();