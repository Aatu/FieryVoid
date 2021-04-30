<?php
class swMonCalMC75 extends BaseShip{
  /*StarWars Mon Calamari MC75 Starcruiser*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1250;
	$this->faction = "ZStarWars";
        $this->phpclass = "swMonCalMC75";
        $this->imagePath = "img/starwars/mc75.png";
        $this->shipClass = "Mon Calamari MC75 Star Cruiser";
        $this->shipSizeClass = 3;
        $this->limited = 10; //Restricted Deployment
	    
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Rebel Alliance, New Republic";
	    
	$this->fighters = array("Fighter Squadrons"=>3);
	    
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
	$this->iniativebonus = -1 *5; 
		
	    
	    
	$this->addPrimarySystem(new CnC(5, 30, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 40, 0, 0));
        $this->addPrimarySystem(new SWScanner(4, 26, 7, 7));
        $this->addPrimarySystem(new Engine(5, 26, 0, 10, 4));
	$this->addPrimarySystem(new SWRayShield(2,10,5,2,180,0)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addPrimarySystem(new SWRayShield(2,10,5,2,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$hyperdrive = new JumpEngine(4, 24, 8, 20);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new Hangar(3, 36, 12));
        
		
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(3,18,6,3,300,60)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWHeavyIon(3, 240, 120, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyIon(3, 240, 120, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 240, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 300, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWTractorBeam(2,240,0,1));
	$this->addFrontSystem(new SWTractorBeam(2,300,60,1));
	$this->addFrontSystem(new SWTractorBeam(2,0,120,1));

        $this->addAftSystem(new Thruster(3, 14, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 14, 0, 3, 2));
 	$this->addAftSystem(new SWRayShield(3,18,6,3,120,240)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWHeavyIon(3, 120, 240, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyIon(3, 120, 240, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumLaser(2, 120, 300, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumLaser(2, 60, 240, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWTractorBeam(2,180,300,1));
	$this->addAftSystem(new SWTractorBeam(2,120,240,1));
	$this->addAftSystem(new SWTractorBeam(2,60,180,1));

	$this->addLeftSystem(new Thruster(3, 12, 0, 3, 3));
	$this->addLeftSystem(new Thruster(3, 12, 0, 3, 3));
	$this->addLeftSystem(new SWRayShield(3,18,8,4,210,330)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new SWHeavyTLaser(3, 210, 330, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWHeavyTLaser(3, 210, 330, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWCapitalProton(3, 210, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWCapitalProton(3, 210, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWCapitalProton(3, 180, 330, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumLaser(2, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumLaser(2, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!	
	    
	$this->addRightSystem(new Thruster(3, 12, 0, 3, 4));
	$this->addRightSystem(new Thruster(3, 12, 0, 3, 4));
	$this->addRightSystem(new SWRayShield(3,18,8,4,30,150)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new SWHeavyTLaser(3, 30, 150, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWHeavyTLaser(3, 30, 150, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWCapitalProton(3, 0, 150, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWCapitalProton(3, 0, 150, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWCapitalProton(3, 30, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumLaser(2, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumLaser(2, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 60));
	    
	    
            $this->hitChart = array(
            0=> array(
                    7 => "Structure",
		    8 => "Ray Shield",
                    10 => "Hyperdrive",
                    12 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
		    4 => "Ray Shield",
                    6 => "Heavy Ion Cannon",
                    8 => "Medium Laser",
		    11 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
		    5 => "Ray Shield",
                    7 => "Heavy Ion Cannon",
                    9 => "Medium Laser",
		    12 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
		    4 => "Ray Shield",
                    6 => "Heavy Turbolaser",
                    9 => "Capital Proton Torpedo",
		    11 => "Medium Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
		    4 => "Ray Shield",
                    6 => "Heavy Turbolaser",
                    9 => "Capital Proton Torpedo",
		    11 => "Medium Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
    );
    }
}
?>