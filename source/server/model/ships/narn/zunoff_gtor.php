<?php
class GTor extends BaseShip{
	/*Narn G'Tor Bolt Cruiser, Showdowns-10 (unofficial)*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    		$this->pointCost = 650;
    		$this->faction = "Narn";
        $this->phpclass = "GTor";
        $this->imagePath = "img/ships/gquan.png";
        $this->shipClass = "G'Tor Bolt Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);

      	$this->occurence = "rare";
      	$this->isd = 2234;
      	$this->unofficial = true;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 24, 5, 8));
        $this->addPrimarySystem(new Engine(6, 18, 0, 11, 3));
        $this->addPrimarySystem(new JumpEngine(6, 30, 3, 20));
        $this->addPrimarySystem(new Hangar(6, 14));
        
        $this->addFrontSystem(new HeavyBolter(4, 10, 6, 300, 60));        
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));        
        $this->addFrontSystem(new HeavyBolter(4, 10, 6, 300, 60));
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 60));
		
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));	
        $this->addAftSystem(new LightBolter(2, 6, 2, 90, 270));
        $this->addAftSystem(new LightBolter(2, 6, 2, 90, 270));
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		
        $this->addLeftSystem(new MediumBolter(2, 8, 4, 270, 90));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 270, 90));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		
        $this->addRightSystem(new MediumBolter(2, 8, 4, 270, 90));
        $this->addRightSystem(new TwinArray(3, 6, 2, 270, 90));	
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
		
		
        $this->addFrontSystem(new Structure(5, 70));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 70));
        $this->addRightSystem(new Structure(4, 70));
        $this->addPrimarySystem(new Structure(6, 50));
		
		

    		$this->hitChart = array(
    			0=> array( //PRIMARY
    				8 => "Structure",
    				11 => "Jump Engine",
    				13 => "Scanner",
    				15 => "Engine",
    				17 => "Hangar",
    				19 => "Reactor",
    				20 => "C&C",
    			),
    			1=> array( //Front
    				3 => "Thruster",
    				7 => "Heavy Bolter",
    				11 => "Medium Laser",
    				18 => "Structure",
    				20 => "Primary",
    			),
    			2=> array( //Aft
    				7 => "Thruster",
    				9 => "Twin Array",
    				11 => "Light Bolter",
    				18 => "Structure",
    				20 => "Primary",
    			),
    			3=> array( //Port
    				4 => "Thruster",
    				7 => "Medium Bolter",
    				9 => "Twin Array",
    				18 => "Structure",
    				20 => "Primary",
    			),
    			4=> array( //Stbd
    				4 => "Thruster",
    				7 => "Medium Bolter",
    				9 => "Twin Array",
    				18 => "Structure",
    				20 => "Primary",
    			),
    		);


    }
}
?>
