<?php
/**
 * Elementor Grid Widget Class.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor Grid Widget Class.
 */
class rtWLSSElementorGridWidget extends \Elementor\Widget_Base {
	/**
	 * Controls
	 *
	 * @var array
	 */
	private $rtControls = [];

	/**
	 * Widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'rt-logo-grid';
	}

	/**
	 * Widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Logo Grid Layout', 'wp-logo-showcase' );
	}

	/**
	 * Widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid wls-element';
	}

	/**
	 * Widget scripts
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return [ 'rt-actual-height-js', 'rt-wls', 'rt-wls-elementor' ];
	}

	/**
	 * Widget styles
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return [ 'rt-wls' ];
	}

	/**
	 * Widget category
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'wls-elementor-widgets' ];
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->controlTabs();

		if ( empty( $this->rtControls ) ) {
			return;
		}

		rtWLSSElementorWidgets::addControls( $this->rtControls, $this );
	}

	/**
	 * Collect the tab controls.
	 *
	 * @return void
	 */
	private function controlTabs() {
		$tabs = [
			rtWLSSElementorGridContent::class,
			rtWLSSElementorGridStyle::class,
		];

		$this->rtControls = rtWLSSElementorWidgets::initTabs( $tabs, $this->rtControls );
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		global $rtWLS;
		$data = $this->get_settings_for_display();
		$html = new rtWLSSElementorRender();
		$rtWLS->print_html( $html->logoRender( $data, 'grid-layout' ), true );
	}
}
