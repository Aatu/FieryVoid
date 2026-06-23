<?php
class Altarus extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 550;
        $this->faction = "House Valheru";
        $this->phpclass = "Altarus";
        $this->imagePath = "img/ships/Altarus4.png";
        $this->shipClass = "Altarus Light Carrier";
        $this->isd = 2150;
        $this->fighters = array("medium"=>12, "heavy"=>6); 
		$this->unofficial = true;

	    $this->notes = 'Common variant if part of a House Valheru only force.';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 12, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 15, 6));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 300, 60));
        $this->addFrontSystem(new HeavyParticleBeam(3, 6, 2, 270, 90));
		$this->addFrontSystem(new Hangar(4, 6, 6));
		
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new JumpEngine(3, 10, 3, 20));
        $this->addAftSystem(new BallisticTorpedo(4, 5, 6, 120, 240));
        $this->addAftSystem(new HeavyParticleBeam(3, 6, 2, 90, 270));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 6, 40 ));
        
            $this->hitChart = array(
                0=> array(
                    6 => "Structure",
                    9 => "Thruster",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    5 => "Hangar",
                    7 => "Heavy Particle Beam",
					9 => "Ballistic Torpedo",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    4 => "Thruster",
                    7 => "Heavy Particle Beam",
                    9 => "Jump Engine",
					11 => "Ballistic Torpedo",
                    18 => "Structure",
                    20 => "Primary",
			),
		);   
        
        
    }

}



?>
