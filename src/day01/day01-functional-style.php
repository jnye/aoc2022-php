<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

$sorted_desc_elf_totals = (new Collection(file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES)))
    ->chunkWhile(fn($value) => $value !== '')
    ->map(fn($chunk) => $chunk->skipWhile(fn($value) => $value === '')->sum())
    ->sortDesc();

// Answer 1 is the top one
print "Answer 1: {$sorted_desc_elf_totals->first()}\n";

// Answer 2 is the sum of top three
print "Answer 2: {$sorted_desc_elf_totals->take(3)->sum()}\n";
