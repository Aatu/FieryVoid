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
    
    public function getHitChanceMod()
    {
        return 0;
    }
    
    public function getRange()
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
    
    public function getHitChanceMod()
    {
        return 3;
    }
    
    public function getRange()
    {
        return 20;
    }
}