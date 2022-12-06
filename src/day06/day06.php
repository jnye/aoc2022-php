<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

$input = str_split(file('input.txt', FILE_IGNORE_NEW_LINES)[0]);
$answer1 = 0;
for ($i = 0; $i < count($input); $i++) {
    $slice = new Collection(array_slice($input, 0, $i));
    $count = $slice->pop(4)->unique()->count();
    if ($count == 4) {
        $answer1 = $i;
        break;
    }
}

print "Answer 1: {$answer1}\n";
