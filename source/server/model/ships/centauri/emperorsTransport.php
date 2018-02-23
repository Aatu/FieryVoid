<?php
class EmperorsTransport extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "Centauri";
        $this->phpclass = "EmperorsTransport";
        $this->imagePath = "img/ships/primus.png";
        $this->shipClass = "Emperor's Transport";
	    $this->variantOf = "Primus Battlecruiser";
        $this->occurence = "special";
        $this->fighters = array("normal"=>12);
	    
	    
	    $this->notes = "Emperor's personal transport, scenario unit only";
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(8, 22, 0, 8));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new CnC(9, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 20, 5, 10));
        $this->addPrimarySystem(new Engine(7, 18, 0, 10, 2));
	$this->addPrimarySystem(new Hangar(7, 14));
        
	$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
	$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));

        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
	$this->addAftSystem(new JumpEngine(6, 25, 3, 16));
        
	$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
	$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));
	$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));

	$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
	$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
	$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 7, 40));
    }
}
?>
