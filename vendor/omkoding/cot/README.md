# COT Report Parser

Parsing raw COT report from original source https://www.cftc.gov/dea/futures/deacmesf.htm or archived version from https://www.cftc.gov/MarketReports/CommitmentsofTraders/HistoricalViewable/index.htm

## Install

```
composer require omkoding/cot
```

## Usage

```php
<?php

require __DIR__.'/../vendor/autoload.php';

use OmKoding\Cot\Report;
use OmKoding\Cot\Symbol;

$report = new Report;

// get latest report
$report->latest();

// get latest report by symbol
$report->latest(Symbol::EURO_FX);

// get by date and symbol
$report->byDate('09/11/2018', Symbol::EURO_FX);

// list all symbol
Symbol::all();
```

## Response

```
array:3 [
  "slug" => "EURO_FX",
  "symbol" => "EURO FX",
  "date" => "2018-09-11",
  "current" => array:2 [
    "non-commercial" => array:3 [
      "long" => 164639
      "short" => 153469
      "spreads" => 19095
    ]
    "commercial" => array:2 [
      "long" => 277060
      "short" => 310471
    ]
  ]
  "changes" => array:2 [
    "non-commercial" => array:3 [
      "long" => -8696
      "short" => -11903
      "spreads" => 5389
    ]
    "commercial" => array:2 [
      "long" => 23044
      "short" => 26696
    ]
  ]
  "open-interest" => array:3 [
    "current" => 550492
    "non-commercial" => array:3 [
      "long" => 29.9
      "short" => 27.9
      "spreads" => 3.5
    ]
    "commercial" => array:2 [
      "long" => 50.3
      "short" => 56.4
    ]
  ]
]
```

## Tests

Test is available under `tests` folder. Run `phpunit` to test it.