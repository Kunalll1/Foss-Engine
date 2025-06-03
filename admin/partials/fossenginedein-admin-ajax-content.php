<?php

/**
 * Handle AJAX request to get topic content
 *
 * @package     foss engine
 * @subpackage  foss_engine/admin
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Add the AJAX action to get topic content
 */
add_action('wp_ajax_get_topic_content', 'fossenginedein_get_topic_content');

/**
 * Handle the AJAX request to get topic content
 */
function fossenginedein_get_topic_content()
{
    // Verify the nonce
    check_ajax_referer('fossenginedein_nonce', 'nonce');

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array(
            'message' => __('You do not have permission to perform this action.', 'fossenginedein')
        ));
    }

    // Get and validate topic ID
    $topic_id = isset($_POST['topic_id']) ? absint($_POST['topic_id']) : 0;

    // Stronger validation
    if ($topic_id <= 0 || $topic_id > PHP_INT_MAX) {
        wp_send_json_error(array(
            'message' => __('Invalid topic ID provided.', 'fossenginedein')
        ));
        exit; // Ensure execution stops here
    }

    try {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fossenginedein_topics';

        // Check if table exists first
        if (!function_exists('fossenginedein_table_exists')) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-fossenginedein.php';
        }

        if (!fossenginedein_table_exists('fossenginedein_topics')) {
            wp_send_json_error(array(
                'message' => __('Database table not found. Please deactivate and reactivate the plugin.', 'fossenginedein')
            ));
            exit;
        }

        // Get the topic and content
        $topic = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $topic_id
            )
        );

        if (!$topic) {
            wp_send_json_error(array(
                'message' => __('Topic not found.', 'fossenginedein')
            ));
        }

        // Check for database errors
        if ($wpdb->last_error) {
            // error_log('Foss Engine - Database Error: ' . $wpdb->last_error);
            wp_send_json_error(array(
                'message' => __('Database error: ', 'fossenginedein') . $wpdb->last_error
            ));
        }

        // // Log success for debugging
        // if (defined('WP_DEBUG') && WP_DEBUG) {
        //     // error_log('Foss Engine - Topic content retrieved successfully for ID: ' . $topic_id);
        // }

        // Sanitize and escape the content before sending it back
        wp_send_json_success(array(
            'content' => wp_kses_post($topic->content), // Allow only approved HTML tags
            'topic' => esc_html($topic->topic),         // Properly escape the topic text
            'status' => esc_attr($topic->status)        // Escape status attribute
        ));
    } catch (Exception $e) {
        // error_log('Foss Engine - Exception in get_topic_content: ' . $e->getMessage());
        wp_send_json_error(array(
            'message' => __('An unexpected error occurred: ', 'fossenginedein') . $e->getMessage()
        ));
    }
}

/**
 * Register the AJAX handler
 */
function fossenginedein_register_ajax_handlers()
{
    // No need to register the handler here as we're using the wp_ajax_ hook directly
}
add_action('init', 'fossenginedein_register_ajax_handlers');
