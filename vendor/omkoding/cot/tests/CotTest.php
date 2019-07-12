<?php

namespace OmKoding\Cot\Tests;

use OmKoding\Cot\Report;
use OmKoding\Cot\Symbol;
use PHPUnit\Framework\TestCase;
use OmKoding\Cot\Exceptions\InvalidDateException;
use OmKoding\Cot\Exceptions\InvalidSymbolException;

class CotTest extends TestCase
{
	public function testReportByDate(): void
	{
		$report = (new Report)->byDate('09/11/18', Symbol::EURO_FX);

		$this->assertSame($report, [
			"slug" => "EURO_FX",
			"symbol" => "EURO FX",
			"date" => "2018-09-11",
			"current" => [
				"non-commercial" => [
					"long" => 164639,
					"short" => 153469,
					"spreads" => 19095,
				],
				"commercial" => [
					"long" => 277060,
					"short" => 310471,
				],
			],
			"changes" => [
				"non-commercial" => [
					"long" => -8696,
					"short" => -11903,
					"spreads" => 5389,
				],
				"commercial" => [
					"long" => 23044,
					"short" => 26696,
				],
			],
			"open-interest" => [
				"current" => 550492,
				"change" => 22913,
				"non-commercial" => [
					"long" => 29.9,
					"short" => 27.9,
					"spreads" => 3.5,
				],
				"commercial" => [
					"long" => 50.3,
					"short" => 56.4,
				],
			],
		]);
	}

	public function testInvalidDate(): void
	{
		$this->expectException(InvalidDateException::class);

		$report = (new Report)->byDate('25/02/18', Symbol::EURO_FX);
	}

	public function testInvalidSymbol(): void
	{
		$this->expectException(InvalidSymbolException::class);

		$report = (new Report)->byDate('09/11/18', 'INVALID_SYMBOL');
	}

	public function testReportLatest(): void
	{
		$report = (new Report)->latest(Symbol::EURO_FX);

		$this->assertNotNull($report['current']['non-commercial']['long']);
		$this->assertNotNull($report['changes']['commercial']['long']);
		$this->assertNotNull($report['open-interest']['current']);
	}
}