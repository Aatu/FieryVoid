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
            $this->q = $q["q"];
            $this->r = $q["r"];
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
        return new OffsetCoordinate(
          $this->q + $position->q,
          $this->r + $position->r
        );
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