<?php
class FtrUrchin extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 65*6;
        $this->faction = "Ipsha";
        $this->phpclass = "FtrUrchin";
        $this->shipClass = "Urchin Heavy flight";
        //$this->imagePath = "img/ships/IpshaUrchin.png";
        $this->imagePath = "img/ships/IpshaBorgUrchin.png";
        $this->isd = 2215;
        
        $this->notes = 'EM hardened';	  
        $this->notes .= '<br>-3 critical roll bonus'; //Urchin should have -2, and additional -1 replaces bonus vs EM weapons for all Ipsha
        $this->EMHardened = true; //EM Hardening - some weapons would check for this value!
        $this->critRollMod = -3; //generalbonus to critical/dropout rolls!
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 16 *5;
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("FtrUrchin", $armour, 13, $this->id);
            $fighter->displayName = "Urchin";
            /*
            $fighter->imagePath = "img/ships/IpshaUrchin.png";
            $fighter->iconPath = "img/ships/IpshaUrchin_large.png";
            */
            $fighter->imagePath = "img/ships/IpshaBorgUrchin.png";
            $fighter->iconPath = "img/ships/IpshaBorgUrchin_large.png";
            $frontGun = new LtSurgeBlaster(330, 30, 2); //2 guns - dmg bonus not selectable
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
       }
    }
}
?>
