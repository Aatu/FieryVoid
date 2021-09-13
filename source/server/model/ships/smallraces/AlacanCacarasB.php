<?php
class AlacanCacarasB extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
		$this->faction = 'Small Races';
        $this->phpclass = "AlacanCacarasB";
        $this->imagePath = "img/ships/AlacanCacaras.png";
			$this->canvasSize = 100; //img has 100px per side
        $this->shipClass = "Alacan Cacaras B Defense Satellite";
//		$this->unofficial = true;
		$this->isd = 2202;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        
        /*$this->turncost = 999;
        $this->turndelaycost = 999;
        $this->accelcost = 999;
        $this->rollcost = 999;
        $this->pivotcost = 999;	*/
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addPrimarySystem(new LaserCutter(2, 6, 4, 270, 90));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 360));
        $this->addPrimarySystem(new LaserCutter(2, 6, 4, 270, 90));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        $this->addPrimarySystem(new Reactor(3, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 3, 2, 4));   
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				13 => "Laser Cutter",
				15 => "Light Particle Beam",
				17 => "Scanner",
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
