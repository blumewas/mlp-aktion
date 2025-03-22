<?php

namespace Blumewas\MlpAktion;

use Blumewas\MlpAktion\Admin\Admin;
use Blumewas\MlpAktion\Admin\Settings\AdminWooSettings;
use Blumewas\MlpAktion\Blocks\Mlp_Aktion_Woocommerce;
use Blumewas\MlpAktion\Helper\Logger;
use Blumewas\MlpAktion\Plugin\Assets;
use Blumewas\MlpAktion\Plugin\Hooks;
use Blumewas\MlpAktion\Registry\Container;
use Blumewas\MlpAktion\I18n\MlpAktionI18n;

class MlpAktion
{
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    public $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $version    The current version of the plugin.
     */
    public $version;

    /**
     * The main instance
     *
     * @var self
     */
    private static $_instance;

    /**
     * Base Container
     *
     * @var Container
     */
    private $container;

    /**
     * Main MLP Aktion Instance
     *
     * Ensures that only one instance of WooCommerceGermanized is loaded or can be loaded.
     *
     * @static
     * @return MlpAktion - Main instance
     */
    public static function instance($basename = null)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($basename);
        }

        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', $this->plugin_name), '1.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', $this->plugin_name), '1.0');
    }

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct(
        private string $basename,
    )
    {
        if (defined('MLP_AKTION_VERSION')) {
            $this->version = MLP_AKTION_VERSION;
        } else {
            $this->version = '1.0.1';
        }

        $this->plugin_name = 'mlp-aktion';

        // Set locale
        $this->set_locale();

        // Init the plugin
        $this->init();

        // // Register/load our WooCommerce Blocks
        // $this->register_custom_blocks();
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init()
    {
        $this->container();

        // Resolve bootstrapper
        $bootstrap = $this->make(Bootstrap::class);

        // Add Settings links
        $this->add_plugin_links();

        // Init Admin and public parts
        $this->init_public();
        $this->init_admin();

        // Register Woo Blocks
        $this->register_custom_blocks();

        // Log initialized
        Logger::log('Initialized');

        // Load the bootstrapper
        $bootstrap->load();
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
    protected function set_locale()
    {
        $plugin_i18n = new MlpAktionI18n();

        add_action('plugins_loaded', [$plugin_i18n, 'load_plugin_textdomain']);
    }

    /**
     * Init public hooks and assets
     *
     * @return void
     */
    protected function init_public()
    {
        /** @var Assets */
        $assets = $this->make(Assets::class);

        $assets->add_public_asset(
            $this->root_url() . 'js/mlp-aktion-public.js',
            ['jquery'],
            $this->version,
        );

        $directory = plugin_dir_path(__FILE__);

        $assets->add_public_asset(plugin_dir_url($directory . '../public') . 'public/css/mlp-aktion-public.css');

    }

    /**
     * Init admin hooks and assets
     *
     * @return void
     */
    protected function init_admin()
    {
        /** @var Assets */
        $assets = $this->make(Assets::class);

        $assets->add_admin_asset(
            $this->root_url() . 'js/mlp-aktion-admin.js',
            ['jquery'],
            $this->version,
        );
        $assets->add_admin_asset($this->root_url() . 'css/mlp-aktion-admin.css');

        // Add Admin Settings
        $settings = $this->make(AdminWooSettings::class);
        $settings->init();

        // Admin Parts
        $admin = $this->make(Admin::class);
        $admin->init();
    }

    /**
     * Initialize the container
     *
     * @return Container
     */
    protected function container()
    {
        // If set get container
        if (isset($this->container) && $this->container instanceof Container)
        {
            return $this->container;
        }

        $container = new Container();

        // register Bootstrap.
        $container->register(
            Bootstrap::class,
            function ( $container ) {
                return new Bootstrap(
                    $container
                );
            }
        );

        $this->container = $container;
        return $container;
    }

    /**
     * Add our plugins links
     *
     * @return void
     */
    protected function add_plugin_links()
    {
        $this->make(Hooks::class)->add_filter(
            "plugin_action_links_{$this->basename}",
            null,
            function ($links) {
                $url = esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=mlp_aktion'));
                $settings_link = "<a href=\"$url\">" . __('Einstellungen', 'mlp-aktion') . '</a>';

                array_unshift($links, $settings_link);
                return $links;
            },
            20
        );
    }

    /**
     * Make a dependency instance
     *
     * @param string $key
     * @return mixed
     */
    public function make($key)
    {
        return $this->container()->get($key);
    }

    /**
     * Root url
     *
     * @return string
     */
    protected function root_url()
    {
        return plugins_url($this->root_dir());
    }

    /**
     * Get the plugin root dir
     *
     * @return string
     */
    protected function root_dir()
    {
        $dir = plugin_dir_path(__FILE__) . '../';

        return $dir;
    }

    // TODO - refactor
    public function register_custom_blocks()
    {
        $plugin_woo = new Mlp_Aktion_Woocommerce($this->plugin_name, $this->version);

        $hooks = $this->make(Hooks::class);

        $hooks->add_action('init', $plugin_woo, 'register_block_types');
        $hooks->add_action('woocommerce_blocks_loaded', $plugin_woo, 'load_mlp_aktion_block_extension');

        $hooks->add_action('block_categories_all', $plugin_woo, 'register_mlp_aktion_block_category', 10, 2);
    }

}
