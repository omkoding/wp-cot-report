<?php

require __DIR__.'/../vendor/autoload.php';

use OmKoding\Cot\Report;
use OmKoding\Cot\Symbol;

$report = new Report;

dump($report->latest());
dump($report->latest(Symbol::EURO_FX));
dump($report->byDate('09/11/18'));
dump($report->byDate('09/11/18', Symbol::EURO_FX));
dump(Symbol::all());