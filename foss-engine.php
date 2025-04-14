<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://designomate.com/
 * @since             1.0.0
 * @package           Foss Engine
 *
 * @wordpress-plugin
 * Plugin Name:       Foss Engine
 * Plugin URI:        https://kunalkr.in/wp-content-generator-uri/
 * Description:       A WordPress plugin that generates content using AI models based on topics from a CSV file, with editing and publishing capabilities.
 * Version:           1.0.1
 * Author:            Kunal Kumar
 * Author URI:        https://kunalkr.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       foss-engine
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('FOSS_ENGINE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 */
function activate_wp_content_generator()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-foss-engine-activator.php';
    WP_Content_Generator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_content_generator()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-foss-engine-deactivator.php';
    WP_Content_Generator_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_content_generator');
register_deactivation_hook(__FILE__, 'deactivate_wp_content_generator');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-foss-engion.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_content_generator()
{
    $plugin = new WP_Content_Generator();
    $plugin->run();
}

run_wp_content_generator();
