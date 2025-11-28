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
 * Get current month parts (e.g., ['mar1', 'mar2'])
 *
 * @return array Array of month part strings for current month
 */
function vs_get_current_month_parts()
{
	$current_month = strtolower(date('M')); // 'jan', 'feb', etc.
	return [$current_month . '1', $current_month . '2'];
}

/**
 * Get top-level seed categories (direct children of Seeds term 276)
 *
 * @return array Array of WP_Term objects
 */
function vs_get_top_level_seed_categories()
{
	$args = array(
		'taxonomy'   => 'product_cat',
		'parent'     => 276,  // Direct children only (not child_of which gets all descendants)
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	return get_categories($args);
}

/**
 * Filter categories by action and month
 *
 * @param array $categories Array of category objects
 * @param string $action Action type ('sow', 'plant', or 'harvest')
 * @param array $month_parts Array of month parts (e.g., ['jan1', 'jan2'])
 * @return array Filtered array of categories
 */
function vs_filter_categories_by_action_month($categories, $action, $month_parts)
{
	if (empty($action) || empty($month_parts)) {
		return $categories;
	}

	// Determine which ACF field to check based on action
	$field_map = [
		'sow' => 'vs_calendar_sow_month_parts',
		'plant' => 'vs_calendar_plant_month_parts',
		'harvest' => 'vs_calendar_harvest_month_parts',
	];

	if (!isset($field_map[$action])) {
		return $categories;
	}

	$field_name = $field_map[$action];

	return array_filter($categories, function ($category) use ($field_name, $month_parts) {
		$category_months = get_field($field_name, 'product_cat_' . $category->term_id);
		return is_array($category_months) && array_intersect($month_parts, $category_months);
	});
}

/**
 * Render category name cell for calendar table
 *
 * @param object $category The category object
 * @param bool $is_first_row Whether this is the first row for this category
 * @return string HTML for the category name cell
 */
function vs_render_category_name_cell($category, $is_first_row)
{
	$category_link = get_term_link($category);
	ob_start();
?>
	<td class="calendar-label category-name">
		<?php if ($is_first_row) : ?>
			<a href="<?php echo esc_url($category_link); ?>">
				<?php echo esc_html($category->name); ?>
			</a>
		<?php endif; ?>
	</td>
<?php
	return ob_get_clean();
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
	// Get filter parameters from URL
	$filter_category = isset($_GET['filter_category']) ? sanitize_text_field($_GET['filter_category']) : '';
	$filter_action = isset($_GET['filter_action']) ? sanitize_text_field($_GET['filter_action']) : '';
	$filter_month = isset($_GET['filter_month']) ? sanitize_text_field($_GET['filter_month']) : '';

	// Check for cached version with filters
	$cache_key = 'vs_all_categories_calendar_' . md5(serialize($_GET));
	$output = get_transient($cache_key);

	// if (false !== $output) {
	// 	return $output;
	// }

	// Start output buffering
	ob_start();

	// Apply category filter if set
	if (!empty($filter_category)) {
		$category = get_term_by('slug', $filter_category, 'product_cat');
		if ($category) {
			// Get the selected category AND all its descendants
			$args = array(
				'taxonomy'   => 'product_cat',
				'child_of'   => $category->term_id,  // Get all descendants
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			);
			$categories = get_categories($args);
			// Add the parent category itself to the list
			array_unshift($categories, $category);
		} else {
			$categories = [];
		}
	} else {
		// Get all seed categories (children of Seeds parent term ID 276)
		$args = array(
			'taxonomy'   => 'product_cat',
			'child_of'   => 276,  // Seeds parent ID
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);
		$categories = get_categories($args);
	}

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

	// Apply action + month filter if set
	// Note: Both action AND month must be set for this filter to work
	// This is because we need to know which calendar field to check (sow/plant/harvest)
	// and which months to look for
	if (!empty($filter_action) && !empty($filter_month)) {
		// Convert month to month parts
		if ($filter_month === 'current') {
			$month_parts = vs_get_current_month_parts();
		} else {
			$month_parts = [$filter_month . '1', $filter_month . '2'];
		}

		$categories_with_calendar = vs_filter_categories_by_action_month(
			$categories_with_calendar,
			$filter_action,
			$month_parts
		);
	} elseif (!empty($filter_action)) {
		// If only action is set, show all categories that have that action defined
		$field_map = [
			'sow' => 'vs_calendar_sow_month_parts',
			'plant' => 'vs_calendar_plant_month_parts',
			'harvest' => 'vs_calendar_harvest_month_parts',
		];

		if (isset($field_map[$filter_action])) {
			$field_name = $field_map[$filter_action];
			$categories_with_calendar = array_filter($categories_with_calendar, function ($category) use ($field_name) {
				$months = get_field($field_name, 'product_cat_' . $category->term_id);
				return is_array($months) && !empty($months);
			});
		}
	} elseif (!empty($filter_month)) {
		// If only month is set, show all categories that have ANY action in that month
		if ($filter_month === 'current') {
			$month_parts = vs_get_current_month_parts();
		} else {
			$month_parts = [$filter_month . '1', $filter_month . '2'];
		}

		$categories_with_calendar = array_filter($categories_with_calendar, function ($category) use ($month_parts) {
			$sow = get_field('vs_calendar_sow_month_parts', 'product_cat_' . $category->term_id);
			$plant = get_field('vs_calendar_plant_month_parts', 'product_cat_' . $category->term_id);
			$harvest = get_field('vs_calendar_harvest_month_parts', 'product_cat_' . $category->term_id);

			return (is_array($sow) && array_intersect($month_parts, $sow)) ||
				(is_array($plant) && array_intersect($month_parts, $plant)) ||
				(is_array($harvest) && array_intersect($month_parts, $harvest));
		});
	}

	if (empty($categories_with_calendar)) {
		echo '<p>No seed categories found matching the selected filters.</p>';
		return ob_get_clean();
	}

	// Get top-level categories for dropdown
	$top_level_categories = vs_get_top_level_seed_categories();

	// Build active filters summary
	$active_filters = array();
	if (!empty($filter_category)) {
		$cat = get_term_by('slug', $filter_category, 'product_cat');
		if ($cat) {
			$active_filters[] = $cat->name;
		}
	}
	if (!empty($filter_action)) {
		$action_labels = ['sow' => 'Sow', 'plant' => 'Plant', 'harvest' => 'Harvest'];
		if (isset($action_labels[$filter_action])) {
			$active_filters[] = $action_labels[$filter_action];
		}
	}
	if (!empty($filter_month)) {
		$month_labels = [
			'current' => 'This Month',
			'jan' => 'January', 'feb' => 'February', 'mar' => 'March',
			'apr' => 'April', 'may' => 'May', 'jun' => 'June',
			'jul' => 'July', 'aug' => 'August', 'sep' => 'September',
			'oct' => 'October', 'nov' => 'November', 'dec' => 'December'
		];
		if (isset($month_labels[$filter_month])) {
			$active_filters[] = $month_labels[$filter_month];
		}
	}

	// Render the page as one big table
?>
	<div class="vs-all-categories-calendar">
		<h1 class="page-title">Sowing Calendars</h1>
		<p class="page-description">View sowing, planting, and harvesting times for all seed categories.</p>

		<!-- Filter Form -->
		<details class="growingguide vs-calendar-filters <?php echo !empty($active_filters) ? 'has-active-filters' : ''; ?>">
			<summary>
				<span class="filter-label">Filters</span><?php if (!empty($active_filters)) : ?><br><span class="active-filters-text"><?php echo esc_html(implode(' • ', $active_filters)); ?></span><?php endif; ?>
			</summary>
			<div>
		<form method="get" action="" class="calendar-filters">
			<div class="filter-row">
				<div class="filter-field">
					<label for="filter_category">Category:</label>
					<select name="filter_category" id="filter_category">
						<option value="">All Categories</option>
						<?php foreach ($top_level_categories as $cat) : ?>
							<option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($filter_category, $cat->slug); ?>>
								<?php echo esc_html($cat->name); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="filter-field">
					<label for="filter_action">Action:</label>
					<select name="filter_action" id="filter_action">
						<option value="">All Actions</option>
						<option value="sow" <?php selected($filter_action, 'sow'); ?>>Sow</option>
						<option value="plant" <?php selected($filter_action, 'plant'); ?>>Plant</option>
						<option value="harvest" <?php selected($filter_action, 'harvest'); ?>>Harvest</option>
					</select>
				</div>

				<div class="filter-field">
					<label for="filter_month">Month:</label>
					<select name="filter_month" id="filter_month">
						<option value="">All Months</option>
						<option value="current" <?php selected($filter_month, 'current'); ?>>This Month</option>
						<option value="jan" <?php selected($filter_month, 'jan'); ?>>January</option>
						<option value="feb" <?php selected($filter_month, 'feb'); ?>>February</option>
						<option value="mar" <?php selected($filter_month, 'mar'); ?>>March</option>
						<option value="apr" <?php selected($filter_month, 'apr'); ?>>April</option>
						<option value="may" <?php selected($filter_month, 'may'); ?>>May</option>
						<option value="jun" <?php selected($filter_month, 'jun'); ?>>June</option>
						<option value="jul" <?php selected($filter_month, 'jul'); ?>>July</option>
						<option value="aug" <?php selected($filter_month, 'aug'); ?>>August</option>
						<option value="sep" <?php selected($filter_month, 'sep'); ?>>September</option>
						<option value="oct" <?php selected($filter_month, 'oct'); ?>>October</option>
						<option value="nov" <?php selected($filter_month, 'nov'); ?>>November</option>
						<option value="dec" <?php selected($filter_month, 'dec'); ?>>December</option>
					</select>
				</div>

				<div class="filter-field">
					<button type="submit" class="button">Apply Filters</button>
					<a href="<?php echo esc_url(strtok($_SERVER['REQUEST_URI'], '?')); ?>" class="button">Clear</a>
				</div>
			</div>
		</form>
		</div></details>

		<div class="vs-calendar summary">
			<table class="sowing-calendar sowing-calendar-all">
				<thead>
					<tr>
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
						$post_id = "term_" . $category->term_id;

						// Get calendar data for this category
						$sow_months = get_field('vs_calendar_sow_month_parts', $post_id);
						$plant_months = get_field('vs_calendar_plant_month_parts', $post_id);
						$harvest_months = get_field('vs_calendar_harvest_month_parts', $post_id);

						// Generate row cells
						$sow_row = get_vs_calendar_row_cells('sow', $sow_months);
						$plant_row = get_vs_calendar_row_cells('plant', $plant_months);
						$harvest_row = get_vs_calendar_row_cells('harvest', $harvest_months);

						// Add separator row between categories (bold line)
						if (!$first_category) {
							echo '<tr class="category-separator"><td colspan="25"></td></tr>';
						}
						$first_category = false;

						// Category name header row
						$category_link = get_term_link($category);
						// Remove the 'Seeds' suffix to category names
						$category_display_name = preg_replace('/ Seeds$/i', '', $category->name);
					?>
						<tr class="category-header-row">
							<td colspan="25" class="category-header">
								<a href="<?php echo esc_url($category_link); ?>">
									<?php echo esc_html($category_display_name); ?>
								</a>
							</td>
						</tr>
					<?php

						// Determine which is the last row
						$has_sow = !empty($sow_row);
						$has_plant = !empty($plant_row);
						$has_harvest = !empty($harvest_row);

						$last_row = '';
						if ($has_harvest) $last_row = 'harvest';
						elseif ($has_plant) $last_row = 'plant';
						elseif ($has_sow) $last_row = 'sow';

						// Sow row
						if ($has_sow) :
					?>
							<tr<?php echo ($last_row === 'sow') ? ' class="last-category-row"' : ''; ?>>
								<td class="calendar-label">Sow</td>
								<?php echo $sow_row; ?>
							</tr>
						<?php endif; ?>

						<?php if ($has_plant) : ?>
							<tr<?php echo ($last_row === 'plant') ? ' class="last-category-row"' : ''; ?>>
								<td class="calendar-label">Plant</td>
								<?php echo $plant_row; ?>
							</tr>
						<?php endif; ?>

						<?php if ($has_harvest) : ?>
							<tr<?php echo ($last_row === 'harvest') ? ' class="last-category-row"' : ''; ?>>
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
		.site {overflow-x: visible;}
		#mega-menu-wrap-primary.mega-sticky {opacity: 1;}
		.vs-all-categories-calendar {
			max-width: 1400px;
			margin: 0 auto;
		}

		.page-title {
			font-size: 2rem;
			margin-bottom: 0.5rem;
		}

		.page-description {
			color: #666;
			margin-bottom: 1rem;
		}

		/* Filter Form Styles */

		.vs-calendar-filters {
			margin-bottom: 1rem;
		}

		.vs-calendar-filters summary {
			cursor: pointer;
			padding: 0.75rem 1rem;
			background: #f5f5f5;
			border: 1px solid #ddd;
			border-radius: 4px;
		}

		.vs-calendar-filters summary:hover {
			background: #efefef;
		}

		.vs-calendar-filters.has-active-filters summary {
			background: #f0f7f0;
			border-left: 4px solid #118800;
		}

		.vs-calendar-filters summary .filter-label {
			font-weight: 600;
		}

		.vs-calendar-filters.has-active-filters summary .filter-label {
			color: #118800;
		}

		.vs-calendar-filters summary .active-filters-text {
			color: #333;
			font-size: 0.9rem;

		}

		.vs-calendar-filters > div {
			padding: 1rem;
			background: #fafafa;
			border: 1px solid #ddd;
			border-top: none;
			border-radius: 0 0 4px 4px;
		}

		/* Filter Form Styles */

		.calendar-filters {
			width: 100%;
		}

		.filter-row {
			display: flex;
			gap: 1rem;
			align-items: flex-end;
			flex-wrap: wrap;
		}

		.filter-field {
			display: flex;
			flex-direction: column;
			gap: 0.5rem;
		}

		.filter-field label {
			font-weight: 600;
			font-size: 0.9rem;
			color: #333;
		}

		.filter-field select {
			padding: 0.3rem;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 1rem;
			min-width: 180px;
		}

		.filter-field button,
		.filter-field .button {
			padding: 0.3rem 0.5rem;
			background: #118800;
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			text-decoration: none;
			display: inline-block;
			font-size: 1rem;
		}

		.filter-field button:hover,
		.filter-field .button:hover {
			background: #0d6600;
		}

		.filter-field .button {
			background: #666;
			margin-left: 0.5rem;
		}

		.filter-field .button:hover {
			background: #444;
		}

		.vs-calendar.summary {
			position: relative;
		}

		.sowing-calendar-all {
			width: 100%;
		}

		.sowing-calendar-all thead {
			position: sticky;
			top: 0;
			z-index: 10;

		}

		.sowing-calendar-all thead th {
			position: sticky;
			top: 85px;
			z-index: 10;

			/* background: #118800; */
			/* box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); */
			border:none;
		}

		table tbody tr:nth-child(2n) td {
			background-color:inherit;
		}

		.sowing-calendar-all .category-separator {
			height: 0px;
			border-width: 0;
		}

		.sowing-calendar-all .category-separator td {
			border-width: 0;
			border-bottom: 2px solid #ccc;
			padding: 0;
		}

		.sowing-calendar-all .category-header-row .category-header {
			font-weight: bold;
			font-size: 0.9rem;
			padding: 0.75rem;
			text-align: left;
			background: #f5f5f5;
			border: solid 1px #DDEEEE;
		}

		.sowing-calendar-all .category-header a {
			color: #333;
			text-decoration: none;
		}

		.sowing-calendar-all .category-header a:hover {
			color: #118800;
			text-decoration: underline;
		}

		/* Fix border alignment - offset by 1 because of Action column */
		.sowing-calendar-all tbody td:nth-child(even) {
			border-left: solid 1px #DDEEEE;
			border-right: none;
		}
		.sowing-calendar-all tbody td:nth-child(odd) {
			border-right: solid 1px #DDEEEE;
			border-left: none;
		}
		/* First column (Action) should have normal borders
		.sowing-calendar-all tbody td:nth-child(1) {
			border: solid 1px #DDEEEE !important;
		} */

		/* Remove bottom border from last row of each category */
		.sowing-calendar-all tbody tr.last-category-row td {
			border-bottom: none;
		}

		@media (max-width: 768px) {
			.page-title {
				font-size: 1.5rem;
			}
			.sowing-calendar-all thead th {
				top: 68px;
			}
		}

		/* Print Styles */
		@media print {
			@page { size: auto;  margin: 0mm; }

			/* Hide everything except the calendar table */
			body > *:not(#page),
			body #page > *:not(#content),
			body .site-header,
			body .site-footer,
			body .mega-menu-wrap,
			body .breadcrumbs,
			body .page-title,
			body .page-description,
			body .vs-calendar-filters {
				display: none !important;
			}

			/* Ensure the calendar table is visible */
			body .vs-all-categories-calendar,
			body .vs-calendar.summary {
				display: block !important;
				width: auto !important;
				max-width: 100% !important;
			}

			body .sowing-calendar-all {
				display: table !important;
				width: auto !important;
			}

			/* Left align the table */
			body .vs-all-categories-calendar {
				margin: 0 !important;
			}

			body .sowing-calendar-all {
				margin: 0 !important;
			}

			/* Remove sticky positioning for print */
			.sowing-calendar-all thead,
			.sowing-calendar-all thead th {
				position: static !important;
			}

			/* Ensure table fits on page */
			.sowing-calendar-all {
				font-size: 9pt;
				page-break-inside: avoid;
			}

			/* Reduce row height */
			.sowing-calendar-all td,
			.sowing-calendar-all th {
				padding: 3px 5px !important;
				line-height: 1.3 !important;
			}

			.sowing-calendar-all .category-header {
				padding: 5px 8px !important;
				font-size: 10pt !important;
			}

			/* Keep category groups together when possible */
			.category-header-row,
			.category-separator {
				page-break-after: avoid;
			}

			/* Optimize for print colors */
			.sowing-calendar-all .category-header {
				background: #f5f5f5 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.sowing-calendar-all .highlight {
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
		}
	</style>
<?php

	$output = ob_get_clean();

	// Cache for 24 hours
	set_transient($cache_key, $output, 24 * HOUR_IN_SECONDS);

	return $output;
}
