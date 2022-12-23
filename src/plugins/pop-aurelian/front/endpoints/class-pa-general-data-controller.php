<?php

namespace PA\front\endpoints;

use PA\classes\PA_Abstract_REST_Controller;
use PA\classes\PA_Const;
use WP_REST_Server;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Controller for General Data API Calls
 */
class PA_General_Data_Controller extends PA_Abstract_REST_Controller {

	public string $base = 'general_data';

	/**
	 * Register Routes
	 */
	public function register_routes() {
		register_rest_route( self::$namespace . self::$version, '/' . $this->base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'fetch_general_data' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );

		register_rest_route( self::$namespace . self::$version, '/' . $this->base . '/refresh', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'refresh_general_data' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );
	}

	/**
	 * Saves the general data to the DB
	 *
	 * @param $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function fetch_general_data( $request ) {
		/**
		 * Instead of doing useless checks if we should be refreshing
		 * the data, let's use a transient which expires every hour,
		 * If we don't have any data in the transient it means we should
		 * re-fetch it
		 */
		$data = get_transient( 'test_project_data' );

		if ( empty( $data ) ) {
			$result = $this->fetch_data();

			if ( ! $result ) {
				return new WP_Error( 'fetch-failed', __( 'Data could not be fetched, please try again.', PA_Const::T ) );
			}
			$data = $result;
		}

		return new WP_REST_Response( json_decode( $data ), 200 );
	}

	/**
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function refresh_general_data() {

		$result = $this->fetch_data();

		if ( ! $result ) {
			return new WP_Error( 'fetch-failed', __( 'Data could not be fetched, please try again.', PA_Const::T ) );
		}

		return new WP_REST_Response( json_decode($result), 200 );
	}

	/**
	 * Fetches the data
	 * @return mixed|WP_Error
	 */
	public function fetch_data() {

		$result = wp_remote_get( PA_Const::API_URL );

		if ( ! $result ) {
			return false;
		}

		set_transient( 'test_project_data', $result['body'], 3600 );

		return $result['body'];
	}
}
