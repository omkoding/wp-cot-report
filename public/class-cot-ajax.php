<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use OmKoding\Cot\Report;
use OmKoding\Cot\Symbol;
use OmKoding\Cot\Exceptions\ResponseException;
use OmKoding\Cot\Exceptions\InvalidDateException;
use OmKoding\Cot\Exceptions\InvalidSymbolException;

class Cot_Ajax
{
	public function run()
	{
		$date = $_GET['date'] ?? null;

		$cotReport = new Report;

		try {
			if ($date) {
				$cotReport->validateDate($date);

				$report = $cotReport->byDate($date);
			} else {
				$report = $cotReport->latest();
			}

			$this->wpdb_bulk_insert('cot', $this->prepareDataForInsert($report));

			wp_send_json($report);
		} catch (ResponseException $e) {
			wp_send_json_error($e->getMessage(), 500);
		} catch (InvalidDateException $e) {
			wp_send_json_error($e->getMessage(), 422);
		} catch (InvalidSymbolException $e) {
			wp_send_json_error($e->getMessage(), 422);
		}		
	}

	private function prepareDataForInsert(array $data)
	{
		$map = array_map(function ($item) {
			return [
				'date' => $item['date'],
				'symbol' => $item['slug'],
				'current_nc_long' => $item['current']['non-commercial']['long'],
				'current_nc_short' => $item['current']['non-commercial']['short'],
				'current_nc_spreads' => $item['current']['non-commercial']['spreads'],
				'current_c_long' => $item['current']['commercial']['long'],
				'current_c_short' => $item['current']['commercial']['short'],
				'changes_nc_long' => $item['changes']['non-commercial']['long'],
				'changes_nc_short' => $item['changes']['non-commercial']['short'],
				'changes_nc_spreads' => $item['changes']['non-commercial']['spreads'],
				'changes_c_long' => $item['changes']['commercial']['long'],
				'changes_c_short' => $item['changes']['commercial']['short'],
				'oi_current' => $item['open-interest']['current'],
				'oi_change' => $item['open-interest']['change'],
				'oi_nc_long' => $item['open-interest']['non-commercial']['long'],
				'oi_nc_short' => $item['open-interest']['non-commercial']['short'],
				'oi_nc_spreads' => $item['open-interest']['non-commercial']['spreads'],
				'oi_c_long' => $item['open-interest']['commercial']['long'],
				'oi_c_short' => $item['open-interest']['commercial']['short'],
				'created_at' => date('Y-m-d'),
			];
		}, $data);

		return array_values($map);
	}

	private function wpdb_bulk_insert($table, $rows) {
		global $wpdb;
		
		// Extract column list from first row of data
		$columns = array_keys($rows[0]);

		asort($columns);
		
		$columnList = '`' . implode('`, `', $columns) . '`';
		
		// Start building SQL, initialise data and placeholder arrays
		$sql = "INSERT IGNORE INTO `$wpdb->prefix$table` ($columnList) VALUES\n";
		
		$placeholders = [];
		
		$data = [];

		// Build placeholders for each row, and add values to data array
		foreach ($rows as $row) {
			ksort($row);
		
			$rowPlaceholders = [];
		
			foreach ($row as $key => $value) {
				$data[] = $value;
		
				$rowPlaceholders[] = '%s';
			}
		
			$placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
		}
		
		// Stitch all rows together
		$sql .= implode(",\n", $placeholders);
		
		// Run the query.  Returns number of affected rows.
		return $wpdb->query($wpdb->prepare($sql, $data));
	}
}