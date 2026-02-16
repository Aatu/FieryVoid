<?php
class shadowRegenBase extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 10000;
        $this->faction = "Shadow Association";       
        $this->phpclass = "shadowRegenBase";
        $this->imagePath = "img/ships/ShadowRegenBase.png";
        $this->canvasSize = 280;
        $this->shipClass = "Regeneration Outpost";
        $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

	    $this->notes = 'Enormous Unit';
		$this->notes .= '<br>Ignores Manoeuvre Hit Modifiers';			

        $this->Enormous = true;
        
        $this->forwardDefense = 21;
        $this->sideDefense = 21;

		$this->advancedArmor = true;   

        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 5;	
		$this->iniativebonus = 2 *5;
		$this->ignoreManoeuvreMods = true;         

		$this->fighters = array("normal"=>24);

		Enhancements::nonstandardEnhancementSet($this, 'ShadowShip');
		
		//BioDrive - first so javascript routines can easily find biothrusters they'll be looking for a lot!
		$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		$bioThruster = new BioThruster(5,12,5); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,12,5); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,12,5); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(5,12,5); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);

        $this->addPrimarySystem($bioDrive);
		
        //Primary systems
        $this->addPrimarySystem(new Reactor(6, 30, 0, 0));
        $this->addPrimarySystem(new ShadowPilot(6, 30, 0, 0));
		$scanner = new Scanner(6, 20, 4, 14);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);
		$scanner = new Scanner(6, 20, 4, 14);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);
        $this->addPrimarySystem(new Hangar(5, 6, 6));
        $this->addPrimarySystem(new Hangar(5, 6, 6));
        $this->addPrimarySystem(new Hangar(5, 6, 6));
        $this->addPrimarySystem(new Hangar(5, 6, 6));
        $this->addPrimarySystem(new SelfRepair(6, 16, 8)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(6, 16, 8)); //armor, structure, output

		//EnergyDiffuser		
		$diffuserPort = new EnergyDiffuser(6, 21, 20, 270, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
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
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addPrimarySystem($diffuserPort);

		$diffuserPort = new EnergyDiffuser(6, 21, 20, 180, 270);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(40,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(30,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(30,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(25,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(20,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'LR');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addPrimarySystem($diffuserPort);

		$diffuserStbd = new EnergyDiffuser(6, 21, 20, 0, 90);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
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
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addPrimarySystem($diffuserStbd);

		$diffuserStbd = new EnergyDiffuser(6, 21, 20, 90, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(40,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(30,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(30,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(25,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(20,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(20,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'RR');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addPrimarySystem($diffuserStbd);

		//weapons - In sections for visual reasons!
        $this->addFrontSystem(new MolecularSlicerBeamM(5, 0, 0, 270, 90));
        $this->addFrontSystem(new MolecularSlicerBeamM(5, 0, 0, 90, 270));
        $this->addFrontSystem(new MolecularSlicerBeamM(5, 0, 0, 180, 360));
        $this->addFrontSystem(new MolecularSlicerBeamM(5, 0, 0, 0, 180));

		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        
	    //Structure
        $this->addPrimarySystem(new Structure( 6, 140));
		
		/*systems on Shadow ships CANNOT be targeted by called shots!*/
		$this->notes .= "<br>Cannot be targeted by called shots.";
		foreach ($this->systems as $sys){
			$sys->isPrimaryTargetable = false; 
			$sys->isTargetable = false; //cannot be targeted ever!
		}
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				9 => "0:Energy Diffuser",
				10 => "2:BioThruster",
				12 => "0:Self Repair",
				14 => "1:Slicer Beam",
				16 => "0:Hangar",
				17 => "0:Scanner",
				18 => "0:Reactor",
				20 => "C&C", //the only difference between sections - outer 19-20 hits are rerolled on Primary table, which is the only chance to hit Pilot
			),
			1=> array(
				6 => "Structure",
				9 => "0:Energy Diffuser",
				10 => "2:BioThruster",
				12 => "0:Self Repair",
				14 => "1:Slicer Beam",
				16 => "0:Hangar",
				17 => "0:Scanner",
				18 => "0:Reactor",
				20 => "Primary", 
			),
			2=> array(
				6 => "Structure",
				9 => "0:Energy Diffuser",
				10 => "2:BioThruster",
				12 => "0:Self Repair",
				14 => "1:Slicer Beam",
				16 => "0:Hangar",
				17 => "0:Scanner",
				18 => "0:Reactor",
				20 => "Primary", 
			),
        );
    }
}

?>
