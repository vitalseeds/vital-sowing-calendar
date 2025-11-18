<?php
/**
 * All Categories Sowing Calendar View
 *
 * Renders a page displaying all seed category sowing calendars in a stacked list format.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Render all seed categories with their sowing calendars
 *
 * Displays a stacked list of all seed categories (children of term ID 276)
 * that have sowing calendar data. Each category shows:
 * - Category name (linked to category archive)
 * - Full sowing calendar table
 *
 * Uses transient caching for 24 hours to improve performance.
 *
 * @return string HTML output
 */
function vs_render_all_categories_calendar()
{
	// Check for cached version
	$cache_key = 'vs_all_categories_calendar';
	$output = get_transient($cache_key);

	if (false !== $output) {
		return $output;
	}

	// Start output buffering
	ob_start();

	// Get all seed categories (children of Seeds parent term ID 276)
	$args = array(
		'taxonomy'   => 'product_cat',
		'child_of'   => 276,  // Seeds parent ID
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);

	$categories = get_categories($args);

	if (empty($categories)) {
		echo '<p>No seed categories found.</p>';
		return ob_get_clean();
	}

	// Filter categories that have calendar data
	$categories_with_calendar = array();

	foreach ($categories as $category) {
		$sow_months = get_field('vs_calendar_sow_month_parts', 'product_cat_' . $category->term_id);
		$plant_months = get_field('vs_calendar_plant_month_parts', 'product_cat_' . $category->term_id);
		$harvest_months = get_field('vs_calendar_harvest_month_parts', 'product_cat_' . $category->term_id);

		// Check if any calendar data exists
		$has_calendar = (is_array($sow_months) && !empty($sow_months)) ||
			(is_array($plant_months) && !empty($plant_months)) ||
			(is_array($harvest_months) && !empty($harvest_months));

		if ($has_calendar) {
			$categories_with_calendar[] = $category;
		}
	}

	if (empty($categories_with_calendar)) {
		echo '<p>No seed categories with sowing calendar data found.</p>';
		return ob_get_clean();
	}

	// Render the page
?>
	<div class="vs-all-categories-calendar">
		<h1 class="page-title">Sowing Calendars</h1>
		<p class="page-description">View sowing, planting, and harvesting times for all seed categories.</p>

		<div class="categories-calendar-list">
			<?php foreach ($categories_with_calendar as $category) :
				$category_link = get_term_link($category);
				$post_id = "term_" . $category->term_id;
			?>
				<div class="category-calendar-item">
					<h2 class="category-calendar-title">
						<a href="<?php echo esc_url($category_link); ?>">
							<?php echo esc_html($category->name); ?>
						</a>
					</h2>

					<div class="category-calendar-content">
						<?php vs_sowing_calendar($post_id); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<style>
		.vs-all-categories-calendar {
			max-width: 1200px;
			margin: 0 auto;
			padding: 2rem;
		}

		.page-title {
			font-size: 2rem;
			margin-bottom: 0.5rem;
		}

		.page-description {
			color: #666;
			margin-bottom: 2rem;
		}

		.categories-calendar-list {
			display: flex;
			flex-direction: column;
			gap: 3rem;
		}

		.category-calendar-item {
			border: 1px solid #e0e0e0;
			border-radius: 8px;
			padding: 1.5rem;
			background: #fff;
		}

		.category-calendar-title {
			margin: 0 0 1rem 0;
			font-size: 1.5rem;
		}

		.category-calendar-title a {
			color: #333;
			text-decoration: none;
		}

		.category-calendar-title a:hover {
			color: #118800;
			text-decoration: underline;
		}

		.category-calendar-content {
			overflow-x: auto;
		}

		@media (max-width: 768px) {
			.vs-all-categories-calendar {
				padding: 1rem;
			}

			.page-title {
				font-size: 1.5rem;
			}

			.categories-calendar-list {
				gap: 2rem;
			}
		}
	</style>
<?php

	$output = ob_get_clean();

	// Cache for 24 hours
	set_transient($cache_key, $output, 24 * HOUR_IN_SECONDS);

	return $output;
}
