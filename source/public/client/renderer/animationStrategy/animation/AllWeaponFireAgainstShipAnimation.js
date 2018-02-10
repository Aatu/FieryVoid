window.AllWeaponFireAgainstShipAnimation = (function() {

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

        var cameraAnimation = new CameraPositionAnimation(getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(ship), this.time), this.time);
        this.animations.push(cameraAnimation);
        this.duration += cameraAnimation.getDuration();

        this.incomingFire.forEach(function (group) {

            var extraTime = 0;

            this.logAnimation.addLogEntryFire(group.map(function(entry) {
                return entry.fireOrder;
            }), this.time + this.duration);

            var durations = group.map(function (group) {
                extraTime += Math.random() * 100 + 300;
                return buildFireAnimations.call(this, group, extraTime) + extraTime;
            }, this);

            this.duration += durations.reduce(function (longest, current){
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
        })
    };

    AllWeaponFireAgainstShipAnimation.prototype.getDuration = function(){
        return this.duration;
    };

    AllWeaponFireAgainstShipAnimation.prototype.cleanUp = function () {
        this.animations.forEach(function (animation) {
            animation.cleanUp();
        })
    };

    function groupByShipAndWeapon(incomingFire) {
        var grouped = {};

        incomingFire.forEach(function(fire) {
            var key = fire.shooter.id + "-" + fire.weapon.constructor.name;

            if (grouped[key]){
                grouped[key].push(fire);
            } else {
                grouped[key] = [fire];
            }
        });

        return Object.keys(grouped).map(function(key) {
            return grouped[key];
        });
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

        while(firstMisses--) {
            duration = addAnimation.call(this, incomingFire, duration, false, timeInterval * shotsFired + extraStartTime, shotsFired);
            shotsFired++;
        }

        while(hits--) {
            var damageData = divideDamage(systemsDestroyed, structuresDestroyed, hits);
            systemsDestroyed = damageData.systemsDestroyed;
            structuresDestroyed = damageData.structuresDestroyed;
            var damage = damageData.damage;


            duration = addAnimation.call(this, incomingFire, duration, true, timeInterval * shotsFired + extraStartTime, shotsFired, damage);
            shotsFired++;
        }

        while(lastMisses--) {
            duration = addAnimation.call(this, incomingFire, duration, false, timeInterval * shotsFired + extraStartTime, shotsFired);
            shotsFired++;
        }

        return (timeInterval * shotsFired) + duration;
    }

    function addAnimation(incomingFire, duration, hit, time, shotsFired, damage) {
        var animation = buildAnimation.call(this, incomingFire, hit, time, shotsFired, damage);

        if (duration < animation.getDuration()) {
            duration = animation.getDuration();
        }

        this.animations.push(animation);
        return duration;
    }

    function buildAnimation(incomingFire, hit, time, shotsFired, damage) {

        var startTime = this.time + this.duration + time;
        var weapon = incomingFire.weapon;
        switch (weapon.animation) {
            case "laser":
                return new LaserEffect(
                    this.shipIconContainer.getByShip(incomingFire.shooter),
                    getShipPositionAtTime.call(this, this.shipIcon, startTime),
                    this.scene,
                    {
                        color: new THREE.Color(weapon.animationColor[0] / 255, weapon.animationColor[1] / 255, weapon.animationColor[2] / 255),
                        hit: hit,
                        time: startTime,
                        damage: damage
                    }
                );
            case "torpedo":
                return new TorpedoEffect(
                    this.particleEmitterContainer,
                    {
                        size: 200 * weapon.animationExplosionScale,
                        origin: getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(incomingFire.shooter), startTime),
                        target: getShotTargetVariance(getShipPositionAtTime.call(this, this.shipIcon, startTime), incomingFire, shotsFired),
                        color: new THREE.Color(weapon.animationColor[0]/255, weapon.animationColor[1]/255, weapon.animationColor[2]/255),
                        hit: hit,
                        damage: damage,
                        time: startTime
                    });
            case "beam":
            case "trail":
            default:
                return new BoltEffect(
                    this.particleEmitterContainer,
                    {
                        size: 300 * weapon.animationExplosionScale,
                        origin: getShipPositionAtTime.call(this, this.shipIconContainer.getByShip(incomingFire.shooter), startTime),
                        target: getShotTargetVariance(getShipPositionAtTime.call(this, this.shipIcon, startTime), incomingFire, shotsFired),
                        color: new THREE.Color(weapon.animationColor[0]/255, weapon.animationColor[1]/255, weapon.animationColor[2]/255),
                        hit: hit,
                        damage: damage,
                        time: startTime
                    });
        }
    }

    function getShipPositionAtTime(icon, time) {
        return FireAnimationHelper.getShipPositionAtTime(icon, time, this.movementAnimations);
    }

    function getShotTargetVariance(target, incomingFire, shotsFired) {

        Math.seedrandom(incomingFire.id);

        var start = {
            x: target.x + Math.random()*30 - 15,
            y: target.y + Math.random()*30 - 15
        };

        start.x += (Math.random()*10 - 5) * shotsFired;
        start.y += (Math.random()*10 - 5) * shotsFired;

        return start;
    }

    function getAmountOfSystemsDestroyed(incomingFire) {
        return incomingFire.damagesCaused
            .filter(function (damage) {
                return !(damage.system instanceof Structure);
            }).reduce(function (amount, damage) {
                return amount + damage.destroyed;
            }, 0);
    }

    function getAmountOfStructuresDestroyed(incomingFire) {
        return incomingFire.damagesCaused
            .filter(function (damage) {
                return damage.system instanceof Structure;
            })
            .reduce(function (amount, damage) {
                return amount + damage.destroyed;
            }, 0);
    }

    function divideDamage(systemsDestroyed, structuresDestroyed, hits) {

        if (hits < systemsDestroyed || hits < structuresDestroyed) {
            return {
                systemsDestroyed: 0,
                structuresDestroyed: 0,
                damage: systemsDestroyed + (structuresDestroyed * 2)
            }
        }

        var systems = Math.round(Math.random() * systemsDestroyed);
        var structure = Math.round(Math.random() * structuresDestroyed);

        return {
            systemsDestroyed: systemsDestroyed - systems,
            structuresDestroyed: structuresDestroyed - structure,
            damage: systems + (structure * 2)
        }

    }

    return AllWeaponFireAgainstShipAnimation;
})();