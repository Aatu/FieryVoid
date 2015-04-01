<?php
class Phalanx extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 250;
		$this->faction = "Centauri";
        $this->phpclass = "Phalanx";
        $this->imagePath = "img/ships/phalanx.png";
        $this->shipClass = 'Phalanx Satellite';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new Reactor(5, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 7, 2, 8)); 
        $this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2)); 

        $this->addPrimarySystem(new BattleLaser(4, 6, 6, 270, 90)); 
        $this->addPrimarySystem(new BattleLaser(4, 6, 6, 270, 90)); 
        $this->addPrimarySystem(new TwinArray(3, 6, 2, 180, 360));
        $this->addPrimarySystem(new TwinArray(3, 6, 2, 180, 360));  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(5, 26));
    }
}