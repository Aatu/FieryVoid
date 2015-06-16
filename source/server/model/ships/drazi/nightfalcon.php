<?php

class Nightfalcon extends BaseShipNoAft{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 725;
        $this->faction = "Drazi";
        $this->phpclass = "Nightfalcon";
        $this->imagePath = "img/ships/stormfalcon.png";
        $this->shipClass = "Nightfalcon Heavy Carrier";
        $this->fighters = array("light" => 12, "serpents" => 3);
        $this->occurence = "rare";

        $this->forwardDefense = 15;
        $this->sideDefense = 14;

        $this->turncost = 0.5;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 5;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 10;

        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 6, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 3));
        $this->addPrimarySystem(new JumpEngine(5, 10, 5, 36));
        $this->addPrimarySystem(new Hangar(4, 14));
        $this->addPrimarySystem(new Catapult(5, 6));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 8, 2));

        $this->addFrontSystem(new ParticleCutter(3, 8, 3, 240, 0));
        $this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
        $this->addFrontSystem(new ParticleCutter(3, 8, 3, 0, 120));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
        $this->addFrontSystem(new Catapult(4, 6));
        $this->addFrontSystem(new Catapult(4, 6));

        $this->addLeftSystem(new ParticleCannon(3, 8, 7, 240, 0));
        $this->addLeftSystem(new ParticleCannon(3, 8, 7, 240, 0));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 120));
        $this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 120));
        $this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 56));
        $this->addLeftSystem(new Structure( 4, 44));
        $this->addRightSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 44));
    }
}
?>
