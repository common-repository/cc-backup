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

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Settings' ) ) {
	class Settings extends Filterer {
		const URL = 'options-general.php?page=%s';

        protected $capability = 'manage_options';
		protected $path       = WP_CONTENT_DIR . '/dumps';
		protected $extension  = 'sql';
		protected $files      = [];

		protected function __construct() {
			parent::__construct();

			foreach ( [ 'capability', 'path', 'extension' ] as $property )
                $this->$property = self::apply_filters( $property, $this->$property );

			$this->set_files();
		}

		public function action_admin_menu_999() {
			add_options_page(
				Backup::$name,
				Backup::get_template( 'menu', [
					'class'   => 'dashicons-before dashicons-backup',
					'content' => Backup::__( 'Backup' )
				] ),
				$this->capability,
				Backup::$slug,
				[ $this, 'page' ]
			);
		}

        public function action_current_screen( $current_screen ) {
            if ( 'settings_page_cc-backup' === $current_screen->id &&
                $action = $this->get_action() ) $this->$action();
        }

		public function page() {
		    if ( ! current_user_can( $this->capability ) ) wp_die( Backup::__( 'Cheatin&#8217; uh?' ) );

			$table = new Table( (array)$this->files );
			$table->prepare_items();

			$action = 'dump';
			$input = self::input( [
					'type'  => 'hidden',
					'name'  => 'action',
					'value' => $action
			] );

			echo Backup::get_template( 'page', [
				'header' => Backup::__( 'Backup' ),
				'errors' => Backup::$slug,
				'table'  => $table,
				'url'    => sprintf( self::URL, Backup::$slug ),
				'action' => $action,
				'input'  => $input,
				'button' => Backup::__( 'Backup' )
			] );
		}

		protected function get_action() {
			foreach( [ 'dump', 'restore', 'remove' ] as $action )
                if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == $action )
                    if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], $action ) ) return $action;
                    else wp_die( Backup::__( 'Cheatin&#8217; uh?' ) );
			return false;
		}

		protected function get_id() {
			return isset( $_GET['id'] )      &&
			       is_numeric( $_GET['id'] ) &&
			       isset( $this->files[(int)$_GET['id']] ) ? (int)$_GET['id'] : false;
		}

		protected function get_file() {
			return $this->get_id() ? $this->files[$this->get_id()] : false;
		}

		protected function set_files() {
            $this->files = new Files( $this->path, $this->extension );
        }

		protected function dump() {
			$file = trailingslashit( $this->path ) . (string)time() . '.' . $this->extension;
			if ( Backup::dump( $file ) ) {
				add_settings_error(
					Backup::$slug,
					'settings_updated',
					sprintf( Backup::__( 'Backup created: %s' ),
						Backup::get_template( 'code', [ 'content' => $file ] )
					),
					'updated'
				);
				$this->set_files();
			} else add_settings_error(
				Backup::$slug,
				'settings_updated',
                sprintf( Backup::__( 'Backup error: %s' ),
                    Backup::get_template( 'code', [ 'content' => $file ] )
                ),
				'error'
			);
		}

		protected function restore() {
			if ( ! $file = $this->get_file() ) add_settings_error(
                Backup::$slug,
                'settings_updated',
                Backup::__( 'Backup restore error' ),
                'error'
            ); else if ( ! Backup::restore( $file ) ) add_settings_error(
                Backup::$slug,
                'settings_updated',
                Backup::__( 'Backup restore error' ),
                'error'
            );
		}

		protected function remove() {
			if ( false !== ( $id = $this->get_id() ) ) {
				$file = (string)$this->get_file();
				unset( $this->files[$id] );
				add_settings_error(
					Backup::$slug,
					'settings_updated',
					sprintf( Backup::__( 'Backup removed: %s' ),
						Backup::get_template( 'code', [ 'content' => $file ] )
					),
					'updated'
				);
				self::redirect();
			} else add_settings_error(
				Backup::$slug,
				'settings_updated',
				Backup::__( 'Backup remove error' ),
				'error'
			);
		}

		static public function redirect() {
            set_transient( 'settings_errors', get_settings_errors(), 30 );
		    if ( wp_safe_redirect( add_query_arg( [
			    'settings-updated' => 'true'
            ], admin_url( sprintf( self::URL, Backup::$slug ) ) ) ) ) exit;
		}

		static public function input( $args ) {
			extract( $args, EXTR_SKIP );

			return Backup::get_template( 'input', [
					'atts' => self::implode( [
							'type'  => isset( $type )  ? $type  : '',
							'class' => isset( $class ) ? $class : '',
							'name'  => isset( $name )  ? $name  : '',
							'value' => isset( $value ) ? $value : ''
						]
					),
					'checked' => isset( $checked ) ? $checked : '',
					'before'  => isset( $before )  ? $before  : '',
					'after'   => isset( $after )   ? $after   : '',
					'desc'    => isset( $desc )    ? $desc    : ''
				]
			);
		}

		static public function implode( $atts = [] ) {
			array_walk( $atts, function ( &$value, $key ) {
				$value = sprintf( '%s="%s"', $key, esc_attr( $value ) );
			} );

			return implode( ' ', $atts );
		}
	}
}
