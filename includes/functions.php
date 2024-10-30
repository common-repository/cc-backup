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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cc_backup_dump' ) ) {
	function cc_backup_dump( $file ) {
		Backup::dump( $file );
	}
}

if ( ! function_exists( 'cc_backup_restore' ) ) {
	function cc_backup_restore( $file ) {
		Backup::restore( $file );
	}
}
