"use strict";


var CustomHeavyMatterCannon = function CustomHeavyMatterCannon(json, ship) {
    Weapon.call(this, json, ship);
};
CustomHeavyMatterCannon.prototype = Object.create(Weapon.prototype);
CustomHeavyMatterCannon.prototype.constructor = CustomHeavyMatterCannon;

var CustomLightMatterCannon = function CustomLightMatterCannon(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLightMatterCannon.prototype = Object.create(Weapon.prototype);
CustomLightMatterCannon.prototype.constructor = CustomLightMatterCannon;

var CustomLightMatterCannonF = function CustomLightMatterCannonF(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLightMatterCannonF.prototype = Object.create(Weapon.prototype);
CustomLightMatterCannonF.prototype.constructor = CustomLightMatterCannonF;


var CustomMatterStream = function CustomMatterStream(json, ship) {
    Weapon.call(this, json, ship);
};
CustomMatterStream.prototype = Object.create(Weapon.prototype);
CustomMatterStream.prototype.constructor = CustomMatterStream;

CustomMatterStream.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];         
    }

	return this;
};

var CustomGatlingMattergunLight = function CustomGatlingMattergunLight(json, ship) {
    Weapon.call(this, json, ship);
};
CustomGatlingMattergunLight.prototype = Object.create(Weapon.prototype);
CustomGatlingMattergunLight.prototype.constructor = CustomGatlingMattergunLight;

var CustomGatlingMattergunMedium = function CustomGatlingMattergunMedium(json, ship) {
    Weapon.call(this, json, ship);
};
CustomGatlingMattergunMedium.prototype = Object.create(Weapon.prototype);
CustomGatlingMattergunMedium.prototype.constructor = CustomGatlingMattergunMedium;

var CustomGatlingMattergunHeavy = function CustomGatlingMattergunHeavy(json, ship) {
    Weapon.call(this, json, ship);
};
CustomGatlingMattergunHeavy.prototype = Object.create(Weapon.prototype);
CustomGatlingMattergunHeavy.prototype.constructor = CustomGatlingMattergunHeavy;

/*moved to official lasers
var CustomStrikeLaser = function CustomStrikeLaser(json, ship) {
    Weapon.call(this, json, ship);
};
CustomStrikeLaser.prototype = Object.create(Weapon.prototype);
CustomStrikeLaser.prototype.constructor = CustomStrikeLaser;
*/

var CustomPulsarLaser = function CustomPulsarLaser(json, ship) {
    Weapon.call(this, json, ship);
};
CustomPulsarLaser.prototype = Object.create(Weapon.prototype);
CustomPulsarLaser.prototype.constructor = CustomPulsarLaser;

var CustomHeavyMatterCannon = function CustomHeavyMatterCannon(json, ship) {
    Weapon.call(this, json, ship);
};
CustomHeavyMatterCannon.prototype = Object.create(Weapon.prototype);
CustomHeavyMatterCannon.prototype.constructor = CustomHeavyMatterCannon;

var Hlpa = function Hlpa(json, ship) {
    Weapon.call(this, json, ship);
};
Hlpa.prototype = Object.create(Weapon.prototype);
Hlpa.prototype.constructor = Hlpa;

var Mlpa = function Mlpa(json, ship) {
    Weapon.call(this, json, ship);
};
Mlpa.prototype = Object.create(Weapon.prototype);
Mlpa.prototype.constructor = Mlpa;

var CustomPhaseDisruptor = function CustomPhaseDisruptor(json, ship) {
    Weapon.call(this, json, ship);
};
CustomPhaseDisruptor.prototype = Object.create(Weapon.prototype);
CustomPhaseDisruptor.prototype.constructor = CustomPhaseDisruptor;


var CustomLtPhaseDisruptorShip = function CustomLtPhaseDisruptorShip(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLtPhaseDisruptorShip.prototype = Object.create(Weapon.prototype);
CustomLtPhaseDisruptorShip.prototype.constructor = CustomLtPhaseDisruptorShip;

var CustomMphasedBeamAcc = function CustomMphasedBeamAcc(json, ship) {
    Weapon.call(this, json, ship);
};
CustomMphasedBeamAcc.prototype = Object.create(Weapon.prototype);
CustomMphasedBeamAcc.prototype.constructor = CustomMphasedBeamAcc;

var CustomLtPolarityPulsar = function CustomLtPolarityPulsar(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLtPolarityPulsar.prototype = Object.create(Weapon.prototype);
CustomLtPolarityPulsar.prototype.constructor = CustomLtPolarityPulsar;

var CustomMedPolarityPulsar = function CustomMedPolarityPulsar(json, ship) {
    Weapon.call(this, json, ship);
};
CustomMedPolarityPulsar.prototype = Object.create(Weapon.prototype);
CustomMedPolarityPulsar.prototype.constructor = CustomMedPolarityPulsar;

var CustomHeavyPolarityPulsar = function CustomHeavyPolarityPulsar(json, ship) {
    Weapon.call(this, json, ship);
};
CustomHeavyPolarityPulsar.prototype = Object.create(Weapon.prototype);
CustomHeavyPolarityPulsar.prototype.constructor = CustomHeavyPolarityPulsar;

var CustomLtPhaseDisruptor = function CustomLtPhaseDisruptor(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLtPhaseDisruptor.prototype = Object.create(Weapon.prototype);
CustomLtPhaseDisruptor.prototype.constructor = CustomLtPhaseDisruptor;

var CustomPhaseSweeper = function CustomLtPhaseDisruptor(json, ship) {
    Weapon.call(this, json, ship);
};
CustomPhaseSweeper.prototype = Object.create(Weapon.prototype);
CustomPhaseSweeper.prototype.constructor = CustomPhaseSweeper;
/*//Moved to Pulse
var LightScattergun = function LightScattergun(json, ship) {
    Weapon.call(this, json, ship);
};
LightScattergun.prototype = Object.create(Weapon.prototype);
LightScattergun.prototype.constructor = LightScattergun;
*/

var CustomERLightPBeam = function CustomERLightPBeam(json, ship) {
    Weapon.call(this, json, ship);
};
CustomERLightPBeam.prototype = Object.create(Weapon.prototype);
CustomERLightPBeam.prototype.constructor = CustomERLightPBeam;



var CustomBPALight = function  CustomBPALight(json, ship) {
    Weapon.call(this, json, ship);
};
CustomBPALight.prototype = Object.create(Weapon.prototype);
CustomBPALight.prototype.constructor =  CustomBPALight;


var CustomBPAMedium = function  CustomBPAMedium(json, ship) {
    Weapon.call(this, json, ship);
};
CustomBPAMedium.prototype = Object.create(Weapon.prototype);
CustomBPAMedium.prototype.constructor =  CustomBPAMedium;


var CustomBPAHeavy = function  CustomBPAHeavy(json, ship) {
    Weapon.call(this, json, ship);
};
CustomBPAHeavy.prototype = Object.create(Weapon.prototype);
CustomBPAHeavy.prototype.constructor =  CustomBPAHeavy;


var CustomIndustrialGrappler = function  CustomIndustrialGrappler(json, ship) {
    Weapon.call(this, json, ship);
};
CustomIndustrialGrappler.prototype = Object.create(Weapon.prototype);
CustomIndustrialGrappler.prototype.constructor =  CustomIndustrialGrappler;

var CustomMiningCutter = function  CustomMiningCutter(json, ship) {
    Weapon.call(this, json, ship);
};
CustomMiningCutter.prototype = Object.create(Weapon.prototype);
CustomMiningCutter.prototype.constructor =  CustomMiningCutter;

var CustomLightOMissileRack = function  CustomLightOMissileRack(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLightOMissileRack.prototype = Object.create(Weapon.prototype);
CustomLightOMissileRack.prototype.constructor =  CustomLightOMissileRack;

var CustomLightSoMissileRack = function  CustomLightSoMissileRack(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLightSoMissileRack.prototype = Object.create(Weapon.prototype);
CustomLightSoMissileRack.prototype.constructor =  CustomLightSoMissileRack;

var CustomLightSMissileRack = function  CustomLightSMissileRack(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLightSMissileRack.prototype = Object.create(Weapon.prototype);
CustomLightSMissileRack.prototype.constructor =  CustomLightSMissileRack;

var GromeLgtRailgun = function GromeLgtRailgun(json, ship) {
    Weapon.call(this, json, ship);
};
GromeLgtRailgun.prototype = Object.create(Weapon.prototype);
GromeLgtRailgun.prototype.constructor = GromeLgtRailgun;

var GromeMedRailgun = function GromeMedRailgun(json, ship) {
    Weapon.call(this, json, ship);
};
GromeMedRailgun.prototype = Object.create(Weapon.prototype);
GromeMedRailgun.prototype.constructor = GromeMedRailgun;

var GromeHvyRailgun = function GromeHvyRailgun(json, ship) {
    Weapon.call(this, json, ship);
};
GromeHvyRailgun.prototype = Object.create(Weapon.prototype);
GromeHvyRailgun.prototype.constructor = GromeHvyRailgun;


var CustomLtParticleCutter = function CustomLtParticleCutter(json, ship) {
    Weapon.call(this, json, ship);
};
CustomLtParticleCutter.prototype = Object.create(Weapon.prototype);
CustomLtParticleCutter.prototype.constructor = CustomLtParticleCutter;

CustomLtParticleCutter.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];         
    }

	return this;
};

var CustomEarlyLtParticleCutter = function CustomEarlyLtParticleCutter(json, ship) {
    Weapon.call(this, json, ship);
};
CustomEarlyLtParticleCutter.prototype = Object.create(Weapon.prototype);
CustomEarlyLtParticleCutter.prototype.constructor = CustomEarlyLtParticleCutter;

var GaimPhotonBomb = function GaimPhotonBomb(json, ship) {
    Weapon.call(this, json, ship);
};
GaimPhotonBomb.prototype = Object.create(Weapon.prototype);
GaimPhotonBomb.prototype.constructor = GaimPhotonBomb;


var TestLaser = function TestLaser(json, ship) {
    Weapon.call(this, json, ship);
};
TestLaser.prototype = Object.create(Weapon.prototype);
TestLaser.prototype.constructor = TestLaser;

