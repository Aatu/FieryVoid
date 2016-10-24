<?php
class zzftrywing extends FighterFlight{
    /*StarWars Y-Wing... once all systems are in place, at least!*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 60*6;
        $this->faction = "StarWars Galactic Empire";
        $this->phpclass = "zzftrywing";
        $this->shipClass = "Y-Wing Tech Demo flight";
        $this->imagePath = "img/starwars/yWing.png";
        
        
        $this->forwardDefense = 7;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
        $this->hasNavigator = true;
    	$this->iniativebonus = 17 *5; //includes Navigator bonus
        
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("zzftrywing", $armour, 15, $this->id);
            $fighter->displayName = "Y-Wing Technology Demonstrator";
            $fighter->imagePath = "img/starwars/yWing.png";
            $fighter->iconPath = "img/starwars/yWing_large.png";
            
            $frontGun = new SWFighterLaser(330, 30, 2, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
            
            $roundGun = new SWFighterIon(330, 30, 1, 2); //all-around Ion Cannons
            $fighter->addFrontSystem($roundGun);
            
            //Proton Torpedo Launchers
            
            
            //Ray Shield
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
