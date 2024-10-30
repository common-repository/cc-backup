=== CC-Backup ===
Contributors: ClearcodeHQ, PiotrPress
Tags: backup, dump, db, database, restore
Requires at least: 4.8.1
Tested up to: 4.9.4
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

This is a simple plugin to dump and restore the WordPress database.

== Description ==

The WordPress CC-Backup plugin allows you to create your sites database backup at any time and simply restore it with one click.

= IMPORTANT =
Copy `db.php` file from: `wp-content/cc-backup` directory, to: `wp-content` directory.

== Installation ==

= From your WordPress Dashboard =

1. Go to 'Plugins > Add New'
2. Search for 'CC-Backup'
3. Activate the plugin from the Plugin section in your WordPress Dashboard.
4. Copy `db.php` file from: `wp-content/cc-backup` directory, to: `wp-content` directory.

= From WordPress.org =

1. Download 'CC-Backup'.
2. Upload the 'cc-backup' directory to your `/wp-content/plugins/` directory using your favorite method (ftp, sftp, scp, etc...)
3. Activate the plugin from the Plugin section in your WordPress Dashboard.
4. Copy `db.php` file from: `wp-content/cc-backup` directory, to: `wp-content` directory.

= Once Activated =

Visit 'Settings > Backup' and create your first backup.

= Multisite =

The plugin can be activated and used for just about any use case.

* Activate at the site level to load the plugin on that site only.
* Activate at the network level for full integration with all sites in your network (this is the most common type of multisite installation).

= What are minimum requirements for the plugin? =

* PHP interpreter version >= 5.3
* MySQL database version >= 4.1.0
* PDO
* Proper credentials to read/write to: `wp-content/dumps` directory.
* Access to the web server to copy `db.php` file from: `wp-content/cc-backup` directory, to: `wp-content` directory.

== Screenshots ==

1. **WordPress General Settings** - Visit 'Settings > Backup', backup & restore dumps.

== Changelog ==

= 1.0.1 =
*Release date: 13.03.2018*

* Fixed: Compatibility with WooCommerce.

= 1.0.0 =
*Release date: 22.08.2017*

* First stable version of the plugin.