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

namespace Clearcode;

use Clearcode\Backup\Plugin;
use Clearcode\Backup\Settings;
use Ifsnop\Mysqldump\Mysqldump;
use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Backup' ) ) {
    /**
     * Class Backup
     * @package Clearcode
     */
    class Backup extends Plugin {
        public function __construct( $file ) {
            parent::__construct( $file );

            Settings::instance();
        }

        /**
         * If option is not exists, add option with default values on plugin activation.
         */
        public function activation() {}

        /**
         *  Remove option on deactivation.
         */
        public function deactivation() {}

        /**
         * Return list of links to display on the plugins page.
         *
         * @param array $links List of links.
         *
         * @return mixed List of links.
         */
        public function filter_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
            if ( empty( static::$name        ) ) return $actions;
            if ( empty( $plugin_data['Name'] ) ) return $actions;
            if ( static::$name == $plugin_data['Name'] )
                array_unshift( $actions, static::get_template( 'link', [
                    'url'  => get_admin_url( null, sprintf( Settings::URL, static::$slug ) ),
                    'link' => static::__( 'Settings' ),
                ] ) );

            return $actions;
        }

        static public function dump( $file ) {
            if ( is_file( $file ) ) return false;
            try {
                $dump = new Mysqldump('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD, [
                    'compress' => Mysqldump::NONE,
                    'reset-auto-increment' => true,
                    //'add-locks' => false,
                    'default-character-set' => Mysqldump::UTF8MB4,
                    //'disable-keys' => false,
                    //'lock-tables' => false,
                    'skip-comments' => true,
                    //'skip-dump-date' => true,
                    //'disable-foreign-keys-check' => true,
                    'add-drop-table' => true,
                    //'no-create-info' => true,
                    //'no-autocommit' => false,
                ] );
                $dump->start( $file );
                    self::error_log(
                   'mysqldump ' . self::__( 'success' ) . ' ' .
                   self::__( 'File' ) . ': ' . $file,
                    __CLASS__,
                    __FUNCTION__
                );
                return true;
            } catch ( Exception $exception ) {
                self::error_log(
                    'mysqldump ' . self::__( 'error' ) . ': ' . $exception->getMessage() . ' ' .
                    self::__( 'File' ) . ': ' . $file,
                    __CLASS__,
                    __FUNCTION__
                );
                return false;
            }
        }

        static public function restore( $file ) {
            global $wpdb;

            if ( ! is_file( $file ) ) return false;
            $query = file_get_contents( $file );

            if ( $wpdb->multi_query( $query ) ) {
                $affected_rows = 0;
                do {
                    if ( $result = $wpdb->store_result() ) {
                        while ( $row = $result->fetch_row() ) {
                            self::error_log(
                                'mysqli_multi_query ' . self::__( 'result' ) . ': ' . $row[0] . ' ' .
                                self::__( 'File' ) . ': ' . $file,
                                __CLASS__,
                                __FUNCTION__
                            );
                        }
                        $result->free();
                    }
                    $affected_rows += $wpdb->affected_rows();
                    if ( $wpdb->more_results() ) {
                        $wpdb->next_result();
                    }
                } while ( $wpdb->more_results() );
                if ( $wpdb->errno() ) {
                    self::error_log(
                        'mysqli_multi_query ' . self::__( 'error' ) . ': ' . $wpdb->error() . ' ' .
                        self::__( 'File' ) . ': ' . $file,
                        __CLASS__,
                        __FUNCTION__
                    );
                    return false;
                } else {
                    self::error_log(
                        'mysqli_multi_query ' . self::__( 'success' ) . ' ' .
                        self::__( 'File' ) . ': ' . $file,
                        __CLASS__,
                        __FUNCTION__
                    );
                    return true;
                }
            } else {
                self::error_log(
                    'mysqli_multi_query ' . self::__( 'error' ) . ' ' .
                    self::__( 'File' ) . ': ' . $file,
                    __CLASS__,
                    __FUNCTION__
                );
                return false;
            }
            return true;
        }
    }
}
