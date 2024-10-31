<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://enigmatheme.club
 * @since             1.0.0
 * @package           Puchi
 *
 * @wordpress-plugin
 * Plugin Name:       Puchi A/B Testing
 * Plugin URI:        http://puchi.enigmatheme.club
 * Description:       Puchi is simple content A/B testing plugin with powerful feature.
 * Version:           1.0.0
 * Author:            Bayu Idham Fathurachman
 * Author URI:        http://enigmatheme.club
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       puchi
 * Domain Path:       /languages
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
define( 'PUCHI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-puchi-activator.php
 */
function activate_puchi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-puchi-activator.php';
	Puchi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-puchi-deactivator.php
 */
function deactivate_puchi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-puchi-deactivator.php';
	Puchi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_puchi' );
register_deactivation_hook( __FILE__, 'deactivate_puchi' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-puchi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_puchi() {

	$plugin = new Puchi();
	$plugin->run();

}
run_puchi();
