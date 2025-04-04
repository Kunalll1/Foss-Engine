<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_Content_Generator
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('wp_content_generator_openai_key');
delete_option('wp_content_generator_prompt_template');

// Delete plugin database table
global $wpdb;
$table_name = $wpdb->prefix . 'content_generator_topics';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Clear any transients
delete_transient('wp_content_generator_pending_topics');
