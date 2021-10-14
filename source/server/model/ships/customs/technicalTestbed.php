<?php
class TechnicalTestbed extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Custom Ships";
        $this->phpclass = "TechnicalTestbed";
        $this->imagePath = "img/ships/ShadowCruiser.png";
        $this->shipClass = "New Technology Testbed";
        $this->canvasSize = 200;
	    $this->isd = 9999;
        $this->shipSizeClass = 2; //it's actually a HCV using MCV layout
        $this->agile = true;
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->halfPhaseThrust = 6; //needed for half phasing; equal to thrust from two BioThrusters on a given ship
        
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = 6 *5;
		
		$this->fighters = array("normal"=>12);
        
		
		$this->enhancementOptionsEnabled[] = 'SHAD_FTRL'; //can launch Shadow fighters (IF hangar capacity allows!)
		$this->enhancementOptionsDisabled[] = 'POOR_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'ELITE_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG'; //no Engine ;)
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(3, 12, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 4, 40, 0, 4, true));//armor, structure, power req, output, has petals    
	    
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 0, 6));
		$this->addPrimarySystem(new JumpEngine(4, 16, 3, 24));		
		$this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new SelfRepair(5, 6, 3)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(5, 6, 3)); //armor, structure, output
		
		
		
		
		//BioDrive
		$bioDrive = new BioDrive();
		
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
				
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
        $this->addPrimarySystem($bioDrive);
		
		
		
		
		//weapons - Forward for visual reasons!
        $this->addFrontSystem(new VorlonDischargeGun(3, 0, 0, 240, 120));
        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 240, 120));
        $this->addFrontSystem(new StdParticleBeam(3, 1, 1, 240, 120));
        
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

       	   
	    //Structure
        $this->addFrontSystem(new Structure( 3, 80));
        $this->addAftSystem(new Structure( 3, 80));
        $this->addPrimarySystem(new Structure( 3, 80));
		
				
	
		$this->hitChart = array(
			0=> array(
				5 => "1:Burst Beam",
				7 => "1:Medium Pulse Cannon",
				14 => "Energy Diffuser",
				15 => "Scanner",
				16 => "2:BioThruster",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
			/*
				5 => "1:Burst Beam",
				7 => "1:Medium Pulse Cannon",
				14 => "0:Energy Diffuser",
				15 => "0:Scanner",
				16 => "2:BioThruster",
				17 => "0:Hangar",
				18 => "0:Reactor",
				20 => "Primary",
				*/
				20 => "0:Jump Engine",
			),
			2=> array(
				5 => "1:Burst Beam",
				7 => "1:Medium Pulse Cannon",
				14 => "0:Energy Diffuser",
				15 => "0:Scanner",
				16 => "2:BioThruster",
				17 => "0:Hangar",
				18 => "0:Reactor",
				20 => "Primary",
			),
		);				
    }
}



?>
