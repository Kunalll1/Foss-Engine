<?php

/**
 * CSV processing Class
 *
 * @link       https://fossengine.com/
 * @since      1.0.1
 *
 * @package    Foss Engine
 * @subpackage Foss_Engine/includes
 */

/**
 * CSV processing Class
 *
 * This class handles CSV file processing.
 *
 * @since      1.0.1
 * @package    Foss Engine
 * @subpackage Foss_Engine/includes
 * @author     Kunal Kumar help@fossengine.com
 */
class Foss_Engine_CSV
{

    /**
     * Process uploaded CSV file and extract topics.
     *
     * @since    1.0.1
     * @param    string    $file_path    Path to the uploaded CSV file.
     * @return   array|WP_Error          Array of topics or WP_Error on failure.
     */
    public function process_csv($file_path)
    {
        // File validation - existence and permissions
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return new WP_Error('file_not_found', __('The uploaded CSV file could not be found or is not readable.', 'foss-engine'));
        }

        // Additional file validation - check file type and extension
        $file_info = wp_check_filetype(basename($file_path));
        if ($file_info['ext'] !== 'csv') {
            return new WP_Error('invalid_file_type', __('The uploaded file is not a valid CSV file.', 'foss-engine'));
        }

        // Size validation
        $filesize = filesize($file_path);
        $max_size = apply_filters('foss_engine_max_csv_size', 1048576); // 1MB default

        if ($filesize <= 0) {
            return new WP_Error('empty_file', __('The uploaded CSV file is empty.', 'foss-engine'));
        }

        if ($filesize > $max_size) {
            return new WP_Error('file_too_large', sprintf(
                /* translators: %s: maximum allowed file size (e.g. "2 MB") */
                __('The uploaded CSV file exceeds the maximum allowed size of %s.', 'foss-engine'),
                size_format($max_size)
            ));
        }

        $topics = array();
        $line_count = 0;
        $max_topics = apply_filters('foss_engine_max_csv_topics', 100);

        try {
            // Open the file for reading
            $handle = fopen($file_path, 'r');
            if (!$handle) {
                return new WP_Error('file_open_error', __('Could not open the CSV file.', 'foss-engine'));
            }

            // Try to determine if there's a header row
            $first_row = fgetcsv($handle, 1000);
            $line_count++;

            if ($first_row === false) {
                fclose($handle);
                return new WP_Error('csv_read_error', __('Could not read the CSV file.', 'foss-engine'));
            }

            // If there's only one column and it contains "topic" (case insensitive), assume it's a header
            $has_header = (count($first_row) === 1 && strtolower(trim($first_row[0])) === 'topic');

            // If it's not a header, add it to topics
            if (!$has_header && count($topics) < $max_topics) {
                // If only one column, take that as the topic
                if (count($first_row) === 1 && !empty(trim($first_row[0]))) {
                    $topics[] = $this->sanitize_topic(trim($first_row[0]));
                }
                // If multiple columns, take the first one as the topic
                else if (count($first_row) > 1 && !empty(trim($first_row[0]))) {
                    $topics[] = $this->sanitize_topic(trim($first_row[0]));
                }
            }

            // Process the rest of the rows with proper error handling
            while (($row = fgetcsv($handle, 1000)) !== false && count($topics) < $max_topics) {
                $line_count++;

                if (!empty($row)) {
                    // If only one column, take that as the topic
                    if (count($row) === 1 && !empty(trim($row[0]))) {
                        $topics[] = $this->sanitize_topic(trim($row[0]));
                    }
                    // If multiple columns, take the first one as the topic
                    else if (count($row) > 1 && !empty(trim($row[0]))) {
                        $topics[] = $this->sanitize_topic(trim($row[0]));
                    }
                }

                // Security measure: avoid processing extremely large files
                if ($line_count > 1000) {
                    break;
                }
            }

            fclose($handle);
        } catch (Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            return new WP_Error('csv_processing_error', $e->getMessage());
        }

        // Final validation
        if (empty($topics)) {
            return new WP_Error('no_topics', __('No valid topics were found in the CSV file.', 'foss-engine'));
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
     * @since    1.0.1
     * @param    string    $topic    The topic to sanitize
     * @return   string              Sanitized topic
     */
    private function sanitize_topic($topic)
    {
        // Apply WordPress sanitization
        $topic = sanitize_text_field($topic);

        // Additional sanitization and validation
        $topic = wp_strip_all_tags($topic);

        // Enforce length limits
        $max_length = apply_filters('foss_engine_max_topic_length', 255);
        if (strlen($topic) > $max_length) {
            $topic = substr($topic, 0, $max_length - 3) . '...';
        }

        return $topic;
    }

    /**
     * Save topics to the database.
     *
     * @since    1.0.1
     * @param    array    $topics    Array of topics to save.
     * @return   boolean|WP_Error    True on success, WP_Error on failure.
     */
    public function save_topics($topics)
    {
        global $wpdb;

        // Security: validate input
        if (!is_array($topics)) {
            return new WP_Error('invalid_input', __('Invalid topics data provided.', 'foss-engine'));
        }

        // Get the correct table name with prefix
        $table_name = $wpdb->prefix . 'foss_engine_topics';

        // Enforce limits for security
        $max_topics = apply_filters('foss_engine_max_topics_to_save', 100);
        if (count($topics) > $max_topics) {
            $topics = array_slice($topics, 0, $max_topics);
            // error_log('Foss Engine - Topics truncated to ' . $max_topics . ' during save operation');
        }

        $inserted = 0;
        $errors = array();

        // Start a transaction for data integrity
        $wpdb->query('START TRANSACTION');

        try {
            foreach ($topics as $topic) {
                // Validate and sanitize each topic again as an extra security measure
                if (!is_string($topic) || empty(trim($topic))) {
                    continue;
                }

                $sanitized_topic = $this->sanitize_topic($topic);

                // Check if the topic already exists using prepared statement
                $existing = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM $table_name WHERE topic = %s",
                        $sanitized_topic
                    )
                );

                if (!$existing) {
                    // Use prepared statement and proper data types for secure insertion
                    $result = $wpdb->insert(
                        $table_name,
                        array(
                            'topic' => $sanitized_topic,
                            'status' => 'pending',
                            'created_at' => current_time('mysql'),
                            'updated_at' => current_time('mysql')
                        ),
                        array('%s', '%s', '%s', '%s')
                    );

                    if ($result) {
                        $inserted++;
                    } else {
                        // Log the error with safe error message (no sensitive details)
                        $errors[] = __('Failed to insert topic into database.', 'foss-engine');
                        // error_log('Foss Engine - DB insert error: ' . $wpdb->last_error);
                    }
                }

                // Security: limit number of inserts in one operation
                if ($inserted >= $max_topics) {
                    break;
                }
            }

            // If there were errors, rollback the transaction
            if (!empty($errors)) {
                $wpdb->query('ROLLBACK');
                return new WP_Error('db_insert_error', implode('<br>', $errors));
            }

            // Otherwise commit the transaction
            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            // Rollback on any exceptions
            $wpdb->query('ROLLBACK');
            // error_log('Foss Engine - Exception during topic save: ' . $e->getMessage());
            return new WP_Error('db_error', __('A database error occurred while saving topics.', 'foss-engine'));
        }

        return $inserted;
    }
}
