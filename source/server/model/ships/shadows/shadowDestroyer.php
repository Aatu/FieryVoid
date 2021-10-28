<?php
class ShadowDestroyer extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1250;
		$this->faction = "Shadows";
        $this->phpclass = "ShadowDestroyer";
        $this->imagePath = "img/ships/ShadowDestroyer.png";
        $this->shipClass = "Destroyer";
        $this->canvasSize = 100;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 1; //it's actually an MCV :)
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
		$this->halfPhaseThrust = 6; //needed for half phasing; equal to thrust from two BioThrusters on a given ship
        //$this->gravitic = true;
        $this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 2; //1.5 by AoG, but this is not possible to do in FV...
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = 14 *5;
		
		//$this->fighters = array("normal"=>12);
		$this->notes = "Atmospheric capable";//even largest Shadow ships are atmospheric capable
        
		
		/*
		$this->enhancementOptionsEnabled[] = 'SHAD_FTRL'; //can launch Shadow fighters (IF hangar capacity allows!)
		$this->enhancementOptionsDisabled[] = 'POOR_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'ELITE_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG'; //no Engine ;)
		*/
		Enhancements::nonstandardEnhancementSet($this, 'ShadowShip');
		
	    
		//BioDrive - first so javascript routines can easily find biothrusters they'll be looking for a lot!
		$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		$bioThruster = new BioThruster(5,6,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,6,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,6,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,6,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
				
       		$this->addPrimarySystem($bioDrive);
	    
	    
         
        	//Primary systems
		$this->addPrimarySystem(new Reactor(5, 18, 0, 0));
		$this->addPrimarySystem(new ShadowPilot(5, 8, 0, 0));
		$scanner = new Scanner(5, 13, 3, 12);
		$scanner->markAdvanced();
       		$this->addPrimarySystem($scanner);
		$this->addPrimarySystem(new PhasingDrive(5, 15, 4, 8));		
		//$this->addPrimarySystem(new Hangar(3, 1));
        	$this->addPrimarySystem(new SelfRepair(2, 3, 2)); //armor, structure, output
		
		
		//EnergyDiffuser		
		$diffuserPort = new EnergyDiffuser(4, 12, 10, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        	$this->addPrimarySystem($diffuserPort);
		
		$diffuserStbd = new EnergyDiffuser(4, 12, 10, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);		
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        	$this->addPrimarySystem($diffuserStbd);
		
				
		
		//weapons - Forward for visual reasons!
        	$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 240, 0));
        	$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 0, 120));
        	$this->addFrontSystem(new MultiphasedCutter(4, 0, 0, 180, 0));
        	$this->addFrontSystem(new MultiphasedCutter(4, 0, 0, 0, 180));
        
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

       	   
	    	//Structure
        	$this->addPrimarySystem(new Structure( 5, 30));
		
		/*systems on Shadow ships CANNOT be targeted by called shots!*/
		$this->notes .= "<br>cannot be targeted by called shots.";
		foreach ($this->systems as $sys){
			$sys->isPrimaryTargetable = false; 
			$sys->isTargetable = false; //cannot be targeted ever!
		}
				
	
		$this->hitChart = array(
			0=> array(
				5 => "Structure",
				8 => "0:Energy Diffuser",
				10 => "2:BioThruster",
				11 => "0:Self Repair",
				13 => "1:Heavy Phasing Pulse Cannon",
				15 => "1:Multiphased Cutter",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "C&C", //the only difference between sections - outer 19-20 hits are rerolled on Primary table, which is the only chance to hit Pilot
			),
			1=> array(
				5 => "Structure",
				8 => "0:Energy Diffuser",
				10 => "2:BioThruster",
				11 => "0:Self Repair",
				13 => "1:Heavy Phasing Pulse Cannon",
				15 => "1:Multiphased Cutter",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
			2=> array(
				5 => "Structure",
				8 => "0:Energy Diffuser",
				10 => "2:BioThruster",
				11 => "0:Self Repair",
				13 => "1:Heavy Phasing Pulse Cannon",
				15 => "1:Multiphased Cutter",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
		);				
    }
}



?>
