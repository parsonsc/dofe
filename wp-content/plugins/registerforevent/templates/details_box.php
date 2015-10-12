<?php
/*
wp_enqueue_script(
    'jquery-datepicker-slider',
    RFE_PLUGIN_URL . '/js/jquery-ui-custom/jquery-ui-1.8.13.custom.min.js',
    array( 'jquery' ),
    '1.8.13',
    true
);
*/
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script(
    'timepicker',
    RFE_PLUGIN_URL . '/js/timepicker/jquery-ui-timepicker-addon.js',
    array( 'jquery' ),
    '0.9.5',
    true
); 
wp_enqueue_style(
    'jquery-datepicker-slider-css',
    RFE_PLUGIN_URL . '/js/jquery-ui-custom/smoothness/jquery-ui-1.8.13.custom.css',
    array(),
    '1.8.13'
);
wp_enqueue_script(
    'rfe-admin',
    RFE_PLUGIN_URL . '/js/rfe-admin.js',
    array( 'timepicker' ),
    '1',
    true
);
wp_enqueue_style(
    'timepicker',
    RFE_PLUGIN_URL . '/js/timepicker/jquery-ui-timepicker-addon.css',
    array( 'jquery-datepicker-slider-css' ),
    '0.9.5'
);

//if is_int
//$organizer_logo = get_post_meta($post->ID, organizer_logo, true);
?>

<?php wp_nonce_field( 'registerforevent', 'registerforevent_nonce' ); ?>
<?php if( is_array( $registerforevent_errors ) ) : ?>
    <div id="message" class="updated fade">
    <?php foreach ( $registerforevent_errors as $e ): ?>
        <p>
            <strong><?php echo $e->error_type; ?></strong>:
            <em><?php echo $e->error_message; ?></em>
        </p>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<p>
    <label for="start_date">
        <strong><?php _e( 'Start Date', 'registerforevent' ); ?></strong>
    </label>
    <input type="text" id="start_date" name="event[start_date]" class="widefat" value="<?php echo $start_date; ?>" />
</p>
<p>
    <label for="end_date">
        <strong><?php _e( 'End Date', 'registerforevent' ); ?></strong>
    </label>
    <input type="text" id="end_date" name="event[end_date]" class="widefat" value="<?php echo $end_date; ?>" />
</p>
<p>
    <label for="capacity">
        <strong><?php _e( 'Capacity', 'registerforevent' ); ?></strong>
    </label>
    <input type="text" id="capacity" name="event[capacity]" class="widefat" value="<?php echo $capacity; ?>" />
</p>
<p>
    <label for="organizer_shortname">
        <strong><?php _e( 'Partner shortname', 'registerforevent' ); ?></strong>
    </label>
    <input type="text" id="organizer_shortname" name="event[organizer_shortname]" class="widefat"  value="<?php echo $organizer_shortname; ?>" />
</p>
<p>
    <label for="organizer_logo">
        <strong><?php _e( 'Partner Logo', 'registerforevent' ); ?></strong>
    </label>
    <span class="awdMetaImage">
    <?php 
        if ($organizer_logo && is_int($organizer_logo)) {
          echo wp_get_attachment_image( $organizer_logo, 'thumbnail', true);
          $attachUrl = wp_get_attachment_url($organizer_logo);
          echo '<p>URL: <a target="_blank" href="'.$attachUrl.'">'.$attachUrl.'</a></p>';
        }
        else{
            echo '<img src="'.$organizer_logo.'" width="200" />';
            echo '<p>URL: <a target="_blank" href="'.$organizer_logo.'">'.$organizer_logo.'</a></p>';
        }
    ?> 
    </span>
    <input type="hidden" 
            class="metaValueField" 
            id="organizer_logo" 
            name="event[organizer_logo]"
            value="<?php echo $organizer_logo; ?>" 
          /> <br />
          <input class="image_upload_button"  type="button" value="Choose File" /> 
          <input class="removeImageBtn" type="button" value="Remove File" />
</p>
