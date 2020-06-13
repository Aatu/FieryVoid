<?php
class OmegaEpsilon  extends BaseShip{
	/*Shadow Omega - AoG rendering*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 2200;
        $this->faction = "EA";
        $this->phpclass = "omegaEpsilon";
        $this->imagePath = "img/ships/omegaShadow.png";
        $this->shipClass = "Shadow Omega Destroyer (Epsilon)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->fighters = array("normal"=>24);
		$this->customFighter = array("Thunderbolt"=>24);
	    $this->notes = 'Thunderbolt capable.';
	    $this->notes .= "<br>This is OFFICIAL AoG ship, but with deployment restrictions of custom unit (eg. requiring opponent's consent).";
	    
		$this->isd = 2260;
		//$this->variantOf = 'Omega Destroyer (Alpha)'; //by SCS it's NOT an Omega variant, but a separate hull!
		//$this->occurence = 'common'; 
		$this->unofficial = true; //this is OFFICIAL AoG unit, but its deployment requires opponent consent - which is essentially the same as custom unit...
        
		
		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tabper with Shadow systems to that extent!
		
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	    
		$this->initiative = 0*5; 
         
        $this->addPrimarySystem(new Reactor(6, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0)); //I'm afraid to put in second C&C, so I combine their structure and increase armor
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
        $this->addPrimarySystem(new Engine(6, 23, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(6, 26, 24));
        $this->addPrimarySystem(new JumpEngine(6, 20, 3, 20));
	    
		
		
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
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new MolecularSlicerBeamL(4, 0, 0, 300, 0));
		$this->addFrontSystem(new MolecularSlicerBeamL(4, 0, 0, 0, 60));
		$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 300, 0));
		$this->addFrontSystem(new PhasingPulseCannonH(4, 0, 0, 0, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
	    
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new PhasingPulseCannonH(4, 0, 0, 180, 240));
		$this->addAftSystem(new PhasingPulseCannonH(4, 0, 0, 120, 180));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
				
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
		
        
		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 0));
        $this->addLeftSystem(new MultiphasedCutterL(2, 0, 0, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));
	    
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 64));
        $this->addAftSystem(new Structure( 5, 54 ));
        $this->addLeftSystem(new Structure( 5, 75));
        $this->addRightSystem(new Structure( 5, 75));
        $this->addPrimarySystem(new Structure( 6,65));
		
		
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
                        6 => "Thruster",
                        9 => "Heavy Phasing Pulse Cannon",
                        11 => "Interceptor II",
                        13 => "Energy Diffuser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Light Multiphased Cutter",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Light Multiphased Cutter",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );		
    }
}
?>
