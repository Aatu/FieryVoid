<?php
class TechnicalTestbed extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Custom Ships";
        $this->phpclass = "TechnicalTestbed";
        $this->imagePath = "img/ships/shokos.png";
        $this->shipClass = "New Technology Testbed";
        $this->canvasSize = 100;
	    $this->isd = 2240;
        $this->shipSizeClass = 2; //it's actually a HCV using MCV layout
        
        $this->forwardDefense = 110;
        $this->sideDefense = 112;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 6 *5;
        
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 2));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 3, 6));
        $this->addPrimarySystem(new Engine(3, 10, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));	
        $this->addPrimarySystem(new Thruster(3, 8, 0, 6, 1));		
        $this->addPrimarySystem(new Thruster(3, 10, 0, 12, 2));	
		
		$diffuserPort = new EnergyDiffuser(2, 20, 4, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(12);//just absorbtion capacity
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10);//just absorbtion capacity
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);		
		$tendril=new DiffuserTendril(5);//just absorbtion capacity
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addPrimarySystem($diffuserPort);
		
		$diffuserStbd = new EnergyDiffuser(2, 20, 4, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(12);//just absorbtion capacity
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10);//just absorbtion capacity
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(5);//just absorbtion capacity
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addPrimarySystem($diffuserStbd);
		
		
		//weapons - Forward for visual reasons!
        $this->addPrimarySystem(new BurstBeam(3, 6, 3, 240, 60));
        $this->addPrimarySystem(new BurstBeam(3, 6, 3, 300, 120));
		$this->addPrimarySystem(new mediumPulse(3, 6, 3, 240, 120));	
        
		//technical thrusters - unlimited, like for MCVs
		
       
        $this->addPrimarySystem(new Structure( 3, 80));
				
	
		$this->hitChart = array(
			0=> array(
				3 => "Thruster",
				5 => "Burst Beam",
				7 => "Medium Pulse Cannon",
				14 => "Energy Diffuser",
				15 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "0:Thruster",
				5 => "0:Burst Beam",
				7 => "0:Medium Pulse Cannon",
				14 => "0:Energy Diffuser",
				15 => "0:Scanner",
				16 => "0:Engine",
				17 => "0:Hangar",
				18 => "0:Reactor",
				20 => "Primary",
			),
			2=> array(
				3 => "0:Thruster",
				5 => "0:Burst Beam",
				7 => "0:Medium Pulse Cannon",
				14 => "0:Energy Diffuser",
				15 => "0:Scanner",
				16 => "0:Engine",
				17 => "0:Hangar",
				18 => "0:Reactor",
				20 => "Primary",
			),
		);				
    }
}



?>
