<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://designomate.com/
 * @since      1.0.0
 *
 * @package     Foss Engine
 * @subpackage WP_Content_Generator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package     Foss Engine
 * @subpackage WP_Content_Generator/includes
 * @author     Your Name <email@example.com>
 */
class WP_Content_Generator
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WP_Content_Generator_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('FOSS_ENGINE_VERSION')) {
            $this->version = FOSS_ENGINE_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'foss-engine';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - WP_Content_Generator_Loader. Orchestrates the hooks of the plugin.
     * - WP_Content_Generator_i18n. Defines internationalization functionality.
     * - WP_Content_Generator_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine-i18n.php';

        /**
         * The class responsible for OpenAI API interactions.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine-openai.php';

        /**
         * The class responsible for CSV file handling.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-foss-engine-csv.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-foss-engine-admin.php';

        $this->loader = new WP_Content_Generator_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the WP_Content_Generator_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new WP_Content_Generator_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new WP_Content_Generator_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        // Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

        // Save/Update our plugin options
        $this->loader->add_action('admin_init', $plugin_admin, 'options_update');

        // AJAX handlers
        $this->loader->add_action('wp_ajax_upload_csv', $plugin_admin, 'handle_csv_upload');
        $this->loader->add_action('wp_ajax_generate_content', $plugin_admin, 'generate_content');
        $this->loader->add_action('wp_ajax_save_content', $plugin_admin, 'save_content');
        $this->loader->add_action('wp_ajax_publish_content', $plugin_admin, 'publish_content');
        $this->loader->add_action('wp_ajax_regenerate_content', $plugin_admin, 'regenerate_content');
        $this->loader->add_action('wp_ajax_get_pending_topics', $plugin_admin, 'get_pending_topics');
        $this->loader->add_action('wp_ajax_test_openai_connection', $plugin_admin, 'test_openai_connection');
        $this->loader->add_action('wp_ajax_get_topic_content', $plugin_admin, 'get_topic_content');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    WP_Content_Generator_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
