<?php
class Brikorta extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Brakiri";
        $this->phpclass = "Brikorta";
        $this->imagePath = "img/ships/ikorta.png";
        $this->shipClass = "Brikorta Light Carrier";
        $this->fighters = array("light"=>12);

        $this->occurence = "uncommon";
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(7, 18, 0, 0));
        $this->addPrimarySystem(new CnC(8, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 12, 6, 8));
        $this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(6, 14));
	$this->addPrimarySystem(new ShieldGenerator(6, 10, 2, 1));
        $this->addPrimarySystem(new GraviticThruster(6, 15, 0, 6, 1));
        $this->addPrimarySystem(new GraviticThruster(6, 18, 0, 10, 2));
        $this->addPrimarySystem(new GravitonPulsar(4, 5, 2, 90, 270));

        $this->addLeftSystem(new GravitonPulsar(4, 5, 2, 240, 60));
        $this->addLeftSystem(new MediumLaser(5, 6, 5, 240, 0));
        $this->addLeftSystem(new GraviticThruster(6, 15, 0, 6, 3));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 3, 180, 360));

        $this->addRightSystem(new GravitonPulsar(4, 5, 2, 300, 120));
        $this->addRightSystem(new MediumLaser(5, 6, 5, 0, 120));
        $this->addRightSystem(new GraviticThruster(6, 15, 0, 6, 4));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 3, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(7, 40));
        $this->addLeftSystem(new Structure(6, 48));
        $this->addRightSystem(new Structure(6, 48));


    }
}