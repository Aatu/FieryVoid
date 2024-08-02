<?php
class Ruqacc extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175;
		$this->faction = "Cascor Commonwealth";
        $this->phpclass = "Ruqacc";
        $this->imagePath = "img/ships/CascorRuqaccOSAT.png";
		$this->canvasSize = 200;
        $this->shipClass = "Ruqacc Ion Satellite";
	    $this->isd = 2231;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addFrontSystem(new IonicLaser(2, 0, 0, 270, 90));
        $this->addFrontSystem(new IonicLaser(2, 0, 0, 270, 90));
        $this->addFrontSystem(new IonTorpedo(2, 5, 4, 270, 90));
        $this->addFrontSystem(new IonTorpedo(2, 5, 4, 270, 90));
        $this->addFrontSystem(new DualIonBolter(1, 0, 0, 0, 360));
        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 4, 2, 4));   
        $this->addAftSystem(new Thruster(2, 4, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 28));

		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "2:Thruster",
                        13 => "1:Ionic Laser",
                        15 => "1:Ion Torpedo",
                        17 => "Scanner",
                        19 => "Reactor",
                        20 => "1:Dual Ion Bolter",
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
