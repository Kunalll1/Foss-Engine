<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://designomate.com/
 * @since      1.0.0
 *
 * @package     Foss Engine
 * @subpackage WP_Content_Generator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package     Foss Engine
 * @subpackage WP_Content_Generator/includes
 * @author     Your Name <email@example.com>
 */
class WP_Content_Generator_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        // Do not delete data on deactivation, only on uninstall if needed
    }
}
