<?php
/**
 * Airship client (For app notifications)
 *
 * @package CoeditorAirship
 */

namespace Coeditor\WordpressAirship\Classes;

use Exception;

/**
 * Class Airship
 *
 * Client class for handling requests and common actions with the Airship API.
 *
 * @package SpectatorPlugin\Clients
 */
class AirshipClient {



	/**
	 * Airship endpoint for API requests.
	 */
	const AIRSHIP_ENDPOINT = 'https://go.urbanairship.com/api';

	/**
	 * Gets the application key for making API requests
	 *
	 * @throws \Exception
	 * @return string app key
	 */
	private function get_app_key() {
		$app_key = get_option( 'coeditor_airship_app_key' );

		if ( empty( $app_key ) ) {
			throw new Exception( esc_html__( 'Airship application key is missing.', 'spectator-plugin' ) );
		}

		return $app_key;
	}

	/**
	 * Gets the master API key
	 *
	 * @throws \Exception
	 * @return string app key
	 */
	private function get_master_secret() {
		$master_secret = get_option( 'coeditor_airship_master_secret' );

		if ( empty( $master_secret ) ) {
			throw new Exception( esc_html__( 'Airship API master secret is missing.', 'spectator-plugin' ) );
		}

		return $master_secret;
	}

	/**
	 * Sets the request headers and authetnication - defaults to JSON
	 *
	 * @param string $accept_type (optional) Default JSON 
	 * @return array The request headers
	 */
	private function request_headers( string $accept_type = 'json' ) {
		$credentials = base64_encode( $this->get_app_key() . ':' . $this->get_master_secret() );

		return [
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/vnd.urbanairship+' . $accept_type . '; version=3;',
			'Authorization' => 'Basic ' . $credentials,
		];
	}

	/**
	 * Get the channel IDs subscribed to a particular list
	 *
	 * @param string $list_id The list ID
	 * @return array Array of channel IDs subscribed to the list
	 */
	public function get_channel_ids_from_list( string $list_id ) {
		$url = self::AIRSHIP_ENDPOINT . '/lists/' . $list_id . '/csv';

		$response = wp_safe_remote_get(
			$url,
			[
				'headers' => $this->request_headers( 'csv' ),
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'Failed to retrieve channel IDs: ' . $response->get_error_message() );
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 200 ) {
			throw new Exception( 'Error retrieving channel IDs: ' . $response_body );
		}

		$lines = explode( PHP_EOL, $response_body );

		$channel_ids = [];

		foreach ( $lines as $index => $line ) {
			if ( $index === 0 ) {
				continue;
			}

			$data = str_getcsv( $line );

			if ( isset( $data[1] ) ) {
				$channel_ids[] = $data[1];
			}
		}

		return $channel_ids;
	}

	/**
	 * Send push notification
	 *
	 * @param array $payload The data to send as part of the notification
	 * @return void
	 */
	public function send_push_notification( $payload ) {
		$url = self::AIRSHIP_ENDPOINT . '/push';

		$body = json_encode( $payload );

		$response = wp_safe_remote_post(
			$url,
			[
				'headers' => $this->request_headers(),
				'body'    => $body,
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'Failed to send push notification: ' . $response->get_error_message() );
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 200 ) {
			throw new Exception( 'Error sending notification: ' . $response_body );
		}
	}

	/**
	 * Get scheduled notifications
	 *
	 * @return array Array of scheduled notifications
	 * @throws Exception If the request fails
	 */
	public function get_scheduled_notifications() {
		 $url = self::AIRSHIP_ENDPOINT . '/schedules';

		$response = wp_safe_remote_get(
			$url,
			[
				'headers' => $this->request_headers(),
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'Failed to retrieve scheduled notifications: ' . $response->get_error_message() );
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 200 ) {
			throw new Exception( 'Error retrieving scheduled notifications: ' . $response_body );
		}

		return json_decode( $response_body, true );
	}

	/**
	 * Delete a scheduled notification
	 *
	 * @param string $schedule_id
	 * @return void
	 */
	public function delete_scheduled_notification( $schedule_id ) {
		$url = self::AIRSHIP_ENDPOINT . '/schedules/' . $schedule_id;

		$response = wp_safe_remote_request(
			$url,
			[
				'method'  => 'DELETE',
				'headers' => $this->request_headers(),
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'Failed to delete scheduled notification: ' . $response->get_error_message() );
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 204 ) {
			throw new Exception( 'Error deleting scheduled notification: ' . wp_remote_retrieve_body( $response ) );
		}
	}

	/**
	 * Get subscription lists
	 *
	 * @return array Array of subscription lists
	 * @throws Exception If the request fails
	 */
	public function get_segments() {
		$url = self::AIRSHIP_ENDPOINT . '/segments';

		$response = wp_safe_remote_get(
			$url,
			[
				'headers' => $this->request_headers(),
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'Failed to retrieve segments: ' . $response->get_error_message() );
		}

		$response_body = wp_remote_retrieve_body( $response );

		return json_decode( $response_body, true );
	}

	/**
	 * Get recent notification responses from Airship
	 *
	 * @param int $limit Number of results to retrieve
	 * @return array|\WP_Error
	 */
	public function get_notifications() {
		$app_key       = get_option( 'coeditor_airship_app_key' );
		$master_secret = get_option( 'coeditor_airship_master_secret' );

		if ( ! $app_key || ! $master_secret ) {
			return new \WP_Error( 'missing_credentials', 'Airship API credentials are missing.' );
		}

		$start = gmdate( 'Y-m-d\TH:i:s', strtotime( '-30 days' ) ); // ISO8601 in UTC
		$end   = gmdate( 'Y-m-d\TH:i:s' ); // now

		$url = add_query_arg(
			[
				'start' => $start,
				'end'   => $end,
				'limit' => 99,
			],
			'https://go.urbanairship.com/api/reports/responses/list'
		);

		$response = wp_remote_get(
			$url,
			[
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( $app_key . ':' . $master_secret ),
					'Accept'        => 'application/vnd.urbanairship+json; version=3;',
				],
				'timeout' => 30,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			return new \WP_Error( 'airship_error', 'Failed to fetch notifications from Airship: ' . $code );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
