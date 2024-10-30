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
if ( ! class_exists( __NAMESPACE__ . '\Plugin' ) ) {
	class Plugin extends Filterer {
		static public $name         = '';
		static public $plugin_uri   = '';
		static public $version      = '';
		static public $description  = '';
		static public $author       = '';
		static public $author_uri   = '';
		static public $text_domain  = '';
		static public $domain_path  = '';
		static public $network      = '';
		static public $title        = '';
		static public $author_name  = '';
		static public $file         = '';
		static public $basename     = '';
		static public $dir          = '';
		static public $url          = '';
		static public $slug         = '';
		static public $namespace    = '';

		static public function autoload( $name ) {
			if ( 0 !== strpos( $name, static::$namespace ) ) return;

			$name = substr( $name, strlen( static::$namespace ) );
			$name = strtolower( $name );
			$name = str_replace( '\\', '/', $name );
			$name = str_replace( '_', '-', $name );
			$name = trim( $name, '/' );

			if ( is_file( $file = static::$dir . '/includes/' . $name . '.php' ) ) require_once( $file );
		}

		static protected function set_plugin_data( $file ) {
			$data = get_plugin_data( $file );

			$data['plugin_uri']  = $data['PluginURI'];
			$data['author_uri']  = $data['AuthorURI'];
			$data['text_domain'] = $data['TextDomain'];
			$data['domain_path'] = $data['DomainPath'];
			$data['author_name'] = $data['AuthorName'];

			foreach( array(
				'PluginURI',
				'AuthorURI',
				'TextDomain',
				'DomainPath',
				'AuthorName' ) as $key ) {
				unset( $data[$key] );
			}

			$data['file']      = $file;
			$data['basename']  = plugin_basename( $file );
			$data['dir']       = plugin_dir_path( $file );
			$data['url']       = plugin_dir_url(  $file );
			$data['slug']      = basename( dirname( $file ) );
			$data['namespace'] = __NAMESPACE__;

			foreach( $data as $key => $value ) {
				$key = strtolower( $key );
				if ( isset( static::$$key ) ) static::$$key = $value;
			}
		}

		protected function __construct( $file ) {
			static::set_plugin_data( $file );

			register_activation_hook(   static::$file, array( $this, 'activation'   ) );
			register_deactivation_hook( static::$file, array( $this, 'deactivation' ) );

			parent::__construct();
		}

		public function activation() {}

		public function deactivation() {}

		public function action_activated_plugin( $plugin, $network_wide = null ) {
			static::switch_plugin_hook( $plugin, $network_wide );
		}

		public function action_deactivated_plugin( $plugin, $network_wide = null ) {
			static::switch_plugin_hook( $plugin, $network_wide );
		}

		public function action_init(){
			load_plugin_textdomain( static::$text_domain, false, static::$dir . static::$domain_path );
		}

		public function filter_network_admin_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			return $actions;
		}

		public function filter_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			return $actions;
		}

		/**
		 * Return list of links to display on the plugins page.
		 *
		 * @param array $plugin_meta List of plugin meta.
		 * @param array $plugin_data List of plugin data.
		 *
		 * @return mixed List of plugin meta.
		 */
		public function filter_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( empty( static::$name        ) ) return $plugin_meta;
			if ( empty( $plugin_data['Name'] ) ) return $plugin_meta;
			if ( static::$name == $plugin_data['Name'] ) {
				$plugin_meta[] = static::__( 'Author' ) . ' ' . static::get_template( 'link', array(
						'url'  => 'http://piotr.press/',
						'link' => 'PiotrPress'
					) );
			}

			return $plugin_meta;
		}

		static public function __( $text ) {
			return __( $text, static::$text_domain );
		}

		static public function error_log( $error = '', $class = '', $function = '' ) {
			$message = static::$name . ': ';
			if ( $error )    $message .= $error . ' ';
			if ( $class )    $message .= static::__( 'Class' )    . ': ' . $class . ' ';
			if ( $function ) $message .= static::__( 'Function' ) . ': ' . $function;

			error_log( $message );
		}

		static public function get_template( $template, $vars = array() ) {
			$template = static::$dir . '/templates/' . $template . '.php';
			$template = static::apply_filters( 'template', $template, $vars );
			if ( ! is_file( $template ) ) return false;

			$vars = static::apply_filters( 'vars', $vars, $template );
			if ( is_array( $vars ) ) extract( $vars, EXTR_SKIP );

			ob_start();
			include $template;

			return ob_get_clean();
		}

		static public function get_sites( $args = array() ) {
			$args = wp_parse_args( $args, array( 'public' => 1 ) );

			$sites = array();
			$sites = function_exists( 'get_sites' ) ? get_sites( $args ) : wp_get_sites( $args );

			return $sites ? array_map( function( $site ) { return (array)$site; }, $sites ) : false;
		}

		public function switch_plugin_hook( $plugin, $network_wide = null ) {
			if ( static::$basename != $plugin ) return;
			if ( ! $network_wide )              return;

			list( $hook ) = explode( '_', current_filter(), 2 );
			$hook = str_replace( 'activated', 'activate_', $hook );
			$hook .= plugin_basename( static::$file );

			$this->call_user_func_array( 'do_action', array( $hook, false ) );
		}

		protected function call_user_func_array( $function, $args = array() ) {
			if ( is_multisite() ) {
				$sites = self::get_sites();

				foreach ( $sites as $site ) {
					switch_to_blog( $site['blog_id'] );
					call_user_func_array( $function, $args );
				}

				restore_current_blog();
			} else $function( $args );
		}
	}
}
