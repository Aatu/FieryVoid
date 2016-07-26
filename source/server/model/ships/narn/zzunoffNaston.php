<?php
class zzunoffNaSton extends BaseShip{
    	/*Narn Na'Ston Explorer, Showdowns-10 (unofficial)*/
	/*it's not a T'Loth variant, but is based on T'Loth hull elements - so I used T'Loth image*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 900;
		$this->faction = "Narn";
        $this->phpclass = "zzunoffNaSton";
        $this->imagePath = "img/ships/tloth.png";
        $this->shipClass = "Na'Ston Explorer";
        //$this->fighters = array("normal"=>12);//12 cargo shuttles by default
        

	//$this->occurence = "rare";
	$this->limited = 33;
	$this->isd = 2221;
	$this->unofficial = true;


        $this->forwardDefense = 17;
        $this->sideDefense = 18;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0; 


        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 27, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 24, 6, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 14));
        $this->addFrontSystem(new CargoBay(2, 50));
        
	//fwd
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
      
		//aft
		$this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
		$this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        
		//left
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 0));
		$this->addLeftSystem(new MediumPlasma(4, 5, 3, 300, 0));
		$this->addLeftSystem(new MediumPlasma(4, 5, 3, 300, 0));
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
              
		//right
		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addRightSystem(new MediumPlasma(4, 5, 3, 0, 60));
		$this->addRightSystem(new MediumPlasma(4, 5, 3, 0, 60));
		$this->addRightSystem(new Thruster(4, 20, 0, 6, 4));

		//structures
        $this->addFrontSystem(new Structure(4, 52));
        $this->addAftSystem(new Structure(4, 75));
        $this->addLeftSystem(new Structure(4, 75));
        $this->addRightSystem(new Structure(4, 75));
        $this->addPrimarySystem(new Structure(5, 68));
		
		/*NOTE: Na'Ston hit chart on SCS seems to be in error, I correct it as I see fit*/
		$this->hitChart = array( 
			0=> array(
				7 => "Structure",
				9 => "Cargo Bay",
				11 => "Jump Engine",
				13 => "ELINT Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				9 => "Twin Array",
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
