window.CameraPositionAnimation = (function() {

    function CameraPositionAnimation(position, time, endTime) {
        Animation.call(this);
        this.time = time;
        this.position = position;
        this.lastTime = null;
        this.endTime = 0;

        this.duration = 1000;

        if (endTime === undefined) {
            this.endTime = 300;
        }

        this.startPosition = null;

        this.curve = new THREE.CubicBezierCurve(
            new THREE.Vector2( 0, 0 ),
            new THREE.Vector2( 0.5, 0.0 ),
            new THREE.Vector2( 0.5, 1 ),
            new THREE.Vector2( 1, 1 )
        );
    }

    CameraPositionAnimation.prototype = Object.create(Animation.prototype);

    CameraPositionAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        smoothMove.call(this, now, total, last, delta, zoom, back);
    };

    function smoothMove(now, total, last, delta, zoom, back, paused) {
        if (back) {
            return;
        }

        if (total > this.time && total < this.time + this.duration && ! paused) {
            if ( this.startPosition === null) {
                this.startPosition ={
                    x: window.webglScene.camera.position.x,
                    y: window.webglScene.camera.position.y
                };
            }
            var done = 1 - ((this.time + this.duration - total) / this.duration);
            var point = this.curve.getPoint(done).y;

            var position = mathlib.getPointBetween(this.startPosition, this.position, point, true);

            doMove(position);
        } else {
            this.startPosition = null;
        }
    }

    function instantMove(now, total, last, delta, zoom, back) {
        if (back) {
            return;
        }

        if (this.lastTime !== null && this.lastTime < this.time && total > this.time) {
            doMove(this.position);
        }

        this.lastTime = total;
    }

    CameraPositionAnimation.prototype.getDuration = function() {
        return this.duration + this.endTime;
    };

    function doMove(position) {
        window.webglScene.moveCameraTo(position);
    }

    return CameraPositionAnimation;
})();