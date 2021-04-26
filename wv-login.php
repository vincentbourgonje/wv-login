<?php
/**
 * Webvise Login
 *
 * @author  Webvise
 * @license GPL-2.0+
 * @link    https://www.webvise.nl
 * @package wv-login
 */

/**
 * Plugin Name:       Webvise Login
 * Plugin URI:        https://www.webvise.nl/plugins/wv-login
 * Description:       A plugin to login users with a link send in an e-mail
 * Version:           1.0.0
 * Author:            Webvise
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       wv-login
 * Requires at least: 5.2
 * Requires PHP:      5.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WV_LOGIN_VERSION', '1.0.0' );
define( 'WV_LOGIN_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wv-login-activator.php
 */
function activate_wv_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wv-login-activator.php';
	wv_Login_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wv-login-deactivator.php
 */
function deactivate_wv_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wv-login-deactivator.php';
	wv_Login_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wv_login' );
register_deactivation_hook( __FILE__, 'deactivate_wv_login' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wv-login.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wv_login() {

	$plugin = new wv_Login();
	$plugin->run();

}
run_wv_login();
