<?php
/**
 * Widget Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSWidget' ) ) :
	/**
	 * Widget Class
	 */
	class rtWLSWidget extends WP_Widget {
		/**
		 * TLP Logo widget setup
		 */
		public function __construct() {

			$widget_ops = [
				'classname'   => 'widget_rt_wls',
				'description' => esc_html__( 'Display the Logo showcase.', 'wp-logo-showcase' ),
			];
			parent::__construct( 'widget_rt_wls', esc_html__( 'WP Logo Showcase', 'wp-logo-showcase' ), $widget_ops );

		}

		/**
		 * Display the widgets on the screen.
		 */
		public function widget( $args, $instance ) {
			extract( $args );

			$id = ( ! empty( $instance['id'] ) ? $instance['id'] : null );
			echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $instance['title'] ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			if ( $id ) {
				echo do_shortcode( '[logo-showcase id="' . absint( $id ) . '"]' );
			}
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $after_widget;
		}

		public function form( $instance ) {
			global $rtWLS;

			$defaults = [
				'title' => esc_html__( 'WP Logo Showcase', 'wp-logo-showcase' ),
				'id'    => null,
			];
			$instance = wp_parse_args( (array) $instance, $defaults );

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-logo-showcase' ); ?></label>
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_html( $instance['title'] ); ?>" style="width:100%;" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Select Shortcode:', 'wp-logo-showcase' ); ?></label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>">
					<option value=''>Select One</option>
					<?php
					$scList = $rtWLS->getWlsShortCodeList();

					if ( ! empty( $scList ) ) {
						foreach ( $scList as $scId => $sc ) {
							$selected = ( $instance['id'] == $scId ? 'selected' : null );
							echo '<option ' . esc_attr( $selected ) . ' value="' . absint( $scId ) . '">' . esc_html( $sc ) . '</option>';
						}
					}
					?>
				</select></p>
			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance          = [];
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
			$instance['id']    = ( ! empty( $new_instance['id'] ) ) ? absint( $new_instance['id'] ) : '';

			return $instance;
		}


	}


endif;
