<?php
class ShadowMollantaAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1800;
		$this->faction = "Custom Ships";
        $this->phpclass = "ShadowMollantaAM";
        $this->imagePath = "img/ships/ShadowMollanta.png";
        $this->shipClass = "Shadow Mollanta Heavy Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
	    $this->isd = 2260;
		$this->notes = "Corillani People's Navy (CPN)";
		$this->notes .= "<br>Just a joke";

		$this->unofficial = true; 

		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tabper with Shadow systems to that extent!
		$this->advancedArmor = true;   
		
        $this->forwardDefense = 13;
        $this->sideDefense = 18;
        
        $this->turncost = 1.66;
        $this->turndelaycost = 1.66;
        $this->accelcost = 6;
        $this->rollcost = 2;
        $this->pivotcost = 4;


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		$this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		$this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X
		$this->enhancementOptionsEnabled[] = 'AMMO_Z';//add enhancement options for other missiles - Class-Z   					
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 8));
        $this->addPrimarySystem(new Engine(4, 20, 0, 16, 4));
        $this->addPrimarySystem(new JumpEngine(4, 15, 4, 36));
        $this->addPrimarySystem(new Hangar(2, 2));

		$diffuserPort = new EnergyDiffuser(4, 9, 2, 270, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addFrontSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuserPort);		
		
		$diffuserStbd = new EnergyDiffuser(4, 9, 2, 0, 90);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addFrontSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuserStbd);
            
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));      
        $this->addFrontSystem(new MolecularSlicerBeamL(4, 0, 0, 300, 60)); 
        $this->addFrontSystem(new MolecularSlicerBeamL(4, 0, 0, 300, 60));
		
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new PhasingPulseCannonH(4, 0, 0, 120, 240));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));       
		
		$diffuserPort = new EnergyDiffuser(4, 9, 2, 180, 270);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addAftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addAftSystem($tendril);
        $this->addAftSystem($diffuserPort);		
		
		$diffuserStbd = new EnergyDiffuser(4, 9, 2, 90, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addAftSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addAftSystem($tendril);
        $this->addAftSystem($diffuserStbd);
        
 		$this->addLeftSystem(new PhasingPulseCannonH(4, 0, 0, 300, 60));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 360));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 360));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 360));        
        $this->addLeftSystem(new Thruster(4, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(4, 10, 0, 3, 3));
        $this->addLeftSystem(new Hangar(2, 6));
		
 		$this->addRightSystem(new PhasingPulseCannonH(4, 0, 0, 300, 60));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));        
        $this->addRightSystem(new Thruster(4, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(4, 10, 0, 3, 4));
        $this->addRightSystem(new Hangar(2, 6));        
		
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(4, 60));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				9 => "Light Slicer Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				9 => "Class-S Missile Rack",
				10 => "Heavy Phasing Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				7 => "Thruster",
				9 => "Heavy Phasing Pulse Cannon",
				12 => "Light Multiphased Cutter",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				7 => "Thruster",
				9 => "Heavy Phasing Pulse Cannon",
				12 => "Light Multiphased Cutter",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
