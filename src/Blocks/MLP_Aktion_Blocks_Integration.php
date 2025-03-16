<?php

namespace Blumewas\MlpAktion\Blocks;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks
 */
class MLP_Aktion_Blocks_Integration implements IntegrationInterface
{

    /**
     * The name of the integration.
     *
     * @return string
     */
    public function get_name()
    {
        return 'mlp-aktion';
    }

    /**
     * When called invokes any initialization/setup for the integration.
     */
    public function initialize()
    {
        $this->register_participate_checkbox_block_frontend_scripts();
        $this->register_participate_checkbox_block_editor_scripts();
        $this->register_participate_checkbox_block_editor_styles();
        $this->register_main_integration();
    }

    /**
     * Registers the main JS file required to add filters and Slot/Fills.
     */
    private function register_main_integration()
    {
        $script_path = '/../../build/index.js';
        $style_path  = '/../../build/style-mlp-aktion-participate-checkbox.css';

        $script_url = plugins_url($script_path, __FILE__);
        $style_url  = plugins_url($style_path, __FILE__);

        $script_asset_path = plugin_dir_path(__FILE__) . '../../build/index.asset.php';
        $script_asset      = file_exists($script_asset_path)
            ? require $script_asset_path
            : array(
                'dependencies' => array(),
                'version'      => $this->get_file_version($script_path),
            );

        wp_enqueue_style(
            'mlp-aktion-blocks-integration',
            $style_url,
            [],
            $this->get_file_version($style_path)
        );

        wp_register_script(
            'mlp-aktion-blocks-integration',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );
        wp_set_script_translations(
            'mlp-aktion-blocks-integration',
            'mlp-aktion',
            dirname(__FILE__) . '/languages'
        );
    }

    /**
     * Returns an array of script handles to enqueue in the frontend context.
     *
     * @return string[]
     */
    public function get_script_handles()
    {
        return [ 'mlp-aktion-blocks-integration', 'mlp-aktion-blocks-frontend' ];
    }

    /**
     * Returns an array of script handles to enqueue in the editor context.
     *
     * @return string[]
     */
    public function get_editor_script_handles()
    {
        return [ 'mlp-aktion-blocks-integration', 'mlp-aktion-blocks-editor' ];
    }

    /**
     * An array of key, value pairs of data made available to the block on the client side.
     *
     * @return array
     */
    public function get_script_data()
    {
        // Get page link
        $selectedPageId = get_option('mlp_aktion_selected_page');
        $pariticipationTermsUrl = $selectedPageId ? get_permalink($selectedPageId) : null;

        // Checkbox label
        $optinCheckboxLabel = wp_kses_post(get_option('mlp_aktion_checkbox_text', ''));

        $data = [
            'aktionActive'    => mlp_aktion_cart_qualifies_for_aktion(),

            'optinCheckboxLabel' => $optinCheckboxLabel,
            'pariticipationTermsUrl' => $pariticipationTermsUrl,

            'phoneNumberRequired' => get_option('mlp_aktion_phone_number_required') == 'yes',

            'advancedWrapperClass' => esc_attr(get_option('mlp_aktion_custom_wrapper_css', '') ?? ''),
            'advancedInputCss' => esc_attr(get_option('mlp_aktion_custom_inputs_css', '') ?? ''),
        ];

        return $data;

    }

    public function register_participate_checkbox_block_editor_styles()
    {
        $style_path = '/../../build/style-mlp-aktion-participate-checkbox.css';

        $style_url = plugins_url($style_path, __FILE__);
        wp_enqueue_style(
            'mlp-aktion-blocks-editor',
            $style_url,
            [],
            $this->get_file_version($style_path)
        );
    }

    public function register_participate_checkbox_block_editor_scripts()
    {
        $script_path       = '/../../build/mlp-aktion-participate-checkbox.js';
        $script_url        = plugins_url($script_path, __FILE__);
        $script_asset_path = plugin_dir_path(__FILE__) . '../../../build/mlp-aktion-participate-checkbox.asset.php';

        $script_asset      = file_exists($script_asset_path)
            ? require $script_asset_path
            : [
                'dependencies' => [],
                'version'      => $this->get_file_version($script_asset_path),
            ];

        wp_register_script(
            'mlp-aktion-blocks-editor',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );

        wp_set_script_translations(
            'mlp-aktion-blocks-editor',
            'mlp-aktion',
            dirname(__FILE__) . '/languages'
        );
    }

    public function register_participate_checkbox_block_frontend_scripts()
    {
        $script_path       = '/../../build/mlp-aktion-participate-checkbox-frontend.js';
        $script_url        = plugins_url($script_path, __FILE__);
        $script_asset_path = plugin_dir_path(__FILE__) . '../../../build/mlp-aktion-participate-checkbox-frontend.asset.php';

        $script_asset      = file_exists($script_asset_path)
            ? require $script_asset_path
            : [
                'dependencies' => [],
                'version'      => $this->get_file_version($script_asset_path),
            ];

        wp_register_script(
            'mlp-aktion-blocks-frontend',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );
        wp_set_script_translations(
            'mlp-aktion-blocks-frontend',
            'mlp-aktion',
            dirname(__FILE__) . '/languages'
        );
    }

    /**
     * Get the file modified time as a cache buster if we're in dev mode.
     *
     * @param string $file Local path to the file.
     * @return string The cache buster value to use for the given file.
     */
    protected function get_file_version($file)
    {
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists($file)) {
            return filemtime($file);
        }

        return MLP_AKTION_VERSION;
    }
}
