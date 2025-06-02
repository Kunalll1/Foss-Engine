<?php

/**
 * Fired during plugin activation
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    foss engine
 * @subpackage foss_engine/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.2
 * @package    foss engine
 * @subpackage foss_engine/includes
 */
class fossenginedein_activator
{

    /**
     * Set up the database tables and plugin options on activation.
     *
     * @since    1.0.2
     */
    public static function fossenginedein_db_activate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'fossenginedein_topics';

        // Create the topics table
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic text NOT NULL,
            content longtext,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Set default options
        add_option('fossenginedein_openai_key', '');
        add_option('fossenginedein_prompt_template', 'Write a comprehensive blog post about [TOPIC]. Include an introduction, several key points, and a conclusion. The content should be informative and engaging.');
        add_option('fossenginedein_model', 'gpt-3.5-turbo');

        // Add new default options for Deepseek integration
        add_option('fossenginedein_provider', 'openai');
        add_option('fossenginedein_deepseek_key', '');
        add_option('fossenginedein_deepseek_model', 'deepseek-chat');

        // Migrate any legacy options
        self::migrate_legacy_options();
    }

    /**
     * Migrate options from old prefixes to fossenginedein_ prefix
     *
     * @since    1.1.0
     */
    public static function migrate_legacy_options()
    {
        // Migrate from wp_content_generator_ prefix
        $old_key = get_option('wp_content_generator_openai_key');
        if ($old_key !== false) {
            update_option('fossenginedein_openai_key', $old_key);
            delete_option('wp_content_generator_openai_key');
        }

        $old_template = get_option('wp_content_generator_prompt_template');
        if ($old_template !== false) {
            update_option('fossenginedein_prompt_template', $old_template);
            delete_option('wp_content_generator_prompt_template');
        }

        $old_topics = get_transient('wp_content_generator_pending_topics');
        if ($old_topics !== false) {
            set_transient('fossenginedein_pending_topics', $old_topics);
            delete_transient('wp_content_generator_pending_topics');
        }

        // Migrate from foss_engine_ prefix
        $options_to_migrate = [
            'foss_engine_openai_key' => 'fossenginedein_openai_key',
            'foss_engine_prompt_template' => 'fossenginedein_prompt_template',
            'foss_engine_model' => 'fossenginedein_model',
            'foss_engine_provider' => 'fossenginedein_provider',
            'foss_engine_deepseek_key' => 'fossenginedein_deepseek_key',
            'foss_engine_deepseek_model' => 'fossenginedein_deepseek_model'
        ];

        foreach ($options_to_migrate as $old_option => $new_option) {
            $old_value = get_option($old_option);
            if ($old_value !== false) {
                update_option($new_option, $old_value);
                delete_option($old_option);
            }
        }

        // Migrate database table if it exists
        global $wpdb;
        $old_table = $wpdb->prefix . 'foss_engine_topics';
        $new_table = $wpdb->prefix . 'fossenginedein_topics';
        
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $old_table));
        if ($table_exists && $table_exists === $old_table) {
            $wpdb->query($wpdb->prepare("RENAME TABLE %s TO %s", $old_table, $new_table));
        }

        // Migrate transients
        $old_transient = get_transient('foss_engine_pending_topics');
        if ($old_transient !== false) {
            set_transient('fossenginedein_pending_topics', $old_transient);
            delete_transient('foss_engine_pending_topics');
        }
    }
}
