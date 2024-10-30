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
use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\File' ) ) {
	class File {
		protected $path = '';
		protected $size = '';
		protected $time = 0;
		protected $date = '';

		public function __construct( $path ) {
			if ( ! is_file( $path ) ) throw new Exception( Backup::__( 'Wrong file path' ) );
			$this->path = $path;
			$this->size = (string)$this->size( filesize( $path ) );
			$this->time = filemtime( $path ); //filectime ?
			$this->date = date( "Y-m-d H:i:s", $this->time );
		}

		public function __isset( $name ) {
            return isset ( $this->$name );
        }

        public function __get( $name ) {
			if ( isset ( $this->$name ) ) return $this->$name;
			throw new Exception( Backup::__( 'Wrong property name' ) );
		}

		public function __toString() {
			return $this->path;
		}

		protected function size( $bytes ) {
			$label = [ 'B', 'KB', 'MB', 'GB' ];
			for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
			return round( $bytes, 0 ) . ' ' . $label[$i];
		}

		public function remove() {
			return unlink( $this->path );
		}
	}
}
