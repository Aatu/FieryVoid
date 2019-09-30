<?php
class DoubleV_Nav extends FighterFlight{
				
		function __construct($id, $userid, $name,  $slot){
			parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 348;
		$this->faction = "Raiders";
		$this->phpclass = "DoubleV_Nav";
		$this->shipClass = "Double-V Medium Flight (with navigator)";
		$this->variantOf = "Double-V Medium Flight";
		$this->variantOf = 'DISABLED'; //disabled because basic fighter will get Navigator as option, no need for separate hull now!
		$this->imagePath = "img/ships/doubleV.png";

		$this->forwardDefense = 5;
		$this->sideDefense = 7;
		$this->freethrust = 10;
		$this->offensivebonus = 4;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
		$this->hasNavigator = true;
		
        $this->notes = 'Needs 2 rail slots.';

		$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(3, 0, 2, 2);
		$fighter = new Fighter("doubleV", $armour, 10, $this->id);
		$fighter->displayName = "Double-V";
		$fighter->imagePath = "img/ships/doubleV.png";
		$fighter->iconPath = "img/ships/doubleV_large.png";
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
		$fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
			
		$this->addSystem($fighter);
	}
				}
}
?>
