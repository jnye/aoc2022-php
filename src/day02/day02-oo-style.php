<?php

enum Move
{
    case Rock;
    case Paper;
    case Scissors;

    static function parse(string $input): Move
    {
        return match ($input) {
            'X', 'A' => Move::Rock,
            'Y', 'B' => Move::Paper,
            'Z', 'C' => Move::Scissors,
            default => throw new InvalidArgumentException($input)
        };
    }

    function scoreValue(): int
    {
        return match ($this) {
            Move::Rock => 1,
            Move::Paper => 2,
            Move::Scissors => 3,
        };
    }

    function responseMove(Outcome $outcome): Move
    {
        return match ($outcome) {
            Outcome::Win => match ($this) {
                Move::Rock => Move::Paper,
                Move::Paper => Move::Scissors,
                Move::Scissors => Move::Rock,
            },
            Outcome::Draw => $this,
            Outcome::Lose => match ($this) {
                Move::Rock => Move::Scissors,
                Move::Paper => Move::Rock,
                Move::Scissors => Move::Paper,
            },
        };
    }

}

enum Outcome
{
    case Win;
    case Draw;
    case Lose;

    static function parse(string $input): Outcome
    {
        return match ($input) {
            'X' => Outcome::Lose,
            'Y' => Outcome::Draw,
            'Z' => Outcome::Win,
            default => throw new InvalidArgumentException($input)
        };
    }

    function scoreValue(): int
    {
        return match ($this) {
            Outcome::Win => 6,
            Outcome::Draw => 3,
            Outcome::Lose => 0,
        };
    }
}

class Scorer
{
    private int $score = 0;

    public function recordRound(Move $opponentMove, Move $myMove): int
    {
        $outcome = $this->determineOutcome($opponentMove, $myMove);
        $roundScore = $myMove->scoreValue() + $outcome->scoreValue();
        $this->score += $roundScore;
        return $roundScore;
    }

    private function determineOutcome(Move $opponentMove, Move $myMove): Outcome
    {
        return match ($myMove) {
            Move::Rock => match ($opponentMove) {
              Move::Rock => Outcome::Draw,
              Move::Paper => Outcome::Lose,
              Move::Scissors => Outcome::Win,
            },
            Move::Paper => match ($opponentMove) {
              Move::Rock => Outcome::Win,
              Move::Paper => Outcome::Draw,
              Move::Scissors => Outcome::Lose,
            },
            Move::Scissors => match ($opponentMove) {
              Move::Rock => Outcome::Lose,
              Move::Paper => Outcome::Win,
              Move::Scissors => Outcome::Draw,
            },
        };
    }

    public function getScore(): int
    {
        return $this->score;
    }
}

// Answer 1: We think the second value is our move
$scorer = new Scorer();
foreach (file('input.txt') as $line) {
    $line = trim($line);
    list ($firstValue, $secondValue) = explode(' ', $line);
    $opponentMove = Move::parse($firstValue);
    $myMove = Move::parse($secondValue);
    $scorer->recordRound($opponentMove, $myMove);
}
print "Answer 1: {$scorer->getScore()}\n";

// Answer 2: We now know the second value is desired outcome
$scorer = new Scorer();
foreach (file('input.txt') as $line) {
    list ($firstValue, $secondValue) = explode(' ', trim($line));
    $opponentMove = Move::parse($firstValue);
    $myMove = $opponentMove->responseMove(Outcome::parse($secondValue));
    $scorer->recordRound($opponentMove, $myMove);
}
print "Answer 2: {$scorer->getScore()}\n";

