/**
 * Settings page script for Foss Engine plugin
 *
 * Handles the toggling of API key visibility and provider settings display.
 *
 * @package    Foss Engine
 * @subpackage Foss_Engine/admin/js
 * @since      1.0.2
 */

// Check if fossEngineAdmin object exists
if (typeof fossEngineAdmin === "undefined") {
  // Create a fallback object to prevent errors
  var fossEngineAdmin = {
    showText: "Show",
    hideText: "Hide",
  };
}

jQuery(document).ready(function ($) {
  // Toggle API key visibility
  $("#toggle-api-key").on("click", function () {
    var $apiKey = $("#foss_engine_openai_key");
    var $button = $(this);

    if ($apiKey.attr("type") === "password") {
      $apiKey.attr("type", "text");
      $button.text(fossEngineAdmin.hideText);
    } else {
      $apiKey.attr("type", "password");
      $button.text(fossEngineAdmin.showText);
    }
  });

  // Toggle Deepseek API key visibility
  $("#toggle-deepseek-key").on("click", function () {
    var $apiKey = $("#foss_engine_deepseek_key");
    var $button = $(this);

    if ($apiKey.attr("type") === "password") {
      $apiKey.attr("type", "text");
      $button.text(fossEngineAdmin.hideText);
    } else {
      $apiKey.attr("type", "password");
      $button.text(fossEngineAdmin.showText);
    }
  });

  // Toggle between OpenAI and Deepseek settings
  $('input[name="foss_engine_provider"]').on("change", function () {
    if ($(this).val() === "openai") {
      $("#openai-settings").show();
      $("#deepseek-settings").hide();
    } else {
      $("#openai-settings").hide();
      $("#deepseek-settings").show();
    }
  });
});
