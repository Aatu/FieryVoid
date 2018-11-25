<?php

class MovementOrder
{

    public $id, $type, $position, $facing, $turn, $value, $target, $rolled, $requiredThrust;

    public function __construct($id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null)
    {

        if (!$requiredThrust) {
            $requiredThrust = new RequiredThrust();
        }

        $this->id = (int) $id;
        $this->position = $position;
        $this->target = $target;
        $this->type = $type;
        $this->facing = (int) $facing;
        $this->rolled = (boolean) $rolled;
        $this->turn = (int) $turn;
        $this->value = $value;
        $this->requiredThrust = $requiredThrust;
    }

    public function getCoPos()
    {
        return mathlib::hexCoToPixel($this->position);
    }

    public function isSpeed()
    {
        return $this->type === "speed";
    }

    public function isPivot()
    {
        return $this->type === "pivot";
    }

    public function isEvade()
    {
        return $this->type === "evade";
    }

    public function isRoll()
    {
        return $this->type === "roll";
    }

    public function isEnd()
    {
        return $this->type === "end";
    }

    public function isStart()
    {
        return $this->type === "start";
    }

    public function isDeploy()
    {
        return $this->type === "deploy";
    }

    function clone () {
        return new MovementOrder(
            $this->id,
            $this->type,
            $this->position,
            $this->target,
            $this->facing,
            $this->rolled,
            $this->turn,
            $this->value,
            $this->requiredThrust
        );
    }

    public function equals($move)
    {
        /*
        echo "\n";
        echo ($this->position->equals($move->position) ? 'true' : 'false') . "\n";
        echo ($this->target->equals($move->target) ? 'true' : 'false') . "\n";
        echo ($this->type === $move->type ? 'true' : 'false') . "\n";
        echo ($this->facing === $move->facing ? 'true' : 'false') . "\n";
        echo ($this->rolled === $move->rolled ? 'true' : 'false') . "\n";
        echo ($this->turn === $move->turn ? 'true' : 'false') . "\n";
        echo ($this->value === $move->value ? 'true' : 'false') . "\n";
         */

        return (
            $this->position->equals($move->position)
            && $this->target->equals($move->target)
            && $this->type === $move->type
            && $this->facing === $move->facing
            && $this->rolled === $move->rolled
            && $this->turn === $move->turn
            && $this->value === $move->value
        );
    }

    public function getFacingAngle()
    {

        $d = $this->facing;
        if ($d == 0) {
            return 0;
        }
        if ($d == 1) {
            return 60;
        }
        if ($d == 2) {
            return 120;
        }
        if ($d == 3) {
            return 180;
        }
        if ($d == 4) {
            return 240;
        }
        if ($d == 5) {
            return 300;
        }

        return 0;
    }

}
