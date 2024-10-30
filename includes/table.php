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
use WP_List_Table;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Table' ) ) {
	class Table extends WP_List_Table {
		protected $files = [];

		public function __construct( $files ) {
            $this->files = $files;

			parent::__construct( [
				'singular' => Backup::$slug,
				'plural'   => Backup::$slug,
				'ajax'     => false
			] );
		}

		public function get_columns() {
			return [
				'date' => Backup::__( 'Date' ),
				'size' => Backup::__( 'Size' )
			];
		}

		public function column_default( $item, $column_name ) {
            return isset( $item->$column_name ) ? $item->$column_name : print_r( $item, true );
		}

		public function column_date( $item ) {
			$actions = [];
			foreach( [ 'restore' => Backup::__( 'Restore' ), 'remove' => Backup::__( 'Remove' ) ] as $action => $label )
				$actions[] = Backup::get_template( 'link', [
					'url'  => wp_nonce_url( add_query_arg( [
                            'action'           => $action,
                            'id'               => $item->time,
                            //'settings-updated' => 'true'
                        ], admin_url( sprintf( Settings::URL, Backup::$slug ) ) ), $action ),
					'link' => $label
				] );

			return sprintf( '%1$s %2$s', $item->date, $this->row_actions( $actions ) );
		}

		public function no_items() {
			echo Backup::__( 'No backups available.' );
		}

		public function get_items( $per_page = 20, $page_number = 1 ) {
			$offset = ( $page_number - 1 ) * $per_page;
			return array_slice( $this->files, $offset, $offset + $per_page );
		}

		public function count_items() {
			return count( $this->files );
		}

		public function prepare_items() {
			$per_page     = $this->get_items_per_page( 'files_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = $this->count_items();

			// TODO wrong pagination
			$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $per_page
			] );

			$columns  = $this->get_columns();
			$hidden   = [];
			$sortable = [];
			$this->_column_headers = [ $columns, $hidden, $sortable ];

			$this->items = $this->get_items( $per_page, $current_page );
		}
	}
}
