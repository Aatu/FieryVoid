<?php
class zzftrTIEInterceptorExport extends FighterFlight{
    /*StarWars weakened TIE Interceptor...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 31*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrtieinterceptorexport";
        $this->shipClass = "TIE Interceptors (export)";
        $this->variantOf = "TIE Interceptors";
        $this->imagePath = "img/starwars/tieInterceptor.png";
        
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: common.";
	    
        //$this->isd = 2214;
        $this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.25;
        
    	$this->iniativebonus = 20 *5; 
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 12; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(0, 0, 1, 1);
            $fighter = new Fighter("zzftrtieinterceptorexport", $armour, 7, $this->id);
            $fighter->displayName = "TIE Interceptor";
            $fighter->imagePath = "img/starwars/tieInterceptor.png";
            $fighter->iconPath = "img/starwars/tieInterceptor_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 1, 4); //front Lasers
            $fighter->addFrontSystem($frontGun);
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
           
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
