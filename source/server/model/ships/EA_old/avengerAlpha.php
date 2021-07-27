<?php
class AvengerAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 420;
        $this->faction = "EA (early)";
        $this->phpclass = "AvengerAlpha";
        $this->imagePath = "img/ships/avenger.png";
        $this->shipClass = "Avenger Heavy Carrier (Alpha Model)";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>48);
//			$this->variantOf = "Avenger Heavy Carrier (Gamma)";
//			$this->occurence = "common";
	    $this->isd = 2168;
		$this->unofficial = true;
	    
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.25;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 6, 4));
    	$this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new JumpEngine(4, 20, 4, 24));
		
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 240, 360));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 0, 120));
        $this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 240, 360));
        $this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 0, 120));

        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
    	$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new InterceptorPrototype(2, 4, 1, 180, 300));
        $this->addAftSystem(new InterceptorPrototype(2, 4, 1, 60, 180));
        
    	$this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));
    	$this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addLeftSystem(new LightLaser(2, 4, 2, 240, 360));
        $this->addLeftSystem(new LightLaser(2, 4, 2, 180, 300));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Hangar(3, 6));
		
    	$this->addRightSystem(new Thruster(3, 13, 0, 3, 4));
    	$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new LightLaser(2, 4, 2, 0, 120));
        $this->addRightSystem(new LightLaser(2, 4, 2, 60, 180));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Hangar(3, 6));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 3, 40));
        $this->addLeftSystem(new Structure( 3, 60));
        $this->addRightSystem(new Structure( 3, 60));
        $this->addPrimarySystem(new Structure( 4, 48));

        $this->hitChart = array(
        0=> array(
                10 => "Structure",
                12 => "Jump Engine",
                14 => "Scanner",
                16 => "Engine",
                17 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
        ),
        1=> array(
                4 => "Thruster",
                6 => "Light Plasma Cannon",
                7 => "Medium Plasma Cannon",
                10 => "Interceptor Prototype",
                18 => "Structure",
                20 => "Primary",
        ),
        2=> array(
                6 => "Thruster",
                10 => "Interceptor Prototype",
                18 => "Structure",
                20 => "Primary",
        ),
        3=> array(
                4 => "Thruster",
                5 => "Medium Plasma Cannon",
                7 => "Light Laser",
                11 => "Hangar",
                18 => "Structure",
                20 => "Primary",
        ),
        4=> array(
                4 => "Thruster",
                5 => "Medium Plasma Cannon",
                7 => "Light Laser",
                11 => "Hangar",
                18 => "Structure",
                20 => "Primary",
            ),
        );

    }
}
