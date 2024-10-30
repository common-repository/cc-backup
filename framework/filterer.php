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

use ReflectionClass;
use ReflectionMethod;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Filterer' ) ) {
	class Filterer extends Singleton {
		protected function __construct() {
			$class = new ReflectionClass( $this );
			foreach ( $class->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
				if ( (bool)$this->is_hook( $method->getName() ) ) {
					$hook     = $this->get_hook( $method->getName() );
					$priority = $this->get_priority( $method->getName() );
					$args     = $method->getNumberOfParameters();

					add_filter( $hook, array( $this, $method->getName() ), $priority, $args );
				}
			}
		}

		protected function get_priority( $method ) {
			$priority = substr( strrchr( $method, '_' ), 1 );

			return is_numeric( $priority ) ? (int)$priority : 10;
		}

		protected function has_priority( $method ) {
			$priority = substr( strrchr( $method, '_' ), 1 );

			return is_numeric( $priority ) ? true : false;
		}

		protected function get_hook( $method ) {
			if ( $this->has_priority( $method ) ) {
				$method = substr( $method, 0, strlen( $method ) - strlen( $this->get_priority( $method ) ) - 1 );
			}
			if ( $hook = $this->is_hook( $method ) ) {
				$method = substr( $method, strlen( $hook ) + 1 );
			}

			return $method;
		}

		protected function is_hook( $method ) {
			foreach ( array( 'filter', 'action' ) as $hook ) {
				if ( 0 === strpos( $method, $hook . '_' ) ) {
					return $hook;
				}
			}

			return false;
		}

		static public function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
			return add_filter( __NAMESPACE__ . '\\' . $tag, $function_to_add, $priority, $accepted_args );
		}

		static public function apply_filters( $tag, $value ) {
			$args    = func_get_args();
			$args[0] = __NAMESPACE__ . '\\' . $args[0];

			return call_user_func_array( 'apply_filters', $args );
		}
	}
}
