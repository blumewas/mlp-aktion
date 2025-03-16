<?php

namespace Blumewas\MlpAktion\Plugin;

use Blumewas\MlpAktion\Helper\Logger;

/**
 * Register all assets for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Blumewas\MlpAktion\Registry
 * @author     Andreas Schneider <anschneider187@gmail.com>
 */
class Assets
{

    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $admin_assets;

    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $public_assets;

    /**
     * Plugin name
     *
     * @var string
     */
    private $plugin_name = 'mlp-aktion';

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    1.0.0
     */
    public function __construct(
        private Hooks $hooks,
    )
    {
        $this->admin_assets = [
            'style' => [],
            'script' => [],
        ];

        $this->public_assets = [
            'style' => [],
            'script' => [],
        ];
    }

    /**
     * @param string           $src    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
     *                                 Default empty.
     * @param string[]         $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
     * @param string|bool|null $ver    Optional. String specifying stylesheet version number, if it has one, which is added to the URL
     *                                 as a query string for cache busting purposes. If version is set to false, a version
     *                                 number is automatically added equal to current installed WordPress version.
     *                                 If set to null, no version is added.
     * @param string           $media  Optional. The media for which this stylesheet has been defined.
     *                                 Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
     *                                 '(orientation: portrait)' and '(max-width: 640px)'.
     * @param array|bool       $args     {
     *     Optional. An array of additional script loading strategies. Default empty array.
     *     Otherwise, it may be a boolean in which case it determines whether the script is printed in the footer. Default false.
     *
     *     @type string    $strategy     Optional. If provided, may be either 'defer' or 'async'.
     *     @type bool      $in_footer    Optional. Whether to print the script in the footer. Default 'false'.
     * }
     * @phpstan-param bool|array{
     *   strategy?: string,
     *   in_footer?: bool,
     * } $args
     * @param string $type
     */
    public function add_admin_asset(
        $src,
        $deps = [],
        $ver = null,
        $media = 'all',
        $args = [],
        $type = null,
    )
    {
        $type = $type ?? $this->get_asset_type($src);

        $this->admin_assets[$type][] = [
            'src' => $src,
            'deps' => $deps,
            'ver' => $ver,
            'media' => $media,
            'args' => $args
        ];
    }

    /**
     * @param string           $src    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
     *                                 Default empty.
     * @param string[]         $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
     * @param string|bool|null $ver    Optional. String specifying stylesheet version number, if it has one, which is added to the URL
     *                                 as a query string for cache busting purposes. If version is set to false, a version
     *                                 number is automatically added equal to current installed WordPress version.
     *                                 If set to null, no version is added.
     * @param string           $media  Optional. The media for which this stylesheet has been defined.
     *                                 Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
     *                                 '(orientation: portrait)' and '(max-width: 640px)'.
     * @param array|bool       $args     {
     *     Optional. An array of additional script loading strategies. Default empty array.
     *     Otherwise, it may be a boolean in which case it determines whether the script is printed in the footer. Default false.
     *
     *     @type string    $strategy     Optional. If provided, may be either 'defer' or 'async'.
     *     @type bool      $in_footer    Optional. Whether to print the script in the footer. Default 'false'.
     * }
     * @phpstan-param bool|array{
     *   strategy?: string,
     *   in_footer?: bool,
     * } $args
     * @param string $type
     */
    public function add_public_asset(
        $src,
        $deps = [],
        $ver = null,
        $media = 'all',
        $args = [],
        $type = null,
    )
    {
        $type = $type ?? $this->get_asset_type($src);

        $this->admin_assets[$type][] = [
            'src' => $src,
            'deps' => $deps,
            'ver' => $ver,
            'media' => $media,
            'args' => $args
        ];
    }

    /**
     * Load the assets
     *
     * @return void
     */
    public function load()
    {
        $this->hooks->add_action(
            'admin_enqueue_scripts',
            null,
            function () {
                Logger::log('admin script');
                $admin_scripts = $this->admin_assets['script'];
                foreach ($admin_scripts as $script) {
                    // enquue scripts
                    wp_enqueue_script(
                        $this->plugin_name,
                        $script['src'],
                        $script['deps'] ?? ['jquery'],
                        $script['ver'],
                        $script['args'] ?? false
                    );
                }

                $admin_styles = $this->admin_assets['style'];

                foreach ($admin_styles as $value) {
                    // enquue styles
                    wp_enqueue_style(
                        $this->plugin_name,
                        $value['src'],
                        $value['deps'] ?? [],
                        $value['ver'],
                        $value['media'] ?? 'all'
                    );
                }
            }
        );

        // Add public scripts
        $this->hooks->add_action(
            'wp_enqueue_scripts',
            null,
            function () {
                $public_scripts = $this->public_assets['script'];
                foreach ($public_scripts as $script) {
                    // enquue scripts
                    wp_enqueue_script(
                        $this->plugin_name,
                        $script['src'],
                        $script['deps'] ?? ['jquery'],
                        $script['ver'],
                        $script['args'] ?? false
                    );
                }

                $public_styles = $this->public_assets['style'];

                foreach ($public_styles as $value) {
                    // enquue styles
                    wp_enqueue_style(
                        $this->plugin_name,
                        $value['src'],
                        $value['deps'] ?? [],
                        $value['ver'],
                        $value['media'] ?? 'all'
                    );
                }
            }
        );
    }

    /**
     * Get asset type from src
     *
     * @param string $asset_src
     * @return string
     */
    private function get_asset_type($asset_src)
    {
        if (! is_string($asset_src)) {
            return 'none';
        }

        $spl = explode('.', basename($asset_src));
        if (count($spl) != 2) {
            return 'none';
        }

        return match($spl[1]) {
            'css' => 'style',
            'js' => 'script',
            default => 'none',
        };
    }

}
