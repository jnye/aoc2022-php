<?php

require_once __DIR__ . '/../common.php';

$input = str_split(file('input.txt', FILE_IGNORE_NEW_LINES)[0]);

function find_start(array $input, int $len): int
{
    for ($i = 0; $i < count($input) - $len; $i++) {
        $count = count(array_unique(array_slice($input, $i, $len)));
        if ($count == $len) {
            return $i + $len;
        }
    }
    return -1;
}

$answer1 = find_start($input, 4);
print "Answer 1: {$answer1}\n";

$answer2 = find_start($input, 14);
print "Answer 2: {$answer2}\n";
