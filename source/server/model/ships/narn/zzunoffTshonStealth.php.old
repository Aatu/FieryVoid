<?php
class zzunoffTShonStealth extends BaseShip{
    	/*Narn T'Shon Early Explorer, Showdowns-10 (unofficial)*/
    	/*with stealth coating and improved sensors applied*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600+120;
		$this->faction = "Narn";
        $this->phpclass = "zzunoffTShonStealth";
        $this->imagePath = "img/ships/tloth.png";
        $this->shipClass = "T'Shon Explorer (Stealth)";
        //$this->fighters = array("normal"=>12); //12 Assault Shuttles in standared configuration

	//$this->variantOf = "T'Loth Assault Cruiser";
	$this->variantOf = "OBSOLETE"; //let's drop stealthed variant, ship enhancements now exist in game
	$this->occurence = "rare";
	$this->isd = 2214;
	$this->unofficial = true;
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0; 
        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 24, 6, 9));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 16));
        
	//fwd
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
      
		//aft
		$this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
		$this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        
		//left
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 0));
		$this->addLeftSystem(new MediumPlasma(4, 5, 3, 300, 0));
		$this->addLeftSystem(new MediumPlasma(4, 5, 3, 300, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
              
		//right
		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addRightSystem(new MediumPlasma(4, 5, 3, 0, 60));
		$this->addRightSystem(new MediumPlasma(4, 5, 3, 0, 60));
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
				12 => "ELINT Scanner",
				14 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				10 => "Thruster",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				7 => "Medium Plasma Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				7 => "Medium Plasma Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		);	
    }
}
?>
