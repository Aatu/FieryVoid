<?php
class RogolonVostorSHF extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 85;
        $this->faction = "Small Races";
        $this->phpclass = "RogolonVostorSHF";
        $this->shipClass = "Rogolon Vostor Assault Fighter";
        $this->imagePath = "img/ships/RogolonVostor.png";
	
	$this->isd = 1959;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;

		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->hasNavigator = true;
        
        $armour = array(4, 4, 3, 3);
        $fighter = new Fighter("RogolonVostorSHF", $armour, 24, $this->id);
        $fighter->displayName = "Vostor SHF";
        $fighter->imagePath = "img/ships/RogolonVostor.png";
        $fighter->iconPath = "img/ships/RogolonVostor_Large.png";

        $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
        $fighter->addFrontSystem(new RogolonLtPlasmaGun(330, 30, 5, 2));
        $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));

        $fighter->addAftSystem(new RogolonLtPlasmaGun(150, 210, 5, 1));
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
        
        $this->addSystem($fighter);
    }
    public function populate(){
        return;
    }
}
?>

  


