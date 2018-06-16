window.StarField = (function(){

    function StarField(webglScene)
    {
        this.starCount = 5000;
        this.emitterContainer = null;
        this.webglScene = webglScene;
        this.lastAnimationTime = null;
        this.totalAnimationTime = 0;
        this.zoomChanged = 0;

        this.create();

    };

    StarField.prototype.create = function()
    {
    
        Math.seedrandom(gamedata.gameid);

        if (this.emitterContainer) {
            this.emitterContainer.cleanUp();
        }

        this.emitterContainer = new ParticleEmitterContainer(this.webglScene.scene, this.starCount, StarParticleEmitter);
        
        return;

        var width =  this.webglScene.width; 
        var height = this.webglScene.height; 

        var stars = this.starCount;
        while(stars--) {
            createStar(this.emitterContainer, width, height);

            if (Math.random() > 0.98) {
                createShiningStar(this.emitterContainer, width, height);
            }
        }

       
        var gas = Math.floor(Math.random() * 5) + 8; 
         
        while(gas--){
            createGasCloud(this.emitterContainer, width, height)
        }

        this.emitterContainer.start();
        this.lastAnimationTime = new Date().getTime();
        this.totalAnimationTime = 0;
        this.zoomChanged = 1;
        return this;
    };

    StarField.prototype.render = function()
    {
        var deltaTime = new Date().getTime() - this.lastAnimationTime;
        this.totalAnimationTime += deltaTime;
        this.emitterContainer.render(0, this.totalAnimationTime, 0, 0, this.zoomChanged);

        if (this.zoomChanged === 1) {
            this.zoomChanged = 0;
        }

        this.lastAnimationTime = new Date().getTime();
    };

    function createStar(emitterContainer, width, height) {
        var particle = emitterContainer.getParticle(this);

        var x = ((Math.random() - 0.5) * width * 1.5);
        var y = ((Math.random() - 0.5) * height * 1.5);

        particle
            .setActivationTime(0)
            .setSize(2 + Math.random() * 2)
            .setOpacity(Math.random() * 0.2 + 0.9)
            .setPosition({x: x, y: y})
            .setColor(new THREE.Color(1, 1, 1))
            .setParallaxFactor(0.005 + Math.random() * 0.005);

        if (Math.random() > 0.9) {
            particle
                .setSineFrequency(Math.random() * 200 + 50)
                .setSineAmplitude(Math.random());
        }
            
    }

    function createShiningStar(emitterContainer, width, height) {
        var particle = emitterContainer.getParticle(this);

        var x = ((Math.random() - 0.5) * width * 1.5);
        var y = ((Math.random() - 0.5) * height * 1.5);

        var size = 6 + Math.random() * 6;
        var parallaxFactor = 0.005 + Math.random() * 0.005;
        var color = new THREE.Color(Math.random() * 0.4 + 0.6, Math.random() * 0.2 + 0.8, Math.random() * 0.4 + 0.6);

        particle
            .setActivationTime(0)
            .setSize(size*0.5)
            .setOpacity(Math.random() * 0.2 + 0.9)
            .setPosition({x: x, y: y})
            .setColor(new THREE.Color(1,1,1))
            .setParallaxFactor(parallaxFactor);

        particle = emitterContainer.getParticle(this);
        particle
            .setActivationTime(0)
            .setSize(size)
            .setOpacity(Math.random() * 0.1 + 0.1)
            .setPosition({x: x, y: y})
            .setColor(color)
            .setParallaxFactor(parallaxFactor)
            .setSineFrequency(Math.random() * 200 + 100)
            .setSineAmplitude(Math.random() * 0.4);
            
        var shines = Math.round(Math.random() * 8) - 3;

        if (shines <= 2) {
            return
        }
        
        var angle = Math.random() * 360;
        var angleChange = (Math.random() - 0.5) * 0.01;

        while (shines--) {

            angle += Math.random() * 60 + 40   
            particle = emitterContainer.getParticle(this);
            particle
                .setActivationTime(0)
                .setSize(size * Math.random() * 10 + 10)
                .setOpacity(Math.random() * 0.1 + 0.1)
                .setPosition({x: x, y: y})
                .setColor(color)
                .setParallaxFactor(parallaxFactor)
                .setSineFrequency(Math.random() * 200 + 100)
                .setSineAmplitude(0.1)
                .setAngle(angle)
                .setAngleChange(angleChange)
                .setTexture(particle.texture.starLine);
        }
        
    }

    function createGasCloud(emitterContainer, width, height) {
        var gas = Math.floor(Math.random() * 10 + 10);
    
        var position = {
            x: ((Math.random() - 0.5) * width),
            y: ((Math.random() - 0.5) * height)
        }

        var vector = {
            x: getRandomBand(0.5, 1) * 100,
            y: getRandomBand(0.5, 1) * 100
        }

        var iterations = Math.floor(Math.random() * 3) + 5;

        while(iterations--) {
            createGasCloudPart(emitterContainer, {x: position.x, y: position.y});
            position.x += getRandomBand(0, 1) * 50 + vector.x; 
            position.y += getRandomBand(0, 1) * 50 + vector.y; 
        }
    }

    function getRandomBand(min, max) {
        var random = Math.random() * (max-min) + min;
        return Math.random() > 0.5 ? random * -1 : random;
    }

    function createGasCloudPart(emitterContainer, position) {
        var gas = Math.floor(Math.random() * 5 + 5);
        var baseRotation = (Math.random() - 0.5) * 0.002;

        while(gas--) {  
            createGas(emitterContainer, position, baseRotation, Math.random() * 250 + 750)
        }
    }

    function createGas(emitterContainer, position, baseRotation, size){
        var particle = emitterContainer.getParticle(this);

        position.x += (Math.random() - 0.5) * 100; 
        position.y += (Math.random() - 0.5) * 100;

        particle
            .setActivationTime(0)
            .setSize( Math.random() * size*0.5 + size*0.5)
            .setOpacity(Math.random() * 0.005 + 0.005)
            .setPosition({x: position.x, y: position.y})
            .setColor(new THREE.Color(104/255, 204/255, 249/255))
            .setTexture(particle.texture.gas)   
            .setAngle(Math.random() * 360)
            .setAngleChange(baseRotation + (Math.random() - 0.5) * 0.001)
            .setParallaxFactor(0.005 + Math.random() * 0.005);

        if (Math.random() > 0.9) {
            particle
                .setActivationTime(0)
                .setSize( Math.random() * size*0.25 + size*0.25)
                .setOpacity(0)
                .setPosition({x: position.x, y: position.y})
                .setColor(new THREE.Color(1, 1, 1))
                .setTexture(particle.texture.gas)   
                .setAngle(Math.random() * 360)
                .setAngleChange(baseRotation + (Math.random() - 0.5) * 0.01)
                .setParallaxFactor(0.005 + Math.random() * 0.005)
                .setSineFrequency(Math.random() * 200 + 200)
                .setSineAmplitude(Math.random()*0.02);
        }

    }

    return StarField
})();