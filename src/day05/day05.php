<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;


$lines = new Collection(file('input.txt', FILE_IGNORE_NEW_LINES));
$stack_lines = $lines->takeWhile(fn($line) => $line !== '')->reverse();
$stacks_numbers = (new Collection(preg_split('/\s+/', $stack_lines->first())))->skip(1);
$stacks_data = $stack_lines->skip(1)
    ->map(fn($line) => (new Collection(str_split($line, 4)))
        ->map(fn($part) => $part[1])
    );
$stacks = new Collection();
$stacks_numbers->each(function ($number) use (&$stacks, $stacks_data) {
    $stacks_data->each(function ($data) use (&$stacks, $number) {
        if (!$stacks->has($number)) $stacks->put($number, new Collection());
        $item = @$data[$number - 1];
        if ($item !== null && $item !== ' ')
            $stacks[$number]->push($item);
    });
});

$moves = $lines->skipUntil(fn($line) => $line === '')->skip(1);
$moves->each(function ($line) use (&$stacks) {
    if (!preg_match('/move (\d+) from (\d+) to (\d+)/', $line, $matches)) throw new InvalidArgumentException($line);
    list($line, $num_moves, $src_stack, $dest_stack) = $matches;
    for ($i = 0; $i < $num_moves; $i++) {
        $item = $stacks->get($src_stack)->pop();
        $stacks->get($dest_stack)->push($item);
    }
});

$answer1 = $stacks->map(fn ($stack) => $stack->pop())->join('');

print "Answer 1: {$answer1}\n";
