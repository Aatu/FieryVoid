"use strict";

window.HexTargetedWeaponFireAnimation = function () {
/*
    function HexTargetedWeaponFireAnimation(time, movementAnimations, shipIconContainer, turn, particleEmitterContainer, logAnimation) {

        this.duration = 0;
        this.allFire = weaponManager.getAllHexTargetedBallistics();
        this.time = time || 0;
        this.animations = [];
        this.shipIconContainer = shipIconContainer;
        this.movementAnimations = movementAnimations;
        this.particleEmitterContainer = particleEmitterContainer;
        this.turn = turn;
        this.logAnimation = logAnimation;

        this.duration = 0;

        this.animations = [];

        this.allFire.forEach(function (fire) {

            this.logAnimation.addLogEntryFire(fire.fireOrder, this.time + this.duration);

            this.duration += buildAnimation.call(this, fire, this.duration + this.time);
        }, this);
    }
*/

function HexTargetedWeaponFireAnimation(time, movementAnimations, shipIconContainer, turn, particleEmitterContainer, logAnimation, fires) {

    this.duration = 0;
    this.time = time || 0;
    this.animations = [];
    this.shipIconContainer = shipIconContainer;
    this.movementAnimations = movementAnimations;
    this.particleEmitterContainer = particleEmitterContainer;
    this.turn = turn;
    this.logAnimation = logAnimation;

    // 🔧 Use the provided fires (per ship) if given, else fall back to global.
    this.allFire = fires || weaponManager.getAllHexTargetedBallistics();

    this.allFire.forEach(function (fire) {
        this.logAnimation.addLogEntryFire(fire.fireOrder, this.time + this.duration);
        this.duration += buildAnimation.call(this, fire, this.duration + this.time);
    }, this);
}

    HexTargetedWeaponFireAnimation.prototype = Object.create(Animation.prototype);

    HexTargetedWeaponFireAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        this.animations.forEach(function (animation) {
            animation.render(now, total, last, delta, zoom, back, paused);
        });
    };

    HexTargetedWeaponFireAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    HexTargetedWeaponFireAnimation.prototype.cleanUp = function () {
        this.animations.forEach(function (animation) {
            animation.cleanUp();
        });
    };

    function buildAnimation(fire, time) {

        var weapon = fire.weapon;
        var shooter = fire.shooter;
        if(shipManager.shouldBeHidden(shooter)) return 0;

        var startPosition = FireAnimationHelper.getShipPositionForFiring(this.shipIconContainer.getByShip(shooter), time, this.movementAnimations, weapon, this.turn);
        var endPosition = window.coordinateConverter.fromHexToGame(new hexagon.Offset(fire.fireOrder.x, fire.fireOrder.y));
		
        var modeIteration = fire.fireOrder.firingMode; //change weapons data to reflect mode actually used - DK - 6 Jan 24
            if(modeIteration != weapon.firingMode){
                while(modeIteration != weapon.firingMode){ //will loop until correct mode is found
                weapon.changeFiringMode();
                }
            }
            
		var color;		
		if (weapon.noProjectile) { //Some weapon like Spark Field shouldn't have projectiles - DK - 4 Jan 24
		    color = new THREE.Color((0 / 255, 0 / 255, 0 / 255));
		} else {
		    color = new THREE.Color(weapon.animationColor[0] / 255, weapon.animationColor[1] / 255, weapon.animationColor[2] / 255);
		}
		
        if (weapon.specialPosNoLauncher){
		var hit = true;       	
        }else{
        var hit = fire.fireOrder.shotshit !== 0;
		}
		
        var shot = null;

        var cameraAnimation = new CameraPositionAnimation(endPosition, time);
        this.animations.push(cameraAnimation);

        switch (weapon.animation) {
            //Could insert new animation/audio here for hex targeted weapons.
            case 'blink':
                shot = new BlinkEffect(this.particleEmitterContainer, {
                    size: 20 * weapon.animationExplosionScale,
                    origin: startPosition,
                    target: endPosition,
                    color: color,
                    hit: hit,
                    damage: 0,
                    time: time
                });
            break;
            case 'ball':
            default:
                shot = new TorpedoEffect(this.particleEmitterContainer, {
                    size: 20 * weapon.animationExplosionScale,
                    origin: startPosition,
                    target: endPosition,
                    color: color,
                    hit: hit,
                    damage: 0,
                    time: time
                });
                break;
        }

        var duration = shot.getDuration();

        this.animations.push(shot);
        if (hit || weapon instanceof ThoughtWave) {
                var explosion = new Explosion(this.particleEmitterContainer, {
                    size: 60 * weapon.animationExplosionScale,
                    position: endPosition,
                    type: "emp",
                    color: new THREE.Color(weapon.animationColor[0] / 255, weapon.animationColor[1] / 255, weapon.animationColor[2] / 255), //Always use weapon colour - DK - 4 Jan 24
                    time: time + shot.getDuration()
                });
                this.animations.push(explosion);
                duration += 1000;  
        }

        return duration;
    }

    return HexTargetedWeaponFireAnimation;
}();