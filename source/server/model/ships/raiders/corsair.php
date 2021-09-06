<?php
class Corsair extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "Raiders";
        $this->phpclass = "Corsair";
        $this->imagePath = "img/ships/pinnace.png"; //need ot change
        $this->shipClass = "Corsair";
        $this->canvasSize = 100;
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 1900;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 3, 4));
		        		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
		$this->addFrontSystem(new LightParticleCannon(3, 6, 5, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 330, 30));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 6, 2));
        $this->addAftSystem(new CargoBay(1, 6));
               
        $this->addPrimarySystem(new Structure(3, 36));
		
        $this->hitChart = array (
        		0=> array (
        				8=>"Thruster",
        				11=>"Scanner",
        				14=>"Engine",
        				16=>"Hangar",
        				18=>"Reactor",
        				20=>"C&C",
        		),
        		1=> array (
        				5=>"Thruster",
						7=>"Light Particle Beam",
        				9=>"Light Particle Cannon",
        				10=>"Medium Plasma Cannon",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				8=>"Thruster",
        				10=>"Cargo Bay",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }
}
?>
