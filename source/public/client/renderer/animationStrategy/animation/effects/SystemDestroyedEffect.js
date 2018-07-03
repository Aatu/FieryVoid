"use strict";

window.SystemDestroyedEffect = function () {

    const TEXTURE_SIZE = 256;

    function SystemDestroyedEffect(scene) {
        Animation.call(this);

        this.scene = scene;

        this.duration = 1000;
        this.fadeOutSpeed = 2000;
        this.velocity = 0.1;
        this.destroyedTexts = [];
    }

    SystemDestroyedEffect.prototype = Object.create(Animation.prototype);

    SystemDestroyedEffect.prototype.cleanUp = function () {
        this.destroyedTexts.forEach(destroyedText => {
            this.scene.remove(destroyedText.sprite.mesh);
            destroyedText.sprite.destroy();
        }, this)

        this.destroyedTexts = {};
    };

    SystemDestroyedEffect.prototype.add = function(position, names, time) {
        names = [].concat(names);
        names.filter(Boolean).forEach(name => {
            this.destroyedTexts.push({
                time: changeTimeIfNeccessary(position, time, this.destroyedTexts),
                position: window.coordinateConverter.fromHexToGame(window.coordinateConverter.fromGameToHex(position)),
                sprite: getSprite(name, position, this.scene)
            });
        }, this)
    }

    SystemDestroyedEffect.prototype.render = function (now, total, last, delta, zoom) {
        this.destroyedTexts.forEach( text => {
            const time = text.time;
            const sprite = text.sprite;

            if (total < time || total > time + this.duration + this.fadeOutSpeed) {
                sprite.setOpacity(0);
                return;
            }

            sprite.setScale(zoom, zoom)
            const ellapsedTime = total - time;
            const newPos = {
                ...text.position,
                y: text.position.y + this.velocity * zoom * ellapsedTime
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
        return 0;
    };

    const getKey = (position, time)  => time + '-' +position.x + '-' + position.y;

    const getSprite = (name, position, scene) => {
        let sprite = null;

        if (name.structure) {
            sprite = new TextSprite(name.name.toUpperCase(), 'rgba(255, 100, 45, 1)', 500, {fontSize: '24px', size: 512})
        } else {
            sprite = new TextSprite(name.name.toUpperCase(), 'rgba(255, 100, 45, 1)', 500, {fontSize: '18px', size: 512})
        }

        sprite.setPosition(position)
        sprite.setOpacity(0)
        scene.add(sprite.mesh);

        return sprite
    }

    const changeTimeIfNeccessary = (position, time, texts) => {

        const inSameHex = getInSameHex(position, texts);

        if (inSameHex.length === 0) {
            return time;
        }

        while(isTooClose(time, inSameHex)) {
            time += 200;
        }

        return time;
    }

    const getInSameHex = (position, texts) => {
        const gamePosition = window.coordinateConverter.fromGameToHex(position)

        return texts.filter(text => window.coordinateConverter.fromGameToHex(text.position).equals(gamePosition))
    }

    const isTooClose = (time, texts) => {
        return texts.some(text => text.time - time < 200 && text.time - time > -200 )
    }

    return SystemDestroyedEffect;
}();