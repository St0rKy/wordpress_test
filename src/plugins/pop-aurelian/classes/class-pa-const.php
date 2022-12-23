<?php

namespace PA\classes;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class PA_Const {
	/**
	 * Translation Domain
	 */
	const T = 'ap-lang';

	/**
	 * Plugin Version
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin Version
	 */
	const REST_NAMESPACE = 'pa/v1';

	/**
	 * API URL for the data
	 */
	const API_URL = 'https://miusage.com/v1/challenge/2/static/';

	/**
	 * Return the class name in the proper format
	 *
	 * @param        $file
	 * @param string $extension
	 *
	 * @return string
	 */
	public static function build_class_name( $file, string $extension = '' ): string {
		$base       = basename( $file, '.php' );
		$class_name = str_replace( 'class-', '', $base );
		$class_name = str_replace( 'pa-', '', $class_name );
		$class_name = str_replace( '-', '_', $class_name );
		$class_name = implode( '_', array_map( 'ucfirst', explode( '_', $class_name ) ) );
		$class_name = 'PA_' . $class_name;

		if ( ! empty( $extension ) ) {
			$class_name .= '_' . $extension;
		}

		return $class_name;
	}

	/**
	 * Set of default settings for the plugin
	 *
	 * @return array[]
	 */
	public static function get_default_settings() {
		return array(
			array(
				'id'    => 1,
				'name'  => 'numrows',
				'value' => 5
			),
			array(
				'id'    => 2,
				'name'  => 'humandate',
				'value' => true
			),
			array(
				'id'    => 3,
				'name'  => 'emails',
				'value' => array(
					get_option( 'admin_email' )
				),
			)
		);
	}
}