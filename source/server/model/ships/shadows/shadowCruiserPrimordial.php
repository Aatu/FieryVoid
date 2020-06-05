<?php
class ShadowCruiserPrimordial extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = round(2750*1.2+10*15);//Primordial: +20%; Additional Tendril: Tendril capacity * Diffuser output
		$this->faction = "Shadows";
        $this->phpclass = "ShadowCruiserPrimordial";
        $this->imagePath = "img/ships/ShadowCruiser.png";
        $this->shipClass = "Cruiser (Primordial)";
        $this->variantOf = "Cruiser";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->shipSizeClass = 3; //it's actually a Capital ship using MCV layout
       
        $this->forwardDefense = 16;
        $this->sideDefense = 15;
        
		$this->halfPhaseThrust = 6; //needed for half phasing; equal to thrust from two BioThrusters on a given ship
        $this->gravitic = true;
		$this->advancedArmor = true;   
        $this->turncost = 1;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 2 *5;
		
		$this->fighters = array("normal"=>6);
		$this->notes = "Atmospheric capable";//even largest Shadow ships are atmospheric capable
        
		
		$this->enhancementOptionsEnabled[] = 'SHAD_FTRL'; //can launch Shadow fighters (IF hangar capacity allows!)
		$this->enhancementOptionsDisabled[] = 'POOR_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'ELITE_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG'; //no Engine ;)
		
		
		
		//BioDrive - first so javascript routines can easily find biothrusters they'll be looking for a lot!
		$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		$bioThruster = new BioThruster(5,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
        $this->addPrimarySystem($bioDrive);
		
		
        //Primary systems
        $this->addPrimarySystem(new Reactor(6, 24, 0, 0));
        $this->addPrimarySystem(new ShadowPilot(6, 12, 0, 0));
		$scanner = new Scanner(6, 20, 4, 14);
		$scanner->markAdvanced();
        $this->addPrimarySystem($scanner);
		$this->addPrimarySystem(new PhasingDrive(6, 20, 4, 8));
        $this->addPrimarySystem(new Hangar(5, 6));
        $this->addPrimarySystem(new SelfRepair(3, 3, 2)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(3, 3, 2)); //armor, structure, output
		
		
		//EnergyDiffuser		
		$diffuserPort = new EnergyDiffuser(5, 21, 15, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
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
		
		$diffuserStbd = new EnergyDiffuser(5, 21, 15, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
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
        $this->addFrontSystem(new MolecularSlicerBeamH(5, 0, 0, 300, 60));
        $this->addFrontSystem(new VortexDisruptor(5, 0, 0, 300, 60));
        
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

       	   
	    //Structure
        $this->addPrimarySystem(new Structure( 6, 40));
		
				
	
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				9 => "0:Energy Diffuser",
				11 => "2:BioThruster",
				12 => "0:Self Repair",
				13 => "1:Heavy Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "C&C", //the only difference between sections - outer 19-20 hits are rerolled on Primary table, which is the only chance to hit Pilot
			),
			1=> array(
				6 => "Structure",
				9 => "0:Energy Diffuser",
				11 => "2:BioThruster",
				12 => "0:Self Repair",
				13 => "1:Heavy Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
			2=> array(
				6 => "Structure",
				9 => "0:Energy Diffuser",
				11 => "2:BioThruster",
				12 => "0:Self Repair",
				13 => "1:Heavy Slicer Beam",
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