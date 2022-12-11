<?php

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

require_once __DIR__ . '/../common.php';

class KeepAwayGame
{
    public Collection $monkeys;
    public int $round;

    public function __construct()
    {
        $this->monkeys = new Collection();
        $this->round = 0;
    }

    public function addMonkey(Monkey $monkey): void
    {
        $monkey->setGame($this);
        $this->monkeys->put($monkey->getNumber(), $monkey);
    }

    public function playRound(): void
    {
        $this->round++;
        $this->monkeys->each(function (Monkey $monkey) {
            $monkey->takeTurn();
        });
    }

    public function throwToMonkey(mixed $worry_level, int $monkey_number)
    {
        /** @var Monkey $monkey */
        $monkey = $this->monkeys->get($monkey_number);
        $monkey->addWorryLevel($worry_level);
    }

    public function monkeyBusiness(): int
    {
        $top = $this->monkeys->map(fn(Monkey $monkey) => $monkey->inspections)->sortDesc()->take(2);
        return $top->first() * $top->skip(1)->first();
    }
}

class Monkey
{
    private ?KeepAwayGame $game;
    private int $number;
    public Collection $worry_levels;
    private Closure $operation;
    private Closure $test;
    private int $trueMonkeyNumber;
    private int $falseMonkeyNumber;
    public int $inspections;

    public function __construct(int $number)
    {
        $this->number = $number;
        $this->worry_levels = new Collection();
        $this->operation = fn($old) => $old;
        $this->inspections = 0;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setItems(array $worry_levels): void
    {
        $this->worry_levels->push(...$worry_levels);
    }

    public function setOperationString(string $operation): void
    {
        if (preg_match('/^new = old \+ (\d+)/', $operation, $matches)) {
            $add = intval($matches[1]);
            $this->operation = fn($old) => $old + $add;
        } else if (preg_match('/^new = old \* old/', $operation, $matches)) {
            $this->operation = fn($old) => $old * $old;
        } else if (preg_match('/^new = old \* (\d+)/', $operation, $matches)) {
            $mul = intval($matches[1]);
            $this->operation = fn($old) => $old * $mul;
        } else throw new InvalidArgumentException($operation);
    }

    public function setTestString(string $test): void
    {
        if (preg_match('/^divisible by (\d+)/', $test, $matches)) {
            $div = intval($matches[1]);
            $this->test = fn(int $interest) => $interest % $div == 0;
        } else throw new InvalidArgumentException($test);
    }

    public function setIfTrueString(string $if_true): void
    {
        if (preg_match('/^throw to monkey (\d+)/', $if_true, $matches)) {
            $this->trueMonkeyNumber = intval($matches[1]);
        } else throw new InvalidArgumentException($if_true);
    }

    public function setIfFalseString(string $if_false): void
    {
        if (preg_match('/^throw to monkey (\d+)/', $if_false, $matches)) {
            $this->falseMonkeyNumber = intval($matches[1]);
        } else throw new InvalidArgumentException($if_false);
    }

    public function setGame(KeepAwayGame $game): void
    {
        $this->game = $game;
    }

    public function takeTurn(): void
    {
        while ($worry_level = $this->worry_levels->shift()) {
            $this->inspections++;
            $worry_level = $this->operation->call($this, $worry_level);
            $worry_level = (int)($worry_level / 3);
            $this->game->throwToMonkey($worry_level, $this->test->call($this, $worry_level) ? $this->trueMonkeyNumber : $this->falseMonkeyNumber);
        }
    }

    public function addWorryLevel(int $worry_level): void
    {
        $this->worry_levels->push($worry_level);
    }

}

$lines = file('input.txt', FILE_IGNORE_NEW_LINES);
$game = new KeepAwayGame();
$monkey = null;
foreach ($lines as $line) {
    if (preg_match('/^Monkey (\d+):/', $line, $matches)) {
        if ($monkey !== null) $game->addMonkey($monkey);
        $monkey_number = $matches[1];
        $monkey = new Monkey($monkey_number);
    } else if (preg_match('/^\s+Starting items: (.*)/', $line, $matches)) {
        $items = preg_split('/(,|\s)+/', $matches[1]);
        $items = array_map('intval', $items);
        $monkey->setItems($items);
    } else if (preg_match('/^\s+Operation: (.*)/', $line, $matches)) {
        $monkey->setOperationString($matches[1]);
    } else if (preg_match('/^\s+Test: (.*)/', $line, $matches)) {
        $monkey->setTestString($matches[1]);
    } else if (preg_match('/^\s+If true: (.*)/', $line, $matches)) {
        $monkey->setIfTrueString($matches[1]);
    } else if (preg_match('/^\s+If false: (.*)/', $line, $matches)) {
        $monkey->setIfFalseString($matches[1]);
    }
}
$game->addMonkey($monkey);

for ($i = 0; $i < 20; $i++)
    $game->playRound();

$answer1 = $game->monkeyBusiness();
print "Answer 1: {$answer1}\n";
Assert::eq($answer1, 66124);
