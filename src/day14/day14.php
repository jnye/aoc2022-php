<?php

namespace Day14;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

require_once __DIR__ . '/../common.php';

enum Thing: string
{
    case Air = '.';
    case Rock = '#';
    case Sand = 'o';
}

class Simulation
{
    public array $map;
    public int $max_y = 0;

    public function place_at(int $x, int $y, Thing $thing): void
    {
        $this->max_y = max($this->max_y, $y);
        $this->map[$y][$x] = $thing;
    }

    public function thing_at(int $x, int $y): Thing
    {
        return @$this->map[$y][$x] ?: Thing::Air;
    }

    /**
     * @return bool True if item stayed on the map, false if it fell off bottom
     */
    public function drop_sand_at(int $x, int $y): bool
    {
        if ($this->thing_at($x, $y) !== Thing::Air) throw new InvalidArgumentException();
        drop:
        while ($this->thing_at($x, $y + 1) == Thing::Air) {
            if ($y > $this->max_y) return false;
            $y++;
        }
        if ($this->thing_at($x - 1, $y + 1) == Thing::Air) {
            $x--;
            goto drop;
        }
        if ($this->thing_at($x + 1, $y + 1) == Thing::Air) {
            $x++;
            goto drop;
        }
        $this->place_at($x, $y, Thing::Sand);
        return true;
    }

    public function print()
    {
        $min_x = $max_x = 500;
        foreach ($this->map as $y => $line) {
            foreach ($line as $x => $item) {
                $min_x = min($min_x, $x);
                $max_x = max($max_x, $x);
            }
        }
        for ($y = 0; $y <= $this->max_y; $y++) {
            for ($x = $min_x; $x <= $max_x; $x++) {
                print $this->thing_at($x, $y)->value;
            }
            print "\n";
        }

    }

}

$lines = new Collection(file('input.txt', FILE_IGNORE_NEW_LINES));
$paths = $lines->map(fn($line) => (new Collection(explode(' -> ', $line)))->sliding(2));

$sim = new Simulation();
$paths->each(function ($segments) use (&$sim) {
    $segments->each(function ($points) use (&$sim) {
        list($x1, $y1) = explode(',', $points->first());
        list($x2, $y2) = explode(',', $points->last());
        if ($x1 == $x2) {
            if ($y1 > $y2) {
                $temp = $y1;
                $y1 = $y2;
                $y2 = $temp;
            }
            for ($i = $y1; $i <= $y2; $i++) {
                $sim->place_at($x1, $i, Thing::Rock);
            }
        } else {
            if ($x1 > $x2) {
                $temp = $x1;
                $x1 = $x2;
                $x2 = $temp;
            }
            for ($i = $x1; $i <= $x2; $i++) {
                $sim->place_at($i, $y1, Thing::Rock);
            }
        }
    });
});
//$sim->print();
//print "\n";

$units = 0;
while ($sim->drop_sand_at(500, 0)) {
    $units++;
}

//$sim->print();

$answer1 = $units;
print "Answer 1: {$answer1}\n";
Assert::eq($answer1, 768);
