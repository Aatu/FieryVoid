<?php
class Antoph extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 470;
        $this->faction = "Brakiri";
        $this->phpclass = "Antoph";
        $this->imagePath = "img/ships/antoph.png";
        $this->shipClass = "Antoph Light Cruiser";
			$this->occurence = "common";
			$this->variantOf = 'Antoph Light Cruiser (upgrade)';
                
		$this->notes = 'Ak-Habil Conglomerate';//Corporation producing the design
		$this->isd = 2220;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6 *5;

        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 6, 7));
        $this->addPrimarySystem(new Engine(5, 14, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new GraviticThruster(3, 13, 0, 5, 4));
        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 240, 0));
        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 0, 120));
        
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GravitonBeam(5, 8, 8, 300, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
        
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 12, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(4, 12, 0, 6, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 5, 33 ));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Gravitic Cannon",					
					12 => "Thruster",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Gravitic Bolt",
					9 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					8 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>
