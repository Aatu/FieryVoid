<?php
class ShoVok extends MediumShip{
	/*Narn Sho'Vok Attack Cutter; source: Showdowns-10*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 345;
		$this->faction = "Narn";
        $this->phpclass = "ShoVok";
        $this->imagePath = "img/ships/shokos.png";
        $this->shipClass = "Sho'Vok Attack Cutter";
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;

        //$this->limited = 33;
	$this->occurence = "common";
	$this->isd = 2211;
	$this->unofficial = true;

        
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
        $this->addFrontSystem(new LightPlasma(3, 4, 2, 240, 60));
        $this->addFrontSystem(new LightPlasma(3, 4, 2, 300, 120));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 120));		
		
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
		$this->addAftSystem(new TwinArray(2, 6, 2, 180, 60));
        $this->addAftSystem(new TwinArray(2, 6, 2, 300, 180));
        
       
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
				8 => "Medium Plasma Cannon",
				12 => "Light Plasma Cannon",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				10 => "Twin Array",
				17 => "Structure",
				20 => "Primary",
			),
		);				
    }
}
?>
