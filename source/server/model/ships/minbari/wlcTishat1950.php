<?php
class wlcTishat1950 extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 390;
		$this->faction = "Minbari";
		$this->phpclass = "wlcTishat1950";
		$this->shipClass = "wlc Early Tishat flight (1950)";
		$this->imagePath = "img/ships/tishat.png";

        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 14;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        $this->iniativebonus = 95;
        $this->gravitic = true;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $tishat = new Fighter("tishat", $armour, 9, $this->id);
            $tishat->displayName = "Early Tishat Medium Fighter";
            $tishat->imagePath = "img/ships/tishat.png";
            $tishat->iconPath = "img/ships/tishat-large.png";
            $tishat->addFrontSystem(new LightFusionCannon(330, 30, 4, 2));
            $this->addSystem($tishat);
        }
    }
}
?>
