<?php
/*
Shadow Omega as rendered by Marcin Sawicki - assuming it was a conversion of standard Omega using technology received from Shadows, and that SHadows didn't give their very best to the EA.
My Shadow Omega got:
 - Improved Sensors (that would be a major thing for hte EA - ability to counter Jammer, at least partially)
 - Energy Diffusers (obviously they were there in the show :) ; I made them stronger than AoG, too, and gave them overlap to the sides - but also made room for them by removing Pulses and half of hangar capacity)
 - Drakh weapons (rather than true Shadow weapons - in the show Shadow Omega's blue beams were quite different from purple of Shadow slicers! powerful concentrated beam option on Concentrators would fit well observed reliance on light beam weapons to fight White Star fleet)
 	Concentrators aren't switched 1:1, being larger and more potent weapons than SPBs. They don't have immense range of Light Multiphased Cutters, too (but would almost match them against WS due to Improved Sensors!) . I gave them a bit more armor and reduced hit chance, too.
 - NO improvements to profile, armor or system ratings
*/
class OmegaEpsilonDrakh  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 1300;
        $this->faction = "EA";
        $this->phpclass = "OmegaEpsilonDrakh";
        $this->imagePath = "img/ships/omega.png";
        $this->shipClass = "Shadow Omega (Epsilon)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->fighters = array("normal"=>12);
		$this->customFighter = array("Thunderbolt"=>12);
	    $this->notes = 'Thunderbolt capable.';
	    $this->notes .= "<br>Special deployment: if deployed as Clark's elite force rather than general EA, treated as Common.";
	    $this->notes .= "<br>CUSTOM rendering of Shadow Omega - with assumption Shadows didn't give EA their very best.";
	    
	    
	$this->isd = 2261;
	$this->variantOf = 'Omega Destroyer (Alpha)';
	$this->occurence = 'uncommon';
	    
	$this->unofficial = true; 
        $this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tabper with Shadow systems to that extent!
		
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
	$scanner = new Scanner(6, 20, 4, 8);	    
	$scanner->markImproved();
        $this->addPrimarySystem($scanner);
        $this->addPrimarySystem(new Engine(6, 20, 0, 8, 3));
	$this->addPrimarySystem(new Hangar(6, 16, 12));//let's make extra room for more shuttles ;) - they're highly experimental and may have increased needs in this regard
        $this->addPrimarySystem(new JumpEngine(6, 20, 3, 20));

	    

	$diffuserPort = new EnergyDiffuser(4, 10, 4, 240, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
	$tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
	$diffuserPort->addTendril($tendril);
	$this->addFrontSystem($tendril);
	$tendril=new DiffuserTendril(8,'L');//absorbtion capacity,side
	$diffuserPort->addTendril($tendril);
	$this->addFrontSystem($tendril);
	$tendril=new DiffuserTendril(4,'L');//absorbtion capacity,side
	$diffuserPort->addTendril($tendril);
	$this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuserPort);		
		
	$diffuserStbd = new EnergyDiffuser(4, 10, 4, 0, 120);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
	$tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
	$diffuserStbd->addTendril($tendril);
	$this->addFrontSystem($tendril);
	$tendril=new DiffuserTendril(8,'R');//absorbtion capacity,side
	$diffuserStbd->addTendril($tendril);
	$this->addFrontSystem($tendril);
	$tendril=new DiffuserTendril(4,'R');//absorbtion capacity,side
	$diffuserStbd->addTendril($tendril);
	$this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuserStbd);
	    
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
	$this->addFrontSystem(new customMphasedBeamAcc(4, 0, 0, 300, 0));
	$this->addFrontSystem(new customMphasedBeamAcc(4, 0, 0, 0, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new customMphasedBeamAcc(4, 0, 0, 180, 240));
	$this->addAftSystem(new customMphasedBeamAcc(4, 0, 0, 120, 180));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        
	
	$diffuserPort = new EnergyDiffuser(4, 10, 4, 180, 300);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
	$tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
	$diffuserPort->addTendril($tendril);
	$this->addAftSystem($tendril);
	$tendril=new DiffuserTendril(8,'L');//absorbtion capacity,side
	$diffuserPort->addTendril($tendril);
	$this->addAftSystem($tendril);
	$tendril=new DiffuserTendril(4,'L');//absorbtion capacity,side
	$diffuserPort->addTendril($tendril);
	$this->addAftSystem($tendril);
        $this->addAftSystem($diffuserPort);		
		
	$diffuserStbd = new EnergyDiffuser(4, 10, 4, 60, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
	$tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
	$diffuserStbd->addTendril($tendril);
	$this->addAftSystem($tendril);
	$tendril=new DiffuserTendril(8,'R');//absorbtion capacity,side
	$diffuserStbd->addTendril($tendril);
	$this->addAftSystem($tendril);
	$tendril=new DiffuserTendril(4,'R');//absorbtion capacity,side
	$diffuserStbd->addTendril($tendril);
	$this->addAftSystem($tendril);
        $this->addAftSystem($diffuserStbd);
	    
	    
	$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 180, 0));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 180, 0));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 180, 0));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));


        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 0, 180));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 0, 180));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 0, 180));
        $this->addLeftSystem(new customLtPhaseDisruptorShip(3, 0, 0, 0, 180));
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 4, 50 ));
        $this->addLeftSystem(new Structure( 4, 70));
        $this->addRightSystem(new Structure( 4, 70));
        $this->addPrimarySystem(new Structure( 6,60));
		
		
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
                        6 => "Multiphased Beam Accelerator",
                        8 => "Energy Diffuser",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        8 => "Multiphased Beam Accelerator",
                        10 => "Energy Diffuser",
                        13 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        8 => "Light Phase Disruptor",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        8 => "Light Phase Disruptor",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );		
    }
}
?>
