<?php
class torataKalorBF extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "Civilians";
        $this->phpclass = "torataKalorBF";
        $this->imagePath = "img/ships/torataKalor.png";
        $this->shipClass = "Torata Kalor Bulk Freighter";
        $this->isd = 2224;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup    
        
        $this->forwardDefense = 15;
        $this->sideDefense = 14;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = -30;        
        

        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 3));
        $this->addPrimarySystem(new Engine(3, 14, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 3, 4));        

        $this->addLeftSystem(new CargoBay(2, 100));        
        $this->addRightSystem(new CargoBay(2, 100));        

		
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));        
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));        
        
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Hangar(2, 4, 1));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));        

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 40));
        $this->addAftSystem(new Structure(3, 30));
        $this->addPrimarySystem(new Structure(3, 50));
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
                    12 => "Thruster",
                    14 => "Scanner",
            		16 => "Engine",
            		17 => "Hangar",
                    18 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
            		7 => "Light Particle Beam",
            		12 => "0:Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
            		6 => "Light Particle Beam",
            		8 => "Hangar",
            		11 => "0:Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}
?>
