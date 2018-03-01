<?php
class FtrAnemone extends FighterFlight{
  /*non-canon, older Ipsha fighters from The Great Machine*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 38*6;
        $this->faction = "Ipsha";
        $this->phpclass = "FtrAnemone";
        $this->shipClass = "Anemone Medium flight";
        //$this->imagePath = "img/ships/IpshaUrchin.png";
        $this->imagePath = "img/ships/IpshaBorgUrchin.png";
        $this->isd = 2165;
        
        $this->unofficial = true;
        
        $this->notes = 'EM hardened';	  
        $this->notes .= '<br>-2 critical roll bonus'; //Anemone should have -1, and additional -1 replaces bonus vs EM weapons for all Ipsha
        $this->EMHardened = true; //EM Hardening - some weapons would check for this value!
        $this->critRollMod = -2; //generalbonus to critical/dropout rolls!
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 18 *5;
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 1, 1);
            $fighter = new Fighter("FtrAnemone", $armour, 11, $this->id);
            $fighter->displayName = "Anemone";
            /*
            $fighter->imagePath = "img/ships/IpshaUrchin.png";
            $fighter->iconPath = "img/ships/IpshaUrchin_large.png";
            */
            $fighter->imagePath = "img/ships/IpshaBorgUrchin.png";
            $fighter->iconPath = "img/ships/IpshaBorgUrchin_large.png";
            $frontGun = new LtSurgeBlaster(330, 30, 1); //1 gun - dmg bonus not selectable
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
       }
    }
}
?>
