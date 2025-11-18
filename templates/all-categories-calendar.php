<?php
/**
 * Template for All Categories Sowing Calendar
 *
 * This template is loaded when the /sowing-calendars/ endpoint is accessed.
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php
		// Render all categories calendar
		if (function_exists('vs_render_all_categories_calendar')) {
			echo vs_render_all_categories_calendar();
		} else {
			echo '<p>Error: Calendar rendering function not found.</p>';
		}
		?>
	</main>
</div>

<?php
get_footer();
