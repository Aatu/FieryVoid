<?php
class Darmoti extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 575;
        $this->faction = "Centauri";
        $this->phpclass = "Darmoti";
        $this->imagePath = "img/ships/demos.png";
        $this->shipClass = "Darmoti Escort Warship";
        $this->occurence = "uncommon";
        $this->variantOf = "Demos Heavy Warship";
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(7, 20, 0, 8));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 9));
        $this->addPrimarySystem(new Engine(7, 13, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(6, 8));
        $this->addPrimarySystem(new Thruster(5, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(5, 15, 0, 5, 4));
        
        
        
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new HeavyArray(3, 8, 4, 240, 120));
        $this->addFrontSystem(new HeavyArray(3, 8, 4, 240, 120));
        $this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
        $this->addFrontSystem(new TractorBeam(4, 4, 0, 0));
        
        
        $this->addAftSystem(new Thruster(4, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 5, 2));
        $this->addAftSystem(new JumpEngine(6, 16, 3, 16));
    
        

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure( 4, 46));
        $this->addPrimarySystem(new Structure( 6, 30));
        
        
                		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				10 => "Thruster",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Thruster",
				4 => "Tractor Beam",
                5 => "Plasma Accelerator",
				9 => "Heavy Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				5 => "Thruster",
				9 => "Jump Engine",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }

}
        
    }
}
