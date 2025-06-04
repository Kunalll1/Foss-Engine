<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package     foss engine
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('fossenginedein_openai_key');
delete_option('fossenginedein_prompt_template');
delete_option('fossenginedein_model');
delete_option('fossenginedein_provider');
delete_option('fossenginedein_deepseek_key');
delete_option('fossenginedein_deepseek_model');

// Delete plugin database table
global $wpdb;
$table_name = $wpdb->prefix . 'fossenginedein_topics';
$wpdb->query("DROP TABLE IF EXISTS " . esc_sql($table_name));


// Clear any transients
delete_transient('fossenginedein_pending_topics');

// Legacy cleanup (in case any installations used the old prefix)
delete_option('wp_content_generator_openai_key');
delete_option('wp_content_generator_prompt_template');
delete_transient('wp_content_generator_pending_topics');
