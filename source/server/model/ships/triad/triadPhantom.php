<?php
class triadPhantom extends FighterFlight {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);

        $this->pointCost = 390; 
        $this->faction = "The Triad"; 
        $this->phpclass = "triadPhantom";
        $this->shipClass = "Neutrality: Phantom Medium Fighter";
        $this->imagePath = "img/ships/triadPhantom.png";
        $this->isd = 'Primordial';
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadFighter');

        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 15;
        $this->offensivebonus = 6; // max 6 fighters; updated dynamically
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        $this->iniativebonus = 100;

		$this->notes = "Max flight size of 6. Reduce offensive bonus by 5 for every fighter less than 6 in the flight.";

        $this->maxFlightSize = 6;//limit flight size to 6 by design

		$this->hangarRequired = "Triad Fighter";

        $this->populate();
    }

    public function populate() {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(3, 4, 4, 4); // no armor sections
            $fighter = new Fighter("Phantom", $armour, 9, $this->id);
            $fighter->displayName = "Phantom";
            $fighter->imagePath = "img/ships/triadPhantom.png";
            $fighter->iconPath = "img/ships/triadPhantom_large.png";

            $gun = new MatterBolt(330, 30, 0);
            $gun->displayName = "Matter Bolt";
            $fighter->addFrontSystem($gun);
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

    public function initializationUpdate($gamedata) {
        parent::initializationUpdate($gamedata);

        // Update offensive bonus dynamically based on alive fighters
        $alive = 0;
        foreach ($this->systems as $fighter) {
            if ($fighter !== null && !$fighter->isDestroyed()) {
                $alive++;
            }
        }
        $this->offensivebonus = $alive;
    }
}
