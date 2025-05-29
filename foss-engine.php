<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://fossengine.com/
 * @since             1.0.2
 * @package           foss engine
 *
 * @wordpress-plugin
 * Plugin Name:       Foss Engine
 * Plugin URI:        https://fossengine.com/
 * Description:       A WordPress plugin that generates content using AI models based on topics from a CSV file, with editing and publishing capabilities.
 * Version:           1.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Designomate
 * Author URI:        https://designomate.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       foss-engine
 * Domain Path:       /languages
 * contributor:      Kunal Kumar
 * contributor url:  https://kunalkr.in/
 * 
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('FOSS_ENGINE_VERSION', '1.0.2');

/**
 * The code that runs during plugin activation.
 */
function activate_foss_engine()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-foss-engine-activator.php';
    fossdein_activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_foss_engine()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-foss-engine-deactivator.php';
    fossdein_deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_foss_engine');
register_deactivation_hook(__FILE__, 'deactivate_foss_engine');

/**
 * Run migrations for existing installations when plugin is updated
 */
function foss_engine_check_for_updates()
{
    $stored_version = get_option('foss_engine_version', '1.0.2');

    // If the stored version is older than current version, run migrations
    if (version_compare($stored_version, FOSS_ENGINE_VERSION, '<')) {
        // Include the activator class if not already included
        if (!class_exists('fossdein_activator')) {
            require_once plugin_dir_path(__FILE__) . 'includes/class-foss-engine-activator.php';
        }

        // Run the legacy options migration
        fossdein_activator::migrate_legacy_options();

        // Update the stored version
        update_option('foss_engine_version', FOSS_ENGINE_VERSION);
    }
}
add_action('plugins_loaded', 'foss_engine_check_for_updates');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-foss-engine.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.2
 */
function run_foss_engine()
{
    $plugin = new fossdein();
    $plugin->fossdein_loader_run();
}

run_foss_engine();
