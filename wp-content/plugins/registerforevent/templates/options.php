<?php if( $flash ) { ?>
    <div id="message" class="updated fade">
        <p><strong><?php echo $flash; ?></strong></p>
    </div>
<?php } ?>
<div id="icon-tools" class="icon32"><br /></div>
<div class="wrap">
    <h2><?php _e( 'Eventbrite for WordPress','eventbrite' ); ?></h2>
    <div id="poststuff" class="metabox-holder">
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'About','eventbritedg' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'Eventbrite for WordPress is a complete solution to manage events directly from WordPress.','eventbritedg' ); ?>
                </p>
                <p>
                    <?php _e( 'To enable the plugin, please fill the fields below.','eventbritedg' ); ?>
                </p>
                <form action="" method="post">
                    <?php wp_nonce_field( 'eventbritedg', 'eventbrite_options_nonce' ); ?>
                    <p>
                        <label for="eventbrite_app_key">
                            <strong><?php _e( 'Eventbrite application key','eventbritedg' )?></strong>
                        </label>
                        <input type="text" id="eventbrite_app_key" name="eventbrite_app_key" class="widefat" value="<?php echo $eventbrite_app_key; ?>" />
                    </p>
                    <p>
                        <label for="eventbrite_user_key">
                            <strong><?php _e( 'Eventbrite user key','eventbritedg' )?></strong>
                        </label>
                        <input type="text" id="eventbrite_user_key" name="eventbrite_user_key" class="widefat" value="<?php echo $eventbrite_user_key; ?>" />
                    </p>
                    <p>
                        <label for="eventbrite_user_email">
                            <strong><?php _e( 'Eventbrite user email','eventbritedg' )?></strong>
                        </label>
                        <input type="text" id="eventbrite_user_email" name="eventbrite_user_email" class="widefat" value="<?php echo $eventbrite_user_email; ?>" />
                    </p>
                    <p>
                        <label for="eventbrite_user_pass">
                            <strong><?php _e( 'Eventbrite user password','eventbritedg' )?></strong>
                        </label>
                        <input type="password" id="eventbrite_user_pass" name="eventbrite_user_pass" class="widefat" value="<?php echo $eventbrite_user_pass; ?>" />
                    </p>
                    <div class="eventbrite-tabs">
                        <ul class="wp-tab-bar">
                            <li class="tabs wp-tab-active"><a href="#eb-tab-1"><?php _e( 'Background Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-2"><?php _e( 'Text Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-3"><?php _e( 'Link Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-4"><?php _e( 'Title Text Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-5"><?php _e( 'Box Background Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-6"><?php _e( 'Box Text Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-7"><?php _e( 'Box Border Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-8"><?php _e( 'Box Header Background Color','eventbritedg' )?></a></li>
                            <li class="tabs"><a href="#eb-tab-9"><?php _e( 'Box Header Text Color','eventbritedg' )?></a></li>
                        </ul>
                        <div id="colorpicker"></div>
                        <div id="eb-tab-1" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_background_color">
                                    <strong><?php _e( 'Background Color','eventbritedg' )?></strong>
                                </label>
                            <p>
                            <input type="text" id="eventbrite_background_color" name="eventbrite_background_color" class="color" value="<?php echo $eventbrite_background_color; ?>" />
                        </div>
                        <div id="eb-tab-2" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_text_color">
                                    <strong><?php _e( 'Text Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_text_color" name="eventbrite_text_color" class="color" value="<?php echo $eventbrite_text_color; ?>" />
                        </div>
                        <div id="eb-tab-3" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_link_color">
                                    <strong><?php _e( 'Link Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_link_color" name="eventbrite_link_color" class="color" value="<?php echo $eventbrite_link_color; ?>" />
                        </div>
                        <div id="eb-tab-4" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_title_text_color">
                                    <strong><?php _e( 'Title Text Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_title_text_color" name="eventbrite_title_text_color" class="color" value="<?php echo $eventbrite_title_text_color; ?>" />
                        </div>
                        <div id="eb-tab-5" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_box_background_color">
                                    <strong><?php _e( 'Box Background Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_box_background_color" name="eventbrite_box_background_color" class="color" value="<?php echo $eventbrite_box_background_color; ?>" />
                        </div>
                        <div id="eb-tab-6" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_box_text_color">
                                    <strong><?php _e( 'Box Text Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_box_text_color" name="eventbrite_box_text_color" class="color" value="<?php echo $eventbrite_box_text_color; ?>" />
                        </div>
                        <div id="eb-tab-7" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_box_border_color">
                                    <strong><?php _e( 'Box Border Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_box_border_color" name="eventbrite_box_border_color" class="color" value="<?php echo $eventbrite_box_border_color; ?>" />
                        </div>
                        <div id="eb-tab-8" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_box_header_background_color">
                                    <strong><?php _e( 'Box Header Background Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_box_header_background_color" name="eventbrite_box_header_background_color" class="color" value="<?php echo $eventbrite_box_header_background_color; ?>" />
                        </div>
                        <div id="eb-tab-9" class="wp-tab-panel">
                            <p>
                                <label for="eventbrite_box_header_text_color">
                                    <strong><?php _e( 'Box Header Text Color','eventbritedg' )?></strong>
                                </label>
                            </p>
                            <input type="text" id="eventbrite_box_header_text_color" name="eventbrite_box_header_text_color" class="color" value="<?php echo $eventbrite_box_header_text_color; ?>" />
                        </div>
                    </div>
                    <p class="clear">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' )?>"/>
                    </p>
                </form>
            </div>
        </div>
        
        <?php do_action( 'eventbrite_options_screen' ); ?>
        
        <?php wp_print_scripts( 'jquery-ui-tabs' ); ?>
        <?php wp_print_scripts( 'farbtastic' ); ?>
        <?php wp_print_styles( 'farbtastic' ); ?>
        
        <script type="text/javascript">
            jQuery(function($) {
                $( ".eventbrite-tabs" ).tabs();
                var f = $.farbtastic( "#colorpicker" );
                $( ".tabs a" ).click( function() {
                    $( ".wp-tab-active" ).removeClass( 'wp-tab-active' );
                    $(this).parent('li').addClass( 'wp-tab-active' );
                });
                $( ".wp-tab-panel .color" ).focus( function() {
                    if( $( this ).val() === '' )
                        $( this ).val( '#FFF' );
                    
                    f.linkTo( this );
                });
            });
        </script>
        <style type="text/css">
            .wp-tab-panel { height: auto; margin-right: 10px;}
            #colorpicker { float: right; }
        </style>
    </div>
</div>
