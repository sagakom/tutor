<?php if(!empty($back_url)): ?>
    <div class="tutor-color-text-primary back">
        <a href="<?php echo $back_url; ?>">
            <span class="tutor-icon previous-line"></span> <span class="text"><?php _e('Back', 'tutor'); ?></span>
        </a>
    </div>
<?php endif; ?>

<div class="text-regular-small tutor-color-text-subsued tutor-mt-30">
    <?php _e('Course', 'tutor'); ?>: <?php echo $course_title; ?>
</div>

<div class="header-title tutor-text-medium-h5 tutor-color-text-primary tutor-mt-10 tutor-mb-20">
    <?php echo $quiz_title; ?>
</div>

<div class="tutor-mb-30 tutor-text-regular-small tutor-color-text-subsued">
    <div class="tutor-bs-d-flex">
        <div class="tutor-mr-15">
            <?php _e('Quiz Time', 'tutor'); ?>: <span class="tutor-color-text-primary"><strong><?php echo $quiz_time; ?></strong></span>
        </div>
        <div>
            <?php _e('Attempt Time', 'tutor'); ?>: <span class="tutor-color-text-primary"><strong><?php echo $attempt_time; ?></strong></span>
        </div>
    </div>
</div>