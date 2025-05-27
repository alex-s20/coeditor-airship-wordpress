<?php
/**
 * Airship Push Notifications integration for WordPress.
 *
 * @package CoeditorAirship
 */

namespace Coeditor\WordpressAirship\Classes;

class AirshipPushNotifications {

	/**
	 * Registers hooks for Airship push notifications.
	 */
	public static function register(): void {
		add_action( 'wp_head', [ __CLASS__, 'add_push_notification_script' ], 3 );
		add_action( 'wp_head', [ __CLASS__, 'register_service_worker_script' ], 4 );
		add_action( 'wp_footer', [ __CLASS__, 'web_notifications_user_consent' ], 5 );

		add_action( 'init', [ __CLASS__, 'add_pushworker' ], 10 );
		add_filter( 'query_vars', [ __CLASS__, 'add_pushworker_query_var' ], 10 );
		add_action( 'parse_request', [ __CLASS__, 'handle_pushworker_request' ], 10 );
	}

	/**
	 * Outputs the Airship push notification SDK script.
	 */
	public static function add_push_notification_script() {
		$vapid_key = esc_js( get_option( 'coeditor_airship_vapid_key', '' ) );
		$app_key   = esc_js( get_option( 'coeditor_airship_sdk_app_key', '' ) );
		$token     = esc_js( get_option( 'coeditor_airship_sdk_token', '' ) );
		?>
	<script type="text/javascript">
		!function(n,r,e,t,c){
			var i,o="Promise" in n,
			u={
				then:function(){ return u },
				catch:function(n){ return n(new Error("Airship SDK Error: Unsupported browser")), u }
			},
			s=o ? new Promise((function(n,r){ i=function(e,t){ e ? r(e) : n(t) }})) : u;
			s._async_setup=function(n){ if(o) try { i(null, n(c)) } catch(n) { i(n) } },
			n[t]=s;
			var a=r.createElement("script");
			a.src=e, a.async=!0, a.id="_uasdk", a.rel=t, r.head.appendChild(a)
		}(window, document, "https://aswpsdkus.com/notify/v2/ua-sdk.min.js", "UA", {
			vapidPublicKey: "<?php echo $vapid_key; ?>",
			appKey: "<?php echo $app_key; ?>",
			token: "<?php echo $token; ?>",
		});
	</script>
		<?php
	}


	/**
	 * Registers the service worker on the front-end.
	 */
	public static function register_service_worker_script(): void {
		?>
		<script>
			if ('serviceWorker' in navigator) {
				window.addEventListener('load', function() {
					navigator.serviceWorker.register('<?php echo esc_url( home_url( '/push-worker.js' ) ); ?>');
				});
			}
		</script>
		<?php
	}

	/**
	 * Outputs the Airship user consent registration script.
	 */
	public static function web_notifications_user_consent(): void {
		?>
		<script>
			UA.then(function(sdk) {
				sdk.register();
			});
		</script>
		<?php
	}

	/**
	 * Adds rewrite rule to serve the push-worker.js file.
	 */
	public static function add_pushworker(): void {
		add_rewrite_rule( '^push-worker\.js$', 'index.php?coeditor_airship_pushworker=true', 'top' );
	}

	/**
	 * Adds custom query var so WP can recognize push-worker requests.
	 *
	 * @param array $vars Public query vars.
	 * @return array Modified query vars.
	 */
	public static function add_pushworker_query_var( $vars ): array {
		$vars[] = 'coeditor_airship_pushworker';
		return $vars;
	}

	/**
	 * Handles the request to serve the service worker JavaScript.
	 *
	 * @param \WP $wp WordPress object.
	 */
	public static function handle_pushworker_request() {
		if ( $_SERVER['REQUEST_URI'] === '/push-worker.js' ) {
			header( 'Content-Type: application/javascript' );

			$custom_script = get_option( 'coeditor_airship_custom_worker_js' );
			if ( ! empty( $custom_script ) ) {
				echo $custom_script;
				exit;
			}

			$theme_dir = get_stylesheet_directory();
			$worker_path = $theme_dir . '/push-worker.js';
			if ( file_exists( $worker_path ) ) {
				readfile( $worker_path );
				exit;
			}

			http_response_code( 404 );
			echo '// No service worker found';
			exit;
		}
	}
}