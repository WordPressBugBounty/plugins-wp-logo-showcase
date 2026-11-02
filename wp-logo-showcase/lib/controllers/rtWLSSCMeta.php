<?php
/**
 * ShortCode Meta field Class
 *
 * This will generate the meta field for ShortCode generator post type
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSCMeta' ) ) :
	/**
	 * ShortCode Meta field Class
	 */
	class rtWLSSCMeta {

		public function __construct() {
			add_action( 'add_meta_boxes', [ $this, 'sc_meta_boxes' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
			add_action( 'save_post', [ $this, 'save_team_sc_meta_data' ], 10, 3 );
			add_action( 'edit_form_after_title', [ $this, 'wls_sc_after_title' ] );
			add_action( 'admin_init', [ $this, 'rt_wls_pro_remove_all_meta_box' ] );
			add_filter( 'manage_edit-wlshowcasesc_columns', [ $this, 'arrange_wl_showcase_sc_columns' ] );
			add_action( 'manage_wlshowcasesc_posts_custom_column', [ $this, 'manage_wl_showcase_sc_columns' ], 10, 2 );
		}

		/**
		 * This will add input text field for shortCode
		 *
		 * @param $post
		 */
		public function wls_sc_after_title( $post ) {
			global $rtWLS;

			if ( $rtWLS->shortCodePT !== $post->post_type ) {
				return;
			}

			$html  = null;
			$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
			$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[logo-showcase id=&quot;' . absint( $post->ID ) . '&quot; title=&quot;' . esc_attr( $post->post_title ) . '&quot;]" class="large-text code rt-code-sc">
			<input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[logo-showcase id=&quot;' . absint( $post->ID ) . '&quot; title=&quot;' . esc_attr( $post->post_title ) . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
			</p>';
			$html .= '</div></div>';

			$rtWLS->print_html( $html, true );
		}

		/**
		 * Arrange the shortCode listing column
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		public function arrange_wl_showcase_sc_columns( $columns ) {
			$shortcode = [ 'wls_short_code' => esc_html__( 'Shortcode', 'wp-logo-showcase' ) ];

			return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
		}

		public function manage_wl_showcase_sc_columns( $column ) {
			switch ( $column ) {
				case 'wls_short_code':
					echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[logo-showcase id=&quot;' . absint( get_the_ID() ) . '&quot; title=&quot;' . esc_html( get_the_title() ) . '&quot;]" class="large-text code rt-code-sc">';
					break;
				default:
					break;
			}
		}

		/**
		 *  Remove all unwanted meta box
		 */
		public function rt_wls_pro_remove_all_meta_box() {
			if ( is_admin() ) {
				global $rtWLS;

				add_filter( "get_user_option_meta-box-order_{$rtWLS->shortCodePT}", [ $this, 'remove_all_meta_boxes_wls_sc' ] );
			}
		}

		/**
		 * Add only custom meta box
		 *
		 * @return array
		 */
		public function remove_all_meta_boxes_wls_sc() {
			global $wp_meta_boxes, $rtWLS;

			$cpt          = $rtWLS->shortCodePT;
			$scBoxKey     = $cpt . '_sc_settings_meta';
			$previewKey   = $cpt . '_sc_preview_meta';
			$publishBox   = isset( $wp_meta_boxes[ $cpt ]['side']['core']['submitdiv'] )
				? $wp_meta_boxes[ $cpt ]['side']['core']['submitdiv']
				: null;
			$scBox        = isset( $wp_meta_boxes[ $cpt ]['normal']['high'][ $scBoxKey ] )
				? $wp_meta_boxes[ $cpt ]['normal']['high'][ $scBoxKey ]
				: null;
			$scPreviewBox = isset( $wp_meta_boxes[ $cpt ]['normal']['high'][ $previewKey ] )
				? $wp_meta_boxes[ $cpt ]['normal']['high'][ $previewKey ]
				: null;
			$docBox       = isset( $wp_meta_boxes[ $cpt ]['side']['low']['rt_plugin_sc_pro_information'] )
				? $wp_meta_boxes[ $cpt ]['side']['low']['rt_plugin_sc_pro_information']
				: null;

			$normalHigh = [];

			if ( $scBox ) {
				$normalHigh[ $scBoxKey ] = $scBox;
			}

			if ( $scPreviewBox ) {
				$normalHigh[ $previewKey ] = $scPreviewBox;
			}

			$wp_meta_boxes[ $cpt ] = [
				'side'   => [
					'core' => $publishBox ? [ 'submitdiv' => $publishBox ] : [],
					'low'  => $docBox ? [ 'rt_plugin_sc_pro_information' => $docBox ] : [],
				],
				'normal' => [
					'high' => $normalHigh,
				],
			];

			return [];
		}

		/**
		 *  Add script for the shortCode generate page only
		 */
		public function admin_enqueue_scripts() {
			global $pagenow, $typenow, $rtWLS;

			// validate page.
			if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php', 'edit.php' ] ) ) {
				return;
			}
			if ( $typenow != $rtWLS->shortCodePT ) {
				return;
			}

			// scripts.
			wp_enqueue_script(
				[
					'jquery',
					'rt-actual-height-js',
					'wp-color-picker',
					'rt-slick',
					'rt-select2',
					'rt-wls-admin',
				]
			);

			// styles.
			wp_enqueue_style(
				[
					'wp-color-picker',
					'rt-select2',
					'rt-wls-admin',
				]
			);

			$nonce = wp_create_nonce( $rtWLS->nonceText( 'sc_save' ) );

			wp_localize_script(
				'rt-wls-admin',
				'wls',
				[
					'nonceID'      => esc_attr( $rtWLS->nonceId() ),
					'nonce'        => esc_attr( $nonce ),
					// Scoped to the preview action only, so it cannot be replayed to save.
					'previewNonce' => esc_attr( wp_create_nonce( $rtWLS->nonceText( 'sc_preview' ) ) ),
					'previewUrl'   => esc_url_raw( $rtWLS->previewUrl() ),
					'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
				]
			);

		}

		public function sc_meta_boxes() {
			global $rtWLS;

			add_meta_box(
				$rtWLS->shortCodePT . '_sc_settings_meta',
				esc_html__( 'Short Code Generator', 'wp-logo-showcase' ),
				[ $this, 'wls_sc_settings_selection' ],
				$rtWLS->shortCodePT,
				'normal',
				'high'
			);

			add_meta_box(
				$rtWLS->shortCodePT . '_sc_preview_meta',
				esc_html__( 'Layout Preview', 'wp-logo-showcase' ),
				[ $this, 'wls_sc_preview_selection' ],
				$rtWLS->shortCodePT,
				'normal',
				'high'
			);

			add_meta_box(
				'rt_plugin_sc_pro_information',
				esc_html__( 'Documentation', 'wp-logo-showcase' ),
				[ $this, 'rt_plugin_sc_pro_information' ],
				$rtWLS->shortCodePT,
				'side',
				'low'
			);
		}


		/**
		 * Layout preview section — wls-admin.js posts the settings to the
		 * front-end preview page (rtWLSPreview) and shows it in the iframe.
		 */
		public function wls_sc_preview_selection() {
			global $rtWLS;

			$devices = [
				'desktop' => [ 'dashicons-desktop', esc_html__( 'Desktop', 'wp-logo-showcase' ) ],
				'tablet'  => [ 'dashicons-tablet', esc_html__( 'Tablet', 'wp-logo-showcase' ) ],
				'mobile'  => [ 'dashicons-smartphone', esc_html__( 'Mobile', 'wp-logo-showcase' ) ],
			];

			$html  = '<div class="wls-preview-toolbar">';
			$html .= '<span class="spinner"></span>';
			$html .= '<div class="wls-preview-devices" role="group" aria-label="' . esc_attr__( 'Preview device', 'wp-logo-showcase' ) . '">';

			foreach ( $devices as $device => $info ) {
				$active = 'desktop' === $device;
				$html  .= sprintf(
					'<button type="button" class="button wls-preview-device%1$s" data-device="%2$s" aria-pressed="%3$s" title="%4$s"><span class="dashicons %5$s"></span><span class="screen-reader-text">%4$s</span></button>',
					$active ? ' is-active' : '',
					esc_attr( $device ),
					$active ? 'true' : 'false',
					$info[1],
					esc_attr( $info[0] )
				);
			}

			$html .= '</div>';
			$html .= '</div>';
			// No allow-top-navigation: theme or plugin frame-busting scripts cannot take over the edit screen.
			$html .= '<div class="wls-preview-viewport"><iframe id="wls-sc-preview-frame" name="wls-sc-preview-frame" title="' . esc_attr__( 'Layout preview', 'wp-logo-showcase' ) . '" src="about:blank" scrolling="no" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe></div>';

			$rtWLS->print_html( $html, true );
		}

		/**
		 * Meta info function
		 *
		 * @internal param $post
		 */
		public function wls_sc_pro_information() {
			$pro = 'https://codecanyon.net/item/wp-logo-showcase-responsive-wp-plugin/16396329?ref=RadiusTheme';
			?>
			<ol>
				<li>Isotope layout</li>
				<li>Carousel Slider with multiple features.</li>
				<li>Custom Logo Re-sizing.</li>
				<li>Drag & Drop Layout builder.</li>
				<li>Drag & Drop Logo ordering.</li>
				<li>Custom Link for each Logo.</li>
				<li>Category wise Isotope Filtering.</li>
				<li>Tooltip Enable/Disable option.</li>
				<li>Box Highlight Enable/Disable.</li>
				<li>Center Mode available.</li>
				<li>RTL Supported.</li>
			</ol>
			<p><a target="_blank" href="<?php echo esc_url( $pro ); ?>" class="rt-pro-link">Get Pro Version</a></p>
			<?php
		}

		/**
		 * Setting Sections
		 *
		 * @param $post
		 */
		public function wls_sc_settings_selection( $post ) {
			global $rtWLS;

			wp_nonce_field( $rtWLS->nonceText( 'sc_save' ), $rtWLS->nonceId() );

			$html  = null;
			$html .= '<div class="rt-tab-container">';
			$html .= '<ul class="rt-tab-nav">
						<li><a href="#sc-wls-layout"><i class="dashicons dashicons-layout"></i>' . esc_html__( 'Layout', 'wp-logo-showcase' ) . '</a></li>
						<li><a href="#sc-wls-filter"><i class="dashicons dashicons-filter"></i>' . esc_html__( 'Filtering', 'wp-logo-showcase' ) . '</a></li>
						<li><a href="#sc-wls-field-selection"><i class="dashicons dashicons-editor-table"></i>' . esc_html__( 'Field Selection', 'wp-logo-showcase' ) . '</a></li>
						<li><a href="#sc-wls-style"><i class="dashicons dashicons-admin-customizer"></i>' . esc_html__( 'Styling', 'wp-logo-showcase' ) . '</a></li>
					</ul>';
			$html .= sprintf( '<div id="sc-wls-layout" class="rt-tab-content">%s</div>', $rtWLS->rtFieldGenerator( $rtWLS->scLayoutMetaFields(), true ) );

			$html .= sprintf( '<div id="sc-wls-filter" class="rt-tab-content">%s</div>', $rtWLS->rtFieldGenerator( $rtWLS->scFilterMetaFields(), true ) );

			// The Content Limit field is shown by wls-admin.js only while Description is checked.
			$html .= sprintf( '<div id="sc-wls-field-selection" class="rt-tab-content">%s</div>', $rtWLS->rtFieldGenerator( array_merge( $rtWLS->scFieldSelectionMetaFields(), $rtWLS->scDescriptionLimitFields() ), true ) );

			$html .= sprintf( '<div id="sc-wls-style" class="rt-tab-content">%s</div>', $rtWLS->rtFieldGenerator( $rtWLS->scStyleFields(), true ) );
			$html .= '</div>';

			$rtWLS->print_html( $html, true );
		}

		public function rt_plugin_sc_pro_information( $post ) {
			global $rtWLS;

			$pro       = 'https://www.radiustheme.com/setup-wp-logo-showcase-free-version-wordpress/';
			$pro_short = 'https://1.envato.market/4jmQ9';
			$contact   = 'https://www.radiustheme.com/contact/';
			$fb        = 'https://www.facebook.com/groups/234799147426640/';
			$chat      = 'https://www.radiustheme.com/';

			$html = sprintf(
				'<div class="rt-document-box">
					<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
					<div class="rt-box-content">
						<h3 class="rt-box-title">%1$s</h3>
							<p>%2$s</p>
							<a href="' . esc_url( $pro ) . '" target="_blank" class="rt-admin-btn">%1$s</a>
					</div>
				</div>',
				esc_html__( 'Documentation', 'wp-logo-showcase' ),
				esc_html__( 'Get started by spending some time with the documentation we included step by step process with screenshots with video.', 'wp-logo-showcase' )
			);

			$html .= '<div class="rt-document-box">
						<div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
						<div class="rt-box-content">
							<h3 class="rt-box-title">Need Help?</h3>
								<p>Stuck with something? Please create a <a href="' . esc_url( $contact ) . '">ticket here</a> or post on <a href="' . esc_url( $fb ) . '">facebook group</a>. For emergency case join our <a href="' . esc_url( $chat ) . '">live chat</a>.</p>
								<a href="' . esc_url( $contact ) . '" target="_blank" class="rt-admin-btn">Get Support</a>
						</div>
					</div>';

			if ( 'settings' === $post ) {
				$html .= '<div class="rt-document-box rt-update-pro-btn-wrap">
							<a href="' . esc_url( $pro_short ) . '" target="_blank" class="rt-update-pro-btn">Update Pro To Get More Features</a>
						</div>';
			} else {
				$html .= sprintf( '<div class="rt-document-box"><div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div><div class="rt-box-content"><h3 class="rt-box-title">Pro Feature</h3>%s</div></div>', $rtWLS->get_pro_feature_list() );
			}

			$rtWLS->print_html( $html );
		}


		/**
		 * Save all the meta value for shortCode meta field
		 *
		 * @param $post_id
		 * @param $post
		 * @param $update
		 *
		 * @return mixed
		 */
		public function save_team_sc_meta_data( $post_id, $post, $update ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			global $rtWLS;

			// Only act on our shortcode post type.
			if ( ! isset( $post->post_type ) || $rtWLS->shortCodePT !== $post->post_type ) {
				return $post_id;
			}

			if ( ! $rtWLS->verifyNonce( 'sc_save' ) ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			$mates = $rtWLS->wlsScMetaNames();

			foreach ( $mates as $field ) {
				$rValue = ! empty( $_POST[ $field['name'] ] ) ? wp_unslash( $_POST[ $field['name'] ] ) : null;
				$value  = $rtWLS->sanitize( $field, $rValue );

				if ( empty( $field['multiple'] ) ) {
					update_post_meta( $post_id, $field['name'], $value );
				} else {
					delete_post_meta( $post_id, $field['name'] );

					if ( is_array( $value ) && ! empty( $value ) ) {
						foreach ( $value as $item ) {
							add_post_meta( $post_id, $field['name'], $item );
						}
					}
				}
			}
		}
	}
endif;
