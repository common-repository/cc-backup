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

use wpdb;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\DB' ) ) {
    class DB extends wpdb {
        public function multi_query( $query ) {
            if ( $this->use_mysqli )
                return mysqli_multi_query( $this->dbh, $query );
            return false;
        }

        public function store_result() {
            if ( $this->use_mysqli )
                return mysqli_store_result( $this->dbh );
            return false;
        }

        public function fetch_row() {
            if ( $this->use_mysqli )
                return mysqli_fetch_row( $this->dbh );
            return false;
        }

        public function more_results() {
            if ( $this->use_mysqli )
                return mysqli_more_results( $this->dbh );
            return false;
        }

        public function next_result() {
            if ( $this->use_mysqli )
                return mysqli_next_result( $this->dbh );
            return false;
        }

        public function errno() {
            if ( $this->use_mysqli )
                return mysqli_errno( $this->dbh );
            return false;
        }

        public function error() {
            if ( $this->use_mysqli )
                return mysqli_error( $this->dbh );
            return false;
        }

        public function affected_rows() {
            if ( $this->use_mysqli )
                return mysqli_affected_rows( $this->dbh );
            return false;
        }
    }
}

global $wpdb;
$wpdb = new DB( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );