<?php
class norek extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Hurr";
        $this->phpclass = "norek";
        $this->imagePath = "img/ships/hurrOrak.png";
        $this->shipClass = "Norek Missile Frigate";
   
        $this->occurence = "common";
        $this->variantOf = 'Orak Frigate';
        $this->isd = 2230;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 4));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 4));
	$this->addPrimarySystem(new Hangar(2, 2));
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
	$this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
	$this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        
        $this->addAftSystem(new Thruster(3, 16, 0, 6, 2));
	$this->addAftSystem(new Engine(4, 15, 0, 6, 5));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addPrimarySystem(new Structure( 5, 45));
        $this->addAftSystem(new Structure( 5, 50));
        
        $this->hitChart = array(
        	0=> array(
        		9 => "Structure",
        		12 => "Thruster",
        		15 => "Scanner",
        		16 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		4 => "Thruster",
        		8 => "Class-S Missile Rack",
        		11 => "Standard Particle Beam",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		8 => "Standard Particle Beam",
			13 => "Engine",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}
?>
