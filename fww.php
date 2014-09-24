<?php
/**
 * Plugin Name: Flexible Widget Widths
 * Plugin URI: http://github.com
 * Description: Dynamically add classes to widgets.
 * Version: 0.8
 * Author: Ryan Kanner
 * Author URI: http://rkanner.com
 * License: GPL2
*/

class flexWidgetWidth {

	public function __construct() {

		if ( is_admin() ) {
			add_action( 'in_widget_form', array( $this, 'widget_options' ), 10, 3 );
			add_filter( 'widget_update_callback', array( $this, 'update_widget_options'), 10, 2 );
		} else {
			add_filter( 'dynamic_sidebar_params', array( $this, 'add_classes' ) );
		}

	}

	public static function widget_options( $widget, $return, $instance ) { 

		$override_options = array(
			array(
				'value' => '',
				'name' => 'Select Width',
			),
			array(
				'value' => 12,
				'name' => '100%',
			),
			array(
				'value' => 9,
				'name' => '75%'
			),
			array(
				'value' => 6,
				'name' => '50%'
			),
			array(
				'value' => 4,
				'name' => '33.3%'
			),
			array(
				'value' => 3,
				'name' => '25%'
			),
			array(
				'value' => 2,
				'name' => '16.6%'
			),
		);

		$override = isset( $instance['override'] ) ? $instance['override'] : null;
		$classes = isset( $instance['classes'] ) ? $instance['classes'] : '';

		echo "\t<p><label for='widget-{$widget->id_base}-{$widget->number}-override'>" . __('Optional Width Override') . "</label>\n";
			echo "\t<select class='widefat' id='widget-{$widget->id_base}-{$widget->number}-override' name='widget-{$widget->id_base}[{$widget->number}][override]'>\n";
				foreach ( $override_options as $option ) {
					echo '<option value="' . $option['value'] . '"' . selected( $override, $option['value'], false ) . '>' . $option['name'] . '</option>';
				}
			echo '</select></p>';
		echo "\t<p><label for='widget-{$widget->id_base}-{$widget->number}-classes'>" . __('CSS Class') . "</label>\n";
		echo "\t<input class='widefat' id='widget-{$widget->id_base}-{$widget->number}-classes' name='widget-{$widget->id_base}[{$widget->number}][classes]' value='{$classes}'></p>\n";

		return $instance;

	}

	public static function update_widget_options( $instance, $new_instance ) {

		$instance['classes'] = esc_attr( $new_instance['classes'] );
		$instance['override'] = esc_attr( $new_instance['override'] );
		return $instance;

	}

	public static function add_classes( $params ) {

		global $wp_registered_widgets, $widget_number;

		$widget_array = wp_get_sidebars_widgets(); //Returns an array of registered widgets
		$sidebar_id   = $params[0]['id'];
		$widget_id    = $params[0]['widget_id'];
		$widget_obj   = $wp_registered_widgets[$widget_id];
		$widget_num   = $widget_obj['params'][0]['number'];

		$widget_opt = get_option( $widget_obj['callback'][0]->option_name );

		$widget_classes	= $widget_opt[$widget_num]['classes'];
		$override       = isset( $widget_opt[$widget_num]['override'] ) ? $widget_opt[$widget_num]['override'] : '';

		$total_columns = apply_filters( 'fww_total_columns', 12 );

		if ( isset( $widget_classes ) ) {
			$classes[] = $widget_classes;
		}

		$column_nums = apply_filters( 'fww_column_widths', 
			array(
				'prefix' => array(
					'lg' => 'large-',
					'med'	=> 'medium-',
					'sm' => 'small-',
				),
				'default' => array(
					'lg' => 3,
					'med' => 6,
					'sm' => 12,
				),
				1 => array(
					'lg' => 12,
					'med' => 12,
					'sm' => 12,
				),
				2 => array(
					'lg' => 2,
					'med' => 6,
					'sm' => 12,
				),
				3 => array(
					'lg' => 3,
					'med' => 4,
					'sm' => 12,
				),
				4 => array(
					'lg' => 4,
					'med' => 6,
					'sm' => 12,
				),
				6 => array(
					'lg' => 6,
					'med' => 6,
					'sm' => 12,
				),
			) 
		);

		if ( array_key_exists( 'auto_width', $params[0] ) && $params[0]['auto_width'] === true ) {

			$widget_count = count($widget_array[$sidebar_id]);

			if ( $override ) {

			}

			$column_calc = $total_columns / $widget_count;
			$column_round = round( $column_calc, 0, PHP_ROUND_HALF_EVEN );

			if ( array_key_exists( intval($column_round), $column_nums ) ) {
				$col_class = $column_nums[$column_round];
			} else {
				$col_class = $column_nums['default'];
			}

			$prefixes = $column_nums['prefix'];
			$combine_classes = array_merge_recursive( $prefixes, $col_class );

			foreach ( $combine_classes as $combine_class ) {
				$classes[] = $combine_class[0] . $combine_class[1];
			}

			$classes[] = apply_filters( 'fww_column_name', 'columns' );

		}

		$class_string = implode( " ", $classes ) . ' ';
		$params[0]['before_widget'] = str_replace( 'class="', "class=\"{$class_string}", $params[0]['before_widget']);

		return $params;

	}
}

$flex_widget_width = new flexWidgetWidth();

?>