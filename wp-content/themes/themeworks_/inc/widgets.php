<?php

/**
 * Custom widgets for this theme.
 *
 * @see  https://codex.wordpress.org/Widgets_API
 * @package themeworks
 */
    
class ThemeWorks_Action_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {

        parent::__construct(
            'themeworks_cta_widget', // Base ID
            __( 'ThemeWorks Call to Action', 'themeworks' ), // Name
            array( 'description' => __( 'Display call to action button', 'themeworks' ), ) // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'Call to Action' : $instance['title'] );
        $actionContent = empty( $instance['actionContent'] ) ? '' : $instance['actionContent'];
        $actionLink = empty( $instance['actionLink'] ) ? '' : $instance['actionLink'];
        $actionButton = empty( $instance['actionButton'] ) ? '' : $instance['actionButton'];

        echo $before_widget;
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

    ?>

    <div class="themeworks-action-content">
        <div class="themeworks-action-text">
            <p><?php echo $actionContent; ?></p>
        </div>
        <div class="themeworks-action-button">
            <?php if ( isset( $actionLink ) && $actionLink <> '' ) { ?>
                <a href="<?php echo $actionLink; ?>" class="button"><?php echo $actionButton; ?></a>
            <?php } ?>
        </div>
    </div><!-- .themeworks-action-content  -->

    <?php
        echo $after_widget;
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = stripslashes( $new_instance['title'] );
        $instance['actionContent'] = stripslashes( $new_instance['actionContent'] );
        $instance['actionLink'] = stripslashes( $new_instance['actionLink'] );
        $instance['actionButton'] = stripslashes( $new_instance['actionButton'] );

        return $instance;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // Defaults
        $instance = wp_parse_args( (array) $instance, array('title'=>'Call to Action Title', 'actionContent'=>'Example Text', 'actionLink' => '', 'actionButton' => 'Take Action Now' ) );

        $title = htmlspecialchars($instance['title']);
        $actionContent = htmlspecialchars($instance['actionContent']);
        $actionLink = htmlspecialchars($instance['actionLink']);
        $actionButton = htmlspecialchars($instance['actionButton']);

        // Title
        echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
        // Action Text
        echo '<p><label for="' . $this->get_field_id('actionContent') . '">' . 'Text:' . '</label><textarea cols="10" rows="10" class="widefat" id="' . $this->get_field_id('actionContent') . '" name="' . $this->get_field_name('actionContent') . '" >'. $actionContent .'</textarea></p>';
        // Action Link
        echo '<p><label for="' . $this->get_field_id('actionLink') . '">' . 'Button Link:' . '</label><textarea cols="10" rows="2" class="widefat" id="' . $this->get_field_id('actionLink') . '" name="' . $this->get_field_name('actionLink') . '" >'. $actionLink .'</textarea></p>';
        // Action Button
        echo '<p><label for="' . $this->get_field_id('actionButton') . '">' . 'Button Text:' . '</label><textarea cols="10" rows="2" class="widefat" id="' . $this->get_field_id('actionButton') . '" name="' . $this->get_field_name('actionButton') . '" >'. $actionButton .'</textarea></p>';
    }

} // end class

/**
 * Register the ThemeWorks_Action_Widget
 */
function ThemeWorks_Action_Widget_Init() {
  register_widget( 'ThemeWorks_Action_Widget' );
}
add_action( 'widgets_init', 'ThemeWorks_Action_Widget_Init' );
