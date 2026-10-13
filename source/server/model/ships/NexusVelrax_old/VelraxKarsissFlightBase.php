<?php
class VelraxKarsissFlightBase extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 200;
		$this->faction = "Nexus Velrax Republic (early)";
        $this->phpclass = "VelraxKarsissFlightBase";
        $this->imagePath = "img/ships/AsteroidS1.png";
        $this->shipClass = "Karsiss Flight Base";
		$this->canvasSize = 160; 
		$this->unofficial = true;
        $this->isd = 2057;
        $this->occurence = "unique"; 

        $this->notes = 'Only 1 per scenario';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

		$this->fighters = array("normal"=>24, "superheavy"=>2);

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 10, 4, 4));   
        $this->addPrimarySystem(new CargoBay(4, 25));   
        $this->addPrimarySystem(new Quarters(4, 16));   
        $this->addFrontSystem(new NexusTwinIonGun(3, 4, 4, 0, 360));
        $this->addFrontSystem(new NexusLaserSpear(4, 5, 3, 300, 60));
        $this->addFrontSystem(new NexusTwinIonGun(3, 4, 4, 0, 360));
		$this->addAftSystem(new Catapult(4, 6));
		$this->addAftSystem(new Catapult(4, 6));
        $this->addAftSystem(new Thruster(4, 20, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 80));

        //Block some enhancements for OSAT units when bought
        Enhancements::nonstandardEnhancementSet($this, 'OSAT');
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				8 => "2:Thruster",
				10 => "1:Laser Spear",
				12 => "1:Twin Ion Gun",
				14 => "2:Catapult",
				16 => "0:Cargo Bay",
				17 => "0:Quarters",
				19 => "0:Scanner",
				20 => "0:Reactor",
			),
			1=> array(
				6 => "Structure",
				8 => "2:Thruster",
				10 => "1:Laser Spear",
				12 => "1:Twin Ion Gun",
				14 => "2:Catapult",
				16 => "0:Cargo Bay",
				17 => "0:Quarters",
				19 => "0:Scanner",
				20 => "0:Reactor",
			),
			2=> array(
				6 => "Structure",
				8 => "2:Thruster",
				10 => "1:Laser Spear",
				12 => "1:Twin Ion Gun",
				14 => "2:Catapult",
				16 => "0:Cargo Bay",
				17 => "0:Quarters",
				19 => "0:Scanner",
				20 => "0:Reactor",
			),
        );
    }
}

?>
