<?php
class RogolonTolov extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 575;
        $this->faction = "Small Races";
        $this->phpclass = "RogolonTolov";
        $this->imagePath = "img/ships/RogolonTolov.png";
        $this->shipClass = "Rogolon Tolov Warship";
        $this->occurence = "common";
        $this->fighters = array("normal" => 12, "superheavy" => 1);

        $this->isd = 1975;

        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 10, 3));
	$this->addPrimarySystem(new Hangar(4, 3));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
	$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 240, 0));
	$this->addFrontSystem(new Catapult(3, 6));
	$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 0, 120));

        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
       
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
	$this->addLeftSystem(new Hangar(4, 6));
	$this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 0));
        $this->addLeftSystem(new SMissileRack(3, 6, 0, 180, 0));
        $this->addLeftSystem(new SMissileRack(3, 6, 0, 180, 0));

        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
	$this->addRightSystem(new Hangar(4, 6));
	$this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new SMissileRack(3, 6, 0, 0, 180));
        $this->addRightSystem(new SMissileRack(3, 6, 0, 0, 180));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 36));
        $this->addRightSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 40));
        
        $this->hitChart = array(
        	0=> array(
        		12 => "Structure",
        		13 => "Hangar",
        		15 => "Scanner",
        		17 => "Engine",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		7 => "Catapult",
        		10 => "Heavy Plasma Cannon",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		9 => "Thruster",
        		18 => "Structure",
        		20 => "Primary",  
		),
                3=> array(
                        5 => "Thruster",
                        7 => "Hangar",
        		9 => "Heavy Plasma Cannon",
        		11 => "Class-S Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
                        7 => "Hangar",
        		9 => "Heavy Plasma Cannon",
        		11 => "Class-S Missile Rack",
                        18 => "Structure",
                        20 => "Primary",    			
        	),
        );
    }
}

?>
