<?php
/**
 * NavMenu class
 *
 * @package CoeditorAirship
 */

namespace Coeditor\WordpressAirship\Classes;

/**
 * Class NavMenu
 *
 * Registers the NavMenu class
 *
 * @package CoeditorAirship
 */
class NavMenu {

	/**
	 * Holds the submenu items
	 *
	 * @var array
	 */
	private $submenus = [
		[
			'page_title' => 'Segments',
			'menu_title' => 'Segments',
			'slug'       => 'airship-segments',
			'template'   => COEDITOR_AIRSHIP_ADMIN_PAGES . 'segments.php',
		],
		[
			'page_title' => 'Airship Settings',
			'menu_title' => 'Settings',
			'slug'       => 'airship-settings',
			'template'   => COEDITOR_AIRSHIP_ADMIN_PAGES . 'settings-page.php',
		],
	];

	public function register() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	/**
	 * Adds the custom menu to the admin dashboard
	 *
	 * @return void
	 */
	public function add_menu() {
		add_menu_page(
			'Airship',
			'Airship',
			'manage_options',
			'airship',
			[ $this, 'render_menu' ],
			'dashicons-buddicons-activity',
			6
		);

		// Register submenus
		foreach ( $this->submenus as $submenu ) {
			add_submenu_page(
				'airship',
				$submenu['page_title'],
				$submenu['menu_title'],
				'manage_options',
				$submenu['slug'],
				function () use ( $submenu ) {
					$this->render_template( $submenu['template'] );
				}
			);
		}
	}

	/**
	 * Registers a submenu item
	 *
	 * @param string $page_title Page title.
	 * @param string $menu_title Menu title.
	 * @param string $slug Unique slug for submenu.
	 * @param string $template Path to template file.
	 */
	public function add_submenu( $page_title, $menu_title, $slug, $template ) {
		$this->submenus[] = [
			'page_title' => $page_title,
			'menu_title' => $menu_title,
			'slug'       => $slug,
			'template'   => $template,
		];
	}

	/**
	 * Renders the main menu page
	 */
	public function render_menu() {
		$this->render_template( COEDITOR_AIRSHIP_ADMIN_PAGES . '/main-page.php' );
	}

	/**
	 * Renders the template file if exists
	 *
	 * @param string $template Path to template file.
	 */
	private function render_template( $template ) {
		if ( file_exists( $template ) ) {
			include $template;
		}
	}
}
