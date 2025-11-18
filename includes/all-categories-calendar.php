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

	// if (false !== $output) {
	// 	return $output;
	// }

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

	// Render the page as one big table
?>
	<div class="vs-all-categories-calendar">
		<h1 class="page-title">Sowing Calendars</h1>
		<p class="page-description">View sowing, planting, and harvesting times for all seed categories.</p>

		<div class="vs-calendar summary">
			<table class="sowing-calendar sowing-calendar-all">
				<thead>
					<tr>
						<th class="calendar-label">Category</th>
						<th class="calendar-label">Month</th>
						<th colspan="2" class="calendar-label--month" title="January">J</th>
						<th colspan="2" class="calendar-label--month" title="February">F</th>
						<th colspan="2" class="calendar-label--month" title="March">M</th>
						<th colspan="2" class="calendar-label--month" title="April">A</th>
						<th colspan="2" class="calendar-label--month" title="May">M</th>
						<th colspan="2" class="calendar-label--month" title="June">J</th>
						<th colspan="2" class="calendar-label--month" title="July">J</th>
						<th colspan="2" class="calendar-label--month" title="August">A</th>
						<th colspan="2" class="calendar-label--month" title="September">S</th>
						<th colspan="2" class="calendar-label--month" title="October">O</th>
						<th colspan="2" class="calendar-label--month" title="November">N</th>
						<th colspan="2" class="calendar-label--month" title="December">D</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$first_category = true;
					foreach ($categories_with_calendar as $category) :
						$category_link = get_term_link($category);
						$post_id = "term_" . $category->term_id;

						// Get calendar data for this category
						$sow_months = get_field('vs_calendar_sow_month_parts', $post_id);
						$plant_months = get_field('vs_calendar_plant_month_parts', $post_id);
						$harvest_months = get_field('vs_calendar_harvest_month_parts', $post_id);

						// Generate row cells
						$sow_row = get_vs_calendar_row_cells('sow', $sow_months);
						$plant_row = get_vs_calendar_row_cells('plant', $plant_months);
						$harvest_row = get_vs_calendar_row_cells('harvest', $harvest_months);

						// Count non-empty rows
						$rows = array_filter([$sow_row, $plant_row, $harvest_row]);
						$row_count = count($rows);

						// Add separator row between categories (bold line)
						if (!$first_category) {
							echo '<tr class="category-separator"><td colspan="26"></td></tr>';
						}
						$first_category = false;

						// Sow row
						if (!empty($sow_row)) :
					?>
							<tr>
								<?php if ($row_count > 0) : ?>
									<td class="calendar-label category-name" rowspan="<?php echo $row_count; ?>">
										<a href="<?php echo esc_url($category_link); ?>">
											<?php echo esc_html($category->name); ?>
										</a>
									</td>
								<?php endif; ?>
								<td class="calendar-label">Sow</td>
								<?php echo $sow_row; ?>
							</tr>
						<?php endif; ?>

						<?php if (!empty($plant_row)) : ?>
							<tr>
								<td class="calendar-label">Plant</td>
								<?php echo $plant_row; ?>
							</tr>
						<?php endif; ?>

						<?php if (!empty($harvest_row)) : ?>
							<tr>
								<td class="calendar-label">Harvest</td>
								<?php echo $harvest_row; ?>
							</tr>
					<?php
						endif;
					endforeach;
					?>
				</tbody>
			</table>
		</div>
	</div>

	<style>
		.vs-all-categories-calendar {
			max-width: 1400px;
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

		.sowing-calendar-all {
			width: 100%;
		}

		.sowing-calendar-all .category-name {
			font-weight: bold;
			vertical-align: middle;
			text-align: left;
			padding: 0.5rem;
			min-width: 150px;
		}

		.sowing-calendar-all .category-name a {
			color: #333;
			text-decoration: none;
		}

		.sowing-calendar-all .category-name a:hover {
			color: #118800;
			text-decoration: underline;
		}

		.sowing-calendar-all .category-separator {
			height: 3px;
		}

		.sowing-calendar-all .category-separator td {
			border-top: 3px solid #333;
			padding: 0;
			height: 3px;
		}

		@media (max-width: 768px) {
			.vs-all-categories-calendar {
				padding: 1rem;
			}

			.page-title {
				font-size: 1.5rem;
			}

			.vs-calendar.summary {
				overflow-x: auto;
			}
		}
	</style>
<?php

	$output = ob_get_clean();

	// Cache for 24 hours
	set_transient($cache_key, $output, 24 * HOUR_IN_SECONDS);

	return $output;
}
