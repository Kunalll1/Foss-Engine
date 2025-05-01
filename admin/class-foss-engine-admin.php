<?php

/**
 * Admin functionality of the Foss Engine plugin.
 *
 * @package    Foss Engine
 * @subpackage Foss_Engine/admin
 */

class Foss_Engine_Admin
{
    // Plugin identifier and version
    private $plugin_name;
    private $version;

    /**
     * Initialize admin class properties
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register stylesheets for admin area
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/foss-engine-admin.css
            ',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register JavaScript for admin area
     */
    public function enqueue_scripts()
    {
        // Enqueue the admin script with stable version
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/foss-engine-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'foss_engine_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('foss_engine_nonce'),
            'plugin_version' => $this->version,
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
            'i18n' => array(
                'error' => esc_html__('Error', 'foss-engine'),
                'success' => esc_html__('Success', 'foss-engine'),
                'confirm_regenerate' => esc_html__('Are you sure you want to regenerate this content? The current content will be lost.', 'foss-engine'),
                'generating' => esc_html__('Generating content...', 'foss-engine'),
                'saving' => esc_html__('Saving...', 'foss-engine'),
                'publishing' => esc_html__('Publishing...', 'foss-engine'),
                'loading' => esc_html__('Loading...', 'foss-engine'),
                'confirm_publish' => esc_html__('Are you sure you want to publish this content?', 'foss-engine'),
                'troubleshooting' => esc_html__('If you continue to experience issues, try selecting the GPT-3.5 Turbo model in settings and ensure your API key has the correct permissions.', 'foss-engine'),
            )
        ));

        // Get current screen
        $screen = get_current_screen();

        // Register and enqueue the settings page script only on the plugin settings page
        if ($screen && $screen->id === $this->plugin_name . '_page_' . $this->plugin_name . '-settings') {
            // Register the settings script
            wp_register_script(
                $this->plugin_name . '-settings',
                plugin_dir_url(__FILE__) . 'js/foss-engine-admin-settings.js',
                array('jquery'),
                $this->version,
                true  // Load in footer
            );

            // Localize the settings script with translations
            wp_localize_script(
                $this->plugin_name . '-settings',
                'fossEngineAdmin',
                array(
                    'showText' => esc_html__('Show', 'foss-engine'),
                    'hideText' => esc_html__('Hide', 'foss-engine')
                )
            );

            // Enqueue the settings script
            wp_enqueue_script($this->plugin_name . '-settings');
        }
    }

    /**
     * Common AJAX security check helper
     *
     * @param string $nonce_action The nonce action to verify
     * @return bool|WP_Error Returns true if checks pass or WP_Error
     */
    private function verify_ajax_request($nonce_action = 'foss_engine_nonce')
    {
        // Check nonce
        if (!check_ajax_referer($nonce_action, 'nonce', false)) {
            return new WP_Error('invalid_nonce', esc_html__('Security check failed.', 'foss-engine'));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            return new WP_Error('insufficient_permissions', esc_html__('You do not have permission to perform this action.', 'foss-engine'));
        }

        return true;
    }

    /**
     * Send standardized JSON error response
     */
    private function send_error_response($error)
    {
        if (is_wp_error($error)) {
            $message = $error->get_error_message();
        } else {
            $message = $error;
        }

        wp_send_json_error(array('message' => $message));
    }

    /**
     * Test OpenAI API connection via AJAX
     */
    public function test_openai_connection()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_test_connection', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify nonce and permissions
        $security_check = $this->verify_ajax_request('foss_engine_test_connection');
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get API key from request
        $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';
        $model = isset($_POST['model']) ? sanitize_text_field(wp_unslash($_POST['model'])) : 'gpt-3.5-turbo';

        if (empty($api_key)) {
            $this->send_error_response(esc_html__('API key is required.', 'foss-engine'));
        }

        // Set the model for testing
        update_option('foss_engine_model', $model);

        // Test the connection
        $openai = new Foss_Engine_OpenAI($api_key);
        $result = $openai->test_connection();

        if (is_wp_error($result)) {
            $this->send_error_response($result);
        } else {
            wp_send_json_success(array(
                'message' => esc_html__('Connection successful!', 'foss-engine')
            ));
        }
    }

    /**
     * Add admin menu pages
     */
    public function add_plugin_admin_menu()
    {
        // Main menu
        add_menu_page(
            esc_html__('Foss Engine', 'foss-engine'),
            esc_html__('Foss Engine', 'foss-engine'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page'),
            plugin_dir_url(__DIR__) . 'icon.png',
            30
        );

        // Settings submenu
        add_submenu_page(
            $this->plugin_name,
            esc_html__('Foss Engine Settings', 'foss-engine'),
            esc_html__('Settings', 'foss-engine'),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_plugin_settings_page')
        );

        // Topic management submenu
        add_submenu_page(
            $this->plugin_name,
            esc_html__('Topics Management', 'foss-engine'),
            esc_html__('Topics', 'foss-engine'),
            'manage_options',
            $this->plugin_name . '-topics',
            array($this, 'display_plugin_topics_page')
        );
    }

    /**
     * Add settings action link to plugins page
     */
    public function add_action_links($links)
    {
        // Verify nonce when processing admin actions
        if (isset($_GET['_wpnonce']) && !wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'plugin_action')) {
            wp_die(esc_html__('Security check failed.', 'foss-engine'));
        }

        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name . '-settings') . '">' .
                esc_html__('Settings', 'foss-engine') . '</a>',
        );
        return array_merge($settings_link, $links);
    }

    /**
     * Display admin pages - each loads a partial template
     */
    public function display_plugin_setup_page()
    {
        // Verify user has permission to access this page
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'foss-engine'));
        }

        include_once('partials/foss-engine-admin-display.php');
    }

    public function display_plugin_settings_page()
    {
        // Verify user has permission to access this page
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'foss-engine'));
        }

        include_once('partials/foss-engine-admin-settings.php');
    }

    public function display_plugin_topics_page()
    {
        // Verify user has permission to access this page
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'foss-engine'));
        }

        include_once('partials/foss-engine-admin-topics.php');
    }

    /**
     * Register plugin settings
     */
    public function options_update()
    {
        // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- sanitize_callback is always set and safe.
        register_setting(
            $this->plugin_name,
            'foss_engine_openai_key',
            array(
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default' => '',
            )
        );

        // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- sanitize_callback is always set and safe.
        register_setting(
            $this->plugin_name,
            'foss_engine_prompt_template',
            array(
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => 'Write a comprehensive blog post about [TOPIC]. Include an introduction, several key points, and a conclusion. The content should be informative and engaging.'
            )
        );

        // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- sanitize_callback is always set and safe.
        register_setting(
            $this->plugin_name,
            'foss_engine_model',
            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'gpt-3.5-turbo'
            )
        );

        // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- sanitize_callback is always set and safe.
        register_setting(
            $this->plugin_name,
            'foss_engine_provider',
            array(
                'sanitize_callback' => array($this, 'sanitize_provider'),
                'default' => 'openai'
            )
        );

        // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- sanitize_callback is always set and safe.
        register_setting(
            $this->plugin_name,
            'foss_engine_deepseek_key',
            array(
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default' => '',
            )
        );

        // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- sanitize_callback is always set and safe.
        register_setting(
            $this->plugin_name,
            'foss_engine_deepseek_model',
            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'deepseek-chat'
            )
        );
    }

    /**
     * Sanitize API key setting
     */
    public function sanitize_api_key($input)
    {
        return sanitize_text_field($input);
    }

    /**
     * Sanitize provider setting
     */
    public function sanitize_provider($input)
    {
        $input = sanitize_text_field($input);

        // Ensure value is either 'openai' or 'deepseek'
        if (!in_array($input, array('openai', 'deepseek'))) {
            $input = 'openai'; // Default to OpenAI if invalid
        }

        return $input;
    }

    /**
     * Get user-friendly upload error message
     */
    private function get_upload_error_message($error_code)
    {
        $error_messages = array(
            UPLOAD_ERR_INI_SIZE => esc_html__('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'foss-engine'),
            UPLOAD_ERR_FORM_SIZE => esc_html__('The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.', 'foss-engine'),
            UPLOAD_ERR_PARTIAL => esc_html__('The uploaded file was only partially uploaded.', 'foss-engine'),
            UPLOAD_ERR_NO_FILE => esc_html__('No file was uploaded.', 'foss-engine'),
            UPLOAD_ERR_NO_TMP_DIR => esc_html__('Missing a temporary folder.', 'foss-engine'),
            UPLOAD_ERR_CANT_WRITE => esc_html__('Failed to write file to disk.', 'foss-engine'),
            UPLOAD_ERR_EXTENSION => esc_html__('A PHP extension stopped the file upload.', 'foss-engine')
        );

        return isset($error_messages[$error_code])
            ? $error_messages[$error_code]
            : esc_html__('Unknown upload error.', 'foss-engine');
    }


    /**
     * Handle CSV file upload for importing topics
     */
    public function handle_csv_upload()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Check if a file was uploaded
        if (!isset($_FILES['csv_file']) || !isset($_FILES['csv_file']['error']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $error = isset($_FILES['csv_file']['error']) ? (int) $_FILES['csv_file']['error'] : UPLOAD_ERR_NO_FILE;
            $this->send_error_response($this->get_upload_error_message($error));
        }

        // Use WordPress upload handling which is more reliable across different server environments
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        $upload_overrides = array(
            'test_form' => false,
            'test_type' => true,
            'mimes' => array('csv' => 'text/csv')
        );

        // This handles the file upload with WordPress functions
        $uploaded_file = wp_handle_upload($_FILES['csv_file'], $upload_overrides);

        if (isset($uploaded_file['error'])) {
            $this->send_error_response($uploaded_file['error']);
            return;
        }

        if (!isset($uploaded_file['file']) || !file_exists($uploaded_file['file'])) {
            $this->send_error_response(esc_html__('Upload failed. Could not process the file.', 'foss-engine'));
            return;
        }

        // Process the CSV file
        $csv_processor = new Foss_Engine_CSV();
        $topics = $csv_processor->process_csv($uploaded_file['file']);

        // Delete the file after processing
        wp_delete_file($uploaded_file['file']);

        if (is_wp_error($topics)) {
            $this->send_error_response($topics);
        }

        // Save topics to the database
        $result = $csv_processor->save_topics($topics);
        if (is_wp_error($result)) {
            $this->send_error_response($result);
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: %d: Number of topics imported */
                esc_html__('%d topics imported successfully.', 'foss-engine'),
                $result
            ),
            'topics_count' => $result
        ));
    }
    /**
     * Helper to get topic by ID with caching
     *
     * @param int $topic_id The topic ID to retrieve
     * @return object|null Topic object or null if not found
     */
    private function get_topic_by_id($topic_id)
    {
        global $wpdb;
        $cache_key = 'topic_' . $topic_id;
        $topic = wp_cache_get($cache_key, 'foss_engine');

        if (!$topic) {
            $table_name = $wpdb->prefix . 'foss_engine_topics';

            // Check if table exists first
            if (!function_exists('foss_engine_table_exists')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine.php';
            }

            if (!foss_engine_table_exists('foss_engine_topics')) {
                $this->send_error_response(esc_html__('Database table not found. Please deactivate and reactivate the plugin.', 'foss-engine'));
                return null;
            }

            // Use get_post() where possible in WordPress, but for custom tables
            // we need to use $wpdb with proper preparation
            $topic = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE id = %d",
                    $topic_id
                )
            );

            if ($topic) {
                wp_cache_set($cache_key, $topic, 'foss_engine', 3600); // Cache for 1 hour
            }
        }

        return $topic;
    }

    /**
     * Generate content for a topic
     */
    public function generate_content()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get topic ID
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'foss-engine'));
        }

        // Get the topic
        $topic = $this->get_topic_by_id($topic_id);
        if (!$topic) {
            $this->send_error_response(esc_html__('Topic not found.', 'foss-engine'));
        }

        // Check if the OpenAI API key is set
        $ai_provider = get_option('foss_engine_provider', 'openai');
        $openai_key = get_option('foss_engine_openai_key');
        $deepseek_key = get_option('foss_engine_deepseek_key');

        if ($ai_provider === 'openai' && empty($openai_key)) {
            $this->send_error_response(esc_html__('OpenAI API key is not set. Please configure it in the settings.', 'foss-engine'));
        } elseif ($ai_provider === 'deepseek' && empty($deepseek_key)) {
            $this->send_error_response(esc_html__('Deepseek API key is not set. Please configure it in the settings.', 'foss-engine'));
        }

        // Generate content using the selected AI provider
        try {
            $openai = new Foss_Engine_OpenAI();
            $result = $openai->generate_content($topic->topic);

            if (is_wp_error($result)) {
                $this->send_error_response($result);
            }
        } catch (Exception $e) {
            $this->send_error_response(esc_html__('An unexpected error occurred during content generation: ', 'foss-engine') . $e->getMessage());
        }

        // Update the topic in the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'foss_engine_topics';

        // Clear cache first
        wp_cache_delete('topic_' . $topic_id, 'foss_engine');
        wp_cache_delete('pending_topics', 'foss_engine');

        $update_result = $wpdb->update(
            $table_name,
            array(
                'content' => $result['content'],
                'status' => 'generated',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $topic_id),
            array('%s', '%s', '%s'),
            array('%d')
        );

        if ($update_result === false) {
            $this->send_error_response(esc_html__('Failed to update content in the database.', 'foss-engine'));
        }

        wp_send_json_success(array(
            'message' => esc_html__('Content generated successfully.', 'foss-engine'),
            'content' => $result['content'],
            'tokens' => $result['total_tokens']
        ));
    }

    /**
     * Save edited content
     */
    public function save_content()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get parameters
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        $content = isset($_POST['content']) ? wp_kses_post(wp_unslash($_POST['content'])) : '';

        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'foss-engine'));
        }

        if (empty($content)) {
            $this->send_error_response(esc_html__('Content cannot be empty.', 'foss-engine'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'foss_engine_topics';

        // Check if table exists first
        if (!function_exists('foss_engine_table_exists')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine.php';
        }

        if (!foss_engine_table_exists('foss_engine_topics')) {
            $this->send_error_response(esc_html__('Database table not found. Please deactivate and reactivate the plugin.', 'foss-engine'));
        }

        // Clear all related caches
        wp_cache_delete('topic_' . $topic_id, 'foss_engine');
        wp_cache_delete('pending_topics', 'foss_engine');

        $update_result = $wpdb->update(
            $table_name,
            array(
                'content' => $content,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $topic_id),
            array('%s', '%s'),  // Format for data (all strings)
            array('%d')         // Format for where clause (integer)
        );

        if ($update_result === false) {
            $this->send_error_response(esc_html__('Failed to save content.', 'foss-engine'));
        }

        wp_send_json_success(array(
            'message' => esc_html__('Content saved successfully.', 'foss-engine')
        ));
    }

    /**
     * Publish content as a post or page
     */
    public function publish_content()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get parameters
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        $publish_type = isset($_POST['publish_type']) ? sanitize_text_field(wp_unslash($_POST['publish_type'])) : 'post';

        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'foss-engine'));
        }

        if (!in_array($publish_type, array('post', 'page'))) {
            $this->send_error_response(esc_html__('Invalid publish type.', 'foss-engine'));
        }

        // Get the topic
        $topic = $this->get_topic_by_id($topic_id);
        if (!$topic) {
            $this->send_error_response(esc_html__('Topic not found.', 'foss-engine'));
        }

        if (empty($topic->content)) {
            $this->send_error_response(esc_html__('Cannot publish empty content.', 'foss-engine'));
        }

        // Create post/page - this is a WordPress API, not a direct DB query
        $post_data = array(
            'post_title'    => $topic->topic,
            'post_content'  => $topic->content,
            'post_status'   => 'draft',
            'post_type'     => $publish_type,
            'post_author'   => get_current_user_id()
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            $this->send_error_response($post_id);
        }

        // Update the topic status
        global $wpdb;
        $table_name = $wpdb->prefix . 'foss_engine_topics';

        // Check if table exists first
        if (!function_exists('foss_engine_table_exists')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine.php';
        }

        if (!foss_engine_table_exists('foss_engine_topics')) {
            $this->send_error_response(esc_html__('Database table not found. Please deactivate and reactivate the plugin.', 'foss-engine'));
        }

        // Clear all related caches
        wp_cache_delete('topic_' . $topic_id, 'foss_engine');
        wp_cache_delete('pending_topics', 'foss_engine');

        $update_result = $wpdb->update(
            $table_name,
            array(
                'status' => 'published',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $topic_id),
            array('%s', '%s'),  // Format for data (all strings)
            array('%d')         // Format for where clause (integer)
        );

        if ($update_result === false) {
            $this->send_error_response(esc_html__('Published content but failed to update topic status.', 'foss-engine'));
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: 1: Content type (post/page), 2: Edit URL, 3: Content type again */
                esc_html__('Content published as a %1$s (draft). <a href="%2$s" target="_blank">Edit %3$s</a>', 'foss-engine'),
                $publish_type,
                esc_url(get_edit_post_link($post_id)),
                $publish_type
            ),
            'post_id' => $post_id,
            'edit_url' => esc_url(get_edit_post_link($post_id))
        ));
    }

    /**
     * Regenerate content alias - calls generate_content()
     */
    public function regenerate_content()
    {
        $this->generate_content();
    }

    /**
     * Get topic content for editing
     */
    public function get_topic_content()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get topic ID
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'foss-engine'));
        }

        // Get the topic
        $topic = $this->get_topic_by_id($topic_id);
        if (!$topic) {
            $this->send_error_response(esc_html__('Topic not found.', 'foss-engine'));
        }

        if (empty($topic->content)) {
            $this->send_error_response(esc_html__('No content found for this topic. Please generate content first.', 'foss-engine'));
        }

        wp_send_json_success(array(
            'topic' => $topic->topic,
            'content' => $topic->content,
            'status' => $topic->status
        ));
    }

    /**
     * Get pending topics
     */
    public function get_pending_topics()
    {
        // Direct nonce verification for coding standards compliance
        if (!isset($_POST['nonce']) || !check_ajax_referer('foss_engine_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'foss-engine')));
            return;
        }

        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'foss_engine_topics';

        // Check if table exists first
        if (!function_exists('foss_engine_table_exists')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine.php';
        }

        if (!foss_engine_table_exists('foss_engine_topics')) {
            $this->send_error_response(esc_html__('Database table not found. Please deactivate and reactivate the plugin.', 'foss-engine'));
        }

        // Get pending and generated topics with caching
        $cache_key = 'pending_topics';
        $topics = wp_cache_get($cache_key, 'foss_engine');

        if (!$topics) {
            // This is a custom table so we need to use $wpdb - properly prepared
            $topics = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE status IN (%s, %s) ORDER BY created_at DESC",
                    'pending',
                    'generated'
                )
            );

            if ($topics) {
                wp_cache_set($cache_key, $topics, 'foss_engine', 60); // Cache for 1 minute
            }
        }

        if ($wpdb->last_error) {
            $this->send_error_response($wpdb->last_error);
        }

        wp_send_json_success(array('topics' => $topics));
    }
}
