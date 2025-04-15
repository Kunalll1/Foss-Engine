<?php

/**
 * Provide a admin area view for the plugin settings
 *
 * This file is used to markup the admin-facing settings aspects of the plugin.
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

// Get saved options
$openai_key = get_option('foss_engine_openai_key', '');
$prompt_template = get_option('foss_engine_prompt_template', 'Write a comprehensive SEO-friendly blog post about [TOPIC]. Format the content with proper HTML structure including h2, h3, and h4 tags for sections and subsections. Start with a compelling title (wrapped in a heading tag). Include an engaging introduction, multiple well-structured sections with appropriate headings, and a strong conclusion. Use semantic HTML like p, ul, ol, strong, and em tags. Make the content scannable with short paragraphs and bullet points where relevant. Optimize for SEO by including the main keyword in headings and within the first paragraph.');

// Get AI provider settings (default to OpenAI)
$ai_provider = get_option('foss_engine_provider', 'openai');
$deepseek_key = get_option('foss_engine_deepseek_key', '');
$deepseek_model = get_option('foss_engine_deepseek_model', 'deepseek-chat');

// Test connection if API key is set for the active provider
$connection_status = '';
if ($ai_provider === 'openai' && !empty($openai_key)) {
    $openai = new Foss_Engine_OpenAI($openai_key);
    $test_result = $openai->test_connection();

    if (is_wp_error($test_result)) {
        $connection_status = '<span class="connection-error">' . esc_html__('Connection Error: ', 'foss-engine') . esc_html($test_result->get_error_message()) . '</span>';
    } else {
        $connection_status = '<span class="connection-success">' . esc_html__('Connected successfully to OpenAI API.', 'foss-engine') . '</span>';
    }
}
?>

<div class="wrap foss-engine-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
        ?>

        <div class="foss-engine-settings-section">
            <h2><?php esc_html_e('AI Provider Settings', 'foss-engine'); ?></h2>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label><?php esc_html_e('Select AI Provider', 'foss-engine'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="foss_engine_provider" value="openai" <?php checked($ai_provider, 'openai'); ?>>
                                <?php esc_html_e('OpenAI', 'foss-engine'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="foss_engine_provider" value="deepseek" <?php checked($ai_provider, 'deepseek'); ?>>
                                <?php esc_html_e('Deepseek', 'foss-engine'); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e('Choose which AI provider to use for content generation.', 'foss-engine'); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

        <div id="openai-settings" class="foss-engine-settings-section" <?php echo $ai_provider === 'deepseek' ? 'style="display:none;"' : ''; ?>>
            <h2><?php esc_html_e('OpenAI API Settings', 'foss-engine'); ?></h2>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="foss_engine_openai_key"><?php esc_html_e('OpenAI API Key', 'foss-engine'); ?></label>
                    </th>
                    <td>
                        <input type="password"
                            id="foss_engine_openai_key"
                            name="foss_engine_openai_key"
                            value="<?php echo esc_attr($openai_key); ?>"
                            class="regular-text" />
                        <button type="button" id="toggle-api-key" class="button button-secondary"><?php esc_html_e('Show', 'foss-engine'); ?></button>
                        <p class="description">
                            <?php echo wp_kses(__('Enter your OpenAI API key. You can get one from <a href="https://platform.openai.com/account/api-keys" target="_blank">OpenAI dashboard</a>.', 'foss-engine'), array(
                                'a' => array(
                                    'href' => array(),
                                    'target' => array(),
                                ),
                            )); ?>
                        </p>
                        <?php if ($ai_provider === 'openai' && !empty($connection_status)): ?>
                            <p class="api-connection-status"><?php echo wp_kses_post($connection_status); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="foss_engine_model"><?php esc_html_e('OpenAI Model', 'foss-engine'); ?></label>
                    </th>
                    <td>
                        <?php
                        $current_model = get_option('foss_engine_model', 'gpt-3.5-turbo');
                        ?>
                        <select id="foss_engine_model" name="foss_engine_model">
                            <option value="gpt-3.5-turbo" <?php selected($current_model, 'gpt-3.5-turbo'); ?>><?php esc_html_e('GPT-3.5 Turbo (Faster)', 'foss-engine'); ?></option>
                            <option value="gpt-3.5-turbo-16k" <?php selected($current_model, 'gpt-3.5-turbo-16k'); ?>><?php esc_html_e('GPT-3.5 Turbo 16K (Longer Content)', 'foss-engine'); ?></option>
                            <option value="gpt-4" <?php selected($current_model, 'gpt-4'); ?>><?php esc_html_e('GPT-4 (Better Quality)', 'foss-engine'); ?></option>
                            <option value="gpt-4-turbo" <?php selected($current_model, 'gpt-4-turbo'); ?>><?php esc_html_e('GPT-4 Turbo (Latest)', 'foss-engine'); ?></option>
                        </select>
                        <p class="description">
                            <?php esc_html_e('Select which OpenAI model to use. GPT-4 may produce better quality content but is slower and more expensive than GPT-3.5 Turbo. Make sure your API key has access to the selected model.', 'foss-engine'); ?>
                        </p>
                        <p style="padding: 10px; background-color: #f8f8f8; border-left: 4px solid #ffb900; margin-top: 10px;">
                            <strong><?php esc_html_e('Troubleshooting Tip:', 'foss-engine'); ?></strong>
                            <?php echo wp_kses(__('If you encounter errors when generating content, your API key might not have access to all models. Try using <code>gpt-3.5-turbo</code>, which is available to most API keys.', 'foss-engine'), array(
                                'code' => array(),
                            )); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div id="deepseek-settings" class="foss-engine-settings-section" <?php echo $ai_provider === 'openai' ? 'style="display:none;"' : ''; ?>>
            <h2><?php esc_html_e('Deepseek API Settings', 'foss-engine'); ?></h2>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="foss_engine_deepseek_key"><?php esc_html_e('Deepseek API Key', 'foss-engine'); ?></label>
                    </th>
                    <td>
                        <input type="password"
                            id="foss_engine_deepseek_key"
                            name="foss_engine_deepseek_key"
                            value="<?php echo esc_attr($deepseek_key); ?>"
                            class="regular-text" />
                        <button type="button" id="toggle-deepseek-key" class="button button-secondary"><?php esc_html_e('Show', 'foss-engine'); ?></button>
                        <p class="description">
                            <?php echo wp_kses(__('Enter your Deepseek API key. You can get one from <a href="https://platform.deepseek.com/" target="_blank">Deepseek dashboard</a>.', 'foss-engine'), array(
                                'a' => array(
                                    'href' => array(),
                                    'target' => array(),
                                ),
                            )); ?>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="foss_engine_deepseek_model"><?php esc_html_e('Deepseek Model', 'foss-engine'); ?></label>
                    </th>
                    <td>
                        <select id="foss_engine_deepseek_model" name="foss_engine_deepseek_model">
                            <option value="deepseek-chat" <?php selected($deepseek_model, 'deepseek-chat'); ?>><?php esc_html_e('Deepseek Chat', 'foss-engine'); ?></option>
                            <option value="deepseek-coder" <?php selected($deepseek_model, 'deepseek-coder'); ?>><?php esc_html_e('Deepseek Coder', 'foss-engine'); ?></option>
                        </select>
                        <p class="description">
                            <?php esc_html_e('Select which Deepseek model to use for content generation.', 'foss-engine'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="foss-engine-settings-section">
            <h2><?php esc_html_e('Content Generation Settings', 'foss-engine'); ?></h2>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="foss_engine_prompt_template"><?php esc_html_e('Prompt Template', 'foss-engine'); ?></label>
                    </th>
                    <td>
                        <textarea id="foss_engine_prompt_template"
                            name="foss_engine_prompt_template"
                            rows="5"
                            class="large-text"><?php echo esc_textarea($prompt_template); ?></textarea>
                        <p class="description">
                            <?php esc_html_e('Enter the prompt template for content generation. Use [TOPIC] as a placeholder for the actual topic.', 'foss-engine'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(esc_html__('Save Settings', 'foss-engine'), 'primary', 'submit', true); ?>
    </form>

    <script>
        jQuery(document).ready(function($) {
            // Toggle API key visibility
            $('#toggle-api-key').on('click', function() {
                var $apiKey = $('#foss_engine_openai_key');
                var $button = $(this);

                if ($apiKey.attr('type') === 'password') {
                    $apiKey.attr('type', 'text');
                    $button.text('<?php echo esc_js(__('Hide', 'foss-engine')); ?>');
                } else {
                    $apiKey.attr('type', 'password');
                    $button.text('<?php echo esc_js(__('Show', 'foss-engine')); ?>');
                }
            });

            // Toggle Deepseek API key visibility
            $('#toggle-deepseek-key').on('click', function() {
                var $apiKey = $('#foss_engine_deepseek_key');
                var $button = $(this);

                if ($apiKey.attr('type') === 'password') {
                    $apiKey.attr('type', 'text');
                    $button.text('<?php echo esc_js(__('Hide', 'foss-engine')); ?>');
                } else {
                    $apiKey.attr('type', 'password');
                    $button.text('<?php echo esc_js(__('Show', 'foss-engine')); ?>');
                }
            });

            // Toggle between OpenAI and Deepseek settings
            $('input[name="foss_engine_provider"]').on('change', function() {
                if ($(this).val() === 'openai') {
                    $('#openai-settings').show();
                    $('#deepseek-settings').hide();
                } else {
                    $('#openai-settings').hide();
                    $('#deepseek-settings').show();
                }
            });
        });
    </script>
</div>