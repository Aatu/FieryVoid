<?php
class Tokrana extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 200;
		$this->faction = "Brakiri";
        $this->phpclass = "Tokrana";
        $this->imagePath = "img/ships/tokrana.png";
        $this->shipClass = "Tokrana Satellite";
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 7, 3, 6)); 
        $this->addPrimarySystem(new GraviticShield(0, 6, 0, 2, 0, 360));
        $this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2)); 

        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 270, 90)); 
        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 270, 90)); 
        $this->addPrimarySystem(new GraviticBolt(3, 5, 2, 180, 360));  
        $this->addPrimarySystem(new GraviticBolt(3, 5, 2, 0, 180));  

        $this->addPrimarySystem(new ShieldGenerator(4, 8, 2, 1));
        

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(5, 30));
    }
}