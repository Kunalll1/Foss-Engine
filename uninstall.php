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
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Also delete old table if it exists
$old_table_name = $wpdb->prefix . 'foss_engine_topics';
$wpdb->query("DROP TABLE IF EXISTS $old_table_name");

// Clear any transients
delete_transient('fossenginedein_pending_topics');
delete_transient('foss_engine_pending_topics'); // Legacy cleanup

// Legacy cleanup (in case any installations used the old prefixes)
delete_option('wp_content_generator_openai_key');
delete_option('wp_content_generator_prompt_template');
delete_transient('wp_content_generator_pending_topics');

// Clean up old foss_engine_ options
delete_option('foss_engine_openai_key');
delete_option('foss_engine_prompt_template');
delete_option('foss_engine_model');
delete_option('foss_engine_provider');
delete_option('foss_engine_deepseek_key');
delete_option('foss_engine_deepseek_model');
delete_option('fossenginedein_version');
delete_option('foss_engine_version');
