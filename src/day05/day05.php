<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

$lines = new Collection(file('input.txt', FILE_IGNORE_NEW_LINES));
$moves = $lines->skipUntil(fn($line) => $line === '')->skip(1);

$stack_lines = $lines->takeWhile(fn($line) => $line !== '')->reverse();
$stacks_numbers = (new Collection(preg_split('/\s+/', $stack_lines->first())))->skip(1);
$stacks_data = $stack_lines->skip(1)
    ->map(fn($line) => (new Collection(str_split($line, 4)))
        ->map(fn($part) => $part[1])
    );


$answer1_stacks = new Collection();
$stacks_numbers->each(function ($number) use (&$answer1_stacks, $stacks_data) {
    $stacks_data->each(function ($data) use (&$answer1_stacks, $number) {
        if (!$answer1_stacks->has($number)) $answer1_stacks->put($number, new Collection());
        $item = @$data[$number - 1];
        if ($item !== null && $item !== ' ')
            $answer1_stacks[$number]->push($item);
    });
});
$moves->each(function ($line) use (&$answer1_stacks) {
    if (!preg_match('/move (\d+) from (\d+) to (\d+)/', $line, $matches)) throw new InvalidArgumentException($line);
    list($line, $num_moves, $src_stack, $dest_stack) = $matches;
    for ($i = 0; $i < $num_moves; $i++) {
        $item = $answer1_stacks->get($src_stack)->pop();
        $answer1_stacks->get($dest_stack)->push($item);
    }
});
$answer1 = $answer1_stacks->map(fn ($stack) => $stack->pop())->join('');

print "Answer 1: {$answer1}\n";

$answer2_stacks = new Collection();
$stacks_numbers->each(function ($number) use (&$answer2_stacks, $stacks_data) {
    $stacks_data->each(function ($data) use (&$answer2_stacks, $number) {
        if (!$answer2_stacks->has($number)) $answer2_stacks->put($number, new Collection());
        $item = @$data[$number - 1];
        if ($item !== null && $item !== ' ')
            $answer2_stacks[$number]->push($item);
    });
});
$moves->each(function ($line) use (&$answer2_stacks) {
    if (!preg_match('/move (\d+) from (\d+) to (\d+)/', $line, $matches)) throw new InvalidArgumentException($line);
    list($line, $num_moves, $src_stack, $dest_stack) = $matches;
    $tmp_stack = new Collection();
    for ($i = 0; $i < $num_moves; $i++) {
        $item = $answer2_stacks->get($src_stack)->pop();
        $tmp_stack->push($item);
    }
    for ($i = 0; $i < $num_moves; $i++) {
        $item = $tmp_stack->pop();
        $answer2_stacks->get($dest_stack)->push($item);
    }
});
$answer2 = $answer2_stacks->map(fn ($stack) => $stack->pop())->join('');

print "Answer 2: {$answer2}\n";
