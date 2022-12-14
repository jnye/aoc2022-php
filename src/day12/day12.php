<?php

namespace Day12;

use Webmozart\Assert\Assert;

require_once __DIR__ . '/../common.php';

class Coord
{
    private Map $map;
    private int $x;
    private int $y;

    public static function fromIndex(Map &$map, int $index): self
    {
        $width = $map->width();
        $y = (int)($index / $width);
        $x = $index % $width;
        return new Coord($map, $x, $y);
    }

    public function __construct(Map &$map, int $x, int $y)
    {
        $this->map = $map;
        if ($x < 0 || $x >= $map->width()) throw new \InvalidArgumentException("x $x is not valid");
        if ($y < 0 || $y >= $map->height()) throw new \InvalidArgumentException("y $y is not valid");
        $this->x = $x;
        $this->y = $y;
    }

    public function index(): int
    {
        return $this->y * $this->map->width() + $this->x;
    }

    public function north(): ?self
    {
        $new_y = $this->y - 1;
        if ($new_y < 0 || $new_y >= $this->map->height()) return null;
        $neighbor = new Coord($this->map, $this->x, $new_y);
        return $this->map->canNavigateTo($this, $neighbor) ? $neighbor : null;
    }

    public function south(): ?self
    {
        $new_y = $this->y + 1;
        if ($new_y < 0 || $new_y >= $this->map->height()) return null;
        $neighbor = new Coord($this->map, $this->x, $new_y);
        return $this->map->canNavigateTo($this, $neighbor) ? $neighbor : null;
    }

    public function east(): ?self
    {
        $new_x = $this->x + 1;
        if ($new_x < 0 || $new_x >= $this->map->width()) return null;
        $neighbor = new Coord($this->map, $new_x, $this->y);
        return $this->map->canNavigateTo($this, $neighbor) ? $neighbor : null;
    }

    public function west(): ?self
    {
        $new_x = $this->x - 1;
        if ($new_x < 0 || $new_x >= $this->map->width()) return null;
        $neighbor = new Coord($this->map, $new_x, $this->y);
        return $this->map->canNavigateTo($this, $neighbor) ? $neighbor : null;
    }

    public function neighbors(): array
    {
        return [$this->north(), $this->east(), $this->south(), $this->west()];
    }

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }

}

class Map
{

    private int $height;
    private int $width;
    private string $heightmap;

    private function __construct(int $width, int $height, string $heightmap)
    {
        $this->width = $width;
        $this->height = $height;
        $this->heightmap = $heightmap;
    }

    public static function fromFile(string $file): self
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $height = count($lines);
        if ($height < 1) throw new \InvalidArgumentException();
        $width = strlen($lines[0]);
        if ($width < 1) throw new \InvalidArgumentException();
        $heightmap = join('', $lines);
        return new Map($width, $height, $heightmap);
    }

    public function start(): Coord
    {
        $index = strpos($this->heightmap, 'S');
        if ($index === false) throw new \InvalidArgumentException();
        return Coord::fromIndex($this, $index);
    }

    public function end(): Coord
    {
        $index = strpos($this->heightmap, 'E');
        if ($index === false) throw new \InvalidArgumentException();
        return Coord::fromIndex($this, $index);
    }

    public function heightAt(Coord &$coord): int
    {
        $index = $coord->index();
        $heightAt = $this->heightmap[$index];
        if ($heightAt == 'S') return ord('a');
        if ($heightAt == 'E') return ord('z');
        return ord($heightAt);
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function canNavigateTo(Coord &$from, Coord &$neighbor): bool
    {
        $diff = $this->heightAt($neighbor) - $this->heightAt($from);
        return !($diff > 1);
    }

}

class Pathfinder
{

    public function minimumSteps(Coord $start, Coord $end): int
    {
        $queue = [[$start]];
        $visited = [$start];
        while (!empty($queue)) {
            $currentPath = array_shift($queue);
            $current = $currentPath[count($currentPath) - 1];

            if ($current == $end) {
                return count($currentPath) - 1;
            }
            $neighbors = $current->neighbors();
            foreach ($neighbors as $neighbor) {
                if ($neighbor === null) continue;
                if (!in_array($neighbor, $visited)) {
                    $newPath = $currentPath;
                    $newPath[] = $neighbor;
                    $queue[] = $newPath;
                    $visited[] = $neighbor;
                }
            }
        }
        return -1;
    }

}

$map = Map::fromFile('input.txt');
$pathfinder = new Pathfinder();

$answer1 = $pathfinder->minimumSteps($map->start(), $map->end());
print "Answer 1: {$answer1}\n";
Assert::eq($answer1, 472);
