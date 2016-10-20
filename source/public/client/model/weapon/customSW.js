var swfighterlaser = function(json, ship){
    Particle.call( this, json, ship);
}
swfighterlaser.prototype = Object.create( Particle.prototype );
swfighterlaser.prototype.constructor = swfighterlaser;
