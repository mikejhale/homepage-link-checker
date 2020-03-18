<?php
/**
 * Plugin Name: MH Link Checker
 * Plugin URI: https://stompgear.com/plugins/link-checker
 * Description: Easily check for broken homepage links
 * Version: 1.0.0
 * Author: Mike Hale
 * Author URI: https://mikehale.me
 * Licence: GPLv2 or later
 *
 * Text Domain: link-checker
 * Domain Path: languages
 *
 * Copyright 2020 Stomp Gear, LLC
 */

namespace LinkChecker;

defined( 'ABSPATH' ) || exit;

require_once 'vendor/autoload.php';

// Link Checker defines.
define( 'LINKCHECKER_VERSION', '1.0.0' );
define( 'LINKCHECKER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'LINKCHECKER_ADMIN_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/admin/' );


add_action( 'plugins_loaded', '\LinkChecker\link_checker_load_textdomain' );

$linkchecker_admin_menu = new \LinkChecker\Admin\Admin_Menu();
$linkchecker_shortcodes = new \LinkChecker\Shortcodes\Link_Checker_Results_Shortcode();

/**
 * Loads Link Checker translations
 *
 * @since 1.0
 * @author Mike Hale
 *
 * @return void
 */
function link_checker_load_textdomain() {
	// Load translations from the languages directory.
	$locale = get_locale();

	// This filter is documented in /wp-includes/l10n.php.
	$locale = apply_filters( 'plugin_locale', $locale, 'link-checker' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	load_textdomain( 'link-checker', WP_LANG_DIR . '/plugins/link-checker-' . $locale . '.mo' );
	load_plugin_textdomain( 'link-checker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
