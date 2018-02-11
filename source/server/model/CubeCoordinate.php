<?php

class CubeCoordinate
{

    public $x = 0;
    public $y = 0;
    public $z = 0;

    private const PRECISION = 4;

    private const NEIGHBOURS = [
        [ "x"=>  1, "y"=> -1, "z"=> 0 ], [ "x"=>  1, "y"=>  0, "z"=> -1 ], [ "x"=>  0, "y"=>  1, "z"=> -1 ],
        [ "x"=> -1, "y"=>  1, "z"=> 0 ], [ "x"=> -1, "y"=>  0, "z"=>  1 ], [ "x"=>  0, "y"=> -1, "z"=>  1 ]
    ];

    public function __construct($x, $y = null, $z = null)
    {
        if ($x instanceof CubeCoordinate){
            $this->x = round((int)$x->x, self::PRECISION);
            $this->y = round((int)$x->y, self::PRECISION);
            $this->z = round((int)$x->z, self::PRECISION);
        } else {
            $this->x = round((int)$x, self::PRECISION);
            $this->y = round((int)$y, self::PRECISION);
            $this->z = round((int)$z, self::PRECISION);
        }

        $this->validate();
    }


    public function round()
    {
        if ($this->x % 1 === 0 && $this->y % 1 === 0 && $this->z % 1 === 0) {
            return $this;
        }

        $rx = round($this->x);
        $ry = round($this->y);
        $rz = round($this->z);

        $x_diff = abs($rx - $this->x);
        $y_diff = abs($ry - $this->y);
        $z_diff = abs($rz - $this->z);

        if ($x_diff > $y_diff && $x_diff > $z_diff) {
            $rx = -$ry - $rz;
        } else if ($y_diff > $z_diff) {
            $ry = -$rx - $rz;
        } else {
            $rz = -$rx - $ry;
        }

        return new CubeCoordinate($rx, $ry, $rz);
    }

    private function validate()
    {
        if (abs($this->x + $this->y + $this->z) > 0.001) {
            throw new Exception(
                "Invalid Cube coordinates: (" + $this->x + ", " + $this->y + ", " + $this->z + ")"
            );
        }
    }

    public function getNeighbours() {
        $neighbours = [];
        foreach (self::NEIGHBOURS[$this->r & 1] as $neighbor) {
            $neighbours[] = $this->add(new CubeCoordinate($neighbor));
        }
    }

    public function moveToDirection($direction) {
        return $this->add(new CubeCoordinate(self::NEIGHBOURS[$direction]));
    }

    public function add(CubeCoordinate $cube)
    {
        return new CubeCoordinate($this->x + $cube->x, $this->x + $cube->y, $this->x + $cube->z);
    }

    public function subtract(CubeCoordinate $cube)
    {
        return new CubeCoordinate($this->x - $cube->x, $this->x - $cube->y, $this->x - $cube->z);
    }

    public function scale($scale)
    {
        return new CubeCoordinate($this->x * $scale, $this->x * $scale, $this->x * $scale);
    }

    public function distanceTo(CubeCoordinate $cube)
    {
        return max(
            abs($this->x - $cube->x),
            abs($this->y - $cube->y),
            abs($this->z - $cube->z)
        );
    }

    public function equals(CubeCoordinate $cube)
    {
        return $this->x === $cube->x &&
            $this->y === $cube->y &&
            $this->z === $cube->z;
    }

    public function getFacing(CubeCoordinate $cube)
    {
        $delta = $cube->subtract($this);

        foreach (self::NEIGHBOURS as $i => $neighbor) {
            if ($delta->equals(new CubeCoordinate($neighbor))){
                return $i;
            }
        }

        throw new Exception('Unable to find facing for cube neighbour');
    }

    public function toOffset() {
        $q = $this->x + ($this->z + ($this->z & 1)) / 2;
        $r = $this->z;

        return new OffsetCoordinate($q, $r);
    }
}