<?php
class Nalor extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
        $this->faction = "The Lion";
        $this->phpclass = "Celerian";
        $this->imagePath = "img/ships/celerian.png";
        $this->shipClass = "Nalor Armored cruiser";
        $this->shipSizeClass = 3;
    //    $this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->occurence = "uncommon";
        
         
        $this->addPrimarySystem(new Reactor(6, 16, 0, 0));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 22, 4, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(4, 2));		
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 360));
        $this->addFrontSystem(new MediumPlasma(3, 6, 3, 300, 360));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 60));
		
        $this->addAftSystem(new Thruster(5, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 10, 0, 3, 2));
        $this->addAftSystem(new JumpEngine(5, 25, 3, 20));
        
		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
		$this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));
        $this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));        
        		
		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new ParticleProjector(2, 6, 1, 0, 180));
        $this->addRightSystem(new ParticleProjector(2, 6, 1, 0, 180));
	
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 38));
        $this->addAftSystem(new Structure( 5, 36));
        $this->addLeftSystem(new Structure( 5, 40));
        $this->addRightSystem(new Structure( 5, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
		
		
    }

}



?>
