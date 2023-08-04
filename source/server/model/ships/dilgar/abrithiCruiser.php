<?php

class AbrithiCruiser extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 390;
        $this->faction = "Dilgar";
        $this->phpclass = "AbrithiCruiser";
        $this->imagePath = "img/ships/abrithi.png";
        $this->shipClass = "Abrithi Early Cruiser";
        $this->shipSizeClass = 3;
			$this->occurence = "common";
			$this->variantOf = "Abrithi Assault Cruiser";
        $this->isd = 2211;        

	    $this->notes = 'Unofficial cruiser to go with the Rishekar';

		$this->unofficial = true;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        
        $this->fighters = array("normal"=>12);

        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 11, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(4, 10, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(4, 16));

        $this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new LightPlasma(1, 4, 2, 270, 90));
        $this->addFrontSystem(new LightPlasma(1, 4, 2, 270, 90));

        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 180, 360));
        $this->addAftSystem(new Thruster(3, 4, 0, 1, 2));
        $this->addAftSystem(new Thruster(2, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 4, 0, 1, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 3, 2));
        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 0, 180));

        $this->addLeftSystem(new Thruster(3, 11, 0, 4, 3));
        $this->addLeftSystem(new LightPlasma(1, 4, 2, 180, 360));

        $this->addRightSystem(new Thruster(3, 11, 0, 4, 4));
        $this->addRightSystem(new LightPlasma(1, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 27));
        $this->addAftSystem(new Structure( 4, 27));
        $this->addLeftSystem(new Structure( 4, 39));
        $this->addRightSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 5, 36));
        
        $this->hitChart = array(
                0=> array(
                    10 => "Structure",
                    12 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Medium Plasma Cannon",
                    8 => "Light Plasma Cannon",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Plasma Torch",
                    9 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    5 => "Thruster",
                    7 => "Light Plasma Cannon",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    5 => "Thruster",
                    7 => "Light Plasma Cannon",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }
}
?>
