<?php
/**
 * Template: Shortcode layout preview
 *
 * A bare front-end page shown in an iframe on the shortcode screen. The
 * theme, plugin styles and scripts and custom CSS load through wp_head() and
 * wp_footer() exactly as on a live page, so the preview matches the front end.
 *
 * @package RT_WSL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<style>
		/* The page background belongs to the theme, not the showcase: let the edit screen show through. */
		html, body.wls-sc-preview-page { background: transparent !important; }
	</style>
</head>
<body <?php body_class( 'wls-sc-preview-page' ); ?>>
<?php wp_body_open(); ?>
<div id="wls-sc-preview-page" class="entry-content" style="padding: 20px;">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by renderShowcase(); echoed unchanged, as the_content does with shortcode output.
	echo $html;
	?>
</div>
<?php wp_footer(); ?>
<script>
	( function () {
		// Report the content height so the shortcode screen can size the iframe.
		var page = document.getElementById( 'wls-sc-preview-page' ),
			origin = <?php echo wp_json_encode( $adminOrigin ); ?>;

		function report() {
			window.parent.postMessage( { wlsPreviewHeight: Math.ceil( page.getBoundingClientRect().bottom + window.pageYOffset ) }, origin );
		}

		if ( window.ResizeObserver ) {
			new ResizeObserver( report ).observe( page );
		}
		window.addEventListener( 'load', report );
		report();
	} )();
</script>
</body>
</html>
