<?php

namespace PA\classes;


use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Base class for extending the WP_REST_Controller taken from http://v2.wp-api.org/extending/adding/
 */

/**
 * Class PLCO_REST_Controller
 */
abstract class PA_Abstract_REST_Controller {

	/**
	 * @var int $version
	 */
	public static int $version = 1;

	/**
	 * @var string $namespace
	 */
	public static string $namespace = 'pa/v';

	/**
	 * @var string $base
	 */
	public string $base = '';

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route( self::$namespace . self::$version, '/' . $this->base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( true ),
			),
		) );

		register_rest_route( self::$namespace . self::$version, '/' . $this->base . '/table', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_table_items' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(),
			),
		) );

		register_rest_route( self::$namespace . self::$version, '/' . $this->base . '/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => array(
						'default' => 'view',
					),
				),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( false ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(
					'force' => array(
						'default' => false,
					),
				),
			),
		) );

		register_rest_route( self::$namespace . self::$version, '/' . $this->base . '/schema', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( self::$namespace . self::$version, '/' . $this->base . '/activity/(?P<id>[\d]+)/(?P<offset>[\d]+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_activity_log_extension' ),
		) );
	}

	/**
	 * Prepare a response for inserting into a collection.
	 *
	 * @param WP_REST_Response $response Response object.
	 *
	 * @return array Response data, ready for insertion into collection data.
	 */
	public function prepare_response_for_collection( WP_REST_Response $response ): array {

		$data  = (array) $response->get_data();
		$links = WP_REST_Server::get_response_links( $response );
		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		return $data;
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_table_items( WP_REST_Request $request ): WP_REST_Response {
		$items = array(); //do a query, call another class, etc
		$data  = array();
		foreach ( $items as $item ) {
			$itemdata = $this->prepare_item_for_response( $item, $request );
			$data[]   = $this->prepare_response_for_collection( $itemdata );
		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_items( WP_REST_Request $request ): WP_REST_Response {
		$items = array(); //do a query, call another class, etc
		$data  = array();
		foreach ( $items as $item ) {
			$itemdata = $this->prepare_item_for_response( $item, $request );
			$data[]   = $this->prepare_response_for_collection( $itemdata );
		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( WP_REST_Request $request ) {
		//get parameters from request
		$params = $request->get_params();
		$item   = array();//do a query, call another class, etc
		$data   = $this->prepare_item_for_response( $item, $request );

		//return a response or error based on some conditional
		if ( 1 == 1 ) {
			return new WP_REST_Response( $data, 200 );
		} else {
			return new WP_Error( 'code', __( 'message', PA_Const::T ) );
		}
	}

	/**
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( WP_REST_Request $request ) {

		$item = $this->prepare_item_for_database( $request );

		if ( function_exists( 'slug_some_function_to_create_item' ) ) {
			$data = slug_some_function_to_create_item( $item );
			if ( is_array( $data ) ) {
				return new WP_REST_Response( $data, 200 );
			}
		}

		return new WP_Error( 'cant-create', __( 'message', PA_Const::T ), array( 'status' => 500 ) );
	}

	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( WP_REST_Request $request ) {
		$item = $this->prepare_item_for_database( $request );

		if ( function_exists( 'slug_some_function_to_update_item' ) ) {
			$data = slug_some_function_to_update_item( $item );
			if ( is_array( $data ) ) {
				return new WP_REST_Response( $data, 200 );
			}
		}

		return new WP_Error( 'cant-update', __( 'message', PA_Const::T ), array( 'status' => 500 ) );

	}

	/**
	 * Delete one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( WP_REST_Request $request ) {
		$item = $this->prepare_item_for_database( $request );

		if ( function_exists( 'slug_some_function_to_delete_item' ) ) {
			$deleted = slug_some_function_to_delete_item( $item );
			if ( $deleted ) {
				return new WP_REST_Response( true, 200 );
			}
		}

		return new WP_Error( 'cant-delete', __( 'message', PA_Const::T ), array( 'status' => 500 ) );
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function get_items_permissions_check( WP_REST_Request $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @return bool
	 */
	public function admin_permissions_check(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @return bool
	 */
	public function no_permission_required(): bool {
		return true;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function login_check( WP_REST_Request $request ): bool {
		$params = $request->get_params();
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! $params["user_id"] ) {
			return false;
		}

		$user_id = get_current_user_id();
		if ( (int) $user_id === (int) $params["user_id"] ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function get_item_permissions_check( WP_REST_Request $request ): bool {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Check if a given request has access to create items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function create_item_permissions_check( WP_REST_Request $request ): bool {
		return current_user_can( 'edit_something' );
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function update_item_permissions_check( WP_REST_Request $request ): bool {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function delete_item_permissions_check( WP_REST_Request $request ): bool {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param WP_REST_Request $request Request object
	 *
	 * @return array
	 */
	protected function prepare_item_for_database( WP_REST_Request $request ): array {
		return array();
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress's representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, WP_REST_Request $request ) {
		return $item;
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params(): array {
		return array(
			'page'     => array(
				'description'       => 'Current page of the collection.',
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => 'Maximum number of items to be returned in result set.',
				'type'              => 'integer',
				'default'           => 10,
				'sanitize_callback' => 'absint',
			),
			'search'   => array(
				'description'       => 'Limit results to those matching a string.',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Get an array of endpoint arguments from the item schema for the controller.
	 *
	 * @param string $method HTTP method of the request. The arguments
	 *                       for `CREATABLE` requests are checked for required
	 *                       values and may fall-back to a given default, this
	 *                       is not done on `EDITABLE` requests. Default is
	 *                       WP_REST_Server::CREATABLE.
	 *
	 * @return array $endpoint_args
	 */
	public function get_endpoint_args_for_item_schema( string $method = WP_REST_Server::CREATABLE ): array {

		$schema            = $this->get_item_schema();
		$schema_properties = ! empty( $schema['properties'] ) ? $schema['properties'] : array();
		$endpoint_args     = array();

		foreach ( $schema_properties as $field_id => $params ) {

			// Arguments specified as `readonly` are not allowed to be set.
			if ( ! empty( $params['readonly'] ) ) {
				continue;
			}

			$endpoint_args[ $field_id ] = array(
				'validate_callback' => array( $this, 'validate_schema_property' ),
				'sanitize_callback' => array( $this, 'sanitize_schema_property' ),
			);

			if ( WP_REST_Server::CREATABLE === $method && isset( $params['default'] ) ) {
				$endpoint_args[ $field_id ]['default'] = $params['default'];
			}

			if ( WP_REST_Server::CREATABLE === $method && ! empty( $params['required'] ) ) {
				$endpoint_args[ $field_id ]['required'] = true;
			}

			// Merge in any options provided by the schema property.
			if ( isset( $params['arg_options'] ) ) {

				// Only use required / default from arg_options on CREATABLE endpoints.
				if ( WP_REST_Server::CREATABLE !== $method ) {
					$params['arg_options'] = array_diff_key( $params['arg_options'], array(
						'required' => '',
						'default'  => ''
					) );
				}

				$endpoint_args[ $field_id ] = array_merge( $endpoint_args[ $field_id ], $params['arg_options'] );
			}
		}

		return $endpoint_args;
	}

	/**
	 * Get the item's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema(): array {
		return $this->add_additional_fields_schema( array() );
	}

	/**
	 * Add the schema from additional fields to a schema array.
	 *
	 * The type of object is inferred from the passed schema.
	 *
	 * @param array $schema Schema array.
	 *
	 * @return array
	 */
	protected function add_additional_fields_schema( array $schema ): array {
		if ( empty( $schema['title'] ) ) {
			return $schema;
		}

		/**
		 * Can't use $this->get_object_type otherwise we cause an inf loop.
		 */
		$object_type = $schema['title'];

		$additional_fields = $this->get_additional_fields( $object_type );

		foreach ( $additional_fields as $field_name => $field_options ) {
			if ( ! $field_options['schema'] ) {
				continue;
			}

			$schema['properties'][ $field_name ] = $field_options['schema'];
		}

		return $schema;
	}

	/**
	 * Get all the registered additional fields for a given object-type.
	 *
	 * @param string|null $object_type
	 *
	 * @return array
	 */
	protected function get_additional_fields( string $object_type = null ): array {

		if ( ! $object_type ) {
			$object_type = $this->get_object_type();
		}

		if ( ! $object_type ) {
			return array();
		}

		global $wp_rest_additional_fields;

		if ( ! $wp_rest_additional_fields || ! isset( $wp_rest_additional_fields[ $object_type ] ) ) {
			return array();
		}

		return $wp_rest_additional_fields[ $object_type ];
	}

	/**
	 * Get the object type this controller is responsible for managing.
	 *
	 * @return string|null
	 */
	protected function get_object_type(): ?string {
		$schema = $this->get_item_schema();

		if ( ! $schema || ! isset( $schema['title'] ) ) {
			return null;
		}

		return $schema['title'];
	}

	/**
	 * Returns a contextual HTTP error code for authorization failure.
	 *
	 * @return integer
	 */
	public function rest_authorization_required_code(): int {
		return is_user_logged_in() ? 403 : 401;
	}
}
