<?php
/**
 * Plugin Name: Pop Aurelian
 * Plugin URI: http://example.com
 * Description: Awesome Motive Test
 * Author URI: http://example.com
 * Version: 1.0.0
 * Author: <a href="http://example.com">Pop Aurelian</a>
 * Text Domain: pa
 * Domain Path: /languages/
 * License:     GPL3 / Apache License, Version 2.0
 *
 * Copyright (C) 2023 Pop Aurelian
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 */

namespace PA;

use PA\classes\PA_Initialize;

require( 'vendor/autoload.php' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Start Plugin Initialization
 */
new PA_Initialize();
