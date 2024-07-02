<?php
class Makar extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 225;
		$this->faction = "Markab Theocracy";
        $this->phpclass = "Makar";
        $this->imagePath = "img/ships/MarkabOSAT.png";
        $this->shipClass = "Makar Defense Satellite";
 		$this->canvasSize = 80;
 		       
        $this->forwardDefense = 11;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->isd = 2201;

		$this->enhancementOptionsEnabled[] = 'MARK_FERV'; //To activate Religious Fervor attributes.   

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 6));   
        $this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
        
        $this->addFrontSystem(new PlasmaWaveTorpedo(3, 0, 0, 270, 90));
        $this->addFrontSystem(new PlasmaWaveTorpedo(3, 0, 0, 270, 90));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 60));
        $this->addAftSystem(new ScatterGun(3, 0, 0, 180, 360));
        $this->addAftSystem(new ScatterGun(3, 0, 0, 0, 180));      
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 30));

		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                       	11 => "2:Thruster",
                        12 => "1:Heavy Plasma Cannon",
                        14 => "1:Plasma Wave",
                        16 => "2:Scattergun",
                        18 => "Scanner",
                        20 => "Reactor",
                ),
        );
    }
}