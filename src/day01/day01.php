<?php

require_once __DIR__ . '/../common.php';

$elf_inventories = [];
$elf_number = 0;

// Read input and build a list of lists
foreach(file('input.txt', FILE_IGNORE_NEW_LINES) as $line) {
    if($line === '') {
        $elf_number++;
    } else {
        $elf_inventories[$elf_number][] = $line;
    }
}

// Build a list of total calories for each elf
$elf_total_calories = [];
foreach ($elf_inventories as $inventory) {
    $elf_total_calories[] = array_sum($inventory);
}

// Sort totals descending
rsort($elf_total_calories);

// First star: Sum of top 1 totals
$sum_top_one = $elf_total_calories[0];
print "Answer 1: $sum_top_one\n";

// Second star: Sum of top 3 totals
$sum_top_three = array_sum(array_slice($elf_total_calories, 0, 3));
print "Answer 2: $sum_top_three\n";
