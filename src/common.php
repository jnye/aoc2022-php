<?php

namespace AdventOfCode2022;

require_once __DIR__ . '/../vendor/autoload.php';

$day = sprintf("%02d", $argv[1] ?? 1);
$file = "day{$day}/day{$day}.php";

require_once $file;
