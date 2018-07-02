"use strict";

window.SystemDestroyedEffect = function () {

    const TEXTURE_SIZE = 256;

    function SystemDestroyedEffect(position, damagedNames, scene, time) {
        Animation.call(this);

        this.time = time || 0;
        this.duration = 1000;
        this.position = position;
        
        this.scene = scene;
        this.fadeOutSpeed = 2000;
        this.velocity = 0.1;

        this.sprites = damagedNames.map(name => {
            if (name.structure) {
                return new TextSprite(name.name.toUpperCase(), 'rgba(255, 100, 45, 1)', 10, {fontSize: '24px', size: 512})
            } else {
                return new TextSprite(name.name.toUpperCase(), 'rgba(255, 100, 45, 1)', 10, {fontSize: '18px', size: 512})
            }
        })
        this.sprites.forEach(sprite => {
            sprite.setOpacity(1);
            sprite.setPosition(position);
            this.scene.add(sprite.mesh);
        }, this)
    
    }

    SystemDestroyedEffect.prototype = Object.create(Animation.prototype);

    SystemDestroyedEffect.prototype.cleanUp = function () {
        this.sprites.forEach(sprite =>  {
            this.scene.remove(sprite.mesh);
            sprite.destroy();
        }, this)
    };

    SystemDestroyedEffect.prototype.render = function (now, total, last, delta, zoom) {
        this.sprites.forEach((sprite, index) => {
            const time = this.time + index * 200;
            if (total < time || total > time + this.duration + this.fadeOutSpeed) {
                sprite.setOpacity(0);
                return;
            }

            sprite.setScale(zoom, zoom)
            const ellapsedTime = total - time;
            const newPos = {
                ...this.position,
                y: this.position.y + this.velocity * zoom * ellapsedTime
            }
            sprite.setPosition(newPos);

            const fadeTime = total - (time + this.duration);
            
            if (fadeTime < 0) {
                sprite.setOpacity(1);
            } else {
                const opacity = 1 - fadeTime / this.fadeOutSpeed
                sprite.setOpacity(opacity);
            }

        }, this)
    };

    SystemDestroyedEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    return SystemDestroyedEffect;
}();