<?php

/**
 * Sowing Calendar Admin Meta Box
 *
 * Adds a meta box to the product edit screen showing whether
 * calendar data is defined for the product.
 *
 * @package VitalSowingCalendar
 */

/**
 * Displays the content of the "Sowing Calendar" meta box on product edit screen.
 *
 * Shows whether calendar data is defined for the product, and if so,
 * whether it comes from the product itself or is inherited from a category.
 *
 * @param WP_Post $post The current post object.
 */
function display_sowing_calendar_link($post)
{
	if (!acf_enabled()) {
		echo '<p>' . __('ACF is required to display sowing calendar data.', 'vital-sowing-calendar') . '</p>';
		return;
	}

	// Check if product has its own calendar data
	$product_sow = get_field('vs_calendar_sow_month_parts', $post->ID);
	$product_plant = get_field('vs_calendar_plant_month_parts', $post->ID);
	$product_harvest = get_field('vs_calendar_harvest_month_parts', $post->ID);

	$has_product_calendar = (is_array($product_sow) && !empty($product_sow)) ||
		(is_array($product_plant) && !empty($product_plant)) ||
		(is_array($product_harvest) && !empty($product_harvest));

	if ($has_product_calendar) {
		echo '<p><span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span> ' . __('Calendar defined', 'vital-sowing-calendar') . '</p>';
		echo '<p><em>' . __('Calendar data is specified on the <strong>product</strong>, so it overrides any category calendar.', 'vital-sowing-calendar') . '</em></p>';
		return;
	}

	// Check categories for calendar data
	$terms = get_the_terms($post->ID, 'product_cat');
	if ($terms && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			$cat_sow = get_field('vs_calendar_sow_month_parts', 'product_cat_' . $term->term_id);
			$cat_plant = get_field('vs_calendar_plant_month_parts', 'product_cat_' . $term->term_id);
			$cat_harvest = get_field('vs_calendar_harvest_month_parts', 'product_cat_' . $term->term_id);

			$has_category_calendar = (is_array($cat_sow) && !empty($cat_sow)) ||
				(is_array($cat_plant) && !empty($cat_plant)) ||
				(is_array($cat_harvest) && !empty($cat_harvest));

			if ($has_category_calendar) {
				echo '<p><span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span> ';
				echo '<a href="' . get_edit_term_link($term->term_id, 'product_cat') . '" target="_blank">' . esc_html($term->name) . '</a>';
				echo '</p>';
				echo '<p><em>' . __('Calendar is inherited from <strong>category</strong> and not overridden by product.', 'vital-sowing-calendar') . '</em></p>';
				return;
			}
		}
	}

	// No calendar found
	echo '<p><span class="dashicons dashicons-warning" style="color: #dba617;"></span> ' . __('No sowing calendar defined.', 'vital-sowing-calendar') . '</p>';
	echo '<p><em>' . __('No calendar is specified for either category or product, so no calendar will be shown.', 'vital-sowing-calendar') . '</em></p>';
}

/**
 * Adds the Sowing Calendar meta box to the product edit screen.
 */
add_action('add_meta_boxes', function () {
	add_meta_box(
		'sowing_calendar_link',
		__('Sowing Calendar', 'vital-sowing-calendar'),
		'display_sowing_calendar_link',
		'product',
		'side',
		'high'
	);
});
