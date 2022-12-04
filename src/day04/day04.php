<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

class Range
{

    public function __construct(public int $start, public int $end)
    {
    }

    public function contains(Range $other): bool
    {
        return $this->start <= $other->start && $this->end >= $other->end;
    }

    public function overlaps(Range $other): bool
    {
        $self = new Collection(range($this->start, $this->end));
        $other = new Collection(range($other->start, $other->end));
        return $self->intersect($other)->count() > 0;
    }
}

$answer1 = (new Collection(file('input.txt', FILE_IGNORE_NEW_LINES)))
    ->map(fn($line) => new Collection(explode(',', $line)))
    ->map(fn($ranges) => $ranges->map(fn($range) => new Range(...explode('-', $range))))
    ->map(fn($ranges) => $ranges->get(0)->contains($ranges->get(1)) || $ranges->get(1)->contains($ranges->get(0)) ? 1 : 0)
    ->sum();

print "Answer 1: {$answer1}\n";

$answer2 = (new Collection(file('input.txt', FILE_IGNORE_NEW_LINES)))
    ->map(fn($line) => new Collection(explode(',', $line)))
    ->map(fn($ranges) => $ranges->map(fn($range) => new Range(...explode('-', $range))))
    ->map(fn($ranges) => $ranges->get(0)->overlaps($ranges->get(1)) ? 1 : 0)
    ->sum();

print "Answer 2: {$answer2}\n";
