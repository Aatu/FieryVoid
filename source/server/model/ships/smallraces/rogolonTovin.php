<?php
class RogolonTovin extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Small Races";
        $this->phpclass = "RogolonTovin";
        $this->imagePath = "img/ships/RogolonSmallWarship.png";
        $this->shipClass = "Rogolon Tovin Small Warship";
        $this->occurence = "common";

        $this->isd = 1966;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 25;
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 10, 0, 8, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));
	$this->addPrimarySystem(new Hangar(4, 3));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
	$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 240, 0));
	$this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
	$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 0, 120));

        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 180, 0));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 36));
        
        $this->hitChart = array(
        	0=> array(
        		9 => "Structure",
        		12 => "Thruster",
        		13 => "Hangar",
        		15 => "Scanner",
        		17 => "Engine",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Heavy Plasma Cannon",
        		10 => "Class-S Missile Rack",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		7 => "Thruster",
        		9 => "Class-S Missile Rack",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}
?>
