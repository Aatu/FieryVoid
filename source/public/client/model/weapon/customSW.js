var swfighterlaser = function(json, ship){
    Weapon.call( this, json, ship);
}
swfighterlaser.prototype = Object.create( Weapon.prototype );
swfighterlaser.prototype.constructor = swfighterlaser;
