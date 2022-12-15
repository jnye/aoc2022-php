<?php

namespace Day13;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

require_once __DIR__ . '/../common.php';

function compare(array|int $left, array|int $right): int
{
    if (is_int($left) && is_int($right)) return $left <=> $right;
    if (is_int($left)) $left = [$left];
    if (is_int($right)) $right = [$right];
    $leftSize = count($left);
    $rightSize = count($right);
    $minSize = min(count($left), count($right));
    for ($i = 0; $i < $minSize; $i++) {
        $ret = compare($left[$i], $right[$i]);
        if ($ret !== 0) return $ret;
    }
    return $leftSize <=> $rightSize;
}

$lines = new Collection(file('input.txt', FILE_IGNORE_NEW_LINES));
$pairs = $lines
    ->chunkWhile(fn($line) => !empty($line))
    ->map(fn($lines) => $lines->skipWhile(fn($line) => empty($line))->map(fn($line) => json_decode($line)));

$i = 1;
$answer1 = 0;
foreach ($pairs as $pair) {
    $ret = compare(...$pair->toArray());
    if ($ret === -1) {
        $answer1 += $i;
    }
    $i++;
}

print "Answer 1: {$answer1}\n";
Assert::eq($answer1, 5330);
