<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://bonnermedis.de
 * @since      1.0.0
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/includes
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
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/includes
 * @author     Andreas Schneider <anschneider187@gmail.com>
 */
class Mlp_Aktion
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Mlp_Aktion_Loader    $loader    Maintains and registers all hooks for the plugin.
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
        if (defined('MLP_AKTION_VERSION')) {
            $this->version = MLP_AKTION_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'mlp-aktion';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        // Register/load our WooCommerce Blocks
        $this->register_custom_blocks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Mlp_Aktion_Loader. Orchestrates the hooks of the plugin.
     * - Mlp_Aktion_i18n. Defines internationalization functionality.
     * - Mlp_Aktion_Admin. Defines all hooks for the admin area.
     * - Mlp_Aktion_Public. Defines all hooks for the public side of the site.
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
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mlp-aktion-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mlp-aktion-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-mlp-aktion-admin.php';

        /**
         * The class responsible for defining all actions that correspond to woocommerce.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'woocommerce/class-mlp-aktion-woocommerce.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-mlp-aktion-public.php';

        $this->loader = new Mlp_Aktion_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Mlp_Aktion_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Mlp_Aktion_i18n();

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
        $plugin_admin = new Mlp_Aktion_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Woo Commerce Admin Hooks
        $this->loader->add_filter('woocommerce_settings_tabs_array', $plugin_admin, 'add_mlp_aktion_settings_tab', 50);
        $this->loader->add_action('woocommerce_settings_tabs_mlp_aktion', $plugin_admin, 'settings_tab_content');
        $this->loader->add_action('woocommerce_update_options_mlp_aktion', $plugin_admin, 'save_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Mlp_Aktion_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Woo Commerce Checkout hooks
        // TODO fallbacks?
        // $this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'validate_mlp_aktion_fields');
        // $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'save_mlp_aktion_meta');
        // $this->loader->add_action('woocommerce_init', $plugin_public, 'add_mlp_aktion_checkbox');
    }

    public function register_custom_blocks()
    {
        $plugin_woo = new Mlp_Aktion_Woocommerce($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $plugin_woo, 'register_block_types');
        $this->loader->add_action('woocommerce_blocks_loaded', $plugin_woo, 'load_mlp_aktion_block_extension');

        $this->loader->add_action('block_categories_all', $plugin_woo, 'register_mlp_aktion_block_category', 10, 2);
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
     * @return    Mlp_Aktion_Loader    Orchestrates the hooks of the plugin.
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
