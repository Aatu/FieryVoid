<?php
class Graemlar extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 180;
        $this->faction = "Llort";
        $this->phpclass = "Graemlar";
        $this->imagePath = "img/ships/LlortGraemlar.png";
        $this->shipClass = 'Graemlar Defense Satellite';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 5)); 
        $this->addPrimarySystem(new Thruster(4, 7, 0, 0, 2)); 
        $this->addPrimarySystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addPrimarySystem(new ScatterGun(2, 8, 3, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(5, 35));
    }
}

?>
