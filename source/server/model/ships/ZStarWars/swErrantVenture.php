<?php
class swErrantVenture extends BaseShip{
  /*captured and downgraded StarWars Imperial I class Star Destroyer, unique*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 925;
	$this->faction = "ZStarWars";
        $this->phpclass = "swErrantVenture";
        $this->imagePath = "img/starwars/ErrantVenture.png";
        $this->shipClass = "Errant Venture";
        $this->shipSizeClass = 3;
        $this->limited = 33; //Limited Deployment
	    
		$this->isd = "7 ABY";
		$this->notes = "Primary (and only) user: Booster Terrik";

	$this->variantOf = "Imperial Star Destroyer";
	$this->occurence = 'unique';
	    
	$this->fighters = array("Fighter Squadrons"=>5, "LCVs" => 4); //no LCVs to actually use it, but it's fluff-correct ;)
	
	
	$this->critRollMod = +2; //general penalty to critical rolls! - ship is in state of disrepair...
	    
	$this->unofficial = true;
	    
        
        $this->forwardDefense = 19;
        $this->sideDefense = 20;
        
        $this->turncost = 1.75;
        $this->turndelaycost = 1.75;
        $this->accelcost = 6;
        $this->rollcost = 5;
        $this->pivotcost = 8;
	$this->iniativebonus = -4 *5; //really large Cap, but not Enormous unit, not fully operational
		
	    
     
	    
	    
	$this->addPrimarySystem(new CnC(6, 36, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 50, 0, 0));
        $this->addPrimarySystem(new SWScanner(5, 12, 6, 3)); //split to Aft, too
        $this->addPrimarySystem(new Engine(5, 24, 0, 6, 8)); //split to Aft, too
	$hyperdrive = new JumpEngine(5, 30, 8, 20);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new Hangar(3, 48, 12));
         
        
		
        $this->addFrontSystem(new Thruster(3, 22, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 22, 0, 5, 1));
	$this->addFrontSystem(new SWRayShield(3,18,8,3,330,30)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumIon(3, 300, 60, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumIon(3, 300, 60, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyTLaser(3, 300, 60, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
        $this->addFrontSystem(new Catapult(3, 6));
        $this->addFrontSystem(new Catapult(3, 6));	    
	    
        $this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
	$this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
	$this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
        $this->addAftSystem(new SWScanner(3, 18, 8, 3)); //split to Primary, too
        $this->addAftSystem(new Engine(4, 20, 0, 4, 8)); //split to Primary, too
	$this->addAftSystem(new SWRayShield(2,18,8,3,150,210)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumIon(3, 120, 240, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumIon(3, 120, 240, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
      
	    
	$this->addLeftSystem(new Thruster(3, 22, 0, 5, 3));
	$this->addLeftSystem(new SWRayShield(3,18,8,3,210,330)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new SWHeavyTLaser(3, 270, 30, 1));
	$this->addLeftSystem(new SWHeavyTLaser(3, 240, 0, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWHeavyTLaser(3, 240, 0, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumIon(3, 240, 0, 1));
	$this->addLeftSystem(new SWMediumIon(3, 240, 0, 1));
	$this->addLeftSystem(new SWMediumIon(3, 240, 0, 1));
	$this->addLeftSystem(new SWTractorBeam(2,240,0,1));
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addLeftSystem($LCVRail);
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
	$this->addLeftSystem($LCVRail);
	
	    
	$this->addRightSystem(new Thruster(3, 22, 0, 5, 4));
	$this->addRightSystem(new SWRayShield(3,18,8,3,30,150)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new SWHeavyTLaser(3, 330, 90, 1));
	$this->addRightSystem(new SWHeavyTLaser(3, 0, 120, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWHeavyTLaser(3, 0, 120, 1)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumIon(3, 0, 120, 1));
	$this->addRightSystem(new SWMediumIon(3, 0, 120, 1));
	$this->addRightSystem(new SWMediumIon(3, 0, 120, 1));
	$this->addRightSystem(new SWTractorBeam(2,0,120,1));
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addRightSystem($LCVRail);
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
	$this->addRightSystem($LCVRail);
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 80));
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
		    5 => "Catapult",
                    8 => "Heavy Turbolaser",
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
                    6 => "Heavy Turbolaser",
                    9 => "Medium Ion Cannon",
                    11 => "LCV Rail",
                    12 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    2 => "Thruster",
		    3 => "Ray Shield",
                    6 => "Heavy Turbolaser",
                    9 => "Medium Ion Cannon",
                    11 => "LCV Rail",
                    12 => "Tractor Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
    );
    }
}
?>