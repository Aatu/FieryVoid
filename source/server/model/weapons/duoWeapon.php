<?php

class DuoWeapon extends Weapon{
    
    
    public $duoWeapon = true;
    public $weapons = array();
    //private $turnsFired = array();

    public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        
        $this->weapons = $weapons;
    }
    
    public function getWeaponForIntercept(){
        return null;
    }
    
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
        foreach ($this->weapons as $weapon){
            $weapon->onConstructed($ship, $turn, $phase);
        }    
    }
    
    public function beforeTurn($ship, $turn, $phase){
        foreach ($this->weapons as $weapon){
            $weapon->beforeTurn($ship, $turn, $phase);
        }    
    }
    
    public function setId($id){
        $this->id = $id;
        
        foreach ($this->weapons as $i=>$weapon){
            $weapon->setId(1000 + ($id*10) + $i);
            $weapon->parentId = $id;
        } 
    }
    
    public function getFireOrders(){
        $fires = array();
        foreach ($this->weapons as $weapon){
            $fires = array_merge($fires, $weapon->getFireOrders());
        } 
        
        return $fires;
    }
    
    public function setFireOrders($fireOrders){
        foreach ($fireOrders as $fire)
        {
            $this->setFireOrder($fire);
        }
    }
    
    public function setFireOrder($fire)
    {
        $this->turnsFired[$fire->turn] = true;
        
        foreach($this->weapons as $weapon){
            if($weapon->id == $fire->weaponid){
                $weapon->setFireOrder($fire);
            }
        }
    }
    
    public function onAdvancingGamedata($ship)
    {
        $data = parent::calculateLoading();
        
        $curLoading = $data->loading;
        
        foreach ($this->weapons as $i=>$weapon)
        {
            // first check if parent weapon is offline. If so, put all the child
            // weapons offline as well.
            if(!$this->isOfflineOnTurn(TacGamedata::$currentTurn)){
                $data = $weapon->calculateLoading();
            }else{
                $data = new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                continue;
            }
            
            if($data->loading < $curLoading){
                $curLoading = $data->loading;
            }else{
//                if($curLoading == 0){
//                    $data->loading = $curLoading;
//                    $data->extrashots = 0;
//                }
            }
            
            if ($data)
                SystemData::addDataForSystem($this->weapons[$i]->id, 0, $ship->id, $data->toJSON());
        }
        
        if($data){
            $this->turnsloaded = $curLoading;
            $data->loading = $curLoading;
            SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
        }
    }
    
    public function setInitialSystemData($ship)
    {
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->getStartLoading();
            if ($data)
                SystemData::addDataForSystem($weapon->id, $i, $ship->id, $data->toJSON());
        }

        $data = $this->getStartLoading();
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }
}

?>
