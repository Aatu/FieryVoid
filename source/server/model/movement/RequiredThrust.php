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

    public function validatePaid()
    {
        foreach ($this->requirements as $direction => $required) {
            foreach ($this->fullfilments[$direction] as $amountAndThruster) {
                $amount = $amountAndThruster['amount'];

                $required -= $amount;
            }

            if ($required !== 0) {
                throw new MovementValidationException("Thrust is not paid. Unpaid thrust: $required");
            }
        }

        return true;
    }

    public function setThrusters($thrusters)
    {
        foreach ($this->fullfilments as $direction => &$fulfilment) {
            foreach ($fulfilment as &$entry) {
                $entry["thruster"] = $this->getThrusterById($entry["thrusterId"], $direction, $thrusters);
            }
        }
    }

    public function validateRequirementsAreCorrect($ship, $move)
    {
        $validReqs = $this->requireMove($ship, $move);

        foreach ($validReqs as $direction => $req) {

            if (!isset($this->requirements[$direction])) {
                throw new MovementValidationException("Movement validation failed: Expected move to require direction '$direction', but got 0");
            }

            if ((int) $this->requirements[$direction] !== (int) $req) {
                throw new MovementValidationException("Movement validation failed: Expected move to require $req, but instead got " . $this->requirements[$direction]);
            }
        }

        return true;
    }

    public function getThrustChanneledBy($thruster)
    {
        foreach ($this->fullfilments as $fulfilment) {
            foreach ($fulfilment as $entry) {
                if ($entry["thruster"] == $thruster) {
                    return $entry["amount"];
                }
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
                throw new MovementValidationException("Movement validation failed: Unrecognized movement type '$move->type'");
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

    private function getThrusterById($id, $direction, $thrusters)
    {
        $selectedThruster = null;

        foreach ($thrusters as $thruster) {
            if ($thruster->id == $id) {
                $selectedThruster = $thruster;
                break;
            }
        }

        if (!$selectedThruster) {
            throw new MovementValidationException("Movement validation failed: Thruster not found");
        }

        if ($selectedThruster->isDestroyed()) {
            throw new MovementValidationException("Movement validation failed: Thruster is destroyed");
        }

        if (!$selectedThruster->isDirection($direction)) {
            throw new MovementValidationException("Movement validation failed: Thruster is not for correct direction");
        }

        return $selectedThruster;

    }

}
