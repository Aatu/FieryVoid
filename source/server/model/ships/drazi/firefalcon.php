<?php

class Firefalcon extends BaseShipNoAft{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 850;
        $this->faction = "Drazi";
        $this->phpclass = "Firefalcon";
        $this->imagePath = "img/ships/stormfalcon.png";
        $this->shipClass = "Firefalcon Command Cruiser";
        $this->fighters = array("light" => 12, "superheavy" => 1);
        $this->occurence = "rare";
        $this->variantOf = 'Stormfalcon Heavy Cruiser';
        $this->limited = 33;
        $this->isd = 2255;

        $this->forwardDefense = 15;
        $this->sideDefense = 14;

        $this->turncost = 0.5;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 5;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 15;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 7, 9));
        $this->addPrimarySystem(new Engine(5, 15, 0, 9, 3));
        $this->addPrimarySystem(new JumpEngine(5, 10, 0, 5, 36));
        $this->addPrimarySystem(new Hangar(4, 14));
        $this->addPrimarySystem(new Catapult(5, 6));
        $this->addPrimarySystem(new Thruster(5, 24, 0, 9, 2));

        $this->addFrontSystem(new ParticleRepeater(3, 6, 4, 240, 0));
        $this->addFrontSystem(new ParticleBlaster(4, 8, 5, 300, 60));
        $this->addFrontSystem(new SolarCannon(4, 7, 3, 300, 60));
        $this->addFrontSystem(new ParticleBlaster(4, 8, 5, 300, 60));
        $this->addFrontSystem(new ParticleRepeater(3, 6, 4, 0, 120));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));

        $this->addLeftSystem(new SolarCannon(3, 7, 3, 240, 0));
        $this->addLeftSystem(new SolarCannon(3, 7, 3, 240, 0));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new SolarCannon(3, 7, 3, 0, 120));
        $this->addRightSystem(new SolarCannon(3, 7, 3, 0, 120));
        $this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 4, 44));
        $this->addRightSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 44));
    }
}
?>
