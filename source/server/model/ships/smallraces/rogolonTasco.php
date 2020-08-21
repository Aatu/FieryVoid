<?php
class RogolonTasco extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 125;
		$this->faction = 'Small Races';
        $this->phpclass = "RogolonTasco";
        $this->imagePath = "img/ships/rogolonTasco.png";
			$this->canvasSize = 40; //img has 40px per side
        $this->shipClass = "Rogolon Tasco Defense Satellite";
		$this->isd = 1950;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new SoMissileRack(2, 6, 0, 270, 90));
        $this->addPrimarySystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addPrimarySystem(new SoMissileRack(2, 6, 0, 270, 90));
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 4));  
        $this->addPrimarySystem(new Thruster(4, 5, 0, 0, 2));
		
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 25));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				13 => "Heavy Plasma",
				16 => "SO-Missile Rack",
				18 => "Scanner",
				20 => "Reactor",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}

?>
