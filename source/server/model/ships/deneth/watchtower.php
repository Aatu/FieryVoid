<?php
class Watchtower extends SmallStarBaseFourSections{ 
/*Base: outer side section arcs should only be 120° arcs*/
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 925;
		$this->faction = "Deneth";
		$this->phpclass = "Watchtower";
		$this->shipClass = "Watchtower Base";
		$this->fighters = array("normal"=>30, "LCVs"=>4); 
		$this->shipSizeClass = 3; //this is Capital base
		$this->base = true;
		$this->smallbase = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 16;
		$this->sideDefense = 16;	
		
		$this->isd = 2231;
		
		
		$this->imagePath = "img/ships/DenethWatchtower.png";
		$this->canvasSize = 200; 
		
		$this->locations = array(1, 4, 2, 3);
		
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				13 => "Cargo Bay",
				15 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
		);
		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Hangar(5, 8, 6));
		$this->addPrimarySystem(new CargoBay(5, 48));
		$this->addPrimarySystem(new Structure( 5, 60));
		
		for ($i = 0; $i < sizeof($this->locations); $i++){
			$min = 270 + ($i*90); //original design has 120-degree arcs (300..60), but it was just very bad and not worth the points
			$max = 90 + ($i*90);
			$systems = array(
				new TwinArray(4, 6, 2, $min, $max),
				new TwinArray(4, 6, 2, $min, $max),
				new TwinArray(4, 6, 2, $min, $max),
				new AssaultLaser(6, 6, 4, $min, $max),
				new AssaultLaser(6, 6, 4, $min, $max),
				new AssaultLaser(6, 6, 4, $min, $max),
				new AssaultLaser(6, 6, 4, $min, $max),
				new Catapult(3, 8),
				new Hangar(4, 6, 6),
				new Structure(4, 60)
			);
			$loc = $this->locations[$i];
			$this->hitChart[$loc] = array(
				4 => "Twin Array",
				8 => "Assault Laser",
				9 => "Hangar",
				10 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			);
			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
	
	
        //Watchtower has atypical arcs, for a base!
	public function getLocations(){        
            $locs = array();
		/* changing to 180-degree-wide...
            $locs[] = array("loc" => 1, "min" => 300, "max" => 60, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 2, "min" => 120, "max" => 240, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->forwardDefense);
	    */
	    $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 2, "min" => 90, "max" => 270, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 3, "min" => 180, "max" => 360, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 4, "min" => 0, "max" => 180, "profile" => $this->forwardDefense);
		
	    return $locs;
        }
	
}

?>
