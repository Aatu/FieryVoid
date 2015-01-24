<?php
class Hastan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
   //     $this->pointCost = 800;
        $this->pointCost = 1;
        $this->faction = "Yolu";
        $this->phpclass = "Hastan";
        $this->imagePath = "img/ships/hastan.png";
        $this->shipClass = "Hastan";
        $this->fighters = array("medium"=>6);
        $this->gravitic = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
   /*     $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(5, 20, 0, 9, 1));

        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 240, 360));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 180));

        $this->addAftSystem(new Thruster(5, 35, 0, 12, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 0, 180));

     */   
        
         
       $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 12));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        $this->addPrimarySystem(new MolecularFlayer(3, 8, 1, 0, 360));
        $this->addPrimarySystem(new MolecularFlayer(3, 8, 1, 0, 360));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 240, 360));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 180));

        $this->addAftSystem(new FusionAgitator(3, 8, 1, 0, 360));
        $this->addAftSystem(new FusionAgitator(3, 8, 1, 0, 360));
        
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new JumpEngine(3, 10, 3, 20));
        $this->addAftSystem(new DestabilizerBeam(3, 8, 1, 0, 360));
        $this->addAftSystem(new DestabilizerBeam(3, 8, 1, 0, 360));

     

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
       $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 46 ));
        
        
    }

}



?>
