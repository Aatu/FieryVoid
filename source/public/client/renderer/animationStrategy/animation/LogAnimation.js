window.LogAnimation = (function(){

    function LogAnimation(){
        Animation.call(this);
        this.entries = [];
        this.nextToDisplay = null;
        this.nextToHide = null;
        this.currentTime = 0;
    }

    LogAnimation.prototype = Object.create(Animation.prototype);

    LogAnimation.prototype.render = function (now, total, last, delta) {

        var lastCurrentTime = this.currentTime;
        this.currentTime = total;

        if (lastCurrentTime > total) { //moving backwards in time
            tryToHideNext.call(this);
        } else {
            tryToShowNext.call(this);
        }
    };

    LogAnimation.prototype.addLogEntryFire = function (fire, time) {
        this.entries.push({time: time, fire: fire, displayed: null});
        calculateDisplay.call(this);
    };

    LogAnimation.prototype.addLogEntryMove = function (movement, time) {
        this.entries.push({time: time, movement: movement, displayed: null});
        calculateDisplay.call(this);
    };

    LogAnimation.prototype.cleanUp = function() {
        this.entries.forEach(function (entry) {
            if (entry.fire) {
                window.combatLog.removeFireOrders(entry.displayed);
            } else {
                //window.combatLog.removeMoves(entry.movement);
            }
        })
    };

    function tryToHideNext() {
        if (this.nextToHide && this.currentTime < this.nextToHide.time) {
            if (this.nextToHide.fire) {
                window.combatLog.removeFireOrders(this.nextToHide.displayed);
            } else {
                //window.combatLog.removeMoves(this.nextToHide.movement);
            }

            calculateDisplay.call(this);
        }
    }

    function tryToShowNext() {
        if (this.nextToDisplay && this.currentTime > this.nextToDisplay.time) {
            if (this.nextToDisplay.fire) {
                this.nextToDisplay.displayed = window.combatLog.logFireOrders(this.nextToDisplay.fire);
            } else {
                //window.combatLog.logMoves(this.nextToDisplay.movement);
            }

            calculateDisplay.call(this);
        }
    }

    function calculateDisplay() {

        var next = null;
        var nextTime = null;

        var last = null;
        var lastTime = null;

        this.entries.forEach(function (entry) {

            var timeDiff = entry.time - this.currentTime;

            if (timeDiff > 0) {
                if (nextTime === null || timeDiff < nextTime) {
                    next = entry;
                    nextTime = entry.time;
                }
            } else {
                if (lastTime === null || timeDiff > lastTime) {
                    last = entry;
                    lastTime = entry.time;
                }
            }
        }, this);

        this.nextToDisplay = next;
        this.nextToHide = last;
    }

    return LogAnimation;
})();