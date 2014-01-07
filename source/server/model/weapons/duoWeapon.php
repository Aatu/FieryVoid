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
    
 /*   public function getFiringWeapon($fireOrder){
        $id = $fireOrder->$weaponid;
        
        foreach( $this->weapons as $weapon){
            if($weapon->id == $id){
                return $weapon;
            }
        }
    }*/
    
/*    public function fire($gamedata, $fireOrder){

        $firingMode = $fireOrder->firingMode;
        $this->weapons[$firingMode]->fire($gamedata, $fireOrder);
    }*/
    
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
        debug::log("*** onAdvancing ***");
        $curLoading = 10;
        
        foreach ($this->weapons as $i=>$weapon)
        {
            debug::log("*** subweapon id: $weapon->id ***");
            $data = $weapon->calculateLoading();
            debug::log("*** subweapon turnsloaded: $weapon->turnsloaded ***");
            debug::log("*** subweapon data: ".$data->toJSON());
            if($data->loading < $curLoading){
                $curLoading = $data->loading;
            }
            debug::log("*** curLoading: $curLoading ***");
            
            if ($data)
                SystemData::addDataForSystem($this->weapons[$i]->id, 0, $ship->id, $data->toJSON());
        }
        
        $data = parent::calculateLoading();
        
        if($data){
            $this->turnsloaded = $curLoading;
            $data->loading = $curLoading;
            debug::log("*** main weapon id: $this->id");
            debug::log("*** main weapon turnsloaded: $this->turnsloaded");
            debug::log("*** data: ".$data->toJSON());
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
    
/*    public function setLoading( $loading )
    {
        //Debug::log("Enter duo setLoading");
        if (!$loading){
            //Debug::log("Exit duo setLoading: nothing");
            return;
        }
        
        foreach ($this->weapons as $i=>$weapon){
            
            $weapon->setLoading($loading);
        }
        
    }*/
    
/*    public function calculateLoading(){
            foreach($this->weapons as $weapon){
                $weapon->calculateLoading();
            }
    }*/
            
}

?>
