<?php
class ShadowBattleCruiser extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4925;
		$this->faction = "Shadows";
        $this->phpclass = "ShadowBattleCruiser";
        $this->imagePath = "img/ships/ShadowBattlecruiser.png";
        $this->shipClass = "Battle Cruiser";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 3; //it's actually a Capital ship using MCV layout
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
$this->faction = "Custom Ships";
$this->variantOf = "Lurking unseen";        
		
        $this->limited = 33;
        
        $this->forwardDefense = 19;
        $this->sideDefense = 18;
        
		$this->halfPhaseThrust = 6; //needed for half phasing; equal to thrust from two BioThrusters on a given ship
        $this->gravitic = true;
		$this->advancedArmor = true;   
        $this->turncost = 1.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
		$this->iniativebonus = 3 *5;
		
		$this->fighters = array("normal"=>24);
		$this->notes = "Atmospheric capable";//even largest Shadow ships are atmospheric capable
		$this->notes .= "<br>Larger than usual changes from AoG layout - see faction description.";//firing arc, Diffuser layout, Diffuser efficiency
        
		
		$this->enhancementOptionsEnabled[] = 'SHAD_FTRL'; //can launch Shadow fighters (IF hangar capacity allows!)
		$this->enhancementOptionsDisabled[] = 'POOR_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'ELITE_CREW'; //no crew ;)
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG'; //no Engine ;)
		
		
		
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
        $this->addPrimarySystem(new Hangar(5, 24));
        $this->addPrimarySystem(new SelfRepair(3, 6, 4)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(3, 6, 4)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(3, 3, 2)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(3, 3, 2)); //armor, structure, output
		
		
		//EnergyDiffuser - 3 on each side, moved to sides!
		$diffuserPort = new EnergyDiffuser(6, 7, 5, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addLeftSystem($diffuserPort);

		$diffuserPort = new EnergyDiffuser(6, 13, 10, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
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
        $this->addLeftSystem($diffuserPort);
		
		$diffuserPort = new EnergyDiffuser(6, 21, 20, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(45,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(25,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(15,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addLeftSystem($diffuserPort);
		
		
		
		
		$diffuserStbd = new EnergyDiffuser(6, 7, 5, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addRightSystem($diffuserStbd);

		$diffuserStbd = new EnergyDiffuser(6, 13, 10, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
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
        $this->addRightSystem($diffuserStbd);
		
		$diffuserStbd = new EnergyDiffuser(6, 21, 20, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(45,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(25,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(15,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addRightSystem($diffuserStbd);
		
		
		
		
		
		
		
		
		//weapons - Forward for visual reasons!
        $this->addFrontSystem(new MolecularSlicerBeamH(5, 0, 0, 270, 90));//widened as a compensation for lack of back arc
        $this->addFrontSystem(new VortexDisruptor(5, 0, 0, 240, 360));
        $this->addFrontSystem(new VortexDisruptor(5, 0, 0, 0, 120));
        
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

       	   
	    //Structure
        $this->addPrimarySystem(new Structure( 6, 84));
		
				
	
		$this->hitChart = array(
			0=> array(
				5 => "Structure",
				6 => "3:Energy Diffuser", //Diffusers split to Port and Starboard set!
				7 => "4:Energy Diffuser",
				9 => "2:BioThruster",
				10 => "0:Self Repair",
				12 => "1:Heavy Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
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
				12 => "1:Heavy Slicer Beam",
				14 => "1:Vortex Disruptor",
				15 => "0:Hangar",
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
				12 => "1:Heavy Slicer Beam",
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
