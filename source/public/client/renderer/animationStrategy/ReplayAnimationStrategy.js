window.ReplayAnimationStrategy = (function(){

    function ReplayAnimationStrategy(onDoneCallback, gameData, shipIcons, scene){
        AnimationStrategy.call(this, onDoneCallback);
        this.shipIconContainer = shipIcons;
        this.turn = gameData.turn;
        buildAnimations.call(this, gamedata);
        this.emitterContainer = new ParticleEmitterContainer(scene);
        this.animations.push(this.emitterContainer);
        this.emitterContainer.start();


        //this.animations.push(new LaserEffect(this.shipIconContainer.getArray()[0], {x: Math.random()*400 - 200, y: Math.random()*400 - 200}, scene, {color: new THREE.Color(255/255, 79/255, 15/255)}));


        var amount = 5;

        while (amount--) {
            this.animations.push(new BoltEffect(
                this.emitterContainer,
                {
                    size: 40,
                    origin: this.shipIconContainer.getArray()[0].getPosition(),
                    target: this.shipIconContainer.getArray()[2].getPosition(),
                    color: new THREE.Color(119/255, 225/255, 255/255),
                    hit: Math.round(Math.random()),
                    time: Math.random() * 1000
                })
            );
        }


        var lamount = 0;
        while (lamount--) {
            this.animations.push(new LaserEffect(
                this.shipIconContainer.getArray()[0],
                this.shipIconContainer.getArray()[2],
                scene,
                {
                    color: new THREE.Color(255 / 255, 79 / 255, 15 / 255),
                    hit: Math.round(Math.random()),
                    time: Math.random() * 1000
                }
                )
            );
        }




        //new Explosion(this.emitterContainer, {size: 20, position: {x:400, y:0}});

        /*
        var particle = this.emitterContainer.getParticle(this);
        particle
            .setOpacity(1.0)
            .setSize(200)
            .setColor({r: 1, g: 0, b: 0})
            .setActivationTime(0)
            .setFadeIn(0, 1000)
            //.setFadeOut(4000, 1000)
            .setTexture(BaseParticle.prototype.texture.bolt)
            .setAngle(45)
            //.setAngleChange(-0.01)
            //.setAcceleration({x:0.0001, y:0.0001})

        var particle2 = this.emitterContainer.getParticle(this);
        particle2
            .setPosition({x:25, y:25})
            .setOpacity(2.0)
            .setSize(100)
            .setColor({r: 1, g: 1, b: 1})
            .setActivationTime(0)
            .setFadeIn(0, 1000)
            //.setFadeOut(4000, 1000)
            .setTexture(BaseParticle.prototype.texture.bolt)
            .setAngle(45)
            //.setAngleChange(-0.01)
            //.setAcceleration({x:0.0001, y:0.0001})
            */
/*
        new Explosion(this.emitterContainer, {size: 20});

        new Explosion(this.emitterContainer, {size: 20, position: {x:400, y:0}});


        new Explosion(this.emitterContainer, {size: 20, position: {x:400, y:400}, type: "glow"});


        new Explosion(this.emitterContainer, {size: 20, position: {x:800, y:0}, type: "emp"});

        new Explosion(this.emitterContainer, {size: 20, position: {x:800, y:400}, type: "emp"});


        new Explosion(this.emitterContainer, {size: 20, position: {x:0, y:200}, type: "pillar"});


        new Explosion(this.emitterContainer, {size: 20, position: {x:800, y:800}, type: "pillar"});
        new Explosion(this.emitterContainer, {size: 20, position: {x:800, y:800}});
*/
/*
        var amount = 100;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                time: 0,
                size: 30,
                position: {x:1000 - amount * 60, y:0},
                type: "gas"
            });
        }

        amount = 100;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                time: 0,
                size: 30,
                position: {x:1000 - amount * 60, y:100},
                type: "glow"
            });
        }

        amount = 100;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                time: 0,
                size: 30,
                position: {x:1000 - amount * 60, y:200},
                type: "pillar"
            });
        }

        amount = 100;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                time: 0,
                size: 30,
                position: {x:1000 - amount * 60, y:300},
                type: "emp"
            });
        }


*/
/*
        var amount = 1000;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                time: Math.random()*10000,
                size: Math.random()*300 + 10,
                position: {x:Math.random()*4000 - 2000, y:Math.random()*4000 - 2000},
                type: ["glow", "emp", "pillar", "gas"][Math.floor(Math.random()*4)]
            });
        }
*/
    }

    ReplayAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    ReplayAnimationStrategy.prototype.activate = function() {
        return this;
    };

    ReplayAnimationStrategy.prototype.deactivate = function(scene) {
        this.animations.forEach(function (animation) {
           animation.cleanUp(scene);
        });
        return this;
    };

    ReplayAnimationStrategy.prototype.update = function() {
        return this;
    };

    ReplayAnimationStrategy.prototype.start = function() {

        this.stop();
        this.animations.forEach(function (animation) {
            animation.stop();
            animation.reset();
        });

        this.animations[0].start();

        AnimationStrategy.prototype.start.call(this);

        return this;
    };

    ReplayAnimationStrategy.prototype.animationDone = function(index) {
        console.log("done with animation ", index);
        this.animations[index].stop();
        if (! this.animations[index+1]) {
            this.done();
            return;
        }

        this.animations[index+1].start();
    };

    function buildAnimations(gamedata) {
        gamedata.ships.forEach(function (ship, i) {
            var icon = this.shipIconContainer.getByShip(ship);
            if (this.animations.some(function (animation) { return animation.shipIcon === icon})) {
                return;
            }

            var animation = new ShipMovementAnimation(icon, this.turn, this.animationDone.bind(this, i));
            this.animations.push(animation);

        }, this);
    }

    return ReplayAnimationStrategy;
})();