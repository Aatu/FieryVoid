<?php
class swMonCalMC80 extends BaseShip{
  /*StarWars Mon Calamari MC80 Battlecruiser*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1700;
	$this->faction = "ZStarWars";
        $this->phpclass = "swMonCalMC80";
        $this->imagePath = "img/starwars/mc80.png";
        $this->shipClass = "Mon Calamari MC80 Battlecruiser";
        $this->shipSizeClass = 3;
	    
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Rebel Alliance, New Republic";
	    
	$this->fighters = array("Fighter Squadrons"=>3);
	    
	$this->unofficial = true;
	    //$this->isd = 2246;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.25;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 5;
	$this->iniativebonus = 0 *5; 
		
	    
	    
	$this->addPrimarySystem(new CnC(5, 36, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 45, 0, 0));
        $this->addPrimarySystem(new SWScanner(4, 30, 8, 8));
        $this->addPrimarySystem(new Engine(5, 32, 0, 14, 4));
	$this->addPrimarySystem(new SWRayShield(3,10,5,2,180,0)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addPrimarySystem(new SWRayShield(3,10,5,2,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$hyperdrive = new JumpEngine(5, 24, 8, 20);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new Hangar(3, 36, 12));
         
        
		
        $this->addFrontSystem(new Thruster(3, 20, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 20, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(3,18,8,4,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumIon(3, 240, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumIon(3, 240, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 270, 30, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 330, 90, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

        $this->addAftSystem(new Thruster(3, 20, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 20, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 20, 0, 4, 2));
 	$this->addAftSystem(new SWRayShield(3,18,8,4,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumIon(3, 60, 300, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumIon(3, 60, 300, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyTLaser(3, 150, 270, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyTLaser(3, 120, 240, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyTLaser(3, 90, 210, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
        
	$this->addLeftSystem(new Thruster(3, 16, 0, 3, 3));
	$this->addLeftSystem(new Thruster(3, 16, 0, 3, 3));
	$this->addLeftSystem(new SWRayShield(3,24,12,5,210,330)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new SWHeavyTLaser(3, 210, 330, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWHeavyTLaser(3, 210, 330, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWHeavyTLaser(3, 210, 330, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumIon(3, 210, 330, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumIon(3, 210, 330, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!		
	$this->addLeftSystem(new SWTractorBeam(2,210,330,1));
	$this->addLeftSystem(new SWTractorBeam(2,210,330,1));
	    
	$this->addRightSystem(new Thruster(3, 16, 0, 3, 4));
	$this->addRightSystem(new Thruster(3, 16, 0, 3, 4));
	$this->addRightSystem(new SWRayShield(3,24,12,5,30,150)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new SWHeavyTLaser(3, 30, 150, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWHeavyTLaser(3, 30, 150, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWHeavyTLaser(3, 30, 150, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumIon(3, 30, 150, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumIon(3, 30, 150, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWTractorBeam(2,30,150,1));
	$this->addRightSystem(new SWTractorBeam(2,30,150,1));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 68));
        $this->addAftSystem(new Structure( 4, 68));
        $this->addLeftSystem(new Structure( 4, 90));
        $this->addRightSystem(new Structure( 4, 90));
        $this->addPrimarySystem(new Structure( 5, 70));
	    
	    
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
                    2 => "Thruster",
		    3 => "Ray Shield",
                    8 => "Heavy Turbolaser",
                    12 => "Medium Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    3 => "Thruster",
		    4 => "Ray Shield",
                    9 => "Heavy Turbolaser",
                    13 => "Medium Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    2 => "Thruster",
		    3 => "Ray Shield",
                    8 => "Heavy Turbolaser",
                    12 => "Medium Ion Cannon",
		    13 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    2 => "Thruster",
		    3 => "Ray Shield",
                    8 => "Heavy Turbolaser",
                    12 => "Medium Ion Cannon",
		    13 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
    );
    }
}
?>
