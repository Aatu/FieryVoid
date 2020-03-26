<?php
class Makar extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 225;
		$this->faction = "Markab";
        $this->phpclass = "Makar";
        $this->imagePath = "img/ships/MarkabOSAT.png";
        $this->shipClass = "Makar Defense Satellite";
        
        $this->forwardDefense = 11;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->isd = 2201;

        $this->addPrimarySystem(new Reactor(5, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 6));   
        $this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2));
        
        $this->addPrimarySystem(new PlasmaWaveTorpedo(3, 0, 0, 270, 90));
        $this->addPrimarySystem(new PlasmaWaveTorpedo(3, 0, 0, 270, 90));
        $this->addPrimarySystem(new HeavyPlasma(4, 8, 5, 300, 60));
        $this->addPrimarySystem(new ScatterGun(3, 0, 0, 180, 360));
        $this->addPrimarySystem(new ScatterGun(3, 0, 0, 0, 180));      
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 30));

		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                       	11 => "Thruster",
                        12 => "Heavy Plasma Cannon",
                        14 => "Plasma Wave",
                        16 => "Scattergun",
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