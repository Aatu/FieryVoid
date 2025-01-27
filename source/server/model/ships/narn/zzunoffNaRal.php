<?php
class zzunoffNaRal extends MediumShip{
	/*Narn Na'Ral Minesweeper; source: Showdowns-10*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 225;
		$this->faction = "Narn Regime";
        $this->phpclass = "zzunoffNaRal";
        $this->imagePath = "img/ships/NarnNaRoth.png";
        $this->shipClass = "Na'Ral Minesweeper";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        $this->minesweeperbonus = 3;
        
		$this->variantOf = "Na'Roth Armored Lander";
		$this->occurence = "common";
		$this->isd = 2218;
 		$this->unofficial = 'S'; //design released after AoG demise

        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 5 * 5;
        
         
        $this->addPrimarySystem(new Reactor(5, 9, 0, 3));
        $this->addPrimarySystem(new CnC(6, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 9, 2, 4));
        $this->addPrimarySystem(new Engine(5, 8, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(5, 3));
		$this->addPrimarySystem(new Thruster(5, 8, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(5, 8, 0, 3, 4));
		
				
        $this->addFrontSystem(new Thruster(5, 8, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(4, 4, 1, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(4, 4, 1, 300, 60));		
		
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));      
		$this->addAftSystem(new TwinArray(4, 6, 2, 0, 360));
        
       
        $this->addPrimarySystem(new Structure( 6, 45));			
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Thruster",
				12 => "Scanner",
				15 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				3 => "Thruster",
				7 => "Standard Particle Beam",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				9 => "Twin Array",				
				17 => "Structure",
				20 => "Primary",
			),
		);				
    }
}
?>
