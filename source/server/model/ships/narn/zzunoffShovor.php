<?php
class zzunoffShoVor extends MediumShip{
	/*Narn Sho'Vor Escort Cutter; source: Showdowns-10*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 365; //LOTS; as it's unofficial, I'm temted to reduce point cost - around 300, I'd say.
		$this->faction = "Narn";
        $this->phpclass = "zzunoffShoVor";
        $this->imagePath = "img/ships/shokos.png";
        $this->shipClass = "Sho'Vor Escort Cutter";
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;

        //$this->limited = 33;
	$this->variantOf = "Sho'Kos Patrol Cutter";
	$this->occurence = "uncommon";
	$this->isd = 2234;
 		$this->unofficial = 'S'; //design released after AoG demise

        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));
		
				
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new LightBolter(3, 6, 2, 240, 60));
        $this->addFrontSystem(new LightBolter(3, 6, 2, 300, 120));
		$this->addFrontSystem(new EnergyPulsar(3, 6, 3, 240, 120));		
		
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
		$this->addAftSystem(new ScatterPulsar(2, 4, 2, 180, 60));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 300, 180));
        
       
        $this->addPrimarySystem(new Structure( 3, 48));
				
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				7 => "Thruster",
				11 => "Scanner",
				14 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				6 => "Thruster",
				8 => "Energy Pulsar",
				12 => "Light Bolter",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				10 => "Scatter Pulsar",
				17 => "Structure",
				20 => "Primary",
			),
		);				
    }
}
?>
