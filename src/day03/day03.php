<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

$answer1 = (new Collection(file('input.txt', FILE_IGNORE_NEW_LINES)))
    ->map(fn($line) => (new Collection(str_split($line)))->split(2))
    ->map(fn($sacks) => $sacks[0]->intersect($sacks[1])->first())
    ->map(fn($item) => ord($item))
    ->map(fn($value) => $value > 90 ? $value - ord('a') + 1 : $value - ord('A') + 27)
    ->sum();

print "Answer 1: {$answer1}\n";

$answer2 = (new Collection(file('input.txt', FILE_IGNORE_NEW_LINES)))
    ->map(fn($line) => new Collection(str_split($line)))
    ->chunk(3)
    ->map(fn($group) => $group->reduce(fn($carry, $item) => $carry === null ? $item : $carry->intersect($item))->first())
    ->map(fn($item) => ord($item))
    ->map(fn($value) => $value > 90 ? $value - ord('a') + 1 : $value - ord('A') + 27)
    ->sum();

print "Answer 2: {$answer2}\n";
