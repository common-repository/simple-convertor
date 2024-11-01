<?php
/*
Plugin Name: Simple Converter
Plugin URI: http://www.fiveeurofood.com/index.php/simple-converter/
Description: A simple volume to/from metric (cups to grams and vice-versa) cooking converter to enable quick conversion of ingredients. To use, simply enable the "Simple Converter" widget under the Widgets menu. The widget needs a sidebar which can handle widgets of 210 pixels in width by default. If the widget doesn't fit in your theme, then you can edit the width/height of the menus and text boxes, and font sizes yourself in the file "widget.html" in the plugin directory. The section you need to edit is clearly marked. For support, feedback, or suggestions, please visit <a href="http://www.fiveeurofood.com/index.php/simple-converter/">the plugin site</a>.
Author: FiveEuroFood
Author URI: http://www.fiveeurofood.com/

Version: 0.8
Donate link: http://www.fiveeurofood.com/simple-converter
License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

class Simple_converter extends WP_Widget {

	/**
	 * Default widget values.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Default widget values.
	 *
	 * @var array
	 */
	protected $sizes;

	/**
	 * Constructor method.
	 *
	 * Set some global values and create widget.
	 */
	function __construct() {

		/**
		 * Default widget option values.
		 */
		$this->defaults = array(
			'title'		=> 'converter',
			'align'		=> 'center',
			'filter'	=> 0,
			'showlink'	=> '1'
		);
		
		/**
		 * Possible widget alignments.
		 */
		$this->align = array( 'left', 'center', 'right' );

		$this->WP_Widget( 'simple-converter', __( 'Simple converter', 'sc' ), $widget_ops, $control_ops );

	}

	/**
	 * Widget Form.
	 *
	 * Outputs the widget form that allows users to control the output of the widget.
	 *
	 */
	function form( $instance ) {

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'sc' ); ?>:</label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
		
		<textarea style="display:none;" class="widefat" rows="14" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo '' ?></textarea>
		
		<p>
            <input id="<?php echo $this->get_field_id('showlink'); ?>" name="<?php echo $this->get_field_name('showlink'); ?>" type="checkbox" checked="yes" <?php checked(isset($instance['showlink']) ? $instance['showlink'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('showlink'); ?>"><?php _e('Show your support for the plugin by adding a tiny link back to me under the widget.', 'simpleconverter'); ?></label>
       </p>	

		<p> Widget alignment:
		<select id="<?php echo $this->get_field_id( 'align' ); ?>" name="<?php echo $this->get_field_name( 'align' ); ?>">
				<?php
				foreach ( (array) $this->align as $align ) {
					printf( '<option value="%s" %s>%s</option>', $align, selected( $align, $instance['align'], 0 ), $align );
				}
				?>
			</select>	   
		<?php

	}

	/**
	 * Form validation and sanitization.
	 *
	 * Runs when you save the widget form. Allows you to validate or sanitize widget options before they are saved.
	 *
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['showlink'] = isset($new_instance['showlink']);
		$instance['align'] = $new_instance['align'];

		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
		$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	/**
	 * Widget Output.
	 *
	 * Outputs the actual widget on the front-end based on the widget options the user selected.
	 *
	 */
	function widget( $args, $instance ) {

		extract( $args );

		$align = ( ! empty( $instance['align'] ) ) ? $instance['align'] : '';
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$showlink = !empty($instance['showlink']) ? true : false;
		echo $before_widget;
		
		if($showlink) {
            $text = '<div align="' . $align . '" style="width:100%;"><iframe src="' . plugins_url( 'widgetwl.html' , __FILE__ ) . '" frameborder="0" scrolling="no" height="208" width="206"></iframe></div>';
        }
		
		if(! $showlink) {
            $text = '<div align="' . $align . '" style="width:100%;"><iframe src="' . plugins_url( 'widget.html' , __FILE__ ) . '" frameborder="0" scrolling="no" height="208" width="206"></iframe></div>';
        }
		
		if ( ! empty( $title ) )
			echo $before_title . $icon . $title . $after_title;
		?>

		<div class="simple-converter"><?php echo ! empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>

		<?php  echo $after_widget;

	}

}

add_action( 'widgets_init', 'sc_load_widget' );
/**
 * Widget Registration.
 *
 * Register Simple converter.
 *
 */
function sc_load_widget() {

	register_widget( 'Simple_converter' );

}