<?php
class KobolAdamant extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
         $this->pointCost = 650;
        $this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolAdamant";
        $this->imagePath = "img/ships/BSG/ColonialAdamant.png";
        $this->shipClass = "Adamant Frigate (Alpha prototype)";
        $this->fighters = array("normal" => 12, "superheavy" => 2);
 //       $this->isd = 2160;
        $this->canvasSize = 145;

		$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 15));
        $this->addPrimarySystem(new Thruster(3, 9, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 9, 0, 3, 4));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addPrimarySystem($hyperdrive);
        
        $this->addFrontSystem(new Thruster(3, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 9, 0, 3, 1));
		$this->addFrontSystem(new LightRailGun(3, 6, 3, 210, 360));
		$this->addFrontSystem(new LightRailGun(3, 6, 3, 210, 360)); 
        $this->addFrontSystem(new LightRailGun(3, 6, 3, 0, 150)); 
		$this->addFrontSystem(new LightRailGun(3, 6, 3, 0, 150)); 
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 270, 30));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 330, 90));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));

        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Bulkhead(0, 4));
        $this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new LightRailGun(3, 6, 3, 180, 330)); 
		$this->addAftSystem(new LightRailGun(3, 6, 3, 180, 330)); 
		$this->addAftSystem(new LightRailGun(3, 6, 3, 30, 180)); 
		$this->addAftSystem(new LightRailGun(3, 6, 3, 30, 180)); 
		$this->addAftSystem(new FlakCannon(3, 4, 2, 150, 270)); 
		$this->addAftSystem(new FlakCannon(3, 4, 2, 90, 210)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 40));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Thruster",
					10 => "Scanner",
					12 => "Engine",
					14 => "Hangar",
					16 => "Reactor",
					18 => "FTL Drive",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					10 => "Light Railgun",
					12 => "Flak Cannon",
                    19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Light Railgun",
					12 => "Flak Cannon",
                    19 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>
