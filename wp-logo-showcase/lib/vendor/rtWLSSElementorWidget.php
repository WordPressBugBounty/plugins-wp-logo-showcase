<?php
/**
 * Elementor widget
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor widget
 */
class rtWLSSElementorWidget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wp-logo-showcase';
	}

	public function get_title() {
		return esc_html__( 'Logo Slider and Showcase', 'wp-logo-showcase' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {
		global $rtWLS;

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Logo Slider and Showcase', 'wp-logo-showcase' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'short_code_id',
			[
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'id'      => 'short_code_id',
				'label'   => esc_html__( 'ShortCode', 'wp-logo-showcase' ),
				'options' => $rtWLS->getWlsShortCodeList(),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( isset( $settings['short_code_id'] ) && ! empty( $settings['short_code_id'] ) && $id = absint( $settings['short_code_id'] ) ) {
			echo do_shortcode( '[logo-showcase id="' . $id . '"]' );
		} else {
			echo esc_html__( 'Please select a post grid', 'wp-logo-showcase' );
		}
	}
}
