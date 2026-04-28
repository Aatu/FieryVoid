<?php
class ShadowGarundaAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 800;
        $this->faction = "Custom Ships";
        $this->phpclass = "ShadowGarundaAM";
        $this->imagePath = "img/ships/ShadowGarunda.png";
        $this->shipClass = "Shadow Garunda Destroyer";
	    $this->isd = 2262;
		$this->notes = "Corillani People's Navy (CPN)";
		$this->notes .= "<br>Just a joke";
		$this->canvasSize= 200;
        $this->fighters = array("normal"=>6);			    
		$this->unofficial = true; 

		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tabper with Shadow systems to that extent!
		$this->advancedArmor = true;   
       
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
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

		$diffuserPort = new EnergyDiffuser(4, 9, 2, 180, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addPrimarySystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addPrimarySystem($tendril);
        $this->addPrimarySystem($diffuserPort);		

		$diffuserStbd = new EnergyDiffuser(4, 9, 2, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addPrimarySystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addPrimarySystem($tendril);
        $this->addPrimarySystem($diffuserStbd);

        $this->addPrimarySystem(new Reactor(4, 17, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 5));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(2, 7));
        $this->addPrimarySystem(new Thruster(4, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 3));        
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 4)); 
        $this->addPrimarySystem(new Thruster(4, 8, 0, 3, 4));
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new PhasingPulseCannonH(5, 0, 0, 300, 60));
        $this->addFrontSystem(new PhasingPulseCannonH(5, 0, 0, 300, 60));
        $this->addFrontSystem(new MultiphasedCutterL(2, 0, 0, 240, 60));
        $this->addFrontSystem(new MultiphasedCutterL(2, 0, 0, 300, 120));

        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));        
        $this->addAftSystem(new MultiphasedCutterL(2, 0, 0, 120, 300));
        $this->addAftSystem(new MultiphasedCutterL(2, 0, 0, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 37));
        $this->addAftSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 4, 40));
			
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				8 => "Thruster",
				10 => "Energy Diffuser",
				12 => "Class-S Missile Rack",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				9 => "Heavy Phasing Pulse Cannon",
				12 => "Light Multiphased Cutter",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				9 => "Light Multiphased Cutter",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>
