<?php
class LinkedWeapon extends Weapon{

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    
    public function fire($gamedata, $fireOrder)
    {
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        $this->firingMode = $fireOrder->firingMode;

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
    
    public function damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null)
    {
        if ($target->isDestroyed())
            return;
       
		$system = $target->getHitSystem($pos, $shooter, $fireOrder, $this, $location);
        
        for ($i=0;$i<$fireOrder->shots;$i++)
        {   
            if ($system == null || $system->isDestroyed())
            {
                $system = $target->getHitSystem($pos, $shooter, $fireOrder, $this, $location);
            }
            
            if ($system == null)
                return;
            
            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
        }
    }
}
