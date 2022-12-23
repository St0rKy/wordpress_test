<?php

namespace PA\admin\classes;

use PA\classes\PA_Abstract_Init;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Admin area initialization
 *
 * Class PA_Admin_Init
 */
class PA_Admin_Init extends PA_Abstract_Init {

	/**
	 * Add hooks here
	 *
	 * @return void
	 */
	protected function add_actions() {

		parent::add_actions();
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Adds the admin menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		/**
		 * Create Capability filter
		 */
		$capability = apply_filters( 'pa_access_capability', 'manage_options' );

		add_menu_page(
			'Pop Aurelian',
			'Pop Aurelian',
			$capability,
			'pa_dashboard',
			array( $this, 'dash_page' ),
			$this->plugin_uri . '/images/icon.png',
			25
		);
	}

	/**
	 * Loads the main template file for the plugin
	 *
	 * @return void
	 */
	public function dash_page() {
		$this->template( 'dashboard.php' );
	}

	/**
	 * Enqueues the scripts for the administration area
	 *
	 * @param $handle
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts( $handle ) {
		/**
		 * Add styles and scripts only to our admin page
		 */
		if ( 'toplevel_page_pa_dashboard' === $handle ) {
			$this->enqueue_style( 'pa-style', $this->plugin_uri . 'css/style.min.css' );
			$this->enqueue_script( 'pa-charts', 'https://cdn.jsdelivr.net/npm/chart.js', array(), false, true );
			$this->enqueue_script( 'pa-app', $this->plugin_uri . 'js/dist/index.min.js', array(), false, true );
			wp_localize_script( 'pa-app', 'PAApp', $this->get_localization() );
		}
	}

	/**
	 * Fetches data to enqueue for the admin scripts
	 * @return array
	 */
	public function get_localization(): array {
		/**
		 * Pre-fetch the data as it's not worth calling an API endpoint in Vue to get this data when
		 * we can get it from the backend. An option to refresh the data is available in Vue so you can
		 * get a new data set
		 */
		$request  = new WP_REST_Request( 'GET', '/pa/v1/general_data' );
		$response = rest_do_request( $request );

		return array(
			'data'     => $response->get_data(),
			'settings' => json_decode( get_option( 'test_project_option' ) ),
			'routes'   => array(
				'general_data' => $this->get_route_url( 'general_data' ),
				'settings'     => $this->get_route_url( 'settings' ),
				'nonce'        => wp_create_nonce( 'wp_rest' )
			),
		);
	}
}