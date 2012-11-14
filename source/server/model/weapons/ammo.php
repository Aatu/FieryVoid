<?php

class Ammo 
{
    public function __construct()
    {
        
    }
    
    public function getDamage()
    {
        return 0;
    }
    
    public function getHitChangeMod()
    {
        return 0;
    }
    
    public function getRangeMod()
    {
        return 0;
    }
}

class BasicMissile extends Ammo
{
    public function getDamage()
    {
        return 20;
    }
    
    public function getHitChangeMod()
    {
        return 3;
    }
    
    public function getRangeMod()
    {
        return 15;
    }
}