var NexusKineticBoxLauncher = function NexusKineticBoxLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
NexusKineticBoxLauncher.prototype = Object.create(Weapon.prototype);
NexusKineticBoxLauncher.prototype.constructor = NexusKineticBoxLauncher;


var NexusChaffLauncher = function NexusChaffLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
NexusChaffLauncher.prototype = Object.create(Weapon.prototype);
NexusChaffLauncher.prototype.constructor = NexusChaffLauncher;



var NexusParticleProjectorLight = function NexusParticleProjectorLight(json, ship) {
    Weapon.call(this, json, ship);
};
NexusParticleProjectorLight.prototype = Object.create(Weapon.prototype);
NexusParticleProjectorLight.prototype.constructor = NexusParticleProjectorLight;

var NexusParticleProjectorHeavy = function NexusParticleProjectorHeavy(json, ship) {
    Weapon.call(this, json, ship);
};
NexusParticleProjectorHeavy.prototype = Object.create(Weapon.prototype);
NexusParticleProjectorHeavy.prototype.constructor = NexusParticleProjectorHeavy;

var NexusParticleAgitator = function NexusParticleAgitator(json, ship) {
    Weapon.call(this, json, ship);
};
NexusParticleAgitator.prototype = Object.create(Weapon.prototype);
NexusParticleAgitator.prototype.constructor = NexusParticleAgitator;
