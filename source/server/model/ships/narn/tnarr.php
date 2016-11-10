<?php
class Tnarr extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "Narn";
        $this->phpclass = "Tnarr";
        $this->imagePath = "img/ships/tloth.png";
        $this->shipClass = "T'Narr Early Cruiser";
        $this->fighters = array("normal"=>12);
	$this->variantOf = "T'Loth Assault Cruiser";
	$this->isd = 2211;

        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;

        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 21, 3, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 14));
        
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
      
		//aft
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        
		//left
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 0));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 300, 0));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 300, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
              
		//right
		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 0, 60));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 0, 60));
		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));

		//structures
        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 62));
        $this->addRightSystem(new Structure(4, 62));
        $this->addPrimarySystem(new Structure(5, 45));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				10 => "Jump Engine",
				12 => "Scanner",
				14 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Light Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				10 => "Thruster",
				12 => "Light Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				7 => "Heavy Plasma Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				7 => "Heavy Plasma Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		);	
    }
}
?>
