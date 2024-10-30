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

use Clearcode\Backup;
use ArrayAccess;
use Countable;
use Iterator;
use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Files' ) ) {
    class Files implements ArrayAccess, Countable, Iterator  {
		public function __construct( $path, $extension ) {
			if ( ! is_dir( $path ) ) throw new Exception( Backup::__( 'Wrong dir path' ) );

			foreach( glob( trailingslashit( $path ) . '*.' . $extension ) as $file ) {
                $file   = new File($file);
			    $offset = (string)$file->time;
                $this->$offset = $file;
            }
		}

		public function offsetSet( $offset, $value ) {}

		public function offsetUnset( $offset ) {
			if ( isset( $this->{(string)$offset} ) && $this->{(string)$offset}->remove() )
				unset( $this->{(string)$offset} );
		}

		public function offsetExists( $offset ) {
			return isset( $this->{(string)$offset} );
		}

		public function offsetGet( $offset ) {
			return isset( $this->{(string)$offset} ) ? $this->{(string)$offset} : null;
		}

		public function count() {
            $array = (array)$this;
			return count( $array );
		}

        public function key() {
		    $array = (array)$this;
            return key( $array );
        }

        public function valid() {
            $array = (array)$this;
            $key   = key( $array );
            return ( null !== $key && false !== $key );
        }

        public function current() {
            $array = (array)$this;
            return current( $array );
        }

        public function next() {
            $array = (array)$this;
            return next( $array );
        }

        public function rewind() {
            $array = (array)$this;
            reset( $array );
        }
	}
}
