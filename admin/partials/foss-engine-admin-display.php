<?php

/**
 * Provide a admin area view for the plugin
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    Foss Engine
 * @subpackage Foss_Engine/admin/partials
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
?>

<div class="wrap foss-engine-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="notice notice-info">
        <p>
            <?php esc_html_e('🚀 Getting Started with FossEngine', 'foss-engine'); ?>
        </p>
    </div>
    <div class="foss-engine-container">

        <div class="foss-engine-section">
            <h2><?php esc_html_e('🔐 Step 1: Add Your API Key', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li>
                        <?php esc_html_e('Go to', 'foss-engine'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-settings')); ?>">
                            <?php esc_html_e('FossEngine → Settings', 'foss-engine'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Under the API Configuration section:', 'foss-engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Choose your preferred AI provider: OpenAI or DeepSeek.', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('Paste your API Key into the corresponding field.', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('Click Save Settings.', 'foss-engine'); ?></li>
                    </ul>
                    <li><?php esc_html_e('Enter the custom prompt for generating content. The AI model will follow the prompt instructions to generate the content.', 'foss-engine'); ?></li>
                    <li><strong><?php esc_html_e('❗ Make sure your API key has enough quota for generating multiple pieces of content.', 'foss-engine'); ?></strong></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('📄 Step 2: Prepare Your CSV File', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Create a CSV file with the list of topics you want content for.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('CSV Guidelines:', 'foss-engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Each topic should be on a separate line.', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('Use only one column.', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('No header row is required.', 'foss-engine'); ?></li>
                    </ul>
                    <li><?php esc_html_e('Example:', 'foss-engine'); ?></li>
                    <pre>
How to grow indoor plants
Benefits of solar energy for homes
Best Shopify apps for conversions
                    </pre>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('⬆️ Step 3: Upload Topics CSV', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li>
                        <?php esc_html_e('Go to', 'foss-engine'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-topics')); ?>">
                            <?php esc_html_e('FossEngine → Topics', 'foss-engine'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Click Upload CSV and select your file.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('The plugin will display a list of imported topics.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('Review the topics to ensure everything looks correct.', 'foss-engine'); ?></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('🤖 Step 4: Generate Content', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('After uploading, select one or more topics from the list.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('Click Generate Content.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('FossEngine will use your chosen AI provider to generate SEO-friendly articles.', 'foss-engine'); ?></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('✏️ Step 5: Review and Edit Content', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Once content is generated:', 'foss-engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Click Edit next to a topic.', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('Make any changes using the built-in editor (includes formatting, links, etc.).', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('You can Regenerate the content if you’re not satisfied.', 'foss-engine'); ?></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('✅ Step 6: Publish Your Posts', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('After editing:', 'foss-engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Click Publish to post it directly to your site.', 'foss-engine'); ?></li>
                        <li><?php esc_html_e('Choose whether to publish it as a Post or a Page.', 'foss-engine'); ?></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('🔁 Additional Features', 'foss-engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Regenerate content for a topic with a single click.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('Bulk actions to generate, edit, or publish multiple topics at once.', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('Track status of each topic (Generated/Pending).', 'foss-engine'); ?></li>
                    <li><?php esc_html_e('Date of adding the topics.', 'foss-engine'); ?></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('Quick Access', 'foss-engine'); ?></h2>
            <div class="foss-engine-quick-access">
                <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-settings')); ?>" class="button button-primary"><?php esc_html_e('Settings', 'foss-engine'); ?></a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-topics')); ?>" class="button button-primary"><?php esc_html_e('Content Manager', 'foss-engine'); ?></a>
            </div>
        </div>
    </div>
</div>