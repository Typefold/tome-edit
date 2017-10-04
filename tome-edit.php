<?php

/**
 * Plugin Name: Tome Edit
 * Description: Edit for Tome project
 * Plugin URI: http://#
 * Author: Jakub Kohout
 * Author URI: http://github.com/itachicz11
 * Version: 1.0
 * License: MIT
 * Text Domain: Text Domain
 * Domain Path: Domain Path
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_tome_edit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tome-edit-activator.php';
	Tome_Edit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_tome_edit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tome-edit-deactivator.php';
	Tome_Edit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tome_edit' );
register_deactivation_hook( __FILE__, 'deactivate_tome_edit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tome-edit.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */


function run_tome_edit() {

	$tome_edit_plugin = new Tome_Edit();
	$tome_edit_plugin->run();

}
run_tome_edit();



