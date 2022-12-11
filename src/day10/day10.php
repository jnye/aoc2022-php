<?php

use Webmozart\Assert\Assert;

require_once __DIR__ . '/../common.php';

$instructions = file('input.txt', FILE_IGNORE_NEW_LINES);
$reg = 1;
$reg_next = $reg;
$cycle = 0;
$trace = [];
$pipeline = [];
foreach ($instructions as $instruction) {
    $parts = explode(' ', $instruction);
    if ($parts[0] == 'noop') {
        $trace[$cycle++] = $reg;
    } else if ($parts[0] == 'addx') {
        $value = intval($parts[1]);
        $trace[$cycle++] = $reg;
        $trace[$cycle++] = $reg + $value;
        $reg = $reg + $value;
    } else throw new InvalidArgumentException($instruction);
}

$sum = 0;
for ($cycle = 20; $cycle < count($trace); $cycle += 40) {
    $i = $cycle - 2;
    $reg = $trace[$i];
    $sig = $cycle * $reg;
    $sum += $sig;
}
$answer1 = $sum;

print "Answer 1: {$answer1}\n";
Assert::eq($answer1, 13220);

//print "Answer 2: {$answer2}\n";
