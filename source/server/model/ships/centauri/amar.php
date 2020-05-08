<?php
class Amar extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 450;
        $this->faction = "Centauri";
        $this->phpclass = "Amar";
        $this->imagePath = "img/ships/darkner.png";
        $this->shipClass = "Amar Fast Carrier";
        $this->occurence = "uncommon";
        $this->variantOf = "Darkner Fast Attack Frigate";
        $this->fighters = array("medium"=>12);

        $this->forwardDefense = 13;
        $this->sideDefense = 13;

        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;


        $this->addPrimarySystem(new Reactor(6, 17, 0, 6));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 5, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 4));



        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Hangar(4, 6));
        $this->addFrontSystem(new Hangar(4, 6));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(4, 6, 2, 240, 45));
        $this->addFrontSystem(new TwinArray(4, 6, 2, 315, 120));
        $this->addFrontSystem(new TwinArray(4, 7, 4, 240, 45));
        $this->addFrontSystem(new TwinArray(4, 7, 4, 315, 120));

        $this->addAftSystem(new Thruster(4, 19, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 19, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(4, 15, 4, 20));





        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 32));

        $this->hitChart = array(
                0=> array(
                    7 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    7 => "Twin Array",
                    10 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    10 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
			),
		);     

    }

}
