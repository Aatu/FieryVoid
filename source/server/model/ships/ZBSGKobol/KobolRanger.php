<?php
class KobolRanger extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
         $this->pointCost = 750;
        $this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolRanger";
        $this->imagePath = "img/ships/BSG/ColonialRanger.png";
        $this->shipClass = "Ranger Missile Cruiser (Alpha prototype)";
 //       $this->isd = 2160;
        $this->canvasSize = 145;

		$this->unofficial = true;
	    $this->notes = 'May only boost sensors by 2.';
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;

        $this->turncost = 0.75;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 18, 6, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 4));
        $this->addPrimarySystem(new ReloadRack(5, 9));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addAftSystem($hyperdrive);
        
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
		$this->addFrontSystem(new Railgun(4, 9, 6, 300, 60)); 
        $this->addFrontSystem(new Railgun(4, 9, 6, 300, 60)); 
		$this->addFrontSystem(new LMissileRack(4, 6, 0, 240, 60)); 
		$this->addFrontSystem(new LMissileRack(4, 6, 0, 300, 120)); 
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 210, 330));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 30, 150));
		$this->addFrontSystem(new LtBlastCannon(3, 4, 1, 210, 330));
		$this->addFrontSystem(new LtBlastCannon(3, 4, 1, 30, 150));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));

        $this->addAftSystem(new Thruster(4, 9, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 4, 2));
        $this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new LtBlastCannon(3, 4, 1, 210, 330)); 
		$this->addAftSystem(new LtBlastCannon(3, 4, 1, 30, 150)); 
		$this->addAftSystem(new FlakCannon(3, 4, 2, 210, 330)); 
		$this->addAftSystem(new FlakCannon(3, 4, 2, 30, 150)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addAftSystem(new Structure( 4, 35));
        $this->addPrimarySystem(new Structure( 4, 45));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Thruster",
					10 => "Reload Rack",
					11 => "Class-S Missile Rack",
					13 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					18 => "FTL Drive",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Railgun",
					8 => "Class-L Missile Rack",
					9 => "Flak Cannon",
					11 => "Light Blast Cannon",
                    19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Light Blast Cannon",
					11 => "Flak Cannon",
                    19 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>
