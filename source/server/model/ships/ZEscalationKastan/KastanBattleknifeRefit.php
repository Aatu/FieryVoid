<?php
class KastanBattleknifeRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZEscalation Kastan Monarchy";
        $this->phpclass = "KastanBattleknifeRefit";
        $this->imagePath = "img/ships/EscalationWars/KastanBattleknife.png";
        $this->shipClass = "Battleknife (1882 refit)";
			$this->variantOf = "Battleknife";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->agile = true;		
        $this->canvasSize = 75;
	    $this->isd = 1882;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 70;
         
		$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 9, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 9, 0, 3, 4));        
        
		$this->addFrontSystem(new EWRoyalLaser(2, 6, 4, 240, 360));
        $this->addFrontSystem(new EWLaserBolt(1, 4, 2, 240, 120));
        $this->addFrontSystem(new EWRoyalLaser(2, 6, 4, 0, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 4, 1));
	    
		$this->addAftSystem(new EWLaserBolt(1, 4, 2, 60, 300));
		$this->addAftSystem(new EWLaserBolt(1, 4, 2, 60, 300));
        $this->addAftSystem(new Thruster(2, 12, 0, 10, 2));    
       
        $this->addPrimarySystem(new Structure(3, 30));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			8 => "Royal Laser",
			10 => "Laser Bolt",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Laser Bolt",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
