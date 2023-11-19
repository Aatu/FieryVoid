<?php
class Auk1935 extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 450;
        $this->faction = "Drazi Freehold (WotCR)";
        $this->phpclass = "Auk1935";
        $this->imagePath = "img/ships/shrike.png";
        $this->shipClass = "Auk Gunship (1935)";
			$this->occurence = 'rare'; //uncommon is for Plasma version
			$this->variantOf = "Shrike Heavy Destroyer";
        $this->isd = 1935;
        $this->canvasSize = 160;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 16, 0, 4));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 7, 3));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addAftSystem(new Thruster(4, 16, 0, 7, 2));

        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 300, 360));
        $this->addLeftSystem(new MediumPlasma(2, 5, 3, 240, 360));
        $this->addLeftSystem(new Thruster(4, 11, 0, 3, 3));

        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 60));
        $this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 120));
        $this->addRightSystem(new Thruster(4, 11, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 24));
        $this->addLeftSystem(new Structure(4, 36));
        $this->addRightSystem(new Structure(4, 36));
                
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "2:Thruster",
				        12 => "1:Heavy Plasma Cannon",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
						6 => "Thruster",
						8 => "Medium Plasma Cannon",
						10 => "Heavy Plasma Cannon",
						18 => "Structure",
						20 => "Primary",
        		),
        		4=> array(
						6 => "Thruster",
                    	8 => "Medium Plasma Cannon",
                    	10 => "Heavy Plasma Cannon",
                        18 => "Structure",
						20 => "Primary",
        		),
        );
    
    }
}
?>