<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

global $post;
global $previous_id;
$currentPost = $post;
$quiz_id = get_the_ID();
$is_started_quiz = tutor_utils()->is_started_quiz();
$course = tutor_utils()->get_course_by_quiz(get_the_ID());
$previous_attempts = tutor_utils()->quiz_attempts();
$attempted_count = is_array($previous_attempts) ? count($previous_attempts) : 0;

$attempts_allowed = tutor_utils()->get_quiz_option(get_the_ID(), 'attempts_allowed', 0);
$passing_grade = tutor_utils()->get_quiz_option(get_the_ID(), 'passing_grade', 0);

$attempt_remaining = $attempts_allowed - $attempted_count;

do_action('tutor_quiz/single/before/top');


?>
<?php
	if (!$is_started_quiz && $attempted_count !== 0) {
?>

<div class="tutor-start-quiz-wrapper mr-auto tutor-p-50">
    <div class="tutor-start-quiz-title tutor-pb-28">
        <p class="text-regular-body color-text-primary tutor-pb-6"><?php _e('Quiz', 'tutor'); ?></p>
        <h6 class="text-medium-h4 color-text-primary"><?php echo get_the_title(); ?></h6>
    </div>
    <div class="tutor-quiz-info-area tutor-mb-60 tutor-mt-22">
		<?php
			$total_questions = tutor_utils()->total_questions_for_student_by_quiz(get_the_ID());

			if($total_questions){
		?>
        <div class="tutor-quiz-info">
            <span class="text-regular-body color-text-hints"><?php _e('Questions', 'tutor'); ?>:</span>
            <span class="text-regular-body color-text-primary"><?php echo $total_questions; ?></span>
        </div>
		<?php 
			
			}

			$time_limit = tutor_utils()->get_quiz_option(get_the_ID(), 'time_limit.time_value');
			if ($time_limit){
				$time_type 	= tutor_utils()->get_quiz_option(get_the_ID(), 'time_limit.time_type');

				$available_time_type = array(
					'seconds'	=> __( 'seconds', 'tutor' ),
					'minutes'	=> __( 'minutes', 'tutor' ),
					'hours'		=> __( 'hours', 'tutor' ),
					'days'		=> __( 'days', 'tutor' ),
					'weeks'		=> __( 'weeks', 'tutor' ),
				);
		?>
        <div class="tutor-quiz-info">
            <span class="text-regular-body color-text-hints"><?php _e('Quize Time', 'tutor'); ?>:</span>
            <span class="text-regular-body color-text-primary"><?php echo $time_limit.' '.sprintf( __( '%s', 'tutor' ), isset( $available_time_type[$time_type] ) ? $available_time_type[$time_type] : $time_type ); ?></span>
        </div>
		<?php } ?>
        <div class="tutor-quiz-info">
            <span class="text-regular-body color-text-hints"><?php _e('Total Attempted', 'tutor'); ?>:</span>
            <span class="text-regular-body color-text-primary">
				<?php
					if($attempted_count){
						echo $attempted_count . '/';
					}
					echo $attempts_allowed == 0 ? __('No limit', 'tutor') : $attempts_allowed;
				?>
			</span>
        </div>
		<?php
			if($passing_grade){
		?>
        <div class="tutor-quiz-info">
            <span class="text-regular-body color-text-hints"><?php _e('Passing Grade', 'tutor'); ?></span>
            <span class="text-regular-body color-text-primary">(<?php echo $passing_grade . '%'; ?>)</span>
        </div>
		<?php } ?>
    </div>
	<?php
		if ($attempt_remaining > 0 || $attempts_allowed == 0) {
		do_action('tuotr_quiz/start_form/before', $quiz_id);
	?>
    <div class="tutor-quiz-btn-grp">
		<form id="tutor-start-quiz" method="post">
			<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>

			<input type="hidden" value="<?php echo $quiz_id; ?>" name="quiz_id"/>
			<input type="hidden" value="tutor_start_quiz" name="tutor_action"/>

			<button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-md start-quiz-btn" name="start_quiz_btn" value="start_quiz">
				<?php _e( 'Start Quiz', 'tutor' ); ?>
			</button>
		</form>
        <button class="tutor-btn tutor-btn-disable-outline tutor-no-hover tutor-btn-md skip-quiz-btn" href="<?php echo get_the_permalink($previous_id);
		?>">
			<?php _e( 'Skip Quiz', 'tutor' ); ?>
        </button>
    </div>
	<?php } ?>
</div>
<?php
		} ?>
<?php do_action('tutor_quiz/single/after/top'); ?>
