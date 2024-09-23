<?php
/**
 * Settings view
 *
 * @package RT_WSL
 */

global $rtWLS;

$doc = 'https://www.radiustheme.com/setup-wp-logo-showcase-free-version-wordpress/';
?>

<div class="wrap">
	<h2><?php esc_html_e( 'WP Logo Showcase Settings', 'wp-logo-showcase' ); ?></h2>
	<div class="rt-settings-container">
		<div class="rt-setting-title">
			<h3>
				<?php esc_html_e( 'General settings', 'wp-logo-showcase' ); ?>
				<a style="margin-left: 15px; font-size: 15px;"
					href="<?php echo esc_url( $doc ); ?>"
					target="_blank"><?php esc_html_e( 'Documentation', 'wp-logo-showcase' ); ?></a>
			</h3>
		</div>
		<div class="rt-setting-content">
			<div class="rt-response"></div>
			<form id="rt-wls-settings-form">
				<div class="rt-tab-container">
					<ul class="rt-tab-nav">
						<li>
							<a href="#s-wls-general"><i class="dashicons dashicons-layout"></i><?php esc_html_e( 'General Settings', 'wp-logo-showcase' ); ?>
							</a>
						</li>
						<li>
							<a href="#s-wls-custom-css"><i class="dashicons dashicons-admin-customizer"></i><?php esc_html_e( 'Custom CSS', 'wp-logo-showcase' ); ?>
							</a>
						</li>
					</ul>
					<div id="s-wls-general" class="rt-tab-content">
						<?php $rtWLS->print_html( $rtWLS->rtFieldGenerator( $rtWLS->rtWLSGeneralSettings(), true ), true ); ?>
					</div>
					<div id="s-wls-custom-css" class="rt-tab-content">
						<?php $rtWLS->print_html( $rtWLS->rtFieldGenerator( $rtWLS->rtWLSCustomCss(), true ), true ); ?>
					</div>
				</div>
				<p class="submit">
					<input type="submit"
							name="submit"
							class="rt-admin-btn button button-primary"
							id="rtSaveButton"
							value="Save Changes">
				</p>
				<?php wp_nonce_field( $rtWLS->nonceText(), $rtWLS->nonceId() ); ?>
			</form>
			<div class="rt-response"></div>
		</div>
		<div class="rt-pro-feature-content">
			<?php $rtWLS->rt_plugin_sc_pro_information( 'settings' ); ?>
		</div>
	</div>
</div>
