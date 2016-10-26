<?php
class LinkedWeapon extends Weapon{
	public $isLinked = true; //indicates that this is linked weapon, no need for overrides

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

/* no longer needed, kept just in case
    public function fire($gamedata, $fireOrder)
    {
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        //$this->firingMode = $fireOrder->firingMode;
	    $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!


        $pos = $shooter->getCoPos();

        $this->calculateHit($gamedata, $fireOrder);
        $intercept = $this->getIntercept($gamedata, $fireOrder);

        $needed = $fireOrder->needed;
        $rolled = Dice::d(100);
        if ($rolled > $needed && $rolled <= $needed+($intercept*5)){
            //$fireOrder->pubnotes .= "Shot intercepted. ";
            $fireOrder->intercepted += $this->shots;
        }

        if ($rolled <= $needed){
            $fireOrder->shotshit = $this->shots;
            $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
        }

        $fireOrder->rolled = 1;//Marks that fire order has been handled
    }
    
    public function damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage)
    {
        if ($target->isDestroyed())
            return;
       
	$trgtLoc = $target->getHitSectionChoice($shooter, $fireOrder, $this);
	$system = $target->getHitSystem($shooter, $fireOrder, $this, $trgtLoc);
        
        for ($i=0;$i<$fireOrder->shots;$i++)
        {   
            if ($system == null || $system->isDestroyed())
            {
                if($target instanceof FighterFlight){
                    // You killed a fighter. Damage does not overkill into other
                    // fighters.
                    return;
                }
                
                // you killed the system with one of your shots. Overkill into structure
                // if there is no structure in this location, go to primary structure
                // If primary structure is destroyed, just return.
                $system = $target->getStructureSystem($system->location);
                
                if(($system == null && $location != 0) || ($system != null && $system->isDestroyed())){
                    $system = $target->getStructureSystem(0);
                }

                if($system == null || $system->isDestroyed()){
                    return;
                }
            }
            
            $damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $trgtLoc);
        }
    }
    */
}
