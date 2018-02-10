window.HexTargetedWeaponFireAnimation = (function(){

    function HexTargetedWeaponFireAnimation(time, movementAnimations, shipIconContainer, turn, particleEmitterContainer) {

        this.duration = 0;
        this.allFire = weaponManager.getAllHexTargetedBallistics();
        this.time = time || 0;
        this.animations = [];
        this.shipIconContainer = shipIconContainer;
        this.movementAnimations = movementAnimations;
        this.particleEmitterContainer = particleEmitterContainer;
        this.turn = turn;
        console.log(this.allFire);

        this.duration = 0;

        this.animations = [];

        this.allFire.forEach(function(fire) {
            this.duration += buildAnimation.call(this, fire, this.duration + this.time);
        }, this);

    }

    HexTargetedWeaponFireAnimation.prototype = Object.create(Animation.prototype);

    HexTargetedWeaponFireAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        this.animations.forEach(function (animation) {
            animation.render(now, total, last, delta, zoom, back, paused);
        })
    };

    HexTargetedWeaponFireAnimation.prototype.getDuration = function(){
        return this.duration;
    };

    HexTargetedWeaponFireAnimation.prototype.cleanUp = function () {
        this.animations.forEach(function (animation) {
            animation.cleanUp();
        })
    };

    function buildAnimation(fire, time) {

        var weapon = fire.weapon;
        var shooter = fire.shooter;
        var startPosition = FireAnimationHelper.getShipPositionForFiring(this.shipIconContainer.getByShip(shooter), time, this.movementAnimations, weapon, this.turn);
        var endPosition = window.coordinateConverter.fromHexToGame(new hexagon.Offset(fire.fireOrder.x, fire.fireOrder.y));

        var hit = fire.fireOrder.shotshit !== 0;

        var shot = null;



        var cameraAnimation = new CameraPositionAnimation(endPosition, time);
        this.animations.push(cameraAnimation);


        switch (weapon.animation) {
            case 'ball':
            default:
                shot = new TorpedoEffect(
                    this.particleEmitterContainer,
                    {
                        size: 20 * weapon.animationExplosionScale,
                        origin: startPosition,
                        target: endPosition,
                        color: new THREE.Color(weapon.animationColor[0]/255, weapon.animationColor[1]/255, weapon.animationColor[2]/255),
                        hit: hit,
                        damage: 0,
                        time: time
                    });
                break;
        }


        var duration = shot.getDuration();


        this.animations.push(shot);
        if (hit) {
            var explosion = new Explosion(
                this.particleEmitterContainer, {
                    size: 60 * weapon.animationExplosionScale,
                    position: endPosition,
                    type: "emp",
                    color: new THREE.Color(weapon.animationColor[0] / 255, weapon.animationColor[1] / 255, weapon.animationColor[2] / 255),
                    time: time + shot.getDuration()
                }
            );
            this.animations.push(explosion);
            duration += 1000;
        }

        return duration;
    }

    return HexTargetedWeaponFireAnimation;
})();