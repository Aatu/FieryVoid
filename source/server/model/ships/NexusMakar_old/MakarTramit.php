<?php
class MakarTramit extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarTramit";
        $this->imagePath = "img/ships/Nexus/makar_loress2.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Tramit Strike Boat";
			$this->variantOf = "Loress Gunboat";
			$this->occurence = "rare";
		$this->unofficial = true;
			$this->isd = 2022;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
	    $this->notes .= '<br>Atmospheric capable';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        
		$this->agile = true;
        $this->turncost = 0.25;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
 
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(4, 12, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 10, 2, 4));
    	$sensors = new Scanner(4, 14, 3, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(4, 13, 0, 6, 2));

		$this->addFrontSystem(new PlasmaTorch(2, 4, 2, 300, 60));
		$this->addFrontSystem(new NexusDefenseGun(2, 4, 1, 270, 90));
		$this->addFrontSystem(new PlasmaTorch(2, 4, 2, 300, 60));
	    
        $this->addPrimarySystem(new Structure(3, 34));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
						13 => "1:Defense Gun",
        				16 => "1:Plasma Torch",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
						13 => "1:Defense Gun",
        				16 => "1:Plasma Torch",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
						13 => "1:Defense Gun",
        				16 => "1:Plasma Torch",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }

        public function getInitiativebonus($gamedata){
	    $iniBonus = parent::getInitiativebonus($gamedata);
            //may be boosted by LCV Controller...
	    $iniBonus += NexusLCVController::getIniBonus($this);
            return $iniBonus;
        }

}
?>
