<?php

function win_score(string $opponent, string $me): int
{
    return match ($opponent) {
        'A' => match ($me) { // Rock
            'X' => 3, // vs Rock
            'Y' => 6, // vs Paper
            'Z' => 0, // vs Scissors
            default => throw new InvalidArgumentException($me),
        },
        'B' => match ($me) { // Paper
            'X' => 0, // vs Rock
            'Y' => 3, // vs Paper
            'Z' => 6, // vs Scissors
            default => throw new InvalidArgumentException($me),
        },
        'C' => match ($me) { // Scissors
            'X' => 6, // vs Rock
            'Y' => 0, // vs Paper
            'Z' => 3, // vs Scissors
            default => throw new InvalidArgumentException($me),
        },
        default => throw new InvalidArgumentException($opponent),
    };
}

function move_score(string $move): int
{
    return match ($move) {
        'A', 'X' => 1, // Rock
        'B', 'Y' => 2, // Paper
        'C', 'Z' => 3, // Scissors
        default => throw new InvalidArgumentException($move),
    };
}

function move_to_win(string $opponent): string
{
    return match ($opponent) {
        'A' => 'Y', // Paper wins over Rock
        'B' => 'Z', // Scissors wins over Paper
        'C' => 'X', // Rock wins over Scissors
        default => throw new InvalidArgumentException($opponent)
    };
}

function move_to_draw(string $opponent): string
{
    return match ($opponent) {
        'A' => 'X', // Rock draws Rock
        'B' => 'Y', // Paper draws Paper
        'C' => 'Z', // Scissors draws Scissors
        default => throw new InvalidArgumentException($opponent)
    };
}

function move_to_lose(string $opponent): string
{
    return match ($opponent) {
        'A' => 'Z', // Scissors loses against Rock
        'B' => 'X', // Rock loses against Paper
        'C' => 'Y', // Paper loses against Scissors
        default => throw new InvalidArgumentException($opponent)
    };
}

function strategy_move(string $strat, string $opponent): string {
    return match ($strat) {
        'X' => move_to_lose($opponent),
        'Y' => move_to_draw($opponent),
        'Z' => move_to_win($opponent),
        default => throw new InvalidArgumentException($opponent)
    };
}

$total_score = 0;
foreach (file('input.txt') as $line) {
    list($opponent, $me) = explode(" ", trim($line));
    $round_score = move_score($me) + win_score($opponent, $me);
    $total_score += $round_score;
}

$total_score_2 = 0;
foreach (file('input.txt') as $line) {
    list($opponent, $strategy) = explode(" ", trim($line));
    $me = strategy_move($strategy, $opponent);
    $round_score = move_score($me) + win_score($opponent, $me);
    $total_score_2 += $round_score;
}

print "Answer 1: $total_score\n";
print "Answer 2: $total_score_2\n";
