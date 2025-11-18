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

function vital_calendar_enqueue_styles()
{
	wp_register_style('calendar-styles', plugin_dir_url(__FILE__) . 'css/calendar.css');
	wp_enqueue_style('calendar-styles');
}
add_action('wp_enqueue_scripts', 'vital_calendar_enqueue_styles');


remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

define('VITAL_SOWING_CALENDAR_INHERIT_CATEGORY', 1);

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

// Map abbreviated month to full month name
define('VITAL_MONTH_FULL_NAMES', array(
	'jan' => 'January',
	'feb' => 'February',
	'mar' => 'March',
	'apr' => 'April',
	'may' => 'May',
	'jun' => 'June',
	'jul' => 'July',
	'aug' => 'August',
	'sep' => 'September',
	'oct' => 'October',
	'nov' => 'November',
	'dec' => 'December',
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
		$title = '';
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
	if (!$post_id && is_product()) {
		$post_id = get_the_ID();
	}


	// TODO: get_field does not yet show current value in preview
	// https://support.advancedcustomfields.com/forums/topic/preview-with-acf-fields-are-incorrect

	// $enabled = get_field('enable_sowing_calendar', $post_id);
	// if (!$enabled && !is_null($enabled)) return;

	// If no months are set, don't display the calendar
	// $vs_calendar_sow_month_parts = get_field('vs_calendar_sow_month_parts', $post_id);
	// $vs_calendar_plant_month_parts = get_field('vs_calendar_plant_month_parts', $post_id);
	// $vs_calendar_harvest_month_parts = get_field('vs_calendar_harvest_month_parts', $post_id);
	$vs_calendar_sow_month_parts = get_value_from_field_or_category('vs_calendar_sow_month_parts', $post_id);
	$vs_calendar_plant_month_parts = get_value_from_field_or_category('vs_calendar_plant_month_parts', $post_id);
	$vs_calendar_harvest_month_parts = get_value_from_field_or_category('vs_calendar_harvest_month_parts', $post_id);

	if (
		!$vs_calendar_sow_month_parts &&
		!$vs_calendar_plant_month_parts &&
		!$vs_calendar_harvest_month_parts
	) {
		return;
	}

	$args = array(
		'sowing_row' => @get_vs_calendar_row_cells(
			'sow',
			$vs_calendar_sow_month_parts,
		),
		'plant_row' => @get_vs_calendar_row_cells(
			'plant',
			$vs_calendar_plant_month_parts,
		),
		'harvest_row' => @get_vs_calendar_row_cells(
			'harvest',
			$vs_calendar_harvest_month_parts,
		),
	);
	// TODO: use a template loader to make this themeable
	// get_template_part('sowing-calendar', null, $args);
	include('includes/sowing-calendar.php');
}


// Moved to the theme
// add_action('woocommerce_after_single_product_summary', 'vs_sowing_calendar', 3);
// add_action('woocommerce_archive_description', function () {
// 	if (is_product_category()) {
// 		$term = get_queried_object();
// 		echo "<h4>Growing calendar</h4>";
// 		vs_sowing_calendar("term_$term->term_id");
// 	}
// }, 10);


function get_value_from_field_or_category($field_name, $post_id)
{
	$value = get_field($field_name, $post_id);
	if (VITAL_SOWING_CALENDAR_INHERIT_CATEGORY) {
		$value = get_field_value_from_category($value, $post_id, $field_name);
	}
	return $value;
};

function get_field_value_from_category($value, $post_id, $field)
{
	// Don't override existing product values
	if ($value || is_product_category()) return $value;


	if (is_product() || wc_get_product($post_id)) {
		$product = wc_get_product($post_id);
		// Get the category of the product
		$cats = wp_get_post_terms($product->get_id(), 'product_cat');
		// Use last category, assumption that the last category is the most specific
		$cat = $cats[array_key_last($cats)];
		// If called direct, rather than from ACF hook, the field will just be name
		$field_name = is_array($field) ? $field['name'] : $field;
		// Get the ACF field value from the category (if it exists)
		if ($default = get_field($field_name, $cat)) {
			return $default;
		}
	}

	return $value;
}

// ACF admin tweaks inspired by:
// https://devmaverick.com/how-to-add-basic-style-to-the-advanced-custom-fields-acf-back-end/
// https://codepen.io/steelwater/pen/BjeZQx


function vital_acf_admin_head()
{
?>
	<style type="text/css">
		<?php include('includes/admin-styles.css'); ?>
	</style>
<?php
}
add_action('acf/input/admin_head', 'vital_acf_admin_head');


function products_by_month($month_parts) {
	// Query all products under the 'Seeds' category
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => 'seeds',
			),
		),
	);
	$query = new WP_Query($args);
	$month_products = [];
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();
			$sow_months = get_value_from_field_or_category('vs_calendar_sow_month_parts', $post_id);
			// Check if the product can be sown in any of the given months
			if (is_array($sow_months) && array_intersect($month_parts, $sow_months)) {
				$month_product_ids[] = $post_id;
			}
		}
		wp_reset_postdata();
	} else {
		return '';
	}
	ob_start();
	get_template_part('parts/calendar', 'month', array(
		'product_ids' => $month_product_ids,
		'show_images' => true,
	));
	return ob_get_clean();
}

function categories_by_month($month_parts) {
	// Query all categories under the 'Seeds' category
	$seed_category = get_term_by('slug', 'seeds', 'product_cat');
	if (!$seed_category) {
		return '';
	}

	$args = array(
		'taxonomy'   => 'product_cat',
		'child_of'   => $seed_category->term_id,
		'hide_empty' => false,
	);
	$categories = get_categories($args);
	$month_categories = [];

	foreach ($categories as $category) {
		$sow_months = get_field('vs_calendar_sow_month_parts', 'product_cat_' . $category->term_id);
		// Check if the category can be sown in any of the given months
		if (is_array($sow_months) && array_intersect($month_parts, $sow_months)) {
			$month_categories[] = $category;
		}
	}

	if (empty($month_categories)) {
		return '';
	}

	ob_start();
	get_template_part('parts/calendar', 'month-categories', array(
		'categories' => $month_categories,
		'show_images' => true,
	));
	return ob_get_clean();
}

/**
 * Shortcode to display seeds that can be sown in a specified month.
 *
 * Usage: [sow_by_month month="jan"]
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function sow_by_month_shortcode($atts, $type = 'product') {
	// $cache_disabled = true;
	$cache_disabled = false;
	$atts = shortcode_atts(array(
		'month' => '',
	), $atts);
	$month = strtolower(trim($atts['month']));
	$cache_key = 'sow_by_month__' . $month;
	$output = get_transient($cache_key);

	if (false === $output || $cache_disabled) {
		$month_parts = [$month . "1", $month . "2"];
		$valid_month_parts = array_intersect($month_parts, array_keys(VITAL_MONTH_CHOICES));
		if (empty($valid_month_parts)) {
			return '<p>Please provide valid months (e.g., jan, feb, etc.) as the "months" attribute, separated by commas.</p>';
		}
		$month_full_name = isset(VITAL_MONTH_FULL_NAMES[$month]) ? VITAL_MONTH_FULL_NAMES[$month] : ucfirst($month);
		$output = "<h2>Seeds to sow in $month_full_name</h2>";

		if ($type === 'product') {
			$output = products_by_month($valid_month_parts);
		} elseif ($type === 'category') {
			$output = categories_by_month($valid_month_parts);
		}

		// Cache for 24 hours
		set_transient($cache_key, $output, 24 * HOUR_IN_SECONDS);
		return $output;
	}

	return $output;
}

function product_by_month_shortcode($atts) {
	return sow_by_month_shortcode($atts, 'product');
}

function category_by_month_shortcode($atts) {
	return sow_by_month_shortcode($atts, 'category');
}

add_shortcode('sow_by_month', 'category_by_month_shortcode');

/**
 * All Categories Calendar View
 * Custom endpoint for displaying all seed category sowing calendars
 */

// Include the all-categories calendar rendering function
require_once plugin_dir_path(__FILE__) . 'includes/all-categories-calendar.php';

/**
 * Add custom rewrite rule for /sowing-calendars/ endpoint
 */
function vs_add_sowing_calendars_rewrite_rule()
{
	add_rewrite_rule(
		'^sowing-calendars/?$',
		'index.php?vs_sowing_calendars=1',
		'top'
	);
}
add_action('init', 'vs_add_sowing_calendars_rewrite_rule');

/**
 * Register custom query var for sowing calendars endpoint
 */
function vs_add_sowing_calendars_query_var($vars)
{
	$vars[] = 'vs_sowing_calendars';
	return $vars;
}
add_filter('query_vars', 'vs_add_sowing_calendars_query_var');

/**
 * Load custom template for sowing calendars endpoint
 */
function vs_sowing_calendars_template_redirect($template)
{
	if (get_query_var('vs_sowing_calendars')) {
		$custom_template = plugin_dir_path(__FILE__) . 'templates/all-categories-calendar.php';

		if (file_exists($custom_template)) {
			return $custom_template;
		}
	}

	return $template;
}
add_filter('template_include', 'vs_sowing_calendars_template_redirect');

/**
 * Flush rewrite rules on plugin activation
 */
function vs_activate_sowing_calendar()
{
	vs_add_sowing_calendars_rewrite_rule();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'vs_activate_sowing_calendar');

/**
 * Flush rewrite rules on plugin deactivation
 */
function vs_deactivate_sowing_calendar()
{
	flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'vs_deactivate_sowing_calendar');
