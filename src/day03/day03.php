<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

$answer1 = (new Collection(file('input.txt', FILE_IGNORE_NEW_LINES)))
    ->map(fn($line) => (new Collection(str_split($line)))->split(2))
    ->map(fn($sacks) => $sacks[0]->intersect($sacks[1])->unique()->first())
    ->map(fn($item) => ord($item) > 90 ? ord($item) - ord('a') + 1 : ord($item) - ord('A') + 27)
    ->sum();

print "Answer 1: {$answer1}\n";
