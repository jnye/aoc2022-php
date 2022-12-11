<?php

use Webmozart\Assert\Assert;

require_once __DIR__ . '/../common.php';

$instructions = file('input.txt', FILE_IGNORE_NEW_LINES);
$x = 1;
$cycle = 0;
$cycles_till_complete = 0;
$next_x = $x;
$trace = []; // value of x during a cycle
foreach ($instructions as $instruction) {
    $parts = explode(' ', $instruction);
    if ($parts[0] == 'noop') {
        $cycles_till_complete = 1;
        $next_x = $x;
    } else if ($parts[0] == 'addx') {
        $cycles_till_complete = 2;
        $next_x = $x + intval($parts[1]);
    } else throw new InvalidArgumentException($instruction);
    while ($cycles_till_complete > 0) {
        $cycles_till_complete--;
        $cycle++;
        $trace[$cycle] = $x;
    }
    $x = $next_x;
}

$sum = 0;
for ($cycle = 20; $cycle < count($trace); $cycle += 40) {
    $x = $trace[$cycle];
    $sig = $cycle * $x;
    $sum += $sig;
}
$answer1 = $sum;

print "Answer 1: {$answer1}\n";
Assert::eq($answer1, 13220);

//print "Answer 2: {$answer2}\n";
