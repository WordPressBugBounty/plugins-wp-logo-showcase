<?php
/**
 * ShortCode Preview Class
 *
 * Renders the live layout preview on the shortcode generator screen.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSPreview' ) ) :
	/**
	 * ShortCode Preview Class
	 */
	class rtWLSPreview {

		public function __construct() {
			// Must run before _wp_admin_bar_init (template_redirect, priority 0) decides to load the admin bar.
			add_filter( 'show_admin_bar', [ $this, 'hideAdminBarInPreview' ] );
			add_action( 'template_redirect', [ $this, 'renderPreviewPage' ], 1 );
		}

		/**
		 * Front-end URL the shortcode screen posts its settings to.
		 *
		 * @return string
		 */
		public function previewUrl() {
			return add_query_arg( 'wls_sc_preview', '1', home_url( '/' ) );
		}

		/**
		 * Whether this is a front-end request for the preview page.
		 *
		 * @return bool
		 */
		private function isPreviewRequest() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- routing flag only; renderPreviewPage() checks the nonce.
			return ! is_admin() && isset( $_GET['wls_sc_preview'] );
		}

		/**
		 * Visitors never see the admin bar, and its 32px offset would shift the layout.
		 *
		 * @param bool $show Whether to show the admin bar.
		 *
		 * @return bool
		 */
		public function hideAdminBarInPreview( $show ) {
			return $this->isPreviewRequest() ? false : $show;
		}

		/**
		 * Render the unsaved shortcode settings as a bare front-end page.
		 *
		 * The shortcode screen loads this page in an iframe. It goes through
		 * wp_head()/wp_footer() like any front-end page, so the theme styles,
		 * the plugin styles and scripts and the custom CSS apply exactly as on
		 * the live site.
		 *
		 * The posted values go through the same sanitize() used when the
		 * shortcode is saved, then through the same renderer as the
		 * [logo-showcase] shortcode, so the preview gets every allowlist and
		 * escaping step the front end has.
		 */
		public function renderPreviewPage() {
			if ( ! $this->isPreviewRequest() ) {
				return;
			}

			global $rtWLS;

			$denied = esc_html__( 'Security Error !!', 'wp-logo-showcase' );

			if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : '' ) ) {
				wp_die( $denied, '', [ 'response' => 405 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			}

			if ( ! $rtWLS->verifyNonce( 'sc_preview' ) ) {
				wp_die( $denied, '', [ 'response' => 403 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			}

			// Nonce verified above via $rtWLS->verifyNonce(), which PHPCS cannot follow.
			// phpcs:disable WordPress.Security.NonceVerification.Missing

			// The preview belongs to one shortcode post: the user must be able to edit it.
			$postId = isset( $_POST['post_ID'] ) ? absint( $_POST['post_ID'] ) : 0;
			$scPost = $postId ? get_post( $postId ) : null;

			if ( ! $scPost || $rtWLS->shortCodePT !== $scPost->post_type || ! current_user_can( 'edit_post', $postId ) ) {
				wp_die( $denied, '', [ 'response' => 403 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			}

			$scMeta          = [];
			$customImageSize = [];

			foreach ( $rtWLS->wlsScMetaNames() as $field ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by $rtWLS->sanitize() below.
				$rValue = ! empty( $_POST[ $field['name'] ] ) ? wp_unslash( $_POST[ $field['name'] ] ) : null;
				$value  = $rtWLS->sanitize( $field, $rValue );

				if ( isset( $field['type'] ) && 'image_size' === $field['type'] ) {
					$customImageSize = $value;
					continue;
				}

				// Same shape get_post_meta( $id ) returns for what save_post would store.
				$scMeta[ $field['name'] ] = empty( $field['multiple'] ) ? [ (string) $value ] : $value;
			}

			// phpcs:enable WordPress.Security.NonceVerification.Missing

			$adminUrl    = wp_parse_url( admin_url() );
			$adminOrigin = $adminUrl['scheme'] . '://' . $adminUrl['host'] . ( ! empty( $adminUrl['port'] ) ? ':' . $adminUrl['port'] : '' );

			// Only for the shortcode screen: never cached, indexed or framed by another site.
			nocache_headers();
			header( 'X-Robots-Tag: noindex, nofollow' );
			header( "Content-Security-Policy: frame-ancestors 'self' " . $adminOrigin );

			$rtWLS->render(
				'preview',
				[
					'html'        => $rtWLS->renderShowcase( $scMeta, $customImageSize, $postId, true ),
					'adminOrigin' => $adminOrigin,
				]
			);
			exit;
		}
	}
endif;
