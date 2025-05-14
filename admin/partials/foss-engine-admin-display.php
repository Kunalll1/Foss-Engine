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
            <?php esc_html_e('🚀 Getting Started with FossEngine', 'Foss-Engine'); ?>
        </p>
    </div>
    <div class="foss-engine-container">

        <div class="foss-engine-section">
            <h2><?php esc_html_e('🔐 Step 1: Add Your API Key', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li>
                        <?php esc_html_e('Go to', 'Foss-Engine'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-settings')); ?>">
                            <?php esc_html_e('FossEngine → Settings', 'Foss-Engine'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Under the API Configuration section:', 'Foss-Engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Choose your preferred AI provider: OpenAI or DeepSeek.', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('Paste your API Key into the corresponding field.', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('Click Save Settings.', 'Foss-Engine'); ?></li>
                    </ul>
                    <li><?php esc_html_e('Enter the custom prompt for generating content. The AI model will follow the prompt instructions to generate the content.', 'Foss-Engine'); ?></li>
                    <li><strong><?php esc_html_e('❗ Make sure your API key has enough quota for generating multiple pieces of content.', 'Foss-Engine'); ?></strong></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('📄 Step 2: Prepare Your CSV File', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Create a CSV file with the list of topics you want content for.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('CSV Guidelines:', 'Foss-Engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Each topic should be on a separate line.', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('Use only one column.', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('No header row is required.', 'Foss-Engine'); ?></li>
                    </ul>
                    <li><?php esc_html_e('Example:', 'Foss-Engine'); ?></li>
                    <pre>
How to grow indoor plants
Benefits of solar energy for homes
Best Shopify apps for conversions
                    </pre>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('⬆️ Step 3: Upload Topics CSV', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li>
                        <?php esc_html_e('Go to', 'Foss-Engine'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-topics')); ?>">
                            <?php esc_html_e('FossEngine → Topics', 'Foss-Engine'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Click Upload CSV and select your file.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('The plugin will display a list of imported topics.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('Review the topics to ensure everything looks correct.', 'Foss-Engine'); ?></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('🤖 Step 4: Generate Content', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('After uploading, select one or more topics from the list.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('Click Generate Content.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('FossEngine will use your chosen AI provider to generate SEO-friendly articles.', 'Foss-Engine'); ?></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('✏️ Step 5: Review and Edit Content', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Once content is generated:', 'Foss-Engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Click Edit next to a topic.', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('Make any changes using the built-in editor (includes formatting, links, etc.).', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('You can Regenerate the content if you’re not satisfied.', 'Foss-Engine'); ?></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('✅ Step 6: Publish Your Posts', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('After editing:', 'Foss-Engine'); ?></li>
                    <ul>
                        <li><?php esc_html_e('Click Publish to post it directly to your site.', 'Foss-Engine'); ?></li>
                        <li><?php esc_html_e('Choose whether to publish it as a Post or a Page.', 'Foss-Engine'); ?></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('🔁 Additional Features', 'Foss-Engine'); ?></h2>
            <div>
                <ul>
                    <li><?php esc_html_e('Regenerate content for a topic with a single click.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('Bulk actions to generate, edit, or publish multiple topics at once.', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('Track status of each topic (Generated/Pending).', 'Foss-Engine'); ?></li>
                    <li><?php esc_html_e('Date of adding the topics.', 'Foss-Engine'); ?></li>
                </ul>
            </div>
        </div>

        <div class="foss-engine-section">
            <h2><?php esc_html_e('Quick Access', 'Foss-Engine'); ?></h2>
            <div class="foss-engine-quick-access">
                <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-settings')); ?>" class="button button-primary"><?php esc_html_e('Settings', 'Foss-Engine'); ?></a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=foss-engine-topics')); ?>" class="button button-primary"><?php esc_html_e('Content Manager', 'Foss-Engine'); ?></a>
            </div>
        </div>
    </div>
</div>