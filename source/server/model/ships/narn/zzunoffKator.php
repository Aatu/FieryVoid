<?php
class zzunoffKaTor extends HeavyCombatVessel{
    /*Narn Ka'Tor Early Battle Destroyer, Showdowns-10 (unofficial)*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 550;
        $this->faction = "Narn";
        $this->phpclass = "zzunoffKaTor";
        $this->imagePath = "img/ships/katoc.png";
        $this->shipClass = "Ka'Tor Battle Destroyer";
        $this->fighters = array("normal"=>6);        
        
	$this->occurence = "common";
	$this->isd = 2234;
	$this->unofficial = true;

        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 16, 0, 6));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 7));
        $this->addPrimarySystem(new Engine(5, 16, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 270, 90));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 270, 90));
        $this->addFrontSystem(new HeavyBolter(5, 10, 6, 300, 60));
        $this->addFrontSystem(new ImperialLaser(4, 8, 5, 240, 0));
        $this->addFrontSystem(new ImperialLaser(4, 8, 5, 0, 120));
        
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 90, 270));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 90, 270));        
        

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 54));
        $this->addAftSystem(new Structure( 4, 54));
        $this->addPrimarySystem(new Structure( 5, 50));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				6 => "Imperial Laser",
				8 => "Heavy Bolter",
				10 => "Scatter Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Scatter Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
		);         
    }
}
?>
