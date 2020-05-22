<?php
class Dargan extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = "Centauri";
        $this->phpclass = "Dargan";
        $this->imagePath = "img/ships/dargan.png";
        $this->shipClass = "Dargan Strike Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
		$this->customFighter = array("Rutarian"=>12);
        $this->limited = 10;
	    
	    $this->isd = 2258;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	    
	    $this->notes = 'Chameleon Sensors (no effect in game).'.
	    $this->notes .= '<br>Rutarian-capable.'.
		
        
         
        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 18, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 20, 4, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 12, 2));
	$this->addPrimarySystem(new Hangar(6, 14));
        $this->addPrimarySystem(new TwinArray(4, 6, 2, 90, 270));
        
		
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
	$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
	$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
        $this->addFrontSystem(new Mattercannon(4, 7, 4, 300, 60));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));

        $this->addAftSystem(new JumpEngine(5, 20, 3, 16));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        
	$this->addLeftSystem(new BattleLaser(3, 6, 6, 240, 0));
	$this->addLeftSystem(new Mattercannon(3, 7, 4, 240, 0));
	$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		
	$this->addRightSystem(new BattleLaser(3, 6, 6, 0, 120));
	$this->addRightSystem(new Mattercannon(3, 7, 4, 0, 120));
	$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		

        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 44));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 5, 52));
        $this->addRightSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 7, 39));
	    
	    
	    
  
	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
                    8 => "Structure",
                    10 => "Twin Array",
                    13 => "ELINT Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array( //Forward
                    3 => "Thruster",
                    5 => "Matter Cannon",
                    9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    7 => "Thruster",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
                    6 => "Battle Laser",
                    9 => "Matter Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
                    6 => "Battle Laser",
                    9 => "Matter Cannon",
                    18 => "Structure",
                    20 => "Primary",
            )
        );
	    
    }
}

?>
