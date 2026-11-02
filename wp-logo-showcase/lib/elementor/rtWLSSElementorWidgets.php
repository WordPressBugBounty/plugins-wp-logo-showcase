<?php
/**
 * Elementor Widgets class.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorWidgets' ) ) :
	/**
	 * Elementor Widgets class.
	 */
	class rtWLSSElementorWidgets {
		/**
		 * Widgets
		 *
		 * @var array
		 */
		private static $widgets;

		/**
		 * Register
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
		 * @return void
		 */
		public static function register( $widgets_manager ) {
			self::includes();

			self::$widgets = apply_filters(
				'rtwls_elementor_widgets',
				[
					rtWLSSElementorGridWidget::class,
					rtWLSSElementorSliderWidget::class,
				]
			);

			if ( empty( self::$widgets ) ) {
				return;
			}

			self::registerWidgets( $widgets_manager );
		}

		/**
		 * Register widgets
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
		 * @return void
		 */
		private static function registerWidgets( $widgets_manager ) {
			foreach ( self::$widgets as $widget ) {
				if ( ! class_exists( $widget ) ) {
					continue;
				}

				$widgets_manager->register( new $widget() );
			}
		}

		/**
		 * Includes
		 *
		 * @return void
		 */
		private static function includes() {
			global $rtWLS;

			$directories = [
				'elementor/elements/sections',
				'elementor/elements/tabs',
				'elementor/elements/renders',
				'elementor/elements',
			];

			foreach ( $directories as $include ) {
				$rtWLS->loadElementorClass( $include );
			}
		}

		/**
		 * Register widget controls.
		 *
		 * Turns the flat field definition arrays used by the section classes
		 * into real Elementor controls on the given widget.
		 *
		 * @param array  $fields Control fields to add.
		 * @param object $obj Widget the controls are added to.
		 * @return void
		 */
		public static function addControls( $fields, $obj ) {
			foreach ( $fields as $field ) {
				$mode = isset( $field['mode'] ) ? $field['mode'] : null;

				if ( 'section_start' === $mode ) {
					$id = $field['id'];
					unset( $field['id'], $field['mode'] );
					$obj->start_controls_section( $id, $field );
				} elseif ( 'section_end' === $mode ) {
					$obj->end_controls_section();
				} elseif ( 'tabs_start' === $mode ) {
					$id = $field['id'];
					unset( $field['id'], $field['mode'] );
					$obj->start_controls_tabs( $id );
				} elseif ( 'tabs_end' === $mode ) {
					$obj->end_controls_tabs();
				} elseif ( 'tab_start' === $mode ) {
					$id = $field['id'];
					unset( $field['id'], $field['mode'] );
					$obj->start_controls_tab( $id, $field );
				} elseif ( 'tab_end' === $mode ) {
					$obj->end_controls_tab();
				} elseif ( 'group' === $mode ) {
					$type          = $field['type'];
					$field['name'] = $field['id'];
					unset( $field['mode'], $field['type'], $field['id'] );
					$obj->add_group_control( $type, $field );
				} elseif ( 'responsive' === $mode ) {
					$id = $field['id'];
					unset( $field['id'], $field['mode'] );
					$obj->add_responsive_control( $id, $field );
				} else {
					$id = $field['id'];
					unset( $field['id'] );
					$obj->add_control( $id, $field );
				}
			}
		}

		/**
		 * Collect the controls of every tab class into a single field list.
		 *
		 * @param array $tabs Tab classes to register.
		 * @param array $controls Controls collected so far.
		 * @return array
		 */
		public static function initTabs( $tabs, $controls ) {
			foreach ( $tabs as $tab ) {
				if ( method_exists( $tab, 'register' ) ) {
					$controls = array_merge( $controls, $tab::register() );
				}
			}

			return $controls;
		}

		/**
		 * Elementor promotional section controls.
		 *
		 * @param array $fields Elementor Controls.
		 * @return array
		 */
		public static function contentPromotion( $fields ) {
			$pro_link = 'https://codecanyon.net/item/wp-logo-showcase-responsive-wp-plugin/16396329';

			$fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_pro_alert',
				'label' => sprintf(
					'<span style="color: #f54">%s</span>',
					esc_html__( 'Go Premium for More Features', 'wp-logo-showcase' )
				),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			];

			$fields[] = [
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'id'   => 'wls_el_get_pro',
				'raw'  => '<div class="elementor-nerd-box"><div class="elementor-nerd-box-title" style="margin-top: 0; margin-bottom: 20px;">' . esc_html__( 'Unlock more possibilities', 'wp-logo-showcase' ) . '</div><div class="elementor-nerd-box-message"><span class="pro-feature" style="font-size: 13px;">' . esc_html__( 'Get the Pro version for more stunning layouts and customization options.', 'wp-logo-showcase' ) . '</span></div><a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-button-go-pro" href="' . esc_url( $pro_link ) . '" target="_blank" rel="noopener">' . esc_html__( 'Get Pro', 'wp-logo-showcase' ) . '</a></div>',
			];

			$fields[] = [
				'mode' => 'section_end',
			];

			return $fields;
		}
	}

endif;
