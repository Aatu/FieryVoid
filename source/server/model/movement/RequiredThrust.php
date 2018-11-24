<?php

class RequiredThrust
{

    private $fullfilments = [];
    private $requirements = [];

    public function __construct($data = null)
    {
        if ($data === null) {
            $data = ["fullfilments" => [], "requirements" => []];
        }

        $this->fullfilments = $data['fullfilments'];
        $this->requirements = $data['requirements'];
    }

    public function setThrusters($thrusters)
    {
        foreach ($this->fullfilments as $fulfilment) {
            $fulfilment["thruster"] = $this->getThrusterById($fulfilment["thrusterId"], $thrusters);
        }
    }

    public function validateRequirementsAreCorrect($ship, $move)
    {
        $validReqs = $this->requireMove($ship, $move);

        foreach ($validReqs as $direction => $req) {
            if ($this->requirements[$direction] != $req) {
                throw new Exception("Movement validation failed: Expected move to require $req, but instead got " . $this->requirements[$direction]);
            }
        }

        return true;
    }

    public function getThrustChanneledBy($thruster)
    {
        foreach ($this->fullfilments as $fulfilment) {
            if ($fulfilment["thruster"] == $thruster) {
                return $fulfilment["amount"];
            }
        }
    }

    public function serialize()
    {
        return '';
    }

    private function requireMove($ship, $move)
    {
        switch ($move->type) {
            case "speed":
                return $this->requireSpeed($ship, $move);
            case "pivot":
                return $this->requirePivot($ship);
            case "roll":
                return $this->requireRoll($ship);
            case "evade":
                return $this->requireEvade($ship, $move);
            default:
                throw new Exception("Movement validation failed: Unrecognized movement type '$move->type'");
        }
    }

    private function requireRoll($ship)
    {
        return [6 => $ship->rollcost];
    }

    private function requireEvade($ship, $move)
    {
        return [6 => $ship->evasioncost * $move->value];
    }

    private function requirePivot($ship)
    {
        return [6 => $ship->pivotcost];
    }

    private function requireSpeed($ship, $move)
    {
        $require = [];

        $facing = $move->facing;
        $direction = $move->value;
        $actualDirection = Mathlib::addToHexFacing(
            Mathlib::addToHexFacing($direction, -$facing),
            3
        );

        $require[$actualDirection] = $ship->accelcost;

        return $require;
    }

    private function getThrusterById($id, $thrusters)
    {

        $selectedThruster = null;

        foreach ($thrusters as $thruster) {
            if ($thruster->id == $id) {
                return $selectedThruster;
            }
        }

        if (!$selectedThruster) {
            throw new Exception("Movement validation failed: Thruster not found");
        }

        if ($selectedThruster->isDestroyed()) {
            throw new Exception("Movement validation failed: Thruster is destroyed");
        }

        return $selectedThruster;

    }

}
