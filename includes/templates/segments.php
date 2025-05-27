<?php
/**
 * Airship Lists Template
 * 
 * @package CoeditorAirship
 */

$client         = new \Coeditor\WordpressAirship\Classes\AirshipClient();
$segments       = [];
$segments_error = null;

try {
	$response = $client->get_segments();
	$segments = $response['segments'] ?? [];
} catch ( Exception $e ) {
	$segments_error = $e->getMessage();
}

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Segments', 'coeditor-airship' ); ?></h1>

	<div class="coeditor-airship__segments">
		<?php if ( $segments_error ) : ?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( 'Error fetching segments: ' . $segments_error ); ?></p>
			</div>

		<?php elseif ( empty( $segments ) ) : ?>
			<p><?php esc_html_e( 'No segments available.', 'coeditor-airship' ); ?></p>

		<?php else : ?>
			<?php foreach ( $segments as $segment ) : ?>
				<div class="coeditor-airship__segments-card">
					<h2><?php echo esc_html( $segment['display_name'] ); ?></h2>
					ID: <?php echo esc_html( $segment['id'] ); ?><br />
					Creation date: <?php echo esc_html( date( 'dS M Y H:i:s', $segment['creation_date'] ) ); ?><br />
					Last modified: <?php echo esc_html( date( 'dS M Y H:i:s', $segment['modification_date'] ) ); ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

</div>
