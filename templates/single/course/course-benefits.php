<?php
/**
 * Template for displaying course benefits
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 */

$course_benefits = tutor_course_benefits();
if ( empty($course_benefits)){
	return;
}

if (is_array($course_benefits) && count($course_benefits)){
	?>

	<div class="tutor-single-course-segment tutor-course-benefits-wrap">

		<div class="course-benefits-title">
			<h3 class="tutor-segment-title"><?php _e('What Will I Learn?', 'tutor'); ?></h3>
		</div>

		<div class="tutor-course-benefits-content">
			<ul class="tutor-course-benefits-items">
				<?php
				foreach ($course_benefits as $benefit){
					echo "<li>{$benefit}</li>";
				}
				?>
			</ul>
		</div>
	</div>

<?php } ?>