<?php

/**
 * Admin functionality of the WP Content Generator plugin.
 *
 * @package    WP_Content_Generator
 * @subpackage WP_Content_Generator/admin
 */

class WP_Content_Generator_Admin
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
            plugin_dir_url(__FILE__) . 'css/wp-content-generator-admin.css',
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
            plugin_dir_url(__FILE__) . 'js/wp-content-generator-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'wp_content_generator_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_content_generator_nonce'),
            'plugin_version' => $this->version,
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
            'i18n' => array(
                'error' => esc_html__('Error', 'wp-content-generator-security-enhanced'),
                'success' => esc_html__('Success', 'wp-content-generator-security-enhanced'),
                'confirm_regenerate' => esc_html__('Are you sure you want to regenerate this content? The current content will be lost.', 'wp-content-generator-security-enhanced'),
                'generating' => esc_html__('Generating content...', 'wp-content-generator-security-enhanced'),
                'saving' => esc_html__('Saving...', 'wp-content-generator-security-enhanced'),
                'publishing' => esc_html__('Publishing...', 'wp-content-generator-security-enhanced'),
                'loading' => esc_html__('Loading...', 'wp-content-generator-security-enhanced'),
                'confirm_publish' => esc_html__('Are you sure you want to publish this content?', 'wp-content-generator-security-enhanced'),
                'troubleshooting' => esc_html__('If you continue to experience issues, try selecting the GPT-3.5 Turbo model in settings and ensure your API key has the correct permissions.', 'wp-content-generator-security-enhanced'),
            )
        ));
    }

    /**
     * Common AJAX security check helper
     * 
     * @param string $nonce_action The nonce action to verify
     * @return bool|WP_Error Returns true if checks pass or WP_Error
     */
    private function verify_ajax_request($nonce_action = 'wp_content_generator_nonce')
    {
        // Check nonce
        if (!check_ajax_referer($nonce_action, 'nonce', false)) {
            return new WP_Error('invalid_nonce', esc_html__('Security check failed.', 'wp-content-generator-security-enhanced'));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            return new WP_Error('insufficient_permissions', esc_html__('You do not have permission to perform this action.', 'wp-content-generator-security-enhanced'));
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
        // Verify nonce and permissions
        $security_check = $this->verify_ajax_request('wp_content_generator_test_connection');
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get API key from request
        $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';
        $model = isset($_POST['model']) ? sanitize_text_field(wp_unslash($_POST['model'])) : 'gpt-3.5-turbo';

        if (empty($api_key)) {
            $this->send_error_response(esc_html__('API key is required.', 'wp-content-generator-security-enhanced'));
        }

        // Force debug mode for this test
        if (!defined('WP_DEBUG')) {
            define('WP_DEBUG', true);
        }

        // Set the model for testing
        update_option('wp_content_generator_model', $model);

        // Test the connection
        $openai = new WP_Content_Generator_OpenAI($api_key);
        $result = $openai->test_connection();

        if (is_wp_error($result)) {
            $this->send_error_response($result);
        } else {
            wp_send_json_success(array(
                'message' => esc_html__('Connection successful!', 'wp-content-generator-security-enhanced')
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
            esc_html__('Content Generator', 'wp-content-generator-security-enhanced'),
            esc_html__('Content Generator', 'wp-content-generator-security-enhanced'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page'),
            'dashicons-edit',
            30
        );

        // Settings submenu
        add_submenu_page(
            $this->plugin_name,
            esc_html__('Content Generator Settings', 'wp-content-generator-security-enhanced'),
            esc_html__('Settings', 'wp-content-generator-security-enhanced'),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_plugin_settings_page')
        );

        // Topic management submenu
        add_submenu_page(
            $this->plugin_name,
            esc_html__('Topics Management', 'wp-content-generator-security-enhanced'),
            esc_html__('Topics', 'wp-content-generator-security-enhanced'),
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
            wp_die(esc_html__('Security check failed.', 'wp-content-generator-security-enhanced'));
        }

        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name . '-settings') . '">' .
                esc_html__('Settings', 'wp-content-generator-security-enhanced') . '</a>',
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
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wp-content-generator-security-enhanced'));
        }

        include_once('partials/wp-content-generator-admin-display.php');
    }

    public function display_plugin_settings_page()
    {
        // Verify user has permission to access this page
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wp-content-generator-security-enhanced'));
        }

        include_once('partials/wp-content-generator-admin-settings.php');
    }

    public function display_plugin_topics_page()
    {
        // Verify user has permission to access this page
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wp-content-generator-security-enhanced'));
        }

        include_once('partials/wp-content-generator-admin-topics.php');
    }

    /**
     * Register plugin settings
     */
    public function options_update()
    {
        register_setting(
            $this->plugin_name,
            'wp_content_generator_openai_key',
            array(
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default' => '',
            )
        );

        register_setting(
            $this->plugin_name,
            'wp_content_generator_prompt_template',
            array(
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => 'Write a comprehensive blog post about [TOPIC]. Include an introduction, several key points, and a conclusion. The content should be informative and engaging.'
            )
        );

        register_setting(
            $this->plugin_name,
            'wp_content_generator_model',
            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'gpt-3.5-turbo'
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
     * Get user-friendly upload error message
     */
    private function get_upload_error_message($error_code)
    {
        $error_messages = array(
            UPLOAD_ERR_INI_SIZE => esc_html__('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'wp-content-generator-security-enhanced'),
            UPLOAD_ERR_FORM_SIZE => esc_html__('The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.', 'wp-content-generator-security-enhanced'),
            UPLOAD_ERR_PARTIAL => esc_html__('The uploaded file was only partially uploaded.', 'wp-content-generator-security-enhanced'),
            UPLOAD_ERR_NO_FILE => esc_html__('No file was uploaded.', 'wp-content-generator-security-enhanced'),
            UPLOAD_ERR_NO_TMP_DIR => esc_html__('Missing a temporary folder.', 'wp-content-generator-security-enhanced'),
            UPLOAD_ERR_CANT_WRITE => esc_html__('Failed to write file to disk.', 'wp-content-generator-security-enhanced'),
            UPLOAD_ERR_EXTENSION => esc_html__('A PHP extension stopped the file upload.', 'wp-content-generator-security-enhanced')
        );

        return isset($error_messages[$error_code])
            ? $error_messages[$error_code]
            : esc_html__('Unknown upload error.', 'wp-content-generator-security-enhanced');
    }

    /**
     * Handle CSV file upload for importing topics
     */
    public function handle_csv_upload()
    {
        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Check if a file was uploaded
        if (!isset($_FILES['csv_file']) || !isset($_FILES['csv_file']['error']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $error = isset($_FILES['csv_file']['error']) ? $_FILES['csv_file']['error'] : UPLOAD_ERR_NO_FILE;
            $this->send_error_response($this->get_upload_error_message($error));
        }

        // Validate file type
        $file_name = isset($_FILES['csv_file']['name']) ? sanitize_file_name(wp_unslash($_FILES['csv_file']['name'])) : '';
        $file_info = wp_check_filetype(basename($file_name));
        if ($file_info['ext'] !== 'csv') {
            $this->send_error_response(esc_html__('Please upload a valid CSV file.', 'wp-content-generator-security-enhanced'));
        }

        // Setup temporary directory
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/wp-content-generator-temp';
        if (!file_exists($temp_dir)) {
            wp_mkdir_p($temp_dir);
        }

        // Create .htaccess file for security
        if (!file_exists($temp_dir . '/.htaccess')) {
            $file_result = file_put_contents($temp_dir . '/.htaccess', 'deny from all');
            if (false === $file_result) {
                $this->send_error_response(esc_html__('Could not create security file.', 'wp-content-generator-security-enhanced'));
            }
        }

        // Create temp file
        $temp_file = tempnam($temp_dir, 'wcg_');
        if (!$temp_file) {
            $this->send_error_response(esc_html__('Could not create temporary file.', 'wp-content-generator-security-enhanced'));
        }

        // Move uploaded file to temp directory
        $tmp_name = isset($_FILES['csv_file']['tmp_name']) ? wp_unslash($_FILES['csv_file']['tmp_name']) : '';
        if (empty($tmp_name) || !copy($tmp_name, $temp_file)) {
            $this->send_error_response(esc_html__('Failed to move uploaded file.', 'wp-content-generator-security-enhanced'));
        }

        // Process the CSV file
        $csv_processor = new WP_Content_Generator_CSV();
        $topics = $csv_processor->process_csv($temp_file);

        // Delete the temporary file
        wp_delete_file($temp_file);

        if (is_wp_error($topics)) {
            $this->send_error_response($topics);
        }

        // Save topics to the database - use WordPress APIs when possible
        // instead of direct database queries
        $result = $csv_processor->save_topics($topics);
        if (is_wp_error($result)) {
            $this->send_error_response($result);
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: %d: Number of topics imported */
                esc_html__('%d topics imported successfully.', 'wp-content-generator-security-enhanced'),
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
        $topic = wp_cache_get($cache_key, 'wp_content_generator');

        if (!$topic) {
            $table_name = $wpdb->prefix . 'content_generator_topics';

            // Use get_post() where possible in WordPress, but for custom tables
            // we need to use $wpdb with proper preparation
            $topic = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE id = %d",
                    $topic_id
                )
            );

            if ($topic) {
                wp_cache_set($cache_key, $topic, 'wp_content_generator', 3600); // Cache for 1 hour
            }
        }

        return $topic;
    }

    /**
     * Generate content for a topic
     */
    public function generate_content()
    {
        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get topic ID
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'wp-content-generator-security-enhanced'));
        }

        // Get the topic
        $topic = $this->get_topic_by_id($topic_id);
        if (!$topic) {
            $this->send_error_response(esc_html__('Topic not found.', 'wp-content-generator-security-enhanced'));
        }

        // Check if the OpenAI API key is set
        $openai_key = get_option('wp_content_generator_openai_key');
        if (empty($openai_key)) {
            $this->send_error_response(esc_html__('OpenAI API key is not set. Please configure it in the settings.', 'wp-content-generator-security-enhanced'));
        }

        // Generate content using OpenAI
        try {
            $openai = new WP_Content_Generator_OpenAI();
            $result = $openai->generate_content($topic->topic);

            if (is_wp_error($result)) {
                $this->send_error_response($result);
            }
        } catch (Exception $e) {
            $this->send_error_response(esc_html__('An unexpected error occurred during content generation: ', 'wp-content-generator-security-enhanced') . $e->getMessage());
        }

        // Update the topic in the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'content_generator_topics';

        // Clear cache first
        wp_cache_delete('topic_' . $topic_id, 'wp_content_generator');
        wp_cache_delete('pending_topics', 'wp_content_generator');

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
            $this->send_error_response(esc_html__('Failed to update content in the database.', 'wp-content-generator-security-enhanced'));
        }

        wp_send_json_success(array(
            'message' => esc_html__('Content generated successfully.', 'wp-content-generator-security-enhanced'),
            'content' => $result['content'],
            'tokens' => $result['total_tokens']
        ));
    }

    /**
     * Save edited content
     */
    public function save_content()
    {
        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get parameters
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        $content = isset($_POST['content']) ? wp_kses_post(wp_unslash($_POST['content'])) : '';

        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'wp-content-generator-security-enhanced'));
        }

        if (empty($content)) {
            $this->send_error_response(esc_html__('Content cannot be empty.', 'wp-content-generator-security-enhanced'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'content_generator_topics';

        // Clear all related caches
        wp_cache_delete('topic_' . $topic_id, 'wp_content_generator');
        wp_cache_delete('pending_topics', 'wp_content_generator');

        $update_result = $wpdb->update(
            $table_name,
            array(
                'content' => $content,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $topic_id),
            array('%s', '%s'),
            array('%d')
        );

        if ($update_result === false) {
            $this->send_error_response(esc_html__('Failed to save content.', 'wp-content-generator-security-enhanced'));
        }

        wp_send_json_success(array(
            'message' => esc_html__('Content saved successfully.', 'wp-content-generator-security-enhanced')
        ));
    }

    /**
     * Publish content as a post or page
     */
    public function publish_content()
    {
        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get parameters
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        $publish_type = isset($_POST['publish_type']) ? sanitize_text_field(wp_unslash($_POST['publish_type'])) : 'post';

        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'wp-content-generator-security-enhanced'));
        }

        if (!in_array($publish_type, array('post', 'page'))) {
            $this->send_error_response(esc_html__('Invalid publish type.', 'wp-content-generator-security-enhanced'));
        }

        // Get the topic
        $topic = $this->get_topic_by_id($topic_id);
        if (!$topic) {
            $this->send_error_response(esc_html__('Topic not found.', 'wp-content-generator-security-enhanced'));
        }

        if (empty($topic->content)) {
            $this->send_error_response(esc_html__('Cannot publish empty content.', 'wp-content-generator-security-enhanced'));
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
        $table_name = $wpdb->prefix . 'content_generator_topics';

        // Clear all related caches
        wp_cache_delete('topic_' . $topic_id, 'wp_content_generator');
        wp_cache_delete('pending_topics', 'wp_content_generator');

        $update_result = $wpdb->update(
            $table_name,
            array(
                'status' => 'published',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $topic_id),
            array('%s', '%s'),
            array('%d')
        );

        if ($update_result === false) {
            $this->send_error_response(esc_html__('Published content but failed to update topic status.', 'wp-content-generator-security-enhanced'));
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: 1: Content type (post/page), 2: Edit URL, 3: Content type again */
                esc_html__('Content published as a %1$s (draft). <a href="%2$s" target="_blank">Edit %3$s</a>', 'wp-content-generator-security-enhanced'),
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
        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        // Get topic ID
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        if ($topic_id <= 0) {
            $this->send_error_response(esc_html__('Invalid topic ID.', 'wp-content-generator-security-enhanced'));
        }

        // Get the topic
        $topic = $this->get_topic_by_id($topic_id);
        if (!$topic) {
            $this->send_error_response(esc_html__('Topic not found.', 'wp-content-generator-security-enhanced'));
        }

        if (empty($topic->content)) {
            $this->send_error_response(esc_html__('No content found for this topic. Please generate content first.', 'wp-content-generator-security-enhanced'));
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
        // Verify AJAX request has proper nonce and permissions
        $security_check = $this->verify_ajax_request();
        if (is_wp_error($security_check)) {
            $this->send_error_response($security_check);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'content_generator_topics';

        // Get pending and generated topics with caching
        $cache_key = 'pending_topics';
        $topics = wp_cache_get($cache_key, 'wp_content_generator');

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
                wp_cache_set($cache_key, $topics, 'wp_content_generator', 60); // Cache for 1 minute
            }
        }

        if ($wpdb->last_error) {
            $this->send_error_response($wpdb->last_error);
        }

        wp_send_json_success(array('topics' => $topics));
    }
}
