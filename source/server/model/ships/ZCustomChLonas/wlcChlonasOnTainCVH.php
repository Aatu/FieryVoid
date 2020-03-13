<?php
class wlcChlonasOnTainCVH extends BaseShipNoFwd{
    /*Ch'Lonas On'Tain Heavy Carrier*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 565;
        $this->phpclass = "wlcChlonasOnTainCVH";
        $this->imagePath = "img/ships/ChlonasOntain.png";
        $this->canvasSize = 200;
        $this->shipClass = "On'Tain Heavy Carrier";
        $this->fighters = array("heavy" => 24, "light"=>12);
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
	    
	    $this->faction = "Ch'Lonas";
        $this->variantOf = "On'Thari Attack Carrier";
	    $this->occurence = "common";
        $this->limited = 33;
	    $this->isd = 2250;
	    $this->unofficial = true;
        
        $this->iniativebonus = 1*5;
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 18, 5, 7));
        $this->addPrimarySystem(new Engine(4, 18, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(4, 14)); //hangar for a squadron of light fighters    
      
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 1)); //Retro
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3)); //Port
        $this->addLeftSystem(new Hangar(4, 12));//hangar for a squadron of heavy fighters
	    $this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 240, 60));
	    $this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 0));
	    $this->addLeftSystem(new CustomStrikeLaser(3, 0, 0, 240, 0));
	    $this->addLeftSystem(new CustomMatterStream(4, 0, 0, 300, 60));
      

        $this->addRightSystem(new Thruster(3, 10, 0, 3, 1)); //Retro
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4)); //Stbd
        $this->addRightSystem(new Hangar(4, 12));//hangar for a squadron of heavy fighters
	    $this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 300, 120));
	    $this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));
	    $this->addRightSystem(new CustomStrikeLaser(3, 0, 0, 0, 120));
	    $this->addRightSystem(new CustomMatterStream(4, 0, 0, 300, 60));
            
      
	      $this->addAftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 0));
	      $this->addAftSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));
	      $this->addAftSystem(new CustomStrikeLaser(3, 0, 0, 120, 240));
	      $this->addAftSystem(new CustomStrikeLaser(3, 0, 0, 120, 240));
        $this->addAftSystem(new Thruster(3, 14, 0, 5, 2)); //Main
        $this->addAftSystem(new Thruster(3, 14, 0, 5, 2)); //Main
      
      
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addLeftSystem(new Structure( 4, 64));
        $this->addRightSystem(new Structure( 4, 64));
        $this->addAftSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 5, 50));
      
      
        //d20 hit chart
        $this->hitChart = array(		
          0=> array( //PRIMARY
            8 => "Structure",
            11 => "Scanner",
            13 => "Hangar",
            16 => "Engine",
            19 => "Reactor",
            20 => "C&C",
          ),
          2=> array( //Aft
            7 => "Thruster",
            9 => "Strike Laser",
            11 => "Light Gatling Mattergun",
            18 => "Structure",
            20 => "Primary",
          ),
          3=> array( //Port
            5 => "Thruster", //no differentiation Retro/side
            7 => "Hangar",
            8 => "Matter Steram",
            10 => "Strike Laser",
            12 => "Light Gatling Mattergun",
            18 => "Structure",
            20 => "Primary",
          ),
          4=> array( //Stbd
            5 => "Thruster", //no differentiation Retro/side
            7 => "Hangar",
            8 => "Matter Steram",
            10 => "Strike Laser",
            12 => "Light Gatling Mattergun",
            18 => "Structure",
            20 => "Primary",
          ),
        );
      
    }
}
?>
