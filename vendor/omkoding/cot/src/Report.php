<?php

namespace OmKoding\Cot;

use DateTime;
use ReflectionClass;
use GuzzleHttp\Client;
use OmKoding\Cot\Exceptions\ResponseException;
use OmKoding\Cot\Exceptions\InvalidDateException;
use OmKoding\Cot\Exceptions\InvalidSymbolException;

class Report
{
	protected $client;

	public function __construct()
	{
		$this->client = new Client([
			'http_errors' => false,
		]);
	}

	public function latest($symbol = null)
	{
		self::validateSymbol($symbol);

		$url = 'https://www.cftc.gov/dea/futures/deacmesf.htm';

		$parsed = $this->fetchAndParse($url);

		return $symbol ? $parsed[$symbol] : $parsed;
	}

    public function byDate($date, $symbol = null)
    {
        self::validateDate($date);

        list($month, $day, $year) = explode('/', $date);

		self::validateSymbol($symbol);

        $url = sprintf(
            'https://www.cftc.gov/sites/default/files/files/dea/cotarchives/%s/futures/deacmesf%s%s%s.htm',
            date('Y', strtotime($date)),
            $month,
            $day,
            $year
        );

        $parsed = $this->fetchAndParse($url);

        return $symbol ? $parsed[$symbol] : $parsed;
    }

    static public function validateDate($date, $format = 'm/d/y'): void
    {
    	$d = DateTime::createFromFormat($format, $date);

        if (! $d || $d->format($format) != $date) {
            throw new InvalidDateException("Date format must be {$format}");
        }
    }

    static public function validateSymbol($symbol): void
    {
    	if (! $symbol) {
    		return;
    	}

    	$symbols = Symbol::all();
    	
    	if (! in_array($symbol, $symbols)) {
    		throw new InvalidSymbolException("Symbol '$symbol' is not exists.");
    	}
    }

	private function parse(string $html): array
	{
		$lines = explode(PHP_EOL, $html);

		$symbols = Symbol::all();

		$result = [];

		foreach ($symbols as $slug => $symbol) {
			foreach ($lines as $index => $line) {
				if ($this->startsWith($line, $symbol . ' - ')) {
					$date = preg_split('/\s+/', trim($lines[$index + 1]));

					$openInterest = str_replace(',', '', preg_split('/\s+/', trim($lines[$index + 7])));

					$openInterestChange = str_replace(',', '', preg_split('/\s+/', trim($lines[$index + 11])));

					$current = str_replace(',', '', preg_split('/\s+/', trim($lines[$index + 9])));

					$changes = str_replace(',', '', preg_split('/\s+/', trim($lines[$index + 12])));

					$percent = preg_split('/\s+/', trim($lines[$index + 15]));

					$result[$symbol] = [
						'slug' => $slug,
						'symbol' => $symbol,
						'date' => date('Y-m-d', strtotime($date[5])),
						'current' => [
							'non-commercial' => [
								'long' => (int) $current[0],
								'short' => (int) $current[1],
								'spreads' => (int) $current[2],
							],
							'commercial' => [
								'long' => (int) $current[3],
								'short' => (int) $current[4],
							],
						],
						'changes' => [
							'non-commercial' => [
								'long' => (int) $changes[0],
								'short' => (int) $changes[1],
								'spreads' => (int) $changes[2],
							],
							'commercial' => [
								'long' => (int) $changes[3],
								'short' => (int) $changes[4],
							],
						],
						'open-interest' => [
							'current' => (int) end($openInterest),
							'change' => (int) rtrim(end($openInterestChange), ')'),
							'non-commercial' => [
								'long' => (float) $percent[0],
								'short' => (float) $percent[1],
								'spreads' => (float) $percent[2],
							],
							'commercial' => [
								'long' => (float) $percent[3],
								'short' => (float) $percent[4],
							],
						],
					];
				}

				continue;
			}
		}

		return $result;
	}

	private function startsWith($haystack, $needle)
	{
	     $length = strlen($needle);

	     return (substr($haystack, 0, $length) === $needle);
	}

	private function fetchAndParse($url)
	{
		$response = $this->client->get($url);

		if ($response->getStatusCode() != 200) {
			throw new ResponseException("URL was returned with code: " . $response->getStatusCode());
		}

        $parsed = $this->parse((string) $response->getBody());
        
        return $parsed;	
	}
}