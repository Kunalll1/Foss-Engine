<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    Foss Engine
 * @subpackage Foss_Engine/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.2
 * @package    Foss Engine
 * @subpackage Foss_Engine/includes
 * @author     Kunal Kumar help@fossengine.com
 */
class FOSSEN_i18n
{

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.2
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'Foss-Engine',
            false,
            plugin_dir_path(dirname(__FILE__)) . 'languages/'
        );
    }
}
