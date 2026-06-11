<?php
class shadowStrikeCruiser extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4500;
		$this->faction = "Custom Ships";
        $this->phpclass = "shadowStrikeCruiser";
        $this->imagePath = "img/ships/ShadowBattlecruiser.png";
        $this->shipClass = "Shadow Strike Cruiser";
        $this->canvasSize = 225;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 3; //it's actually a Capital ship using MCV layout
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
		$this->halfPhaseThrust = 6; //needed for half phasing; equal to thrust from two BioThrusters on a given ship
		$this->advancedArmor = true;   
        $this->turncost = 1;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 3 *5;
		
	    $this->notes = 'For All Alone in the Night campaign';
		$this->notes .= "<br>Atmospheric capable";//even largest Shadow ships are atmospheric capable
		
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
        $this->addPrimarySystem(new Reactor(6, 30, 0, 0));
        $this->addPrimarySystem(new ShadowPilot(7, 24, 0, 0));
		$scanner = new Scanner(6, 28, 6, 14);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);
		$this->addPrimarySystem(new PhasingDrive(6, 20, 6, 8));
        $this->addPrimarySystem(new SelfRepair(3, 6, 4)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(3, 6, 4)); //armor, structure, output
		
		
		//EnergyDiffuser - 2 on each side, moved to sides so it's readable!
		$diffuserPort = new EnergyDiffuser(6, 21, 15, 180, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
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
        $this->addLeftSystem($diffuserPort);
		
		$diffuserPort = new EnergyDiffuser(6, 21, 15, 180, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(40,'L');//absorbtion capacity,side
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
        $this->addLeftSystem($diffuserPort);
		
		
		
		
		$diffuserStbd = new EnergyDiffuser(6, 21, 15, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
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
        $this->addRightSystem($diffuserStbd);
		
		$diffuserStbd = new EnergyDiffuser(6, 21, 15, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(40,'R');//absorbtion capacity,side
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
        $this->addRightSystem($diffuserStbd);
		
		
		
		//weapons - Forward for visual reasons!
        $this->addFrontSystem(new MolecularSlicerBeamA(5, 0, 0, 300, 60));
       	$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 240, 360));
       	$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 240, 360));
       	$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 0, 120));
       	$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 0, 120));
	    
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

       	   
	    //Structure
        $this->addPrimarySystem(new Structure( 6, 72));
		
		/*systems on Shadow ships CANNOT be targeted by called shots!*/
		$this->notes .= "<br>Cannot be targeted by called shots.";
		foreach ($this->systems as $sys){
			$sys->isPrimaryTargetable = false; 
			$sys->isTargetable = false; //cannot be targeted ever!
		}
				
	
		$this->hitChart = array(
			0=> array(
				5 => "Structure",
				6 => "3:Energy Diffuser", //Diffusers split to Port and Starboard set!
				7 => "4:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Advanced Slicer Beam",
				14 => "1:Heavy Phasing Pulse Cannon",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "C&C", //the only difference between sections - outer 19-20 hits are rerolled on Primary table, which is the only chance to hit Pilot
			),
			1=> array(
				5 => "Structure",
				6 => "3:Energy Diffuser", //Diffusers split to Port and Starboard set!
				7 => "4:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Advanced Slicer Beam",
				14 => "1:Heavy Phasing Pulse Cannon",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
			2=> array(
				5 => "Structure",
				6 => "3:Energy Diffuser", //Diffusers split to Port and Starboard set!
				7 => "4:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Advanced Slicer Beam",
				14 => "1:Heavy Phasing Pulse Cannon",
				16 => "0:Scanner",
				17 => "0:Reactor",
				18 => "0:Phasing Drive",
				20 => "Primary",
			),
		);				
    }
}



?>
