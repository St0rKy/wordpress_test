<?php

namespace PA\front\endpoints;


use PA\classes\PA_Abstract_REST_Controller;
use PA\classes\PA_Const;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Controller for Settings API Calls
 */
class PA_Settings_Controller extends PA_Abstract_REST_Controller {

	public string $base = 'settings';

	/**
	 * Register Routes
	 */
	public function register_routes() {
		register_rest_route( self::$namespace . self::$version, '/' . $this->base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'fetch_settings' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );

		register_rest_route( self::$namespace . self::$version, '/' . $this->base . '/(?P<ID>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'edit_setting' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );
	}

	/**
	 * Fetches all the settings from the db
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function fetch_settings( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( json_decode( get_option( 'test_project_option' ) ), 200 );
	}

	/**
	 * Edits a specific setting based on the ID
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function edit_setting( WP_REST_Request $request ) {
		$params = $request->get_params();
		$value = json_decode($request->get_body())->value;
		$data = (array) json_decode( get_option( 'test_project_option' ) );

		foreach ($data as $key => $item ) {
			if($item->id === (int) $params['ID']) {
				$data[$key]->value = $value;
			}
		}

		update_option( 'test_project_option', json_encode( (object) $data ) );

		return new WP_REST_Response( true, 200 );
	}


}
