<?php

require_once __DIR__ . '/../common.php';

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

    private Vector $head;
    private Vector $tail;
    private array $tail_history;

    public function __construct()
    {
        $this->head = new Vector([0, 0]);
        $this->tail = new Vector([0, 0]);
        $this->tail_history[] = '0,0';
    }


    public function moveHead(string $dir, int $steps)
    {
        $move_vector = (new Vector(self::DIRECTION_MAP[$dir]));
        for ($i = 0; $i < $steps; $i++) {
            $this->head = $this->head->add($move_vector);
            $this->dragTail();
            $this->tail_history[] = sprintf('%d,%d', ...$this->tail->getVector());
        }

    }

    private function dragTail(): void
    {
        $delta_vector = $this->tail->subtract($this->head);
        $length = $delta_vector->length();
        if ($length <= self::MAX_LENGTH) return;
        $this->tail = $this->tail->subtract($delta_vector->scalarMultiply(self::MAX_LENGTH / $length));
        $this->tail = new Vector([round($this->tail->get(0)), round($this->tail->get(1))]);
    }

    public function countTailPositions(): int
    {
        return count(array_unique($this->tail_history));
    }
}

$commands = file('input.txt', FILE_IGNORE_NEW_LINES);

$tracer = new Tracer();
foreach ($commands as $command) {
    list($dir, $steps) = explode(' ', $command);
    $tracer->moveHead($dir, $steps);
}
$answer1 = $tracer->countTailPositions();

print "Answer 1: {$answer1}\n";
