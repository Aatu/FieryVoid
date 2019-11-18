<?php
class zzftrywing extends FighterFlight{
    /*StarWars Y-Wing... once all systems are in place, at least!*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 65*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrywing";
        $this->shipClass = "Y-Wing Assault Fighters";
        $this->imagePath = "img/starwars/yWing.png";
        
        //$this->isd = 2214;
        $this->unofficial = true;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
        $this->hasNavigator = true;
    	$this->iniativebonus = 17 *5; //includes Navigator bonus
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 9; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("zzftrywing", $armour, 15, $this->id);
            $fighter->displayName = "Y-Wing";
            $fighter->imagePath = "img/starwars/yWing.png";
            $fighter->iconPath = "img/starwars/yWing_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
            
            $roundGun = new SWFighterIon(0, 360, 1, 2); //all-around Ion Cannons
            //$roundGun->exclusive = true; //either this or other weapons! no - gunner operates that...
            $fighter->addFrontSystem($roundGun);
           
            //2 forward Proton Torpedo Launchers, 4 shots each
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(4, 330, 30, 2);//single dual launcher! like for direct fire
            $fighter->addFrontSystem($torpedoLauncher);
            /*
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(4, 330, 30);
            $fighter->addFrontSystem($torpedoLauncher);
            */
            

            
            //Ray Shield, 2 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 2, 0, 360));

            
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
