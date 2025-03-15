<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bonnermedis.de
 * @since      1.0.0
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/woocommerce
 * @author     Andreas Schneider <anschneider187@gmail.com>
 */
class Mlp_Aktion_Woocommerce
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    public function register_block_types()
    {
        register_block_type_from_metadata( __DIR__ . '/../build/js/participate-checkbox' );
    }

    public function load_mlp_aktion_block_extension()
    {
		require_once __DIR__ . '/MLP_Aktion_Extend_Store_Endpoint.php';
        require_once __DIR__ . '/MLP_Aktion_Extend_Woo_Core.php';
        require_once __DIR__ . '/MLP_Aktion_Blocks_Integration.php';

        // Initialize our store endpoint extension when WC Blocks is loaded.
        MLP_Aktion_Extend_Store_Endpoint::init();

        // Add hooks relevant to extending the Woo core experience.
        $extend_core = new MLP_Aktion_Extend_Woo_Core($this->plugin_name);
        $extend_core->init();

        add_action(
			'woocommerce_blocks_checkout_block_registration',
			function( $integration_registry ) {
				$integration_registry->register( new MLP_Aktion_Blocks_Integration() );
			}
		);

        // Woocommerce Germanized Multilevel
        if ($this->is_germanized_multistep_active()) {
            add_filter(
                'woocommerce_gzdp_multilevel_checkout_step_configuration',
                function ($configuration, $content, $dom) {
                    $confirmationConf = $configuration['confirmation'] ?? null;

                    // empty conf
                    if (empty($confirmationConf) || ! isset($confirmationConf['blocks'])) {
                        return $configuration;
                    }

                    // Add our block after checkboxes block
                    $index = array_search('woocommerce-germanized/checkout-checkboxes', $confirmationConf['blocks'], true);
                    if ($index !== false) {
                        array_splice($confirmationConf['blocks'], $index + 1, 0, 'mlp-aktion/participate-checkbox');
                    }

                    $configuration['confirmation'] = $confirmationConf;

                    // Insert our wp block
                    $xpath = new \DOMXPath($dom);
                    $targetElement = $xpath->query('//*[@data-block-name="woocommerce-germanized/checkout-checkboxes"]')->item(0);

                    if ($targetElement) {
                        // Step 3: Create the new element to insert after the target
                        $newElement = $dom->createElement('div');

                        // Step 4: Define custom attributes for the new element
                        $newElement->setAttribute('class', 'wp-block-mlp-aktion-participate-checkbox');
                        $newElement->setAttribute('data-block-name', 'mlp-aktion/participate-checkbox');

                        // Step 5: Insert the new element after the target element
                        $targetElement->parentNode->insertBefore($newElement, $targetElement->nextSibling);
                    }

                    return $configuration;
                },
                10,
                3,
            );

            add_filter(
                'woocommerce_gzdp_multilevel_checkout_block_content',
                function ($block_content, $step, $content) {
                    return $block_content;
                },
                10,
                3,
            );
        }
    }

    public function register_mlp_aktion_block_category($categories)
    {
        return array_merge(
            $categories,
            [
                [
                    'slug'  => $this->plugin_name,
                    'title' => __( 'MLP Aktion Blocks', $this->plugin_name ),
                ],
            ]
        );
    }

    protected function is_germanized_multistep_active() {
		return ( 'yes' === get_option( 'woocommerce_gzdp_checkout_enable' ) ? true : false );
	}
}
