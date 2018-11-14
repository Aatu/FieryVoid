<?php
class OffsetCoordinate
{

    /**
     * @var integer
     */
    public $q;
    /**
     * @var integer
     */
    public $r;

    private const NEIGHBOURS = [
        [
            ["q"=> 1, "r"=> 0],
            ["q"=> 1, "r"=> -1],
            ["q"=> 0, "r"=> -1],
            ["q"=> -1, "r"=> 0],
            ["q"=> 0, "r"=> 1],
            ["q"=> 1, "r"=> 1]
        ],
        [
            ["q"=> 1, "r"=> 0],
            ["q"=> 0, "r"=> -1],
            ["q"=> -1, "r"=> -1],
            ["q"=> -1, "r"=> 0],
            ["q"=> -1, "r"=> 1],
            ["q"=> 0, "r"=> 1]
        ]
    ];

    public function __construct($q, $r = null)
    {
        if ($q instanceof OffsetCoordinate) {
            $this->q = $q->q;
            $this->r = $q->r;
        } else if (isset($q["q"]) && isset($q["r"])) {
            $this->q = (int)$q["q"];
            $this->r = (int)$q["r"];
        }else {
            $this->q = (int)$q;
            $this->r = (int)$r;
        }
        
    }

    public function getNeighbours() {
        $neighbours = [];
        foreach (self::NEIGHBOURS[$this->r & 1] as $neighbor) {
            $neighbours[] = $this->add(new OffsetCoordinate($neighbor));
        }
    }

    public function add(OffsetCoordinate $position): OffsetCoordinate {
        return $this->toCube()->add($position->toCube())->toOffset();
    }

    public function moveToDirection($direction, $steps = 1): OffsetCoordinate {
        $asCube = $this->toCube();

        while($steps--) {
            $asCube = $asCube->moveToDirection($direction);
        }

        return $asCube->toOffset();
    }

    public function distanceTo(OffsetCoordinate $position) {
        return $this->toCube()->distanceTo($position->toCube());
    }

    public function equals(OffsetCoordinate $position): bool {
        return $this->q === $position->q && $this->r = $position->r;
    }

    public function toCube(): CubeCoordinate
    {
        $x = $this->q - ($this->r + ($this->r & 1)) / 2;
        $z = $this->r;
        $y = -$x - $z;

        return (new CubeCoordinate($x, $y, $z))->round();
    }
}