<?php

/**
 * CSV processing Class
 *
 * @link       https://fossengine.com/
 * @since      1.0.2
 *
 * @package    foss engine
 * @subpackage foss_engine/includes
 */

/**
 * CSV processing Class
 *
 * This class handles CSV file processing.
 *
 * @since      1.0.2
 * @package    foss engine
 * @subpackage foss_engine/includes
 * @author     Designomate help@fossengine.com
 */
class fossenginedein_csv
{

    /**
     * Process uploaded CSV file and extract topics.
     *
     * @since    1.0.2
     * @param    string    $file_path    Path to the uploaded CSV file.
     * @return   array|WP_Error          Array of topics or WP_Error on failure.
     */
    public function fossenginedein_process_csv($file_path)
    {
        // File validation - existence and permissions
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return new WP_Error('file_not_found', __('The uploaded CSV file could not be found or is not readable.', 'fossenginedein'));
        }

        // Additional file validation - check file type and extension
        $file_info = wp_check_filetype(basename($file_path));
        if ($file_info['ext'] !== 'csv') {
            return new WP_Error('invalid_file_type', __('The uploaded file is not a valid CSV file.', 'fossenginedein'));
        }

        // Size validation
        $filesize = filesize($file_path);
        $max_size = apply_filters('fossenginedein_max_csv_size', 1048576); // 1MB default

        if ($filesize <= 0) {
            return new WP_Error('empty_file', __('The uploaded CSV file is empty.', 'fossenginedein'));
        }

        if ($filesize > $max_size) {
            return new WP_Error('file_too_large', sprintf(
                /* translators: %s: maximum allowed file size (e.g. "2 MB") */
                __('The uploaded CSV file exceeds the maximum allowed size of %s.', 'fossenginedein'),
                size_format($max_size)
            ));
        }

        $topics = array();
        $line_count = 0;
        $max_topics = apply_filters('fossenginedein_max_csv_topics', 100);

        // Use WP_Filesystem
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }

        try {
            $file_contents = $wp_filesystem->get_contents($file_path);
            if ($file_contents === false) {
                return new WP_Error('file_open_error', __('Could not open the CSV file.', 'fossenginedein'));
            }

            // Convert file contents into lines
            $lines = preg_split('/\r\n|\r|\n/', $file_contents);

            if (empty($lines) || !is_array($lines)) {
                return new WP_Error('csv_read_error', __('Could not read the CSV file.', 'fossenginedein'));
            }

            // Parse CSV lines
            $first_row = str_getcsv($lines[0]);
            $line_count++;

            // If there's only one column and it contains "topic" (case insensitive), assume it's a header
            $has_header = (count($first_row) === 1 && strtolower(trim($first_row[0])) === 'topic');

            // If it's not a header, add it to topics
            if (!$has_header && count($topics) < $max_topics) {
                if (count($first_row) === 1 && !empty(trim($first_row[0]))) {
                    $topics[] = $this->fossenginedein_sanitize_topic(trim($first_row[0]));
                } elseif (count($first_row) > 1 && !empty(trim($first_row[0]))) {
                    $topics[] = $this->fossenginedein_sanitize_topic(trim($first_row[0]));
                }
            }

            // Process the rest of the rows
            for ($i = 1; $i < count($lines) && count($topics) < $max_topics; $i++) {
                $row = str_getcsv($lines[$i]);
                $line_count++;

                if (!empty($row)) {
                    if (count($row) === 1 && !empty(trim($row[0]))) {
                        $topics[] = $this->fossenginedein_sanitize_topic(trim($row[0]));
                    } elseif (count($row) > 1 && !empty(trim($row[0]))) {
                        $topics[] = $this->fossenginedein_sanitize_topic(trim($row[0]));
                    }
                }

                if ($line_count > 1000) {
                    break;
                }
            }
        } catch (Exception $e) {
            return new WP_Error('csv_processing_error', $e->getMessage());
        }

        // Final validation
        if (empty($topics)) {
            return new WP_Error('no_topics', __('No valid topics were found in the CSV file.', 'fossenginedein'));
        }

        // Security measure: limit number of topics
        if (count($topics) > $max_topics) {
            $topics = array_slice($topics, 0, $max_topics);
        }

        return $topics;
    }

    /**
     * Sanitize a topic string for security
     *
     * @since    1.0.2
     * @param    string    $topic    The topic to sanitize
     * @return   string              Sanitized topic
     */
    private function fossenginedein_sanitize_topic($topic)
    {
        // Apply WordPress sanitization
        $topic = sanitize_text_field($topic);

        // Additional sanitization and validation
        $topic = wp_strip_all_tags($topic);

        // Enforce length limits
        $max_length = apply_filters('fossenginedein_max_topic_length', 255);
        if (strlen($topic) > $max_length) {
            $topic = substr($topic, 0, $max_length - 3) . '...';
        }

        return $topic;
    }

    /**
     * Save topics to the database
     *
     * @param array $topics Array of topic strings to save
     * @return int|WP_Error Number of topics saved or error
     */
    public function fossenginedein_save_topics($topics)
    {
        global $wpdb;

        // Security: validate input
        if (!is_array($topics)) {
            return new WP_Error('invalid_input', __('Invalid topics data provided.', 'fossenginedein'));
        }

        $table_name = $wpdb->prefix . 'fossenginedein_topics';

        // Check if table exists first
        if (!function_exists('fossenginedein_table_exists')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fossenginedein.php';
        }

        if (!fossenginedein_table_exists('fossenginedein_topics')) {
            return new WP_Error('table_not_found', __('Database table not found. Please deactivate and reactivate the plugin.', 'fossenginedein'));
        }

        $count = 0;
        $current_date = current_time('mysql');

        // Start transaction for multiple inserts
        $wpdb->query('START TRANSACTION');

        try {
            foreach ($topics as $topic) {
                // Check for duplicate topic (case-insensitive)
                $existing = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM $table_name WHERE LOWER(topic) = LOWER(%s)",
                        $topic
                    )
                );

                if ($existing > 0) {
                    // Skip duplicate
                    continue;
                }

                $result = $wpdb->insert(
                    $table_name,
                    array(
                        'topic'      => $topic,
                        'status'     => 'pending',
                        'created_at' => $current_date,
                        'updated_at' => $current_date
                    ),
                    array('%s', '%s', '%s', '%s')
                );

                if ($result) {
                    $count++;
                } else {
                    // Log the error
                    error_log("FossEngineDein: Failed to insert topic: " . $wpdb->last_error);
                }
            }

            // If all went well, commit the transaction
            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            // If there was an error, rollback the transaction
            $wpdb->query('ROLLBACK');
            return new WP_Error('database_error', $e->getMessage());
        }

        // Clear any cached topics
        wp_cache_delete('pending_topics', 'fossenginedein');

        return $count;
    }
}
