<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    foss engine
 * @subpackage foss_engine/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.2
 * @package    foss engine
 * @subpackage foss_engine/includes
 * @author     Kunal Kumar help@fossengine.com
 */
class fossdein_deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.2
     */
    public static function deactivate()
    {
        // Do not delete data on deactivation, only on uninstall if needed
    }
}
