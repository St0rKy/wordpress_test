<?php

namespace PA\classes;

use DirectoryIterator;
use ReflectionClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Abstract Class useed for both admin area and frontend
 */
abstract class PA_Abstract_Init {

	/**
	 * Flag to make sure we're in the admin if necessary
	 *
	 * @var bool
	 */
	protected $is_admin = false;

	/**
	 * The Plugin path
	 *
	 * @var string|null
	 */
	protected ?string $plugin_path = null;

	/**
	 * The plugin Url
	 *
	 * @var string|null
	 */
	protected ?string $plugin_uri = null;

	/**
	 * Directory used
	 *
	 * @var string
	 */
	private string $include_dir;

	/**
	 * The version of the plugin
	 *
	 * @var string
	 */
	protected string $version = '1.0.0';

	/**
	 * @var array
	 */
	protected array $endpoints;


	/**
	 * Constructor
	 */
	public function __construct( $is_admin = false ) {
		$this->is_admin    = $is_admin;
		$this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );
		$this->plugin_uri  = plugin_dir_url( dirname( __FILE__ ) );
		$this->include_dir = $is_admin ? 'admin' : 'front';

		$this->load_dependencies();
		$this->add_actions();
		$this->init();
	}

	/**
	 * Loads the plugin dependencies
	 * @return void
	 */
	protected function load_dependencies() {
		$endpoints_dir = $this->plugin_path . $this->include_dir . '/endpoints';

		/**
		 * Autoload the endpoint classes
		 */
		if ( file_exists( $endpoints_dir ) ) {

			$dir = new DirectoryIterator( $endpoints_dir );

			foreach ( $dir as $file_info ) {
				if ( $file_info->isDot() ) {
					continue;
				}

				/**
				 * Create the classname from the filename
				 * The files should be in the following format:  class-pa-{class-name}.php
				 */
				$file       = PA_Const::build_class_name( $file_info->getFilename() );
				$reflector  = new ReflectionClass( static::class );
				$namespaced = str_replace( 'classes', 'endpoints', $reflector->getNamespaceName() ) . "\\" . $file;


				$this->endpoints[] = $namespaced;

				require_once( $file_info->getPathname() );

			}

			add_action( 'rest_api_init', array( $this, 'create_initial_rest_routes' ) );
		}
	}

	/**
	 * Abstract actions, this can also be used to set actions which should run both on the front and admin area
	 *
	 * @return void
	 */
	protected function add_actions() {

	}

	/**
	 * Loads a template file
	 *
	 * @param $file
	 *
	 * @return void
	 */
	protected function template( $file ) {
		$path = $this->is_admin ? 'admin' : 'front';
		require( $this->plugin_path . $path . '/templates/' . $file );
	}

	/**
	 * Abstract init to be extended
	 */
	public function init() {

	}

	/**
	 * Wrapper over the wp_enqueue_script function
	 * it will add the plugin version to the script source if no version is specified
	 *
	 * @param             $handle
	 * @param string|bool $src
	 * @param array $deps
	 * @param bool $ver
	 * @param bool $in_footer
	 */
	protected function enqueue_script( $handle, $src = false, array $deps = array(), bool $ver = false, bool $in_footer = false ) {
		if ( $ver === false ) {
			$ver = $this->version;
		}

		if ( defined( 'PA_DEBUG' ) && PA_DEBUG ) {
			$src = preg_replace( '#dist/#', '', $src );
			$src = preg_replace( '#\.min\.js$#', '.js', $src );
		}

		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
	}

	/**
	 * wrapper over the wp_enqueue_style function
	 * it will add the plugin version to the style link if no version is specified
	 *
	 * @param             $handle
	 * @param string|bool $src
	 * @param array $deps
	 * @param bool|string $ver
	 * @param string $media
	 */
	protected function enqueue_style( $handle, $src = false, array $deps = array(), $ver = false, string $media = 'all' ) {
		if ( $ver === false ) {
			$ver = $this->version;
		}
		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Get the route URL
	 *
	 * @param       $endpoint
	 * @param int $id
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_route_url( $endpoint, $id = 0, $args = array() ): string {

		$url = get_rest_url() . PA_Const::REST_NAMESPACE . '/' . $endpoint;

		if ( ! empty( $id ) && is_numeric( $id ) ) {
			$url .= '/' . $id;
		}

		if ( ! empty( $args ) ) {
			add_query_arg( $args, $url );
		}

		return $url;
	}

	/**
	 * Instantiate the rest routes for ajax calls
	 */
	public function create_initial_rest_routes() {
		foreach ( $this->endpoints as $e ) {
			/** @var PA_Abstract_REST_Controller $controller */
			$controller = new $e();
			$controller->register_routes();
		}
	}
}