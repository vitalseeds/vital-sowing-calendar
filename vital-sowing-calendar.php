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


function vital_calendar_enqueue_styles()
{
	wp_enqueue_style('calendar-styles');
}
add_action('wp_enqueue_scripts', 'vital_calendar_enqueue_styles');


remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

define('VITAL_SOWING_CALENDAR_INHERIT_CATEGORY', 1);

define('VITAL_CALENDAR_FIELDS', array(
	'enable_sowing_calendar' => "field_664377a727bda",

	'vs_calendar_sow_month_parts' => 'field_664343b5fb761',
	'vs_calendar_plant_month_parts' => 'field_66436615d6cc8',
	'vs_calendar_harvest_month_parts' => 'field_66436639d6cc9',

	'vs_calendar_other1_month_parts' => 'field_6643685092a3f',
	'vs_calendar_other1_label' => 'field_664368ca92a41',
	'vs_calendar_other2_month_parts' => 'field_664368a492a40',
	'vs_calendar_other2_label' => 'field_6643690f92a42',
));

define('VITAL_MONTH_CHOICES', array(
	'jan1' => 'Jan',
	'jan2' => 'Jan',
	'feb1' => 'Feb',
	'feb2' => 'Feb',
	'mar1' => 'Mar',
	'mar2' => 'Mar',
	'apr1' => 'Apr',
	'apr2' => 'Apr',
	'may1' => 'May',
	'may2' => 'May',
	'jun1' => 'Jun',
	'jun2' => 'Jun',
	'jul1' => 'Jul',
	'jul2' => 'Jul',
	'aug1' => 'Aug',
	'aug2' => 'Aug',
	'sep1' => 'Sep',
	'sep2' => 'Sep',
	'oct1' => 'Oct',
	'oct2' => 'Oct',
	'nov1' => 'Nov',
	'nov2' => 'Nov',
	'dec1' => 'Dec',
	'dec2' => 'Dec',
));

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
 * @param string $month	the start date field
 * @param string $end_month		the end date field
 * @return string				the row of cells (td elements)
 */
function get_vs_calendar_row_cells(
	$action,
	$selected_months,
) {
	if (!$selected_months) {
		return '';
	}
	$row = [];
	foreach (VITAL_MONTH_CHOICES as $key => $month_name) {
		$month_class = strtolower($month_name);
		$hl_class = '';
		if (in_array($key, $selected_months)) {
			$hl_class .= ' highlight highlight-' . $action;
			$title = "title='$action in $month_name'";
		}
		$row[] = "<td class='$month_class $hl_class' $title></td>";
	}
	return implode("\n", $row);
}


/**
 * Displays a sowing calendar for the product.
 */
function vs_sowing_calendar($post_id = false)
{
	if (!acf_enabled()) return;

	// TODO: get_field does not yet show current value in preview
	// https://support.advancedcustomfields.com/forums/topic/preview-with-acf-fields-are-incorrect

	if (!get_field('enable_sowing_calendar', $post_id)) {
		return;
	}
	// If no months are set, don't display the calendar
	if (
		!get_field('vs_calendar_sow_month_parts', $post_id) &&
		!get_field('vs_calendar_plant_month_parts', $post_id) &&
		!get_field('vs_calendar_harvest_month_parts', $post_id)
	) {
		return;
	}

	$args = array(
		'sowing_row' => @get_vs_calendar_row_cells(
			'sow',
			get_field('vs_calendar_sow_month_parts', $post_id),
		),
		'plant_row' => @get_vs_calendar_row_cells(
			'plant',
			get_field('vs_calendar_plant_month_parts', $post_id),
		),
		'harvest_row' => @get_vs_calendar_row_cells(
			'harvest',
			get_field('vs_calendar_harvest_month_parts', $post_id),
		),
	);
	// TODO: use a template loader to make this themeable
	// get_template_part('sowing-calendar', null, $args);
	include('includes/sowing-calendar.php');
}

add_action('woocommerce_after_single_product_summary', 'vs_sowing_calendar', 3);
add_action('woocommerce_before_main_content', function () {
	if (is_product_category()) {
		$term = get_queried_object();

		// get_field('enable_sowing_calendar', "term_$term->term_id");
		// get_field('vs_calendar_sow_month_parts', "term_$term->term_id");
		vs_sowing_calendar("term_$term->term_id");
	}
});


function get_field_value_from_category($value, $post_id, $field)
{
	// Don't override existing product values
	if ($value || is_product_category()) return $value;

	if (is_product()) {
		$product = wc_get_product($post_id);
		// Get the category of the product
		// TODO: get and cache all custom fields for the category at once?
		$cats = wp_get_post_terms($product->id, 'product_cat');
		// Use last category, assumption that the last category is the most specific
		$cat = $cats[array_key_last($cats)];
		// Get the ACF field value from the category (if it exists)
		// if (str_contains($cat->slug, 'seed') && $default = get_field($field['name'], $cat)) {
		if ($default = get_field($field['name'], $cat)) {
			if (!is_array($default)) return $default;
		}
	}

	return $value;
}

if (VITAL_SOWING_CALENDAR_INHERIT_CATEGORY && !is_admin()) {
	// Default each calendar field value to that of the category
	// Uses field keys instead of names to prevent conflicts
	foreach (VITAL_CALENDAR_FIELDS as $field_name => $field_key) {
		add_filter("acf/load_value/key=$field_key", 'get_field_value_from_category', 10, 3);
	}
}

function my_acf_admin_head()
{ ?>
	<style type="text/css">
		<?php include('includes/admin-styles.css'); ?>
	</style>
<?php
}

// ACF admin tweaks inspired :
// https://devmaverick.com/how-to-add-basic-style-to-the-advanced-custom-fields-acf-back-end/
// https://codepen.io/steelwater/pen/BjeZQx

add_action('acf/input/admin_head', 'my_acf_admin_head');
