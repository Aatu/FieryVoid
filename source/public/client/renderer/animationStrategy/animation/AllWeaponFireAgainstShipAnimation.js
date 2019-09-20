"use strict";

window.AllWeaponFireAgainstShipAnimation = function () {

    function AllWeaponFireAgainstShipAnimation(ship, shipIconContainer, particleEmitterContainer, gamedata, time, scene, movementAnimations, logAnimation) {
        Animation.call(this);

        this.gamedata = gamedata;
        this.ship = ship;
        this.shipIcon = shipIconContainer.getByShip(ship);
        this.shipIconContainer = shipIconContainer;
        this.particleEmitterContainer = particleEmitterContainer;
        this.time = time || 0;
        this.duration = 0;
        this.scene = scene;
        this.movementAnimations = movementAnimations;
        this.logAnimation = logAnimation;

        this.incomingFire = groupByShipAndWeapon(weaponManager.getAllFireOrdersForDisplayingAgainst(ship));

        this.animations = [];

        if (this.incomingFire.length === 0) {
            return;
        }

        this.systemDestroyedEffect = new SystemDestroyedEffect(scene)
        this.animations.push(this.systemDestroyedEffect)
	 
        var cameraAnimation = new CameraPositionAnimation(getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(ship), this.time), this.time);
        this.animations.push(cameraAnimation);
        this.duration += cameraAnimation.getDuration();

        this.incomingFire.forEach(function (group) {

            var extraTime = 0;

            this.logAnimation.addLogEntryFire(group.map(function (entry) {
                return entry.fireOrder;
            }), this.time + this.duration);

            var durations = group.map(function (group) {
                extraTime += Math.random() * 100 + 300;
                return buildFireAnimations.call(this, group, extraTime) + extraTime;
            }, this);

            this.duration += durations.reduce(function (longest, current) {
                if (current > longest) {
                    return current;
                }
                return longest;
            }, 0);
        }, this);

        this.duration += 1000;
    }

    AllWeaponFireAgainstShipAnimation.prototype = Object.create(Animation.prototype);

    AllWeaponFireAgainstShipAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        this.animations.forEach(function (animation) {
            animation.render(now, total, last, delta, zoom, back, paused);
        });
    };

    AllWeaponFireAgainstShipAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    AllWeaponFireAgainstShipAnimation.prototype.cleanUp = function () {
        this.animations.forEach(function (animation) {
            animation.cleanUp();
        });
    };

    function groupByShipAndWeapon(incomingFire) {
        var grouped = {};        
        
        incomingFire.forEach(function (fire) {
            var key = fire.shooter.id + "-" + fire.weapon.constructor.name + "-" + fire.firingMode + '-' + fire.calledid; //split called shots as well!
            if (grouped[key]) {
                grouped[key].push(fire);
            } else {
                grouped[key] = [fire];
            }
        });
        
        //can't sort main array directly...
        var groupedKeys = Object.keys(grouped);        
        groupedKeys.sort(function (a, b){ 
            //compare first object in both groups - every group should contain only fire by one shooter from one weapon, and by default at one target
            var obj1 = grouped[a][0];
            var obj2 = grouped[b][0];
			//Marcin Sawicki September 2019: use actual firing resolution order if possible! Same weapons firing at same target should have consecutive resolution more or less	
	      if (obj1.fireOrder.resolutionOrder > obj2.fireOrder.resolutionOrder){ //shots resolved earlier displayed earlier
		      return 1;
	      }else if (obj1.fireOrder.resolutionOrder < obj2.fireOrder.resolutionOrder){ 
		      return -1;
	      }		  
            else if(obj1.shooter.flight && !obj2.shooter.flight){ //fighters after ships
                return 1;                   
            }else if(!obj1.shooter.flight && obj2.shooter.flight){ //fighters after ships
                return -1;                   
            }else if (obj1.weapon.priority !== obj2.weapon.priority){
                return obj1.weapon.priority-obj2.weapon.priority; 
            }
            else {
                var val = obj1.shooter.id - obj2.shooter.id;
                if (val == 0) val = obj1.id - obj2.id;
                return val;
            } 
        });        
        return groupedKeys.map(function (key) {
            return grouped[key];
        });
        /* otiginal version, before sorting keys
        return Object.keys(grouped).map(function (key) {
            return grouped[key];
        });
        */
    }

    function buildFireAnimations(incomingFire, extraStartTime) {

        var timeInterval = 50;

        Math.seedrandom(incomingFire.id);

        var duration = 0;

        var shots = incomingFire.shots;
        var hits = incomingFire.hits;
        var misses = shots - hits;

        var firstMisses = Math.round(Math.random() * misses);
        var lastMisses = misses - firstMisses;

        var shotsFired = 0;

        var systemsDestroyed = getAmountOfSystemsDestroyed(incomingFire);
        var structuresDestroyed = getAmountOfStructuresDestroyed(incomingFire);

        while (firstMisses--) {
            duration = addAnimation.call(this, incomingFire, duration, false, timeInterval * shotsFired + extraStartTime, shotsFired);
            shotsFired++;
        }

        let destroyedNames = getSystemNamesDestroyed(incomingFire);
        while (hits--) {
            var damageData = divideDamage(systemsDestroyed, structuresDestroyed, hits);
            systemsDestroyed = damageData.systemsDestroyed;
            structuresDestroyed = damageData.structuresDestroyed;
            var damage = damageData.damage;

            var picked = destroyedNames;
            destroyedNames = [];
            

            duration = addAnimation.call(this, incomingFire, duration, true, timeInterval * shotsFired + extraStartTime, shotsFired, damage, picked);
            shotsFired++;
        }

        while (lastMisses--) {
            duration = addAnimation.call(this, incomingFire, duration, false, timeInterval * shotsFired + extraStartTime, shotsFired);
            shotsFired++;
        }

        return timeInterval * shotsFired + duration;
    }
/*
    function pickAmountOfSystems(names, hitsLeft) {

        let amount = names.length / hitsLeft;

        if (amount < 1 && amount > 0 && Math.random() > 0.7) {
            amount = 1;
        }


        let picked = [];

        const remaining = names.filter(name => {
            if (amount > 0 && !name.structure) {
                amount--;
                picked.push(name);
                return false;
            }

            return true;
        })
        return {remaining, picked}
    }
*/

    function addAnimation(incomingFire, duration, hit, time, shotsFired, damage, damagedNames) {
        var animation = buildAnimation.call(this, incomingFire, hit, time, shotsFired, damage, damagedNames);


        if (duration < animation.getDuration()) {
            duration = animation.getDuration();
        }

        this.animations.push(animation);
        return duration;
    }

    function buildAnimation(incomingFire, hit, time, shotsFired, damage, damagedNames) {

        var startTime = this.time + this.duration + time;
        var weapon = incomingFire.weapon;
        var animationType = weapon.animationArray[incomingFire.firingMode] || weapon.animation;
        var animationColor = weapon.animationColorArray[incomingFire.firingMode] || weapon.animationColor;

        switch (animationType) {
            case "laser":
                return new LaserEffect(this.shipIconContainer.getByShip(incomingFire.shooter), getShipPositionAtTime.call(this, this.shipIcon, startTime), this.scene, {
                    color: new THREE.Color(animationColor[0] / 255, animationColor[1] / 255, animationColor[2] / 255),
                    hit: hit,
                    time: startTime,
                    damage: damage,
                    damagedNames: damagedNames,
                    systemDestroyedEffect: this.systemDestroyedEffect
                });
            case "torpedo":
                return new TorpedoEffect(this.particleEmitterContainer, {
                    size: 200 * weapon.animationExplosionScale,
                    //origin: getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(incomingFire.shooter), startTime),
		    origin: getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(incomingFire.shooter), 0), //startTime set at 0 = beginning of turn!	
                    target: getShotTargetVariance(getShipPositionAtTime.call(this, this.shipIcon, startTime), incomingFire, shotsFired),
                    color: new THREE.Color(animationColor[0] / 255, animationColor[1] / 255, animationColor[2] / 255),
                    hit: hit,
                    damage: damage,
                    time: startTime,
                    damagedNames: damagedNames,
                    systemDestroyedEffect: this.systemDestroyedEffect
                });
            case "beam":
            case "trail":
            default:
                return new BoltEffect(this.particleEmitterContainer, {
                    size: 300 * weapon.animationExplosionScale,
                    origin: getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(incomingFire.shooter), startTime),
                    target: getShotTargetVariance(getShipPositionAtTime.call(this, this.shipIcon, startTime), incomingFire, shotsFired),
                    color: new THREE.Color(animationColor[0] / 255, animationColor[1] / 255, animationColor[2] / 255),
                    hit: hit,
                    damage: damage,
                    time: startTime,
                    damagedNames: damagedNames,
                    systemDestroyedEffect: this.systemDestroyedEffect
                });
        }
    }

    function getSystemNamesDestroyed(incomingFire) {
        return incomingFire.damagesCaused.filter(damage => damage.destroyed)
            .map(damage => ({
                name: shipManager.systems.getDisplayName(damage.system),
                structure: damage.system instanceof Structure
            }));
    }

    function getShipPositionAtTime(icon, time) {
        return FireAnimationHelper.getShipPositionAtTime(icon, time, this.movementAnimations);
    }

    function getShotTargetVariance(target, incomingFire, shotsFired) {

        Math.seedrandom(incomingFire.id);

        var start = {
            x: target.x + Math.random() * 30 - 15,
            y: target.y + Math.random() * 30 - 15
        };

        start.x += (Math.random() * 10 - 5) * shotsFired;
        start.y += (Math.random() * 10 - 5) * shotsFired;

        return start;
    }

    function getAmountOfSystemsDestroyed(incomingFire) {
        return incomingFire.damagesCaused.filter(function (damage) {
            return !(damage.system instanceof Structure);
        }).reduce(function (amount, damage) {
            return amount + (damage.destroyed ? 1 : 0);
        }, 0);
    }

    function getAmountOfStructuresDestroyed(incomingFire) {
        return incomingFire.damagesCaused.filter(function (damage) {
            return damage.system instanceof Structure;
        }).reduce(function (amount, damage) {
            return amount + (damage.destroyed ? 1 : 0);
        }, 0);
    }

    function divideDamage(systemsDestroyed, structuresDestroyed, hits) {

        if (hits < systemsDestroyed || hits < structuresDestroyed) {
            return {
                systemsDestroyed: 0,
                structuresDestroyed: 0,
                damage: systemsDestroyed + structuresDestroyed * 2
            };
        }

        var systems = Math.round(Math.random() * systemsDestroyed);
        var structure = Math.round(Math.random() * structuresDestroyed);

        return {
            systemsDestroyed: systemsDestroyed - systems,
            structuresDestroyed: structuresDestroyed - structure,
            damage: systems + structure * 2
        };
    }

    return AllWeaponFireAgainstShipAnimation;
}();
