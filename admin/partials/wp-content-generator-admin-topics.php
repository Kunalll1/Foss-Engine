<?php

/**
 * Provide a admin area view for the plugin topics management
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

// Check if OpenAI API key is set
$openai_key = get_option('wp_content_generator_openai_key', '');
$api_key_set = !empty($openai_key);

// Get topics from database
global $wpdb;
$table_name = $wpdb->prefix . 'content_generator_topics';
$topics = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap wp-content-generator-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (!$api_key_set): ?>
        <div class="notice notice-warning">
            <p><?php echo wp_kses(__('OpenAI API key is not set. Please configure it in the <a href="admin.php?page=wp-content-generator-settings">Settings</a> page.', 'wp-content-generator-security-enhanced'), array(
                    'a' => array(
                        'href' => array(),
                    ),
                )); ?></p>
        </div>
    <?php endif; ?>

    <div class="wp-content-generator-container">
        <div class="wp-content-generator-section">
            <h2><?php esc_html_e('Upload CSV File', 'wp-content-generator-security-enhanced'); ?></h2>
            <form id="csv-upload-form" enctype="multipart/form-data">
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required />
                <button type="submit" class="button button-primary"><?php esc_html_e('Upload and Import', 'wp-content-generator-security-enhanced'); ?></button>
                <div id="upload-progress" style="display: none;">
                    <span class="spinner is-active"></span>
                    <span><?php esc_html_e('Uploading...', 'wp-content-generator-security-enhanced'); ?></span>
                </div>
                <div id="upload-results" class="upload-results"></div>
            </form>
        </div>

        <div class="wp-content-generator-section">
            <h2><?php esc_html_e('Manage Topics', 'wp-content-generator-security-enhanced'); ?></h2>

            <div id="topics-table-wrapper">
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <button id="generate-all-button" class="button button-primary" <?php echo !$api_key_set ? 'disabled' : ''; ?>>
                            <?php esc_html_e('Generate Content for All Pending Topics', 'wp-content-generator-security-enhanced'); ?>
                        </button>
                    </div>
                    <div class="tablenav-pages">
                        <span class="displaying-num">
                            <?php
                            echo esc_html(sprintf(
                                /* translators: %s: number of topics */
                                _n('%s topic', '%s topics', count($topics), 'wp-content-generator-security-enhanced'),
                                number_format_i18n(count($topics))
                            ));
                            ?>
                        </span>
                    </div>
                    <br class="clear">
                </div>

                <table class="wp-list-table widefat fixed striped topics-table">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-topic"><?php esc_html_e('Topic', 'wp-content-generator-security-enhanced'); ?></th>
                            <th scope="col" class="manage-column column-status"><?php esc_html_e('Status', 'wp-content-generator-security-enhanced'); ?></th>
                            <th scope="col" class="manage-column column-date"><?php esc_html_e('Date Added', 'wp-content-generator-security-enhanced'); ?></th>
                            <th scope="col" class="manage-column column-actions"><?php esc_html_e('Actions', 'wp-content-generator-security-enhanced'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="the-list">
                        <?php if (empty($topics)): ?>
                            <tr class="no-items">
                                <td class="colspanchange" colspan="4"><?php esc_html_e('No topics found. Upload a CSV file to get started.', 'wp-content-generator-security-enhanced'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topics as $topic): ?>
                                <tr id="topic-row-<?php echo esc_attr($topic->id); ?>" data-id="<?php echo esc_attr($topic->id); ?>">
                                    <td class="column-topic">
                                        <?php echo esc_html($topic->topic); ?>
                                    </td>
                                    <td class="column-status">
                                        <span class="status-badge status-<?php echo esc_attr($topic->status); ?>">
                                            <?php
                                            switch ($topic->status) {
                                                case 'pending':
                                                    esc_html_e('Pending', 'wp-content-generator-security-enhanced');
                                                    break;
                                                case 'generated':
                                                    esc_html_e('Generated', 'wp-content-generator-security-enhanced');
                                                    break;
                                                case 'published':
                                                    esc_html_e('Published', 'wp-content-generator-security-enhanced');
                                                    break;
                                                default:
                                                    echo esc_html(ucfirst($topic->status));
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td class="column-date">
                                        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($topic->created_at))); ?>
                                    </td>
                                    <td class="column-actions">
                                        <?php if ($topic->status === 'pending'): ?>
                                            <button class="button generate-content-button" data-id="<?php echo esc_attr($topic->id); ?>" <?php echo !$api_key_set ? 'disabled' : ''; ?>>
                                                <?php esc_html_e('Generate', 'wp-content-generator-security-enhanced'); ?>
                                            </button>
                                        <?php elseif ($topic->status === 'generated'): ?>
                                            <button class="button button-primary edit-content-button" data-id="<?php echo esc_attr($topic->id); ?>">
                                                <?php esc_html_e('Edit', 'wp-content-generator-security-enhanced'); ?>
                                            </button>
                                            <button class="button regenerate-content-button" data-id="<?php echo esc_attr($topic->id); ?>" <?php echo !$api_key_set ? 'disabled' : ''; ?>>
                                                <?php esc_html_e('Regenerate', 'wp-content-generator-security-enhanced'); ?>
                                            </button>
                                        <?php elseif ($topic->status === 'published'): ?>
                                            <span class="published-status"><?php esc_html_e('Content Published', 'wp-content-generator-security-enhanced'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Content Editor Modal -->
    <div id="content-editor-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2 id="modal-title"><?php esc_html_e('Edit Content', 'wp-content-generator-security-enhanced'); ?></h2>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editing-topic-id" value="">
                <div class="topic-title-container">
                    <h3><?php esc_html_e('Topic:', 'wp-content-generator-security-enhanced'); ?> <span id="editing-topic-title"></span></h3>
                </div>
                <div class="content-editor-container">
                    <?php
                    wp_editor('', 'topic-content-editor', array(
                        'media_buttons' => true,
                        'textarea_rows' => 15,
                        'teeny' => false,
                        'tinymce' => array(
                            'height' => 400,
                            'resize' => false,
                        ),
                    ));
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <button id="save-content-button" class="button button-primary"><?php esc_html_e('Save Content', 'wp-content-generator-security-enhanced'); ?></button>
                <button id="publish-content-button" class="button button-primary"><?php esc_html_e('Approve & Publish', 'wp-content-generator-security-enhanced'); ?></button>
                <button id="cancel-edit-button" class="button"><?php esc_html_e('Cancel', 'wp-content-generator-security-enhanced'); ?></button>
            </div>
        </div>
    </div>

    <!-- Publish Options Modal -->
    <div id="publish-options-modal" class="modal" style="display: none;">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2><?php esc_html_e('Publish Options', 'wp-content-generator-security-enhanced'); ?></h2>
            </div>
            <div class="modal-body">
                <p><?php esc_html_e('How would you like to publish this content?', 'wp-content-generator-security-enhanced'); ?></p>
                <div class="publish-options">
                    <label>
                        <input type="radio" name="publish-type" value="post" checked>
                        <?php esc_html_e('As a Post', 'wp-content-generator-security-enhanced'); ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="publish-type" value="page">
                        <?php esc_html_e('As a Page', 'wp-content-generator-security-enhanced'); ?>
                    </label>
                </div>
                <p class="description"><?php esc_html_e('The content will be created as a draft, which you can review before publishing.', 'wp-content-generator-security-enhanced'); ?></p>
            </div>
            <div class="modal-footer">
                <button id="confirm-publish-button" class="button button-primary"><?php esc_html_e('Publish', 'wp-content-generator-security-enhanced'); ?></button>
                <button id="cancel-publish-button" class="button"><?php esc_html_e('Cancel', 'wp-content-generator-security-enhanced'); ?></button>
            </div>
        </div>
    </div>
</div>