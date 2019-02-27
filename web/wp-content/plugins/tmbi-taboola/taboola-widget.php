<?php

class Taboola_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'taboola_widget', // Base ID
			esc_html__( 'Taboola', 'taboola' ), // Name
			array( 'description' => esc_html__( 'Taboola recirculation module', 'taboola' ) )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		// All params are required.
		$mode        = $instance['mode'];
		$placement   = $instance['placement'];
		$target_type = $instance['target_type'];
		if ( empty( $mode ) || empty( $placement ) || empty( $target_type ) ) {
			return;
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$div_id = uniqid( 'taboola' );
		?>
		<div id="<?php echo esc_attr( $div_id ); ?>"></div>
		<script type="text/javascript">
			window._taboola = window._taboola || [];
			_taboola.push({
				'mode': '<?php echo esc_attr( $mode ); ?>',
				'placement': '<?php echo esc_attr( $placement ); ?>',
				'target_type': '<?php echo esc_attr( $target_type ); ?>',
				'container': '<?php echo esc_attr( $div_id ); ?>'
			});
		</script>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'taboola' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		$mode = ! empty( $instance['mode'] ) ? $instance['mode'] : 'thumbnails-a';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'mode' ) ); ?>"><?php esc_attr_e( 'Mode:', 'taboola' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mode' ) ); ?>" type="text" value="<?php echo esc_attr( $mode ); ?>">
		</p>
		<?php
		$placement = ! empty( $instance['placement'] ) ? $instance['placement'] : 'Below Article Thumbnails';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'placement' ) ); ?>"><?php esc_attr_e( 'Placement:', 'taboola' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placement' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placement' ) ); ?>" type="text" value="<?php echo esc_attr( $placement ); ?>">
		</p>
		<?php
		$target_type = ! empty( $instance['target_type'] ) ? $instance['target_type'] : 'mix';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'target_type' ) ); ?>"><?php esc_attr_e( 'Target Type:', 'taboola' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'target_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_type' ) ); ?>" type="text" value="<?php echo esc_attr( $target_type ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['mode']        = ( ! empty( $new_instance['mode'] ) ) ? sanitize_text_field( $new_instance['mode'] ) : '';
		$instance['placement']   = ( ! empty( $new_instance['placement'] ) ) ? sanitize_text_field( $new_instance['placement'] ) : '';
		$instance['target_type'] = ( ! empty( $new_instance['target_type'] ) ) ? sanitize_text_field( $new_instance['target_type'] ) : '';

		return $instance;
	}
}

add_action( 'customize_controls_print_styles', 'taboola_widget_add_custom_icon' );
function taboola_widget_add_custom_icon() {
	?>
	<style type="text/css">
	#available-widgets [class*=taboola] .widget-title:before{
		content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' version='1' width='20' height='20'%3E%3Cpath d='M5 4c-3 1-5 3-5 6s2 5 5 5l5-2c0-1 0-1 0 0l4 2c3 0 6-2 6-6 0-3-2-5-6-5l-4 2H9L6 4H5m1 3l1 3-2 2-1-3c0-1 0-2 2-2m9 0l1 2-1 3-2-2c0-2 1-3 2-3m3 8h-1a21 21 0 0 1-15 0l-1 2 1 1c5 2 10 3 15 1l1-1v-3' fill='%23144c93' fill-rule='evenodd'/%3E%3C/svg%3E");
	}</style>
	<?php
}
