<?php

/**
 * Class for handling OpenAI API interactions.
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    foss engine
 * @subpackage foss_engine/includes
 */

/**
 * Class for handling OpenAI API interactions.
 *
 * Handles all API communication with OpenAI for content generation.
 *
 * @since      1.0.2
 * @package    foss engine
 * @subpackage foss_engine/includes
 * @author     Designomate help@fossengine.com
 */
class fossenginedein_openai
{

    /**
     * The OpenAI API key.
     *
     * @since    1.0.2
     * @access   private
     * @var      string    $api_key    The OpenAI API key.
     */
    private $api_key;

    /**
     * The Deepseek API key.
     *
     * @since    1.0.2
     * @access   private
     * @var      string    $deepseek_key    The Deepseek API key.
     */
    private $deepseek_key;

    /**
     * The selected AI provider.
     *
     * @since    1.0.2
     * @access   private
     * @var      string    $provider    The selected AI provider (openai or deepseek).
     */
    private $provider;

    /**
     * The OpenAI API endpoint.
     *
     * @since    1.0.2
     * @access   private
     * @var      string    $openai_endpoint    The OpenAI API endpoint.
     */
    private $openai_endpoint = 'https://api.openai.com/v1/chat/completions';

    /**
     * The Deepseek API endpoint.
     *
     * @since    1.0.2
     * @access   private
     * @var      string    $deepseek_endpoint    The Deepseek API endpoint.
     */
    private $deepseek_endpoint = 'https://api.deepseek.com/v1/chat/completions';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.2
     * @param    string    $api_key    The OpenAI API key.
     */
    public function __construct($api_key = null)
    {
        // Get selected provider, default to OpenAI
        $this->provider = get_option('fossenginedein_provider', 'openai');

        // Set OpenAI API key
        $this->api_key = $api_key ?: get_option('fossenginedein_openai_key');

        // Set Deepseek API key
        $this->deepseek_key = get_option('fossenginedein_deepseek_key');
    }

    /**
     * Generate content using selected AI API.
     *
     * @since    1.0.2
     * @param    string    $topic    The topic to generate content for.
     * @return   array|WP_Error    Generated content or error.
     */
    public function fossenginedein_create_ai_content($topic)
    {
        // Check which provider to use
        if ($this->provider === 'deepseek') {
            return $this->fossenginedein_generate_content_deepseek($topic);
        } else {
            return $this->fossenginedein_generate_content_openai($topic);
        }
    }

    /**
     * Generate content using OpenAI API.
     *
     * @since    1.0.2
     * @param    string    $topic    The topic to generate content for.
     * @return   array|WP_Error    Generated content or error.
     */
    private function fossenginedein_generate_content_openai($topic)
    {
        if (empty($this->api_key)) {
            return new WP_Error('missing_api_key', __('OpenAI API key is not set.', 'foss-engine'));
        }

        // Get and sanitize prompt template
        $default_prompt = 'Write a comprehensive blog post about [TOPIC]. Include an introduction, several key points, and a conclusion. The content should be informative and engaging.';
        $prompt_template = get_option('fossenginedein_prompt_template', $default_prompt);

        // Validate prompt template
        if (empty($prompt_template) || !is_string($prompt_template)) {
            $prompt_template = $default_prompt;
        }

        // Sanitize topic before using it in the prompt
        $sanitized_topic = sanitize_text_field($topic);

        // Replace placeholder with sanitized topic
        $prompt = str_replace('[TOPIC]', $sanitized_topic, $prompt_template);

        // Get preferred model, default to GPT-3.5-Turbo if not set
        $preferred_model = get_option('fossenginedein_model', 'gpt-3.5-turbo');

        // Ensure we have a valid model, fallback to GPT-3.5 if there's an issue
        if (empty($preferred_model)) {
            $preferred_model = 'gpt-3.5-turbo';
            // error_log('Foss Engine - No model specified, falling back to gpt-3.5-turbo');
        }

        // Log selected model
        // error_log('Foss Engine - Using model: ' . $preferred_model . ' for topic: ' . $topic);

        // Truncate prompt if it's too long (OpenAI has token limitations)
        $max_prompt_length = 4000; // Safe limit
        if (strlen($prompt) > $max_prompt_length) {
            $prompt = substr($prompt, 0, $max_prompt_length);
            // error_log('Foss Engine - Prompt truncated due to length');
        }

        $body = array(
            'model' => $preferred_model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => sanitize_text_field('You are a professional content writer who creates high-quality, SEO-friendly blog posts. Format your content with proper HTML structure using h2, h3, and h4 tags for headings and subheadings. Include relevant semantic HTML like p, ul, ol, strong, and em tags. DO NOT add a title at the beginning - the title will be added separately. Start directly with an engaging introduction paragraph. Organize content with a clear hierarchy: introduction, multiple sections with appropriate headings, and a conclusion. Ensure proper keyword placement in headings and first paragraphs. Use descriptive anchor text for any links. Make content scannable with short paragraphs and bullet points where appropriate.')
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'temperature' => 0.7,
            'max_tokens' => 3000,
        );

        // Sanitize API request parameters
        $sanitized_body = array(
            'model' => sanitize_text_field($body['model']),
            'messages' => $body['messages'], // Messages are already sanitized above
            'temperature' => is_numeric($body['temperature']) ? floatval($body['temperature']) : 0.7,
            'max_tokens' => is_numeric($body['max_tokens']) ? intval($body['max_tokens']) : 3000,
        );

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($sanitized_body), // Use wp_json_encode for better security
            'method' => 'POST',
            'timeout' => 60,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'data_format' => 'body',
            'sslverify' => true, // Enforce SSL verification
        );

        // Log the request for debugging in a secure way (avoid logging sensitive data)
        // error_log('Foss Engine - OpenAI API Request: ' . wp_json_encode([
        //     'endpoint' => $this->openai_endpoint,
        //     'model' => $preferred_model,
        //     'prompt_length' => strlen($prompt)
        //     // Omitting request_body which may contain sensitive data
        // ]));

        // Debug the exact request with sanitized values
        // error_log('Foss Engine - API Request Info:');
        // error_log('Method: ' . $args['method']);
        // error_log('Timeout: ' . $args['timeout']);
        // Do not log the full body or headers, as they may contain sensitive info

        // Ensure the API key is valid
        if (empty($this->api_key) || strlen($this->api_key) < 20) {
            // error_log('Foss Engine - API Key appears to be invalid or too short: ' . substr($this->api_key, 0, 5) . '...');
            return new WP_Error('invalid_api_key', __('The OpenAI API key appears to be invalid. It should be a long token starting with "sk-".', 'foss-engine'));
        }

        // Make the API call
        $response = wp_remote_post($this->openai_endpoint, $args);

        // Detailed error logging for WordPress errors
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_code = $response->get_error_code();
            // error_log('Foss Engine - OpenAI API WordPress Error: ' . $error_code . ' - ' . $error_message);

            // Add more context about the error
            if ($error_code === 'http_request_failed') {
                // error_log('Foss Engine - This is likely a connection error. Check server connectivity to api.openai.com.');
            }

            return new WP_Error($error_code, __('API Connection Error: ', 'foss-engine') . $error_message);
        }

        // Log response status for debugging, but filter out sensitive data
        // error_log('Foss Engine - OpenAI API Response received with status code: ' . wp_remote_retrieve_response_code($response));

        // Check HTTP response code
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_message = 'HTTP Error: ' . $response_code . ' - ' . wp_remote_retrieve_response_message($response);
            $response_body = wp_remote_retrieve_body($response);
            // error_log('Foss Engine - OpenAI API HTTP Error: ' . $error_message);
            // error_log('Foss Engine - Response Body: ' . $response_body);

            // Try to extract more specific error message from response body
            $response_data = json_decode($response_body, true);
            if (isset($response_data['error']['message'])) {
                $error_message = $response_data['error']['message'];
            }

            return new WP_Error('http_error', $error_message);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Log minimal response info for debugging (usage stats only, no content)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $usage_info = isset($data['usage']) ? $data['usage'] : array('info' => 'usage data not available');
            // error_log('Foss Engine - OpenAI API Response usage info: ' . wp_json_encode($usage_info));
        }

        if (isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Unknown error occurred while communicating with OpenAI API.', 'foss-engine');
            // error_log('Foss Engine - OpenAI API Error: ' . $error_message);
            return new WP_Error('openai_api_error', $error_message);
        }

        if (!isset($data['choices'][0]['message']['content'])) {
            // error_log('Foss Engine - OpenAI API Invalid Response structure received');
            return new WP_Error('invalid_response', __('Invalid response from OpenAI API.', 'foss-engine'));
        }

        return array(
            'content' => $data['choices'][0]['message']['content'],
            'completion_tokens' => isset($data['usage']['completion_tokens']) ? $data['usage']['completion_tokens'] : 0,
            'prompt_tokens' => isset($data['usage']['prompt_tokens']) ? $data['usage']['prompt_tokens'] : 0,
            'total_tokens' => isset($data['usage']['total_tokens']) ? $data['usage']['total_tokens'] : 0,
        );
    }

    /**
     * Generate content using Deepseek API.
     *
     * @since    1.0.2
     * @param    string    $topic    The topic to generate content for.
     * @return   array|WP_Error    Generated content or error.
     */
    private function fossenginedein_generate_content_deepseek($topic)
    {
        if (empty($this->deepseek_key)) {
            return new WP_Error('missing_api_key', __('Deepseek API key is not set.', 'foss-engine'));
        }

        // Get and sanitize prompt template
        $default_prompt = 'Write a comprehensive blog post about [TOPIC]. Include an introduction, several key points, and a conclusion. The content should be informative and engaging.';
        $prompt_template = get_option('fossenginedein_prompt_template', $default_prompt);

        // Validate prompt template
        if (empty($prompt_template) || !is_string($prompt_template)) {
            $prompt_template = $default_prompt;
        }

        // Sanitize topic before using it in the prompt
        $sanitized_topic = sanitize_text_field($topic);

        // Replace placeholder with sanitized topic
        $prompt = str_replace('[TOPIC]', $sanitized_topic, $prompt_template);

        // Get preferred model, default to deepseek-chat if not set
        $preferred_model = get_option('fossenginedein_deepseek_model', 'deepseek-chat');

        // Ensure we have a valid model, fallback to deepseek-chat if there's an issue
        if (empty($preferred_model)) {
            $preferred_model = 'deepseek-chat';
        }

        // Truncate prompt if it's too long
        $max_prompt_length = 4000; // Safe limit
        if (strlen($prompt) > $max_prompt_length) {
            $prompt = substr($prompt, 0, $max_prompt_length);
        }

        $body = array(
            'model' => $preferred_model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => sanitize_text_field('You are a professional content writer who creates high-quality, SEO-friendly blog posts. Format your content with proper HTML structure using h2, h3, and h4 tags for headings and subheadings. Include relevant semantic HTML like p, ul, ol, strong, and em tags. DO NOT add a title at the beginning - the title will be added separately. Start directly with an engaging introduction paragraph. Organize content with a clear hierarchy: introduction, multiple sections with appropriate headings, and a conclusion. Ensure proper keyword placement in headings and first paragraphs. Use descriptive anchor text for any links. Make content scannable with short paragraphs and bullet points where appropriate.')
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'temperature' => 0.7,
            'max_tokens' => 3000,
        );

        // Sanitize API request parameters
        $sanitized_body = array(
            'model' => sanitize_text_field($body['model']),
            'messages' => $body['messages'], // Messages are already sanitized above
            'temperature' => is_numeric($body['temperature']) ? floatval($body['temperature']) : 0.7,
            'max_tokens' => is_numeric($body['max_tokens']) ? intval($body['max_tokens']) : 3000,
        );

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->deepseek_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($sanitized_body),
            'method' => 'POST',
            'timeout' => 60,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'data_format' => 'body',
            'sslverify' => true,
        );

        // Ensure the API key is valid
        if (empty($this->deepseek_key) || strlen($this->deepseek_key) < 20) {
            return new WP_Error('invalid_api_key', __('The Deepseek API key appears to be invalid. It should be a long token.', 'foss-engine'));
        }

        // Make the API call
        $response = wp_remote_post($this->deepseek_endpoint, $args);

        // Handle WordPress errors
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_code = $response->get_error_code();
            return new WP_Error($error_code, __('API Connection Error: ', 'foss-engine') . $error_message);
        }

        // Check HTTP response code
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_message = 'HTTP Error: ' . $response_code . ' - ' . wp_remote_retrieve_response_message($response);
            $response_body = wp_remote_retrieve_body($response);

            // Try to extract more specific error message from response body
            $response_data = json_decode($response_body, true);
            if (isset($response_data['error']['message'])) {
                $error_message = $response_data['error']['message'];
            }

            return new WP_Error('http_error', $error_message);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Unknown error occurred while communicating with Deepseek API.', 'foss-engine');
            return new WP_Error('deepseek_api_error', $error_message);
        }

        // Deepseek response structure is similar to OpenAI, but let's handle potential differences
        if (!isset($data['choices'][0]['message']['content'])) {
            return new WP_Error('invalid_response', __('Invalid response from Deepseek API.', 'foss-engine'));
        }

        // Return in the same format as OpenAI for compatibility
        return array(
            'content' => $data['choices'][0]['message']['content'],
            'completion_tokens' => isset($data['usage']['completion_tokens']) ? $data['usage']['completion_tokens'] : 0,
            'prompt_tokens' => isset($data['usage']['prompt_tokens']) ? $data['usage']['prompt_tokens'] : 0,
            'total_tokens' => isset($data['usage']['total_tokens']) ? $data['usage']['total_tokens'] : 0,
        );
    }

    /**
     * Test the AI API connection.
     *
     * @since    1.0.2
     * @return   boolean|WP_Error    True if successful, WP_Error otherwise.
     */
    public function fossenginedein_verify_api_connection()
    {
        // Check which provider to use
        if ($this->provider === 'deepseek') {
            return $this->fossenginedein_test_connection_deepseek();
        } else {
            return $this->fossenginedein_test_connection_openai();
        }
    }

    /**
     * Test the OpenAI API connection.
     *
     * @since    1.0.2
     * @return   boolean|WP_Error    True if successful, WP_Error otherwise.
     */
    private function fossenginedein_test_connection_openai()
    {
        if (empty($this->api_key)) {
            return new WP_Error('missing_api_key', __('OpenAI API key is not set.', 'foss-engine'));
        }

        // Get preferred model, default to GPT-3.5-Turbo if not set
        $preferred_model = get_option('fossenginedein_model', 'gpt-3.5-turbo');

        $body = array(
            'model' => $preferred_model,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => 'Hello, this is a test message. Please respond with "Connection successful".'
                )
            ),
            'temperature' => 0.7,
            'max_tokens' => 20,
        );

        // Sanitize test connection parameters
        $sanitized_test_body = array(
            'model' => sanitize_text_field($body['model']),
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => sanitize_text_field($body['messages'][0]['content'])
                )
            ),
            'temperature' => is_numeric($body['temperature']) ? floatval($body['temperature']) : 0.7,
            'max_tokens' => is_numeric($body['max_tokens']) ? intval($body['max_tokens']) : 20,
        );

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($sanitized_test_body),
            'method' => 'POST',
            'timeout' => 15,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'data_format' => 'body',
            'sslverify' => true, // Enforce SSL verification
        );

        $response = wp_remote_post($this->openai_endpoint, $args);

        if (is_wp_error($response)) {
            // Log the WordPress error
            // error_log('Foss Engine - OpenAI API Connection Test Error: ' . $response->get_error_message());
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_message = 'HTTP Error: ' . $response_code . ' - ' . wp_remote_retrieve_response_message($response);
            // error_log('Foss Engine - OpenAI API Connection Test HTTP Error: ' . $error_message);
            return new WP_Error('http_error', $error_message);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Log only a status message for the test connection response, no data
        if (defined('WP_DEBUG') && WP_DEBUG) {
            // error_log('Foss Engine - OpenAI API Connection Test completed with status: success');
        }

        if (isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Unknown error occurred while communicating with OpenAI API.', 'foss-engine');
            // error_log('Foss Engine - OpenAI API Connection Test Error: ' . $error_message);
            return new WP_Error('openai_api_error', $error_message);
        }

        return true;
    }

    /**
     * Test the Deepseek API connection.
     *
     * @since    1.0.2
     * @return   boolean|WP_Error    True if successful, WP_Error otherwise.
     */
    private function fossenginedein_test_connection_deepseek()
    {
        if (empty($this->deepseek_key)) {
            return new WP_Error('missing_api_key', __('Deepseek API key is not set.', 'foss-engine'));
        }

        // Get preferred model, default to deepseek-chat if not set
        $preferred_model = get_option('fossenginedein_deepseek_model', 'deepseek-chat');

        $body = array(
            'model' => $preferred_model,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => 'Hello, this is a test message. Please respond with "Connection successful".'
                )
            ),
            'temperature' => 0.7,
            'max_tokens' => 20,
        );

        // Sanitize test connection parameters
        $sanitized_test_body = array(
            'model' => sanitize_text_field($body['model']),
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => sanitize_text_field($body['messages'][0]['content'])
                )
            ),
            'temperature' => is_numeric($body['temperature']) ? floatval($body['temperature']) : 0.7,
            'max_tokens' => is_numeric($body['max_tokens']) ? intval($body['max_tokens']) : 20,
        );

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->deepseek_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($sanitized_test_body),
            'method' => 'POST',
            'timeout' => 15,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'data_format' => 'body',
            'sslverify' => true,
        );

        $response = wp_remote_post($this->deepseek_endpoint, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_message = 'HTTP Error: ' . $response_code . ' - ' . wp_remote_retrieve_response_message($response);
            return new WP_Error('http_error', $error_message);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Unknown error occurred while communicating with Deepseek API.', 'foss-engine');
            return new WP_Error('deepseek_api_error', $error_message);
        }

        return true;
    }
}
