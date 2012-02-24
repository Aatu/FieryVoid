<?php
class Varnic extends BaseShip{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 580;
		$this->faction = "Narn";
        $this->phpclass = "Varnic";
        $this->imagePath = "ships/varnic.png";
        $this->shipClass = "Var'Nic";
        $this->shipSizeClass = 3;
        
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 10;

        //primary
        $this->addSystem(new Reactor(5, 18, 0, 0, 0));
        $this->addSystem(new CnC(5, 16, 0, 0, 0));
        $this->addSystem(new Scanner(5, 18, 0, 5, 9));
        $this->addSystem(new Engine(4, 14, 0, 0, 12, 2));
		$this->addSystem(new JumpEngine(5, 18, 0, 3, 20));
		$this->addSystem(new Hangar(5, 17, 0));
        
		//front
        $this->addSystem(new HeavyPulse(5, 6, 1, 4, 300, 60));
        
        $this->addSystem(new Thruster(4, 8, 1, 0, 4, 1));
        $this->addSystem(new Thruster(4, 8, 1, 0, 4, 1));

		//aft
		          


		$this->addSystem(new LightPulse(2, 4, 2, 2, 90, 270));
		$this->addSystem(new LightPulse(2, 4, 2, 2, 90, 270));

		
        $this->addSystem(new Thruster(3, 24, 2, 0, 12, 2));

		
        
		//left
		
		
		$this->addSystem(new TwinArray(3, 6, 3, 2, 240, 60));
		$this->addSystem(new MediumLaser(3, 6, 3, 5, 240, 60));
		$this->addSystem(new MediumLaser(3, 6, 3, 5, 240, 60));
        $this->addSystem(new Thruster(4, 15, 3, 0, 5, 3));
              

		//right
		
		
		$this->addSystem(new TwinArray(3, 6, 4, 2, 300, 120));
		$this->addSystem(new IonTorpedo(4, 5, 4, 4, 300, 120));
		$this->addSystem(new Thruster(4, 15, 4, 0, 5, 4));
        
		
		//structures
        $this->addSystem(new Structure(4, 40, 1));
        $this->addSystem(new Structure(4, 28, 2));
        $this->addSystem(new Structure(5, 60, 3));
        $this->addSystem(new Structure(4, 39, 4));
        $this->addSystem(new Structure(5, 36, 0));
        
    }

}



?>
