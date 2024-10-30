<?php

/*
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

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Singleton' ) ) {
	abstract class Singleton {
		final private function __clone() {
			_doing_it_wrong( __METHOD__, __( 'Cheatin&#8217; uh?' ), '' );
		}
		final private function __wakeup() {
			_doing_it_wrong( __METHOD__, __( 'Cheatin&#8217; uh?' ), '' );
		}
		
		protected function __construct() {
			_doing_it_wrong( __METHOD__, __( 'Cheatin&#8217; uh?' ), '' );
		}

		public static function instance() {
			static $instance = null;

			if ( $instance ) return $instance;

			$args   = func_get_args();
			$params = array();
			for ( $num = 0; $num < func_num_args(); $num ++ )
				$params[] = sprintf( '$args[%s]', $num );

			eval( sprintf( '$instance = new static( %s );', implode( ', ', $params ) ) );

			return $instance;
		}
	}
}
