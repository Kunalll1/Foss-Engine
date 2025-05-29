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
class fossdein_activator
{

    /**
     * Set up the database tables and plugin options on activation.
     *
     * @since    1.0.2
     */
    public static function fossdein_db_activate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'foss_engine_topics';

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
        add_option('foss_engine_openai_key', '');
        add_option('foss_engine_prompt_template', 'Write a comprehensive blog post about [TOPIC]. Include an introduction, several key points, and a conclusion. The content should be informative and engaging.');
        add_option('foss_engine_model', 'gpt-3.5-turbo');

        // Add new default options for Deepseek integration
        add_option('foss_engine_provider', 'openai');
        add_option('foss_engine_deepseek_key', '');
        add_option('foss_engine_deepseek_model', 'deepseek-chat');

        // Migrate any legacy options
        self::migrate_legacy_options();
    }

    /**
     * Migrate options from old wp_content_generator_ prefix to foss_engine_ prefix
     *
     * @since    1.1.0
     */
    public static function migrate_legacy_options()
    {
        // Migrate OpenAI key
        $old_key = get_option('wp_content_generator_openai_key');
        if ($old_key !== false) {
            update_option('foss_engine_openai_key', $old_key);
            delete_option('wp_content_generator_openai_key');
        }

        // Migrate prompt template
        $old_template = get_option('wp_content_generator_prompt_template');
        if ($old_template !== false) {
            update_option('foss_engine_prompt_template', $old_template);
            delete_option('wp_content_generator_prompt_template');
        }

        // Migrate pending topics transient
        $old_topics = get_transient('wp_content_generator_pending_topics');
        if ($old_topics !== false) {
            set_transient('foss_engine_pending_topics', $old_topics);
            delete_transient('wp_content_generator_pending_topics');
        }
    }
}
