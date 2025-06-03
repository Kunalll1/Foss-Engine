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
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Designomate
 * Author URI:        https://designomate.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fossenginedein
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
define('FOSSENGINEDEIN_VERSION', '1.0.2');

/**
 * The code that runs during plugin activation.
 */
function fossenginedein_activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fossenginedein-activator.php';
    fossenginedein_activator::fossenginedein_db_activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function fossenginedein_deactivate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fossenginedein-deactivator.php';
    fossenginedein_deactivator::deactivate();
}

register_activation_hook(__FILE__, 'fossenginedein_activate');
register_deactivation_hook(__FILE__, 'fossenginedein_deactivate');

/**
 * Run migrations for existing installations when plugin is updated
 */
function fossenginedein_check_for_updates()
{
    $stored_version = get_option('fossenginedein_version', '1.0.2');

    // If the stored version is older than current version, run migrations
    if (version_compare($stored_version, FOSSENGINEDEIN_VERSION, '<')) {
        // Include the activator class if not already included
        if (!class_exists('fossenginedein_activator')) {
            require_once plugin_dir_path(__FILE__) . 'includes/class-fossenginedein-activator.php';
        }

        // Run the legacy options migration
        fossenginedein_activator::migrate_legacy_options();

        // Update the stored version
        update_option('fossenginedein_version', FOSSENGINEDEIN_VERSION);
    }
}
add_action('plugins_loaded', 'fossenginedein_check_for_updates');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-fossenginedein.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.2
 */
function fossenginedein_run()
{
    $plugin = new fossenginedein();
    $plugin->fossenginedein_loader_run();
}

fossenginedein_run();
