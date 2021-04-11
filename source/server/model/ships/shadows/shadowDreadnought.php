<?php
class ShadowDreadnought extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4250;
		$this->faction = "Shadows";
        $this->phpclass = "ShadowDreadnought";
        $this->imagePath = "img/ships/ShadowDreadnought.png";
        $this->shipClass = "Dreadnought";
        $this->canvasSize = 265;
        $this->shipSizeClass = 3; //it's actually a Capital ship using MCV layout
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->isd = 'Ancient';
       
        $this->forwardDefense = 18;
        $this->sideDefense = 17;
	    
        $this->limited = 33;
        
		$this->halfPhaseThrust = 6; //needed for half phasing; equal to thrust from two BioThrusters on a given ship
        $this->gravitic = true;
		$this->advancedArmor = true;   
        $this->turncost = 1.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
		$this->iniativebonus = 2 *5;
		
		$this->fighters = array("normal"=>12);
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
		
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
	    
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
	    
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
	    
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
	    
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
	    
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
	    
		$bioThruster = new BioThruster(6,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
        $this->addPrimarySystem($bioDrive);
		
		
        //Primary systems
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new ShadowPilot(7, 18, 0, 0));
		$scanner = new Scanner(6, 24, 5, 14);
		$scanner->markAdvanced();
        $this->addPrimarySystem($scanner);
		$this->addPrimarySystem(new PhasingDrive(6, 20, 5, 8));
        $this->addPrimarySystem(new Hangar(5, 12,12));
        $this->addPrimarySystem(new SelfRepair(3, 6, 4)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(3, 6, 4)); //armor, structure, output
		
		
		//EnergyDiffuser		
		$diffuserPort = new EnergyDiffuser(6, 28, 25, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(40,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(25,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(25,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        	$this->addPrimarySystem($diffuserPort);
		
		$diffuserStbd = new EnergyDiffuser(6, 28, 25, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(40,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(25,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(25,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addPrimarySystem($diffuserStbd);
		
		
		
		
		
		
		//weapons - Forward for visual reasons!
        $this->addFrontSystem(new MolecularSlicerBeamM(5, 0, 0, 240, 0));
        $this->addFrontSystem(new MolecularSlicerBeamM(5, 0, 0, 0, 120));
        $this->addFrontSystem(new VortexDisruptor(5, 0, 0, 240, 0));
        $this->addFrontSystem(new VortexDisruptor(5, 0, 0, 0, 120));
        
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

       	   
	    //Structure
        $this->addPrimarySystem(new Structure( 6, 72));
		
		/*systems on Shadow ships CANNOT be targeted by called shots!*/
		$this->notes .= "<br>cannot be targeted by called shots.";
		foreach ($this->systems as $sys){
			$sys->isPrimaryTargetable = false; 
			$sys->isTargetable = false; //cannot be targeted ever!
		}
				
	
		$this->hitChart = array(
			0=> array(
				5 => "Structure",
				7 => "0:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "C&C", //the only difference between sections - outer 19-20 hits are rerolled on Primary table, which is the only chance to hit Pilot
			),
			1=> array(
				5 => "Structure",
				7 => "0:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
			2=> array(
				5 => "Structure",
				7 => "0:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
		);				
    }
}



?>
