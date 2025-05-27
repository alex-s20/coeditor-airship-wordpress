<?php
/**
 * Settings page
 *
 * @package CoeditorAirship
 */
?>
<div class="wrap">
	<h1><?php esc_html_e( 'WordPress » Airship settings', 'your-textdomain' ); ?></h1>

	<nav class="nav-tab-wrapper">
		<a href="#tab1" class="coeditor-airship__nav-tab nav-tab nav-tab-active"><?php esc_html_e( 'General', 'your-textdomain' ); ?></a>
		<a href="#tab2" class="coeditor-airship__nav-tab nav-tab"><?php esc_html_e( 'Desktop notifications', 'your-textdomain' ); ?></a>
		<a href="#tab3" class="coeditor-airship__nav-tab nav-tab"><?php esc_html_e( 'Permissions', 'your-textdomain' ); ?></a>
		<a href="#tab4" class="coeditor-airship__nav-tab nav-tab"><?php esc_html_e( 'API settings', 'your-textdomain' ); ?></a>
	</nav>

	<div class="coeditor-airship__tab-content">
		<div id="tab1" class="coeditor-airship__tab-pane active">
			<h2><?php esc_html_e( 'General Settings', 'your-textdomain' ); ?></h2>
		</div>

		<div id="tab2" class="coeditor-airship__tab-pane">
			<h2><?php esc_html_e( 'Desktop notifications', 'your-textdomain' ); ?></h2>
			<p><?php esc_html_e( 'More advanced settings go here.', 'your-textdomain' ); ?></p>
			Enabled<br />
			Show prompt only on homepage<br />
		</div>

		<div id="tab3" class="coeditor-airship__tab-pane">
			<h2><?php esc_html_e( 'Permissions', 'your-textdomain' ); ?></h2>
			<p><?php esc_html_e( 'Information about the plugin.', 'your-textdomain' ); ?></p>
		</div>

		<div id="tab4" class="coeditor-airship__tab-pane">
			<h2><?php esc_html_e( 'API settings', 'your-textdomain' ); ?></h2>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'coeditor_airship_settings' );
				do_settings_sections( 'coeditor_airship_settings' );
				?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'App Key', 'your-textdomain' ); ?></th>
						<td><input type="text" name="coeditor_airship_app_key" value="<?php echo esc_attr( get_option( 'coeditor_airship_app_key' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Master Secret', 'your-textdomain' ); ?></th>
						<td><input type="text" name="coeditor_airship_master_secret" value="<?php echo esc_attr( get_option( 'coeditor_airship_master_secret' ) ); ?>" class="regular-text" /></td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Notifications Keys', 'your-textdomain' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="coeditor_airship_vapid_key">VAPID Public Key</label></th>
						<td><input type="text" name="coeditor_airship_vapid_key" id="coeditor_airship_vapid_key" class="regular-text" value="<?php echo esc_attr( get_option( 'coeditor_airship_vapid_key', '' ) ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="coeditor_airship_sdk_app_key">App Key</label></th>
						<td><input type="text" name="coeditor_airship_sdk_app_key" id="coeditor_airship_sdk_app_key" class="regular-text" value="<?php echo esc_attr( get_option( 'coeditor_airship_sdk_app_key', '' ) ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="coeditor_airship_sdk_token">Token</label></th>
						<td><input type="text" name="coeditor_airship_sdk_token" id="coeditor_airship_sdk_token" class="regular-text" value="<?php echo esc_attr( get_option( 'coeditor_airship_sdk_token', '' ) ); ?>"></td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Push-worker.js Code', 'your-textdomain' ); ?></h2>
				<p><?php esc_html_e( 'Paste your push-worker.js contents here. This will be served from /push-worker.js. The field clears on page reload for security.', 'your-textdomain' ); ?></p>

				<table class="form-table">
					<tr>
						<th scope="row"><label for="coeditor_airship_custom_worker_js">Service Worker Code</label></th>
						<td>
							<textarea name="coeditor_airship_custom_worker_js" id="coeditor_airship_custom_worker_js" rows="10" cols="80" class="large-text code"><?php echo ''; ?></textarea>
							<p class="description"><?php esc_html_e( 'Paste your JavaScript here. Saved securely. This box clears on reload.', 'your-textdomain' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<?php if ( get_option( 'coeditor_airship_custom_worker_js' ) ) : ?>
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'coeditor_airship_delete_worker_action', '_wpnonce' ); ?>
					<input type="hidden" name="action" value="coeditor_airship_delete_worker">
					<?php submit_button( __( 'Delete Script', 'your-textdomain' ), 'secondary' ); ?>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>
