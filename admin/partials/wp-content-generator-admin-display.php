<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_Content_Generator
 * @subpackage WP_Content_Generator/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap wp-content-generator-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="notice notice-info">
        <p><?php esc_html_e('Welcome to the WordPress Content Generator. This plugin helps you generate content using OpenAI based on topics from a CSV file.', 'wp-content-generator-security-enhanced'); ?></p>
    </div>

    <div class="wp-content-generator-container">
        <div class="wp-content-generator-section">
            <h2><?php esc_html_e('Getting Started', 'wp-content-generator-security-enhanced'); ?></h2>
            <ol>
                <li><?php
                    printf(
                        /* translators: %s: URL to settings page */
                        esc_html__('First, go to the %s page and enter your OpenAI API key.', 'wp-content-generator-security-enhanced'),
                        '<a href="' . esc_url(admin_url('admin.php?page=wp-content-generator-settings')) . '">' . esc_html__('Settings', 'wp-content-generator-security-enhanced') . '</a>'
                    );
                    ?></li>
                <li><?php esc_html_e('Prepare a CSV file with a list of topics. Each topic should be on a new line.', 'wp-content-generator-security-enhanced'); ?></li>
                <li><?php
                    printf(
                        /* translators: %s: URL to topics page */
                        esc_html__('Upload your CSV file in the %s section.', 'wp-content-generator-security-enhanced'),
                        '<a href="' . esc_url(admin_url('admin.php?page=wp-content-generator-topics')) . '">' . esc_html__('Topics', 'wp-content-generator-security-enhanced') . '</a>'
                    );
                    ?></li>
                <li><?php esc_html_e('Generate content for each topic, review, edit, and publish as needed.', 'wp-content-generator-security-enhanced'); ?></li>
            </ol>
        </div>

        <div class="wp-content-generator-section">
            <h2><?php esc_html_e('Features', 'wp-content-generator-security-enhanced'); ?></h2>
            <ul>
                <li><?php esc_html_e('Import topics from a CSV file', 'wp-content-generator-security-enhanced'); ?></li>
                <li><?php esc_html_e('Generate content using OpenAI for each topic', 'wp-content-generator-security-enhanced'); ?></li>
                <li><?php esc_html_e('Edit generated content before publishing', 'wp-content-generator-security-enhanced'); ?></li>
                <li><?php esc_html_e('Publish content as WordPress posts or pages', 'wp-content-generator-security-enhanced'); ?></li>
                <li><?php esc_html_e('Regenerate content if needed', 'wp-content-generator-security-enhanced'); ?></li>
                <li><?php esc_html_e('Track the status of each topic', 'wp-content-generator-security-enhanced'); ?></li>
            </ul>
        </div>

        <div class="wp-content-generator-section">
            <h2><?php esc_html_e('CSV Format', 'wp-content-generator-security-enhanced'); ?></h2>
            <p><?php esc_html_e('Your CSV file should have one topic per line. The first column will be used as the topic.', 'wp-content-generator-security-enhanced'); ?></p>
            <p><?php esc_html_e('Example:', 'wp-content-generator-security-enhanced'); ?></p>
            <pre>Best practices for WordPress security
How to improve website loading speed
Benefits of using a CDN for your website
Top WordPress plugins for SEO</pre>
            <p><?php esc_html_e('You can also include a header row if you prefer:', 'wp-content-generator-security-enhanced'); ?></p>
            <pre>topic
Best practices for WordPress security
How to improve website loading speed
Benefits of using a CDN for your website
Top WordPress plugins for SEO</pre>
        </div>

        <div class="wp-content-generator-section">
            <h2><?php esc_html_e('Quick Access', 'wp-content-generator-security-enhanced'); ?></h2>
            <div class="wp-content-generator-quick-access">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wp-content-generator-settings')); ?>" class="button button-primary"><?php esc_html_e('Settings', 'wp-content-generator-security-enhanced'); ?></a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wp-content-generator-topics')); ?>" class="button button-primary"><?php esc_html_e('Manage Topics', 'wp-content-generator-security-enhanced'); ?></a>
            </div>
        </div>
    </div>
</div>