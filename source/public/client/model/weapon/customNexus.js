var NexusKineticBoxLauncher = function NexusKineticBoxLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
NexusKineticBoxLauncher.prototype = Object.create(Weapon.prototype);
NexusKineticBoxLauncher.prototype.constructor = NexusKineticBoxLauncher;

