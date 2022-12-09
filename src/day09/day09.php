<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;
use MathPHP\LinearAlgebra\Vector;

class Tracer
{
    private const DIRECTION_MAP = [
        'U' => [0, -1],
        'D' => [0, 1],
        'L' => [-1, 0],
        'R' => [1, 0],
    ];

    private const MAX_LENGTH = M_SQRT2;

    private Collection $knots;
    private array $tail_history;

    public function __construct(int $num_knots = 2)
    {
        $this->knots = new Collection();
        for ($i = 0; $i < $num_knots; $i++) {
            $this->knots->add(new Vector([0, 0]));
        }
        $this->tail_history[] = '0,0';
    }

    private function head(): Vector
    {
        return $this->knots->first();
    }


    private function tail(): Vector
    {
        return $this->knots->last();
    }

    public function moveHead(string $dir, int $steps)
    {
        $move_vector = (new Vector(self::DIRECTION_MAP[$dir]));
        for ($i = 0; $i < $steps; $i++) {
            $this->knots->put(0, $this->head()->add($move_vector));
            $this->dragTail();
            $this->tail_history[] = sprintf('%d,%d', ...$this->tail()->getVector());
        }
    }

    private function dragTail(): void
    {
        $num_knots = $this->knots->count();
        for ($i = 1; $i < $num_knots; $i++) {
            $knot = $this->knots->get($i);
            $delta_vector = $knot->subtract($this->knots->get($i - 1));
            $length = $delta_vector->length();
            if ($length <= self::MAX_LENGTH) continue;
            $this->knots->put($i, $this->knots->get($i)->subtract($delta_vector->scalarMultiply(self::MAX_LENGTH / $length)));
            $this->knots->put($i, new Vector([round($this->knots->get($i)->get(0)), round($this->knots->get($i)->get(1))]));
        }
    }

    public function countTailPositions(): int
    {
        return count(array_unique($this->tail_history));
    }
}

$commands = file('input.txt', FILE_IGNORE_NEW_LINES);

$tracer = new Tracer(2);
foreach ($commands as $command) {
    list($dir, $steps) = explode(' ', $command);
    $tracer->moveHead($dir, $steps);
}
$answer1 = $tracer->countTailPositions();

//Assert::eq($answer1, 5981);
print "Answer 1: {$answer1}\n";

$tracer = new Tracer(10);
foreach ($commands as $command) {
    list($dir, $steps) = explode(' ', $command);
    $tracer->moveHead($dir, $steps);
}
$answer2 = $tracer->countTailPositions();

//Assert::eq($answer2, 2352);
print "Answer 2: {$answer2}\n";
