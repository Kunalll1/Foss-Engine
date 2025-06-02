<?php

/**
 * Provide a admin area view for the plugin
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    foss engine
 * @subpackage foss_engine/admin/partials
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
?>

<div class="wrap fossenginedein-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="notice notice-info">
        <p>
            <?php esc_html_e('🚀 Getting Started with FossEngine', 'fossenginedein'); ?>
        </p>
    </div>
    <div class="fossenginedein-container">

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('🔐 Step 1: Add Your API Key', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li>
                        <?php esc_html_e('Go to', 'fossenginedein'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=fossenginedein-settings')); ?>">
                            <?php esc_html_e('FossEngine → Settings', 'fossenginedein'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Under the API Configuration section:', 'fossenginedein'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Choose your preferred AI provider: OpenAI or DeepSeek.', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('Paste your API Key into the corresponding field.', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('Click Save Settings.', 'fossenginedein'); ?></li>
                    </ul>
                    <li><?php esc_html_e('Enter the custom prompt for generating content. The AI model will follow the prompt instructions to generate the content.', 'fossenginedein'); ?></li>
                    <li><strong><?php esc_html_e('❗ Make sure your API key has enough quota for generating multiple pieces of content.', 'fossenginedein'); ?></strong></li>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('📄 Step 2: Prepare Your CSV File', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Create a CSV file with the list of topics you want content for.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('CSV Guidelines:', 'fossenginedein'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Each topic should be on a separate line.', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('Use only one column.', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('No header row is required.', 'fossenginedein'); ?></li>
                    </ul>
                    <li><?php esc_html_e('Example:', 'fossenginedein'); ?></li>
                    <pre>
How to grow indoor plants
Benefits of solar energy for homes
Best Shopify apps for conversions
                    </pre>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('⬆️ Step 3: Upload Topics CSV', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li>
                        <?php esc_html_e('Go to', 'fossenginedein'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=fossenginedein-topics')); ?>">
                            <?php esc_html_e('FossEngine → Topics', 'fossenginedein'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Click Upload CSV and select your file.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('The plugin will display a list of imported topics.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('Review the topics to ensure everything looks correct.', 'fossenginedein'); ?></li>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('🤖 Step 4: Generate Content', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('After uploading, select one or more topics from the list.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('Click Generate Content.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('FossEngine will use your chosen AI provider to generate SEO-friendly articles.', 'fossenginedein'); ?></li>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('✏️ Step 5: Review and Edit Content', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Once content is generated:', 'fossenginedein'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Click Edit next to a topic.', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('Make any changes using the built-in editor (includes formatting, links, etc.).', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('You can Regenerate the content if you’re not satisfied.', 'fossenginedein'); ?></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('✅ Step 6: Publish Your Posts', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('After editing:', 'fossenginedein'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Click Publish to post it directly to your site.', 'fossenginedein'); ?></li>
                        <li><?php esc_html_e('Choose whether to publish it as a Post or a Page.', 'fossenginedein'); ?></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('🔁 Additional Features', 'fossenginedein'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Regenerate content for a topic with a single click.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('Bulk actions to generate, edit, or publish multiple topics at once.', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('Track status of each topic (Generated/Pending).', 'fossenginedein'); ?></li>
                    <li><?php esc_html_e('Date of adding the topics.', 'fossenginedein'); ?></li>
                </ul>
            </div>
        </div>

        <div class="fossenginedein-section">
            <h2><?php esc_html_e('Quick Access', 'fossenginedein'); ?></h2>
            <div class="fossenginedein-quick-access">
                <a href="<?php echo esc_url(admin_url('admin.php?page=fossenginedein-settings')); ?>" class="button button-primary"><?php esc_html_e('Settings', 'fossenginedein'); ?></a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=fossenginedein-topics')); ?>" class="button button-primary"><?php esc_html_e('Content Manager', 'fossenginedein'); ?></a>
            </div>
        </div>
    </div>
</div>