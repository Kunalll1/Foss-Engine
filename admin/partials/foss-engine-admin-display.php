<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://fossengine.com/
 * @since      1.0.1
 *
 * @package    Foss Engine
 * @subpackage Foss_Engine/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Check which provider is selected
$ai_provider = get_option('foss_engine_provider', 'openai');

// Check if the appropriate API key is set based on selected provider
$api_key_set = false;
$api_key_message = '';

if ($ai_provider === 'openai') {
    $openai_key = get_option('foss_engine_openai_key', '');
    $api_key_set = !empty($openai_key);
    $api_key_message = 'OpenAI API key is not set. Please configure it in the';
} else {
    $deepseek_key = get_option('foss_engine_deepseek_key', '');
    $api_key_set = !empty($deepseek_key);
    $api_key_message = 'Deepseek API key is not set. Please configure it in the';
}
?>

<div class="wrap foss-engine-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (!$api_key_set): ?>
        <div class="notice notice-info">
            <p>
                <?php
                // Create a single literal string and use placeholders
                $message_text = sprintf(
                    /* translators: %1$s is dynamic text like "OpenAI API key is not set...", %2$s is the Settings page URL */
                    __('%1$s <a href="%2$s">Settings</a> page.', 'foss-engine'),
                    esc_html($api_key_message),
                    esc_url(admin_url('admin.php?page=foss-engine-settings'))
                );

                // Safely output the string
                echo wp_kses(
                    $message_text,
                    array(
                        'a' => array('href' => array()),
                    )
                );
                ?>
            </p>
        </div>
    <?php else: ?>
        <div class="notice notice-info">
            <p><?php esc_html_e('Welcome to the Foss Engine. This plugin helps you generate content using AI based on topics from a CSV file.', 'foss-engine'); ?></p>
        </div>
    <?php endif; ?>

    <div class="foss-engine-container">
        <div class="foss-engine-section">
            <h2><?php esc_html_e('Getting Started', 'foss-engine'); ?></h2>
            <ol>
                <li><?php
                    printf(
                        /* translators: %s: URL to settings page */
                        esc_html__('First, go to the %s page and enter your API key.', 'foss-engine'),
                        '<a href="' . esc_url(admin_url('admin.php?page=foss-engine-settings')) . '">' . esc_html__('Settings', 'foss-engine') . '</a>'
                    );
                    ?></li>
                <li><?php esc_html_e('Prepare a CSV file with a list of topics. Each topic should be on a new line.', 'foss-engine'); ?></li>
                <li><?php
                    printf(
                        /* translators: %s: URL to topics page */
                        esc_html__('Upload your CSV file in the %s section.', 'foss-engine'),
                        '<a href="' . esc_url(admin_url('admin.php?page=foss-engine-topics')) . '">' . esc_html__('Topics', 'foss-engine') . '</a>'
                    );
                    ?></li>
                <li><?php esc_html_e('Generate content for each topic, review, edit, and publish as needed.', 'foss-engine'); ?></li>
            </ol>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('Features', 'foss-engine'); ?></h2>
            <ul>
                <li><?php esc_html_e('Import topics from a CSV file', 'foss-engine'); ?></li>
                <li><?php esc_html_e('Generate content using AI for each topic', 'foss-engine'); ?></li>
                <li><?php esc_html_e('Edit generated content before publishing', 'foss-engine'); ?></li>
                <li><?php esc_html_e('Publish content as WordPress posts or pages', 'foss-engine'); ?></li>
                <li><?php esc_html_e('Regenerate content if needed', 'foss-engine'); ?></li>
                <li><?php esc_html_e('Track the status of each topic', 'foss-engine'); ?></li>
            </ul>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('CSV Format', 'foss-engine'); ?></h2>
            <p><?php esc_html_e('Your CSV file should have one topic per line. The first column will be used as the topic.', 'foss-engine'); ?></p>
            <p><?php esc_html_e('Example:', 'foss-engine'); ?></p>
            <pre>Best practices for WordPress security
How to improve website loading speed
Benefits of using a CDN for your website
Top WordPress plugins for SEO</pre>
            <p><?php esc_html_e('You can also include a header row if you prefer:', 'foss-engine'); ?></p>
            <pre>topic
Best practices for WordPress security
How to improve website loading speed
Benefits of using a CDN for your website
Top WordPress plugins for SEO</pre>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('Quick Access', 'foss-engine'); ?></h2>
            <div class="foss-engine-quick-access">
                <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-settings')); ?>" class="button button-primary"><?php esc_html_e('Settings', 'foss-engine'); ?></a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-topics')); ?>" class="button button-primary"><?php esc_html_e('Manage Topics', 'foss-engine'); ?></a>
            </div>
        </div>
    </div>
</div>