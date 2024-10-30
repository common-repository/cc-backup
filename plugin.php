<?php

/*
	Plugin Name: CC-Backup
	Plugin URI: https://wordpress.org/plugins/cc-backup
	Description: This is a simple plugin to dump and restore the WordPress database.
	Version: 1.0.1
	Author: Clearcode
	Author URI: https://clearcode.cc
	Text Domain: cc-backup
	Domain Path: /languages/
	License: GPLv3
	License URI: http://www.gnu.org/licenses/gpl-3.0.txt

	Copyright (C) 2018 by Clearcode <https://clearcode.cc>
	and associates (see AUTHORS.txt file).

	This file is part of CC-Backup plugin.

	CC-Backup plugin is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	CC-Backup plugin is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with CC-Backup plugin; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Clearcode\Backup;

use Clearcode\Backup;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

require_once( __DIR__ . '/vendor/autoload.php' );

foreach ( [ 'singleton', 'filterer', 'plugin' ] as $file ) {
	require_once( __DIR__ . "/framework/$file.php" );
}

foreach ( [ 'backup', 'functions' ] as $file ) {
	require_once( __DIR__ . "/includes/$file.php" );
}

try {
	spl_autoload_register( __NAMESPACE__ . '::autoload' );

	if ( ! has_action( __NAMESPACE__ ) ) {
		do_action( __NAMESPACE__, Backup::instance( __FILE__ ) );
	}
} catch ( Exception $exception ) {
	if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
		echo $exception->getMessage();
		// TODO log errors
	}
}
