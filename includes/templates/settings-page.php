<?php
/**
 * Settings page
 *
 * @package CoeditorAirship
 */
?>
<div class="wrap">
	<h1><?php esc_html_e( 'WordPress » Airship settings', 'coeditor-airship' ); ?></h1>

	<nav class="nav-tab-wrapper">
		<a href="#settings-tab1" class="coeditor-airship__nav-tab nav-tab nav-tab-active"><?php esc_html_e( 'General', 'coeditor-airship' ); ?></a>
		<a href="#settings-tab2" class="coeditor-airship__nav-tab nav-tab"><?php esc_html_e( 'Desktop notifications', 'coeditor-airship' ); ?></a>
		<a href="#settings-tab3" class="coeditor-airship__nav-tab nav-tab"><?php esc_html_e( 'Permissions', 'coeditor-airship' ); ?></a>
		<a href="#settings-tab4" class="coeditor-airship__nav-tab nav-tab"><?php esc_html_e( 'API settings', 'coeditor-airship' ); ?></a>
	</nav>

	<div class="coeditor-airship__tab-content">
		<div id="settings-tab1" class="coeditor-airship__tab-pane active">
			<h2><?php esc_html_e( 'General Settings', 'coeditor-airship' ); ?></h2>
		</div>

		<div id="settings-tab2" class="coeditor-airship__tab-pane">
			<h2><?php esc_html_e( 'Desktop notifications', 'coeditor-airship' ); ?></h2>
			<p><?php esc_html_e( 'More advanced settings go here.', 'coeditor-airship' ); ?></p>
			Enabled<br />
			Show prompt only on homepage<br />
		</div>

		<div id="settings-tab3" class="coeditor-airship__tab-pane">
			<h2><?php esc_html_e( 'Permissions', 'coeditor-airship' ); ?></h2>
			<p><?php esc_html_e( 'Information about the plugin.', 'coeditor-airship' ); ?></p>
		</div>

		<div id="settings-tab4" class="coeditor-airship__tab-pane">
			<h2><?php esc_html_e( 'API settings', 'coeditor-airship' ); ?></h2>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'coeditor_airship_settings' );
				do_settings_sections( 'coeditor_airship_settings' );
				?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'App Key', 'coeditor-airship' ); ?></th>
						<td><input type="text" name="coeditor_airship_app_key" value="<?php echo esc_attr( get_option( 'coeditor_airship_app_key' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Master Secret', 'coeditor-airship' ); ?></th>
						<td><input type="text" name="coeditor_airship_master_secret" value="<?php echo esc_attr( get_option( 'coeditor_airship_master_secret' ) ); ?>" class="regular-text" /></td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Notifications Keys', 'coeditor-airship' ); ?></h2>
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

				<h2><?php esc_html_e( 'Push-worker.js Code', 'coeditor-airship' ); ?></h2>
				<p><?php esc_html_e( 'Paste your push-worker.js contents here. This will be served from /push-worker.js. The field clears on page reload for security.', 'coeditor-airship' ); ?></p>

				<table class="form-table">
					<tr>
						<th scope="row"><label for="coeditor_airship_custom_worker_js">Service Worker Code</label></th>
						<td>
							<textarea name="coeditor_airship_custom_worker_js" id="coeditor_airship_custom_worker_js" rows="10" cols="80" class="large-text code"><?php echo ''; ?></textarea>
							<p class="description"><?php esc_html_e( 'Paste your JavaScript here. Saved securely. This box clears on reload.', 'coeditor-airship' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<?php if ( get_option( 'coeditor_airship_custom_worker_js' ) ) : ?>
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'coeditor_airship_delete_worker_action', '_wpnonce' ); ?>
					<input type="hidden" name="action" value="coeditor_airship_delete_worker">
					<?php submit_button( __( 'Delete Script', 'coeditor-airship' ), 'secondary' ); ?>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>
