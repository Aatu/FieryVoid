window.BaseParticle = (function(){
    function BaseParticle(material, geometry)
    {
        this.material = material;
        this.geometry = geometry;
        this.index = 0;
    }

    BaseParticle.prototype.texture = {
        gas: 0,
        bolt: 1,
        glow: 2,
        ring: 3
    };

    BaseParticle.prototype.create = function(index)
    {
        this.index = index;
        return this;
    };

    BaseParticle.prototype.setInitialValues = function()
    {
        this.setPosition({x:0, y: 0});
        this.setColor(new THREE.Color(0, 0, 0));
        this.setOpacity(0.0);
        this.setFadeIn(0.0, 0.0);
        this.setFadeOut(0.0, 0.0);
        this.setSize(0.0);
        this.setSizeChange(0.0);
        this.setAngle(0.0);
        this.setAngleChange(0.0);
        this.setActivationTime(0.0);
        this.setVelocity(new THREE.Vector3(0,0,0));
        this.setAcceleration(new THREE.Vector3(0,0,0));
        this.setTexture(this.texture.glow);

        return this;
    };

    BaseParticle.prototype.setTexture = function(tex)
    {
        changeAttribute(this.geometry, this.index, 'textureNumber', tex);

        return this;
    };


    BaseParticle.prototype.setSize = function(size)
    {
        changeAttribute(this.geometry, this.index, 'size', size);
        return this;
    };

    BaseParticle.prototype.setSizeChange = function(size)
    {
        changeAttribute(this.geometry, this.index, 'sizeChange', size);
        return this;
    };

    BaseParticle.prototype.setColor = function(color)
    {
        changeAttribute(this.geometry, this.index, 'color', [color.r, color.g, color.b]);
        return this;
    };

    BaseParticle.prototype.setOpacity = function(opacity)
    {
        changeAttribute(this.geometry, this.index, 'opacity', opacity);
        return this;
    };

    BaseParticle.prototype.setFadeIn = function(time, speed)
    {
        if ( ! typeof speed === "undefined")
            speed = 1000;

        changeAttribute(this.geometry, this.index, 'fadeInTime', time);
        changeAttribute(this.geometry, this.index, 'fadeInSpeed', speed);
        return this;
    };

    BaseParticle.prototype.setFadeOut = function(time, speed)
    {
        if ( ! typeof speed === "undefined")
            speed = 1000;

        changeAttribute(this.geometry, this.index, 'fadeOutTime', time);
        changeAttribute(this.geometry, this.index, 'fadeOutSpeed', speed);
        return this;
    };

    BaseParticle.prototype.setPosition = function(pos)
    {
        changeAttribute(this.geometry, this.index, 'position', [pos.x, pos.y, 0], true);
        return this;
    };

    BaseParticle.prototype.setAngle = function(angle)
    {
        changeAttribute(this.geometry, this.index, 'angle', mathlib.degreeToRadian(angle));
        return this;
    };

    BaseParticle.prototype.setAngleChange = function(angle)
    {
        changeAttribute(this.geometry, this.index, 'angleChange', mathlib.degreeToRadian(angle));
        return this;
    };

    BaseParticle.prototype.setVelocity = function(velocity)
    {
        changeAttribute(this.geometry, this.index, 'velocity', [velocity.x, velocity.y, 0]);
        return this;
    };

    BaseParticle.prototype.setAcceleration = function(acceleration)
    {
        changeAttribute(this.geometry, this.index, 'acceleration', [acceleration.x, acceleration.y, 0]);
        return this;
    };

    BaseParticle.prototype.deactivate = function()
    {
        this.setInitialValues();
        return this;
    };

    BaseParticle.prototype.setActivationTime = function(gameTime)
    {
        changeAttribute(this.geometry, this.index, 'activationGameTime', gameTime);
        return this;
    };

    function changeAttribute(geometry, index, key, values, debug) {
        values = [].concat(values);

        var target = geometry.attributes[key].array;

        values.forEach(function (value, i) {
            target[index * values.length + i] = value;
        });

        geometry.attributes[key].needsUpdate = true;
    }


    return BaseParticle;
})(); 

