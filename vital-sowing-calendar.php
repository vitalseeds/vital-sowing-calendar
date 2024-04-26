<?php

/*
Plugin Name:  Vital Sowing Calendar
Plugin URI:   https://github.com/vitalseeds/vital-sowing-calendar
Description:  Sowing, planting and harvesting calendar for Wordpress. Requires Advanced Custom Fields (ACF).
Version:      2.0
Author:       tombola
Author URI:   https://github.com/tombola
License:      GPL2
License URI:  https://github.com/vitalseeds/vital-sowing-calendar/blob/main/LICENSE
Text Domain:  vital-sowing-calendar
Domain Path:  /languages
*/

// function my_theme_enqueue_styles()
// {

// 	$parent_style = 'storefront-style';

// 	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
// 	wp_enqueue_style(
// 		'child-style',
// 		get_stylesheet_directory_uri() . '/style.css',
// 		array($parent_style),
// 		wp_get_theme()->get('Version')
// 	);
// }
// add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

wp_register_style('calendar-styles', plugin_dir_url(__FILE__) . 'css/calendar.css');

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);


// ACF helper functions

function acf_enabled()
{
	return function_exists('get_field');
}

if (acf_enabled()) {
	// Add the ACF field group for the sowing calendar
	require_once('includes/acf/fields/acf-seed-calendar.php');
	function get_group_field(string $group, string $field, $post_id = 0)
	{
		$group_data = get_field($group, $post_id);
		if (is_array($group_data) && array_key_exists($field, $group_data)) {
			return $group_data[$field];
		}
	}
	function get_group_field_int(string $group, string $field, $post_id = 0)
	{
		return intval(get_group_field($group, $field, $post_id)) ?: null;
	}
} else {
	function vital_calendar_admin_notice()
	{
		echo // Customize the message below as needed
		'<div class="notice notice-warning is-dismissible">
		<p>Vital Sowing calendar will not display unless Advanced Custom Fields plugin is installed.</p>
		</div>';
	}
	add_action('admin_notices', 'vital_calendar_admin_notice');
}


/**
 * Returns a row of cells for a calendar table.
 *
 * Expects the start and end date fields to be in the format 'dd/mm/yyyy'.
 *
 * @param string $action		eg sow, plant, harvest
 * @param string $start_month	the start date field
 * @param string $end_month		the end date field
 * @return string				the row of cells (td elements)
 */
function get_vs_calendar_row_cells(
	$action,
	$start_month,
	$end_month
) {
	if (!$start_month || !$end_month) {
		return '';
	}
	$row = [];
	for ($i = 1; $i <= 12; $i++) {
		$month = date('F', mktime(0, 0, 0, $i, 10));
		$month_class = strtolower($month);
		$hl_class = '';
		if ($i >= $start_month && $i <= $end_month) {
			$hl_class .= ' highlight highlight-' . $action;
			$title = "title='$action in $month'";
		}
		$row[] = "<td class='$month_class $hl_class' $title></td>";
	}
	return implode("\n", $row);
}


/**
 * Displays a sowing calendar for the product.
 */
function vs_sowing_calendar()
{
	if (!acf_enabled()) return;

	// TODO: get_field does not yet show current value in preview
	// https://support.advancedcustomfields.com/forums/topic/preview-with-acf-fields-are-incorrect
	if (!get_field('sow_months') && !get_field('plant_months') && !get_field('harvest_months')) {
		return;
	}
	wp_enqueue_style('calendar-styles');

	$args = array(
		'sowing_row' => @get_vs_calendar_row_cells(
			'sow',
			get_group_field('sow_months', 'start_month'),
			get_group_field('sow_months', 'end_month')
		),
		'plant_row' => @get_vs_calendar_row_cells(
			'plant',
			get_group_field('plant_months', 'start_month'),
			get_group_field('plant_months', 'end_month')
		),
		'harvest_row' => @get_vs_calendar_row_cells(
			'harvest',
			get_group_field('harvest_months', 'start_month'),
			get_group_field('harvest_months', 'end_month')
		),
	);
	// TODO: use a template loader to make this themeable
	// get_template_part('sowing-calendar', null, $args);
	include('includes/sowing-calendar.php');
}

add_action('woocommerce_after_single_product_summary', 'vs_sowing_calendar', 3);


// function default_start_month($value, $post_id, $field)
// {
// 	if ($value) return $value;

// 	$product = wc_get_product($post_id);
// 	$cats = wp_get_post_terms($product->id, 'product_cat');

// 	// Use last category, assumption that the last category is the most specific
// 	$cat = $cats[array_key_last($cats)];
// 	// Get the field value from the category if it exists
// 	if (str_contains($cat->slug, 'seed') && $default = get_field($field['name'], $cat)) {
// 		return $default;
// 	}
// 	// TODO: this currently overwrites the value on the product edit page,
// 	// which means after save no longer inherits from the category
// 	return $field;
// }


// add_filter('acf/load_value/name=start_month', 'default_start_month', 10, 3);
