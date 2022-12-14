<?php

require_once __DIR__ . '/../common.php';

class Coord
{
    public function __construct(public int $x, public int $y, public int $width, public int $height)
    {
        if ($x < 0 || $x >= $width) throw new InvalidArgumentException();
        if ($y < 0 || $y >= $height) throw new InvalidArgumentException();
    }

    public function index(): int
    {
        return $this->y * $this->width + $this->x;
    }

    public function north(): ?Coord
    {
        if ($this->y - 1 < 0) return null;
        return new Coord($this->x, $this->y - 1, $this->width, $this->height);
    }

    public function south(): ?Coord
    {
        if ($this->y + 1 >= $this->height) return null;
        return new Coord($this->x, $this->y + 1, $this->width, $this->height);
    }

    public function east(): ?Coord
    {
        if ($this->x + 1 >= $this->width) return null;
        return new Coord($this->x + 1, $this->y, $this->width, $this->height);
    }

    public function west(): ?Coord
    {
        if ($this->x - 1 < 0) return null;
        return new Coord($this->x - 1, $this->y, $this->width, $this->height);
    }

}

class Forest
{
    private int $width;
    private int $height;
    private string $heights;

    public function __construct(array $height_lines)
    {
        $this->width = strlen($height_lines[0]);
        $this->height = count($height_lines);
        $this->heights = join('', $height_lines);
    }

    private function coord(int $x, int $y): Coord
    {
        return new Coord($x, $y, $this->width, $this->height);
    }

    public function getVisibleTreeCount(): int
    {
        $visible_indexes = [];
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $coord = $this->coord($x, $y);
                $tree_height = $this->heights[$coord->index()];
                $tree_index = $coord->index();
                foreach (['north', 'east', 'south', 'west'] as $direction) {
                    $max_tree_height_seen = $this->maxTreeHeight($coord, $direction);
                    if ($tree_height > $max_tree_height_seen) $visible_indexes[] = $tree_index;
                }
            }
        }
        return count(array_unique($visible_indexes));
    }

    private function maxTreeHeight(Coord $coord, string $direction): int
    {
        $max = -1;
        while (($coord = $coord->$direction()) !== null) {
            $max = max($max, $this->heights[$coord->index()]);
        }
        return $max;
    }

    public function getMaxScenicScore(): int
    {
        $scenic_scores = [];
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $coord = $this->coord($x, $y);
                $tree_index = $coord->index();
                $scenic_scores[$tree_index] = $this->getScenicScore($coord);
            }
        }
        return max($scenic_scores);
    }

    private function getScenicScore(Coord $coord): int
    {
        $scores = [];
        foreach (['north', 'east', 'south', 'west'] as $direction) {
            $scores[] = $this->viewingDistance($coord, $direction);
        }
        return array_product($scores);
    }

    private function viewingDistance(Coord $coord, string $direction): int
    {
        $distance = 0;
        $tree_height = $this->heights[$coord->index()];
        while (($coord = $coord->$direction()) !== null) {
            $distance++;
            if ($this->heights[$coord->index()] >= $tree_height) break;
        }
        return $distance;
    }

}


$forest = new Forest(file('input.txt', FILE_IGNORE_NEW_LINES));

$answer1 = $forest->getVisibleTreeCount();
print "Answer 1: {$answer1}\n";

$answer2 = $forest->getMaxScenicScore();
print "Answer 2: {$answer2}\n";
