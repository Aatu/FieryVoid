<?php

class MovementValidator
{

    private $ship;

    public function __construct($ship)
    {
        $this->ship = $ship;
    }

    public function validate()
    {
        $movement = $ship->movement;
    }

    private function getThrusters()
    {
        $thrusters = [];
        foreach ($ship->systems as $system) {
            if ($system instanceof Thruster) {
                $thrusters[] = $system;
            }
        }
    }
}
