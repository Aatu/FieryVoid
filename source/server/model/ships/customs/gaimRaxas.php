<?php
class gaimRaxas extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1500;
		$this->faction = 'Custom Ships';
		$this->phpclass = "gaimRaxas";
		$this->imagePath = "img/ships/GaimRaxas.png";
		$this->shipClass = "S-Raxas Experimental Platform";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
		$this->occurence = "unique";
		$this->unofficial = true;
	    $this->notes = 'Unique ship created for the Queens Gambit campaign';				
	    
        $this->isd = 2266;

		$this->forwardDefense = 15;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 5;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = 0*5;

		$this->advancedArmor = true;  		
		
		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for non-Shadow ships - they don't have know how to tamper with Shadow systems to that extent!		


		$this->addPrimarySystem(new Reactor(5, 24, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 3, 6));
		$this->addPrimarySystem(new Engine(5, 13, 0, 8, 4));
		$this->addPrimarySystem(new JumpEngine(4, 20, 5, 48));
		$this->addPrimarySystem(new MultiphasedCutterL(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new MultiphasedCutterL(3, 0, 0, 0, 360));
		
		
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
        		

		$this->addFrontSystem(new Thruster(4, 20, 0, 5, 1));
		$this->addFrontSystem(new MolecularSlicerBeamL(4, 12, 10, 300, 60));
		$this->addFrontSystem(new MolecularSlicerBeamL(4, 12, 10, 300, 60));
		$this->addFrontSystem(new PhasingPulseCannonH(4, 9, 5, 240, 60));
		$this->addFrontSystem(new PhasingPulseCannonH(4, 9, 5, 300, 120));				
		$this->addFrontSystem(new Bulkhead(0, 2));
		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new PhasingPulseCannonH(4, 9, 5, 120, 240));
		$this->addAftSystem(new PhasingPulseCannonH(4, 9, 5, 120, 240));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));
		
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

		$this->addLeftSystem(new Thruster(4, 15, 0, 3, 3));
		$this->addLeftSystem(new MultiphasedCutter(4, 8, 3, 180, 360));
		$this->addLeftSystem(new MultiphasedCutter(4, 8, 3, 180, 360));
		$this->addLeftSystem(new Bulkhead(0, 2));
		$this->addLeftSystem(new Bulkhead(0, 2));

		$this->addRightSystem(new Thruster(4, 15, 0, 3, 4));
		$this->addRightSystem(new MultiphasedCutter(4, 8, 3, 0, 180));
		$this->addRightSystem(new MultiphasedCutter(4, 8, 3, 0, 180));
		$this->addRightSystem(new Bulkhead(0, 2));
		$this->addRightSystem(new Bulkhead(0, 2));
        
        $this->addFrontSystem(new Structure( 5, 56));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 6, 65));
        $this->addRightSystem(new Structure( 6, 65));
        $this->addPrimarySystem(new Structure( 6, 65));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Light Multiphased Cutter",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Light Slicer Beam",
                        10 => "Heavy Phasing Pulse Cannon",
						12 => "Energy Diffuser",         
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Heavy Phasing Pulse Cannon",
						11 => "Energy Diffuser",                           
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Multiphased Cutter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Multiphased Cutter",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
