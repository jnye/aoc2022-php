<?php

require_once __DIR__ . '/../common.php';

use Illuminate\Support\Collection;

class Dir
{
    private string $name;
    private ?Dir $parent;
    private Collection $dirs;
    private Collection $files;

    public function __construct(string $name, ?Dir $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->dirs = new Collection();
        $this->files = new Collection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): Dir
    {
        return $this->parent;
    }

    public function addDirectory(string $name): void
    {
        $this->dirs->add(new Dir($name, $this));
    }

    public function addFile(string $name, string $size): void
    {
        $this->files->add([$name, $size]);
    }

    public function cd(string $name): ?Dir
    {
        return $this->dirs->firstWhere(fn($d) => $d->getName() == $name) ?: throw new InvalidArgumentException($name);
    }

    public function dirSize(): int
    {
        $dir_sum = $this->dirs->map(fn($dir) => $dir->dirSize())->sum();
        $file_sum = $this->files->map(fn($file) => $file[1])->sum();
        return $dir_sum + $file_sum;
    }

    public function eachDir(callable $fn): void
    {
        $fn($this);
        $this->dirs->each(fn($dir) => $dir->eachDir($fn));
    }

}

class Parser
{
    private Dir $root;
    private Dir $cwd;

    public function __construct(Dir $root)
    {
        $this->root = $root;
        $this->cwd = $this->root;
    }

    public function parse(string $line): void
    {
        if (str_starts_with($line, '$')) {
            $this->parse_command($line);
        } else {
            $this->parse_output($line);
        }
    }

    private function parse_command(string $line): void
    {
        if ($line == '$ cd /') {
            $this->cwd = $this->root;
        } else if ($line == '$ cd ..') {
            $this->cwd = $this->cwd->getParent();
        } else if ($line == '$ ls') {
            // ignore
        } else if (preg_match('/^\$ cd (\S+)/', $line, $matches)) {
            list($line, $name) = $matches;
            $this->cwd = $this->cwd->cd($name);
        } else throw new InvalidArgumentException($line);
    }

    private function parse_output(string $line): void
    {
        list($part1, $part2) = explode(' ', $line, 2);
        if ($part1 == 'dir') {
            $this->cwd->addDirectory($part2);
        } else {
            $this->cwd->addFile($part2, $part1);
        }
    }

    public function getRoot(): Dir
    {
        return $this->root;
    }

}

$lines = file('input.txt', FILE_IGNORE_NEW_LINES);
$root = new Dir("/");
$parser = new Parser($root);
foreach ($lines as $line) {
    $parser->parse($line);
}

$answer1 = 0;
$parser->getRoot()->eachDir(function ($dir) use (&$answer1) {
    $dir_size = $dir->dirSize();
    $answer1 += $dir_size <= 100000 ? $dir_size : 0;
});

print "Answer 1: {$answer1}\n";
