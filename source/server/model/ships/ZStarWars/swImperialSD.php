<?php
class swImperialSD extends BaseShip{
  /*StarWars Imperial I class Star Destroyer*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 2300;
	$this->faction = "ZStarWars";
        $this->phpclass = "swImperialSD";
        $this->imagePath = "img/starwars/imperator.png";
        $this->shipClass = "Imperial Star Destroyer";
        $this->shipSizeClass = 3;
        $this->limited = 33; //Limited Deployment
	    
		$this->isd = "22 BBY";
		$this->notes = "Primary users: Galactic Empire";
	    
	$this->fighters = array("Fighter Squadrons"=>6, "Assault Squadrons"=>6);
	    
	$this->unofficial = true;
	    
        
        $this->forwardDefense = 19;
        $this->sideDefense = 20;
        
        $this->turncost = 1.75;
        $this->turndelaycost = 1.75;
        $this->accelcost = 6;
        $this->rollcost = 5;
        $this->pivotcost = 8;
	$this->iniativebonus = -2 *5; //really large Cap, but not Enormous unit
		
	    
     
	    
	    
	$this->addPrimarySystem(new CnC(6, 36, 0, 0));
        $this->addPrimarySystem(new Reactor(6, 60, 0, 0));
        $this->addPrimarySystem(new SWScanner(5, 12, 6, 3)); //split to Aft, too
        $this->addPrimarySystem(new Engine(5, 24, 0, 6, 8)); //split to Aft, too
	$hyperdrive = new JumpEngine(5, 30, 8, 20);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new Hangar(3, 60, 12));
         
        
		
        $this->addFrontSystem(new Thruster(3, 22, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 22, 0, 5, 1));
	$this->addFrontSystem(new SWRayShield(3,20,12,4,330,30)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumIon(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumIon(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumIon(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	    
        $this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
	$this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
	$this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
        $this->addAftSystem(new SWScanner(3, 18, 8, 4)); //split to Primary, too
        $this->addAftSystem(new Engine(4, 20, 0, 6, 8)); //split to Primary, too
	$this->addAftSystem(new SWRayShield(2,18,8,3,150,210)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumIon(3, 120, 240, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumIon(3, 120, 240, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumIon(3, 120, 240, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
        
	    
	$this->addLeftSystem(new Thruster(3, 22, 0, 5, 3));
	$this->addLeftSystem(new SWRayShield(3,20,12,4,210,330)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new SWHeavyTLaser(3, 270, 30, 4));
	$this->addLeftSystem(new SWHeavyTLaser(3, 270, 30, 4));
	$this->addLeftSystem(new SWHeavyTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWHeavyTLaser(3, 240, 0, 4));
	$this->addLeftSystem(new SWHeavyTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumIon(3, 240, 0, 4));
	$this->addLeftSystem(new SWMediumIon(3, 240, 0, 4));
	$this->addLeftSystem(new SWMediumIon(3, 240, 0, 4));
	$this->addLeftSystem(new SWTractorBeam(2,240,0,1));
	$this->addLeftSystem(new SWTractorBeam(2,240,0,1));
	$this->addLeftSystem(new SWTractorBeam(2,240,0,1));
		
	    
	$this->addRightSystem(new Thruster(3, 22, 0, 5, 4));
	$this->addRightSystem(new SWRayShield(3,20,12,4,30,150)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new SWHeavyTLaser(3, 330, 90, 4));
	$this->addRightSystem(new SWHeavyTLaser(3, 330, 90, 4));
	$this->addRightSystem(new SWHeavyTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWHeavyTLaser(3, 0, 120, 4));
	$this->addRightSystem(new SWHeavyTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumIon(3, 0, 120, 4));
	$this->addRightSystem(new SWMediumIon(3, 0, 120, 4));
	$this->addRightSystem(new SWMediumIon(3, 0, 120, 4));
	$this->addRightSystem(new SWTractorBeam(2,0,120,1));
	$this->addRightSystem(new SWTractorBeam(2,0,120,1));
	$this->addRightSystem(new SWTractorBeam(2,0,120,1));
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 80));
        $this->addAftSystem(new Structure( 3, 80));
        $this->addLeftSystem(new Structure( 4, 80));
        $this->addRightSystem(new Structure( 4, 80));
        $this->addPrimarySystem(new Structure( 5, 80));
	    
	    
            $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    9 => "Hyperdrive",
                    10 => "Scanner",
                    12 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    2 => "Thruster",
		    3 => "Ray Shield",
                    7 => "Heavy Turbolaser",
                    10 => "Medium Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
		    5 => "Ray Shield",
		    8 => "Medium Ion Cannon",
                    11 => "Scanner",
                    12 => "Engine",
                    17 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    2 => "Thruster",
		    3 => "Ray Shield",
                    7 => "Heavy Turbolaser",
                    10 => "Medium Ion Cannon",
                    12 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    2 => "Thruster",
		    3 => "Ray Shield",
                    7 => "Heavy Turbolaser",
                    10 => "Medium Ion Cannon",
                    12 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
    );
    }
}
?>
