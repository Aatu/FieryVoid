<?php
class Prototype extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20;
        $this->faction = "Custom Ships";
        $this->phpclass = "Prototype";
        $this->imagePath = "img/ships/Prototype.png";
        $this->shipClass = "Advanced Cruiser Prototype";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->fighters = array("normal"=>30);
		$this->customFighter = array("Thunderbolt"=>30);
	    $this->notes = 'Thunderbolt capable.';
	    $this->notes .= "<br>This is a custom ship only intended for use in a specific scenario, please do not use unless you've agreed it with your opponent first!";
	    
		$this->isd = 2262;
		$this->unofficial = true; 
        
		
		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tamper with Shadow systems to that extent!
		$this->advancedArmor = true; 		
		
        $this->forwardDefense = 15;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	    
		$this->iniativebonus = 1*5; 
         
        $this->addPrimarySystem(new Reactor(7, 36, 0, 0));
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
  //      $this->addPrimarySystem(new Scanner(6, 24, 5, 9));
		$sensors = new Scanner(6, 24, 5, 9);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);        
        $this->addPrimarySystem(new Engine(6, 28, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(6, 34));
        $this->addPrimarySystem(new JumpEngine(6, 24, 3, 24));
	    
		
		
		$diffuserPort = new EnergyDiffuser(5, 12, 4, 270, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addFrontSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuserPort);		
		
		$diffuserStbd = new EnergyDiffuser(5, 12, 4, 0, 90);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addFrontSystem($tendril);
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuserStbd);
		
        $this->addFrontSystem(new Thruster(4, 15, 0, 5, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 5, 1));
		$this->addFrontSystem(new MolecularSlicerBeamL(5, 0, 0, 300, 0));
		$this->addFrontSystem(new MolecularSlicerBeamL(5, 0, 0, 330, 30));
		$this->addFrontSystem(new MolecularSlicerBeamL(5, 0, 0, 0, 60));		
		$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 300, 0));
		$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 0, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
	    

		$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
		$this->addAftSystem(new Thruster(3, 7, 0, 2, 2));		
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
		
		$this->addAftSystem(new MolecularSlicerBeamL(5, 0, 0, 150, 210));
		$this->addAftSystem(new PhasingPulseCannonH(4, 0, 0, 180, 240));
		$this->addAftSystem(new PhasingPulseCannonH(4, 0, 0, 120, 180));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
				
		$diffuserPort = new EnergyDiffuser(5, 12, 4, 180, 270);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addAftSystem($tendril);
		$tendril=new DiffuserTendril(20,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addAftSystem($tendril);
        $this->addAftSystem($diffuserPort);		
		
		$diffuserStbd = new EnergyDiffuser(5, 12, 4, 90, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addAftSystem($tendril);
		$tendril=new DiffuserTendril(20,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addAftSystem($tendril);
        $this->addAftSystem($diffuserStbd);
		
        
		$this->addLeftSystem(new Thruster(5, 24, 0, 6, 3));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(3, 0, 0, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));
	    
        $this->addRightSystem(new Thruster(5, 24, 0, 6, 4));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(3, 0, 0, 0, 180));                
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 72));
        $this->addAftSystem(new Structure( 5, 60 ));
        $this->addLeftSystem(new Structure( 5, 85));
        $this->addRightSystem(new Structure( 5, 85));
        $this->addPrimarySystem(new Structure( 6, 66));
		
		
		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Jump Engine",
                        14 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        6 => "Light Slicer Beam",
                        8 => "Heavy Phasing Pulse Cannon",
                        10 => "Interceptor II",
                        12 => "Energy Diffuser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        7 => "Light Slicer Beam",
                        9 => "Heavy Phasing Pulse Cannon",
                        10 => "Interceptor II",
                        11 => "Energy Diffuser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        10 => "Light Multiphased Cutter",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        10 => "Light Multiphased Cutter",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );		
    }
}
?>