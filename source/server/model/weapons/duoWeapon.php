<?php

class DuoWeapon extends Weapon{
    
    
    public $duoWeapon = true;
    public $weapons = array();
    private $turnsFired = array();

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
        parent::setId($id);
        
        $count = 0;
        
        foreach ($this->weapons as $weapon){
            $weapon->setId(1000 + ($id*10) + $count);
            $weapon->parentId = $id;
            $count++;
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
        $this->weapons[$fire->firingMode]->setFireOrder($fire);
    }
    
    public function onAdvancingGamedata($ship)
    {
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->calculateLoading();
            if ($data)
                SystemData::addDataForSystem($this->id, $i, $ship->id, $data->toJSON());
        }
    }
    
    public function setSystemData($data, $subsystem)
    {
        //Debug::log("setSystemData: duoWeapon");
        foreach ($this->weapons as $i=>$weapon){
            $weapon->setSystemData($data, $subsystem);
        }
        //$this->weapons[$subsystem]->setSystemData($data, $subsystem);
        
    }
    
    public function setInitialSystemData($ship)
    {
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->getStartLoading();
            if ($data)
                SystemData::addDataForSystem($this->id, $i, $ship->id, $data->toJSON());
        }
    }
    
    public function setLoading( $loading )
    {
        //Debug::log("Enter duo setLoading");
        if (!$loading){
            //Debug::log("Exit duo setLoading: nothing");
            return;
        }
        
        foreach ($this->weapons as $i=>$weapon){
            
            $weapon->setLoading($loading);
        }
        
    }
    
/*    public function calculateLoading(){
        if($this->duoWeapon){
            foreach($this->weapons as $weapon){
                $weapon->calculateLoading();
            }
            
            return;
        }        
    }*/
            
}

?>
