(function( $ ) {
    'use strict';

    $(document).ready(function() {
        // CSV Upload Form Handling
        $('#csv-upload-form').on('submit', function(e) {
            e.preventDefault();
            
            var fileInput = $('#csv_file')[0];
            if (fileInput.files.length === 0) {
                alert(wp_content_generator_ajax.i18n.error + ': ' + 'Please select a CSV file to upload.');
                return;
            }

            var formData = new FormData();
            formData.append('action', 'upload_csv');
            formData.append('nonce', wp_content_generator_ajax.nonce);
            formData.append('csv_file', fileInput.files[0]);

            $('#upload-progress').show();
            $('#upload-results').hide();

            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#upload-progress').hide();
                    
                    if (response.success) {
                        $('#upload-results').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>').show();
                        // Reload the page to show new topics
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#upload-results').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>').show();
                    }
                },
                error: function() {
                    $('#upload-progress').hide();
                    $('#upload-results').html('<div class="notice notice-error"><p>An unexpected error occurred. Please try again.</p></div>').show();
                }
            });
        });

        // Generate content for a single topic
        $('.topics-table').on('click', '.generate-content-button', function() {
            var button = $(this);
            var topicId = button.data('id');
            var rowElement = $('#topic-row-' + topicId);
            
            // Disable button and show loading state
            button.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-top: 0;"></span> ' + wp_content_generator_ajax.i18n.generating);
            
            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'generate_content',
                    nonce: wp_content_generator_ajax.nonce,
                    topic_id: topicId
                },
                success: function(response) {
                    if (response.success) {
                        // Update the row to reflect the new status
                        rowElement.find('.column-status .status-badge')
                            .removeClass('status-pending')
                            .addClass('status-generated')
                            .text('Generated');
                        
                        // Replace the generate button with edit and regenerate buttons
                        var newButtons = '<button class="button button-primary edit-content-button" data-id="' + topicId + '">Edit</button> ' +
                                         '<button class="button regenerate-content-button" data-id="' + topicId + '">Regenerate</button>';
                        rowElement.find('.column-actions').html(newButtons);
                        
                        // Show success message
                        showNotification(wp_content_generator_ajax.i18n.success, response.data.message, 'success');
                    } else {
                        // Reset button state
                        button.prop('disabled', false).text('Generate');
                        // Show error message
                        showNotification(wp_content_generator_ajax.i18n.error, response.data.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    // Reset button state
                    button.prop('disabled', false).text('Generate');
                    
                    // Create detailed error message with all available information
                    var errorMsg = 'An unexpected error occurred. Status: ' + status;
                    errorMsg += '\nError: ' + error;
                    
                    if (xhr.responseText) {
                        errorMsg += '\nResponse: ' + xhr.responseText.substring(0, 500);
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMsg = response.data.message;
                            }
                        } catch(e) {
                            errorMsg += '\nJSON Parse Error: ' + e.message;
                        }
                    }
                    
                    // Log error details to console for debugging
                    console.error('Content generation error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    // Alert with full error details
                    alert('Error generating content:\n\n' + errorMsg);
                    
                    // Also show in the notification
                    showNotification(wp_content_generator_ajax.i18n.error, 'Content generation failed. See alert for details.', 'error');
                }
            });
        });

        // Regenerate content
        $('.topics-table').on('click', '.regenerate-content-button', function() {
            if (!confirm(wp_content_generator_ajax.i18n.confirm_regenerate)) {
                return;
            }
            
            var button = $(this);
            var topicId = button.data('id');
            
            // Disable button and show loading state
            button.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-top: 0;"></span> ' + wp_content_generator_ajax.i18n.generating);
            
            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'regenerate_content',
                    nonce: wp_content_generator_ajax.nonce,
                    topic_id: topicId
                },
                success: function(response) {
                    // Reset button state
                    button.prop('disabled', false).text('Regenerate');
                    
                    if (response.success) {
                        // Show success message
                        showNotification(wp_content_generator_ajax.i18n.success, response.data.message, 'success');
                    } else {
                        // Show error message
                        showNotification(wp_content_generator_ajax.i18n.error, response.data.message, 'error');
                    }
                },
                error: function() {
                    // Reset button state
                    button.prop('disabled', false).text('Regenerate');
                    // Show error message
                    showNotification(wp_content_generator_ajax.i18n.error, 'An unexpected error occurred. Please try again.', 'error');
                }
            });
        });

        // Edit content - open modal
        $('.topics-table').on('click', '.edit-content-button', function() {
            var topicId = $(this).data('id');
            var topicTitle = $('#topic-row-' + topicId + ' .column-topic').text().trim();
            
            $('#editing-topic-id').val(topicId);
            $('#editing-topic-title').text(topicTitle);
            
            // Clear the editor and show loading
            if (typeof tinymce !== 'undefined' && tinymce.get('topic-content-editor')) {
                tinymce.get('topic-content-editor').setContent('<p>' + wp_content_generator_ajax.i18n.loading + '</p>');
            } else {
                $('#topic-content-editor').val(wp_content_generator_ajax.i18n.loading);
            }
            
            // Fetch the content from the database
            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_topic_content',
                    nonce: wp_content_generator_ajax.nonce,
                    topic_id: topicId
                },
                success: function(response) {
                    if (response.success) {
                        // Set the content in the editor
                        if (typeof tinymce !== 'undefined' && tinymce.get('topic-content-editor')) {
                            tinymce.get('topic-content-editor').setContent(response.data.content);
                        } else {
                            $('#topic-content-editor').val(response.data.content);
                        }
                    } else {
                        // Show error in editor
                        if (typeof tinymce !== 'undefined' && tinymce.get('topic-content-editor')) {
                            tinymce.get('topic-content-editor').setContent('<p>Error: ' + response.data.message + '</p>');
                        } else {
                            $('#topic-content-editor').val('Error: ' + response.data.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Log error details to console for debugging
                    console.error('Error fetching content:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    // Create a detailed error message with all available info
                    var errorMsg = 'An unexpected error occurred while fetching content.\n\n';
                    errorMsg += 'Status: ' + status + '\n';
                    errorMsg += 'Error: ' + error + '\n';
                    
                    // Try to get response data
                    if (xhr.responseText) {
                        errorMsg += 'Response Text: ' + xhr.responseText + '\n';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMsg += 'Error Message: ' + response.data.message + '\n';
                            }
                        } catch(e) {
                            errorMsg += 'JSON Parse Error: ' + e.message + '\n';
                        }
                    }
                    
                    // Show detailed error in editor
                    if (typeof tinymce !== 'undefined' && tinymce.get('topic-content-editor')) {
                        tinymce.get('topic-content-editor').setContent('<p>Error Details:</p><pre>' + errorMsg + '</pre>');
                    } else {
                        $('#topic-content-editor').val('Error Details:\n' + errorMsg);
                    }
                    
                    // Show alert with basic error info
                    alert('Error fetching content. Check browser console for details.');
                }
            });
            
            // Show the modal
            $('#content-editor-modal').show();
        });

        // Close modal when clicking the X or Cancel button
        $('.modal .close, #cancel-edit-button').on('click', function() {
            $('.modal').hide();
        });

        // Close modal when clicking outside of it
        $(window).on('click', function(event) {
            if ($(event.target).hasClass('modal')) {
                $('.modal').hide();
            }
        });

        // Save content button
        $('#save-content-button').on('click', function() {
            var topicId = $('#editing-topic-id').val();
            var content;
            
            // Get content from the editor
            if (typeof tinymce !== 'undefined' && tinymce.get('topic-content-editor')) {
                content = tinymce.get('topic-content-editor').getContent();
            } else {
                content = $('#topic-content-editor').val();
            }
            
            if (!content) {
                alert('Content cannot be empty.');
                return;
            }
            
            // Disable button and show saving state
            var button = $(this);
            button.prop('disabled', true).text(wp_content_generator_ajax.i18n.saving);
            
            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_content',
                    nonce: wp_content_generator_ajax.nonce,
                    topic_id: topicId,
                    content: content
                },
                success: function(response) {
                    // Reset button state
                    button.prop('disabled', false).text('Save Content');
                    
                    if (response.success) {
                        // Close the modal
                        $('#content-editor-modal').hide();
                        // Show success message
                        showNotification(wp_content_generator_ajax.i18n.success, response.data.message, 'success');
                    } else {
                        // Show error message
                        showNotification(wp_content_generator_ajax.i18n.error, response.data.message, 'error');
                    }
                },
                error: function() {
                    // Reset button state
                    button.prop('disabled', false).text('Save Content');
                    // Show error message
                    showNotification(wp_content_generator_ajax.i18n.error, 'An unexpected error occurred while saving content.', 'error');
                }
            });
        });

        // Publish button - show publish options modal
        $('#publish-content-button').on('click', function() {
            $('#content-editor-modal').hide();
            $('#publish-options-modal').show();
        });

        // Cancel publish button
        $('#cancel-publish-button').on('click', function() {
            $('#publish-options-modal').hide();
            $('#content-editor-modal').show();
        });

        // Confirm publish button
        $('#confirm-publish-button').on('click', function() {
            var topicId = $('#editing-topic-id').val();
            var publishType = $('input[name="publish-type"]:checked').val();
            
            // Disable button and show publishing state
            var button = $(this);
            button.prop('disabled', true).text(wp_content_generator_ajax.i18n.publishing);
            
            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'publish_content',
                    nonce: wp_content_generator_ajax.nonce,
                    topic_id: topicId,
                    publish_type: publishType
                },
                success: function(response) {
                    // Reset button state
                    button.prop('disabled', false).text('Publish');
                    
                    // Close the modal
                    $('#publish-options-modal').hide();
                    
                    if (response.success) {
                        // Update the row to reflect the published status
                        var rowElement = $('#topic-row-' + topicId);
                        rowElement.find('.column-status .status-badge')
                            .removeClass('status-generated')
                            .addClass('status-published')
                            .text('Published');
                        
                        // Replace the buttons with a published message
                        rowElement.find('.column-actions').html('<span class="published-status">Content Published</span>');
                        
                        // Show success message
                        showNotification(wp_content_generator_ajax.i18n.success, response.data.message, 'success');
                    } else {
                        // Show error message
                        showNotification(wp_content_generator_ajax.i18n.error, response.data.message, 'error');
                    }
                },
                error: function() {
                    // Reset button state
                    button.prop('disabled', false).text('Publish');
                    // Close the modal
                    $('#publish-options-modal').hide();
                    // Show error message
                    showNotification(wp_content_generator_ajax.i18n.error, 'An unexpected error occurred while publishing content.', 'error');
                }
            });
        });

        // Generate all pending topics
        $('#generate-all-button').on('click', function() {
            // Get all pending topics
            var pendingButtons = $('.generate-content-button:not(:disabled)');
            
            if (pendingButtons.length === 0) {
                alert('No pending topics found.');
                return;
            }
            
            if (!confirm('Are you sure you want to generate content for all ' + pendingButtons.length + ' pending topics? This might take some time.')) {
                return;
            }
            
            // Disable the button
            $(this).prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-top: 0;"></span> Generating all...');
            
            // Process each topic one by one
            processNextTopic(pendingButtons, 0);
        });

        // Helper function to process topics one by one
        function processNextTopic(buttons, index) {
            if (index >= buttons.length) {
                // All done, re-enable the button
                $('#generate-all-button').prop('disabled', false).text('Generate Content for All Pending Topics');
                showNotification(wp_content_generator_ajax.i18n.success, 'Completed generating content for all pending topics.', 'success');
                return;
            }
            
            var button = buttons.eq(index);
            var topicId = button.data('id');
            var rowElement = $('#topic-row-' + topicId);
            
            // Update button to show loading
            button.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-top: 0;"></span> ' + wp_content_generator_ajax.i18n.generating);
            
            $.ajax({
                url: wp_content_generator_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'generate_content',
                    nonce: wp_content_generator_ajax.nonce,
                    topic_id: topicId
                },
                success: function(response) {
                    if (response.success) {
                        // Update the row to reflect the new status
                        rowElement.find('.column-status .status-badge')
                            .removeClass('status-pending')
                            .addClass('status-generated')
                            .text('Generated');
                        
                        // Replace the generate button with edit and regenerate buttons
                        var newButtons = '<button class="button button-primary edit-content-button" data-id="' + topicId + '">Edit</button> ' +
                                         '<button class="button regenerate-content-button" data-id="' + topicId + '">Regenerate</button>';
                        rowElement.find('.column-actions').html(newButtons);
                    } else {
                        // Reset button state to show error
                        button.prop('disabled', false).text('Error - Try Again');
                    }
                    
                    // Process the next topic
                    setTimeout(function() {
                        processNextTopic(buttons, index + 1);
                    }, 1000);
                },
                error: function() {
                    // Reset button state to show error
                    button.prop('disabled', false).text('Error - Try Again');
                    
                    // Process the next topic
                    setTimeout(function() {
                        processNextTopic(buttons, index + 1);
                    }, 1000);
                }
            });
        }

        // Helper function to show notifications
        function showNotification(title, message, type) {
            // Create notification container if it doesn't exist
            if ($('#wp-content-generator-notifications').length === 0) {
                $('body').append('<div id="wp-content-generator-notifications"></div>');
            }
            
            // Create notification element
            var notificationId = 'notification-' + Date.now();
            var notification = $('<div class="notification notification-' + type + '" id="' + notificationId + '">' +
                                '<h4>' + title + '</h4>' +
                                '<p>' + message + '</p>' +
                                '<button class="close-notification">&times;</button>' +
                                '</div>');
            
            // Add to container
            $('#wp-content-generator-notifications').append(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('#' + notificationId).fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Close on click
            notification.find('.close-notification').on('click', function() {
                $(this).parent().fadeOut(function() {
                    $(this).remove();
                });
            });
        }

        // Hook into the WordPress get_topic_content AJAX handler
        $(document).on('wp-content-generator-get-topic-content-response', function(e, response) {
            if (response.success) {
                // Set the content in the editor
                if (typeof tinymce !== 'undefined' && tinymce.get('topic-content-editor')) {
                    tinymce.get('topic-content-editor').setContent(response.data.content);
                } else {
                    $('#topic-content-editor').val(response.data.content);
                }
            }
        });
    });

})( jQuery );
