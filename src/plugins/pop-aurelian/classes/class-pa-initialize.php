<?php

namespace PA\classes;

use PA\admin\classes\PA_Admin_Init;
use PA\front\classes\PA_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Initialize the plugin
 *
 * Class PA_Initialize
 */
class PA_Initialize {

	/**
	 * Constructor
	 */
	public function __construct() {
		register_activation_hook( dirname( dirname( __FILE__ ) ) . '/pop-aurelian.php', array(
			$this,
			'add_default_settings'
		) );

		$this->init();
		$this->admin_init();
	}

	/**
	 * Initialize the front end
	 */
	private function init() {
		new PA_Init();
	}

	/**
	 * Initialize the backend
	 */
	private function admin_init() {
		if ( is_admin() ) {
			new PA_Admin_Init( true );
		}
	}

	/**
	 * Sets the default settings on plugin activation
	 *
	 * @return void
	 */
	public function add_default_settings() {
		$settings = get_option( 'test_project_option' );

		if ( ! $settings ) {
			update_option( 'test_project_option', json_encode( (object) PA_Const::get_default_settings() ) );
		}
	}
}
