<!-- wp:template-part {"slug":"sowing-calendar"} /-->
<div class="vs-calendar summary">
	<table class="sowing-calendar">
		<thead>
			<tr>
				<th class="calendar-label"></th>
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
			<?php if (!empty($args['sow_row'])) : ?>
				<tr>
					<td class="calendar-label">Sow</td>
					<?php echo $args['sow_row']; ?>
				</tr>
			<?php endif; ?>

			<?php if (!empty($args['plant_row'])) : ?>
				<tr>
					<td class="calendar-label">Plant</td>
					<?php echo $args['plant_row']; ?>
				</tr>
			<?php endif; ?>

			<?php if (!empty($args['harvest_row'])) : ?>
				<tr>
					<td class="calendar-label">Harvest</td>
					<?php echo $args['harvest_row']; ?>
				</tr>
			<?php endif; ?>

			<?php if (!empty($args['bloom_row'])) : ?>
				<tr>
					<td class="calendar-label">Bloom</td>
					<?php echo $args['bloom_row']; ?>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<details class="calendar-key">
		<summary>Key</summary>
		<div class="key-items">
			<?php if (!empty($args['sow_row'])) : ?>
				<span class="key-item key-sow-indoors">Sow indoors</span>
				<span class="key-item key-sow-outdoors">Sow outdoors</span>
			<?php endif; ?>
			<?php if (!empty($args['plant_row'])) : ?>
				<span class="key-item key-plant-outdoors">Plant outdoors</span>
				<span class="key-item key-plant-undercover">Plant undercover</span>
			<?php endif; ?>
			<?php if (!empty($args['harvest_row'])) : ?>
				<span class="key-item key-harvest">Harvest</span>
			<?php endif; ?>
			<?php if (!empty($args['bloom_row'])) : ?>
				<span class="key-item key-bloom">Bloom</span>
			<?php endif; ?>
		</div>
	</details>
</div>
