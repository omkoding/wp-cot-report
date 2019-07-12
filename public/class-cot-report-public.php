<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.omkoding.com/
 * @since      1.0.0
 *
 * @package    Cot_Report
 * @subpackage Cot_Report/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cot_Report
 * @subpackage Cot_Report/public
 * @author     Om Koding <omkodingdotcom@gmail.com>
 */

use OmKoding\Cot\Symbol;

class Cot_Report_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style('cot', plugin_dir_url( __FILE__ ) . 'css/cot-report-public.css', [], $this->version);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script('vue', '//cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js');
		wp_register_script('cot', plugin_dir_url( __FILE__ ) . 'js/cot-report-public.js', ['vue'], $this->version, true);
	}

	public function add_query_vars_filter($vars) {
		$vars[] = "cot-symbol";
		$vars[] = "cot-year";

	  	return $vars;
	}

	public function loadReports($symbol = 'EURO_FX', $year) {
		global $wpdb;

		if (! in_array($symbol, array_keys(Symbol::all()))) {
			$symbol = 'EURO_FX';
		}

		if (! ($year >= "2005" && $year <= date('Y'))) {
			$year = date('Y');
		}

		$table = $wpdb->prefix . 'cot';

		$query = $wpdb->get_results($wpdb->prepare("
		    SELECT * FROM {$table}
		    WHERE symbol = %s AND YEAR(date) = %s
		    ORDER BY date DESC
		", $symbol, $year));

		$reports = collect($query)->map(function ($item) {
		    return [
		        'id' => (int) $item->id,
		        'date' => $item->date,
		        'symbol' => $item->symbol,
		        'current_nc_long' => (float) $item->current_nc_long,
		        'current_nc_short' => (float) $item->current_nc_short,
		        'current_nc_spreads' => (float) $item->current_nc_spreads,
		        'current_c_long' => (float) $item->current_c_long,
		        'current_c_short' => (float) $item->current_c_short,
		        'changes_nc_long' => (float) $item->changes_nc_long,
		        'changes_nc_short' => (float) $item->changes_nc_short,
		        'changes_nc_spreads' => (float) $item->changes_nc_spreads,
		        'changes_c_long' => (float) $item->changes_c_long,
		        'changes_c_short' => (float) $item->changes_c_short,
		        'oi_current' => (float) $item->oi_current,
		        'oi_change' => (float) $item->oi_change,
		        'oi_nc_long' => $item->oi_nc_long,
		        'oi_nc_short' => $item->oi_nc_short,
		        'oi_nc_spreads' => $item->oi_nc_spreads,
		        'oi_c_long' => $item->oi_c_long,
		        'oi_c_short' => $item->oi_c_short,
		        'created_at' => $item->created_at,
		    ];
		})->all();

		return [
			'data' => $reports,
			'symbol' => $symbol,
			'year' => $year,
		];
	}

	public function add_shortcode($attr) {
		ob_start();
		
		require_once plugin_dir_path( __FILE__ ) . 'cot-report-shortcode-template.php';
		
		$result = ob_get_clean();

		wp_enqueue_script('cot');

		$symbols = Symbol::all();

		$reports = $this->loadReports(
			get_query_var('cot-symbol', 'EURO_FX'),
			get_query_var('cot-year', date('Y'))
		);

		wp_localize_script('cot', 'cot', [
			'page_url' => wp_get_canonical_url(),
		    'symbols' => $symbols,
		    'reports' => $reports['data'],
		    'symbol' => $reports['symbol'],
		    'year' => $reports['year'],
		]);

		wp_enqueue_style('cot');

		return $result;
	}
}
