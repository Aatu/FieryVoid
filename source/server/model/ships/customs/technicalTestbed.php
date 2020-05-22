<?php
class TechnicalTestbed extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Custom Ships";
        $this->phpclass = "TechnicalTestbed";
        $this->imagePath = "img/ships/ShadowCruiser.png";
        $this->shipClass = "New Technology Testbed";
        $this->canvasSize = 200;
	    $this->isd = 2240;
        $this->shipSizeClass = 2; //it's actually a HCV using MCV layout
        $this->agile = true;
        
        $this->forwardDefense = 110;
        $this->sideDefense = 112;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = 6 *5;
        
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 2));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 3, 6));
		$this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new SelfRepair(5, 6, 0)); //armor, structure, output
		
		
		
		//EnergyDiffuser		
		$diffuserPort = new EnergyDiffuser(2, 20, 4, 180, 0);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'L');//absorbtion capacity,side
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
		$tendril=new DiffuserTendril(6,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addPrimarySystem($diffuserPort);
		
		$diffuserStbd = new EnergyDiffuser(2, 20, 4, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(30,'R');//absorbtion capacity,side
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
		$tendril=new DiffuserTendril(6,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addPrimarySystem($diffuserStbd);
		
		
		//BioDrive
		$bioDrive = new BioDrive(3); //ONLY efficiency
		
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
				
		$bioThruster = new BioThruster(4,10,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$this->addAftSystem($bioThruster);
		
        $this->addPrimarySystem($bioDrive);
		
		
		
		
		//weapons - Forward for visual reasons!
        $this->addFrontSystem(new BurstBeam(3, 6, 3, 240, 60));
        $this->addFrontSystem(new BurstBeam(3, 6, 3, 300, 120));
		$this->addFrontSystem(new mediumPulse(3, 6, 3, 240, 120));	
        
		//technical thrusters - unlimited, like for MCVs		
		$this->addLeftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addLeftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addRightSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
		$this->addRightSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance

       	   
	    //Structure
        $this->addPrimarySystem(new Structure( 3, 80));
		
				
	
		$this->hitChart = array(
			0=> array(
				3 => "Thruster",
				5 => "1:Burst Beam",
				7 => "1:Medium Pulse Cannon",
				14 => "Energy Diffuser",
				15 => "Scanner",
				16 => "2:BioThruster",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				/*
				3 => "0:Thruster",
				5 => "1:Burst Beam",
				7 => "1:Medium Pulse Cannon",
				14 => "0:Energy Diffuser",
				15 => "0:Scanner",
				16 => "2:BioThruster",
				17 => "0:Hangar",
				18 => "0:Reactor",
				20 => "Primary",
				*/
				20=>"0:BioThruster",
			),
			2=> array(
				3 => "0:Thruster",
				5 => "1:Burst Beam",
				7 => "1:Medium Pulse Cannon",
				14 => "0:Energy Diffuser",
				15 => "0:Scanner",
				16 => "2:BioThruster",
				17 => "0:Hangar",
				18 => "0:Reactor",
				20 => "Primary",
			),
		);				
    }
}



?>
