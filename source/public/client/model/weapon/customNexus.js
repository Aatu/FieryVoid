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
