<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bonnermedis.de
 * @since      1.0.0
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/admin
 * @author     Andreas Schneider <anschneider187@gmail.com>
 */
class Mlp_Aktion_Admin
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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Mlp_Aktion_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Mlp_Aktion_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/mlp-aktion-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Mlp_Aktion_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Mlp_Aktion_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/mlp-aktion-admin.js', array( 'jquery' ), $this->version, false);

    }

    /**
     * Add settings tab to woocommerce
     *
     * @param array $tabs
     * @return void
     */
    public function add_mlp_aktion_settings_tab($tabs)
    {
        $tabs['mlp_aktion'] = __('MLP Aktion', $this->plugin_name);
        return $tabs;
    }

    public function settings_tab_content()
    {
        woocommerce_admin_fields($this->mlp_aktion_admin_settings());
    }

    public function save_settings()
    {
        woocommerce_update_options($this->mlp_aktion_admin_settings());

    }

    private function mlp_aktion_admin_settings()
    {
        $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);

        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return [
            'section_title' => [
                'name'     => __('MLP Aktion', $this->plugin_name),
                'type'     => 'title',
                'desc'     => 'Einstellungen für die aktuelle MLP Aktion.',
                'id'       => 'mlp_aktion_settings_section_title'
            ],
            'mlp_aktion_selected_page' => [
                'name'    => __('Teilnahme Bedingungen Seite', $this->plugin_name),
                'type'    => 'single_select_page',
                'desc'    => __('Wählen Sie eine Seite aus, die die Teilnahmebedingungen der Aktion darstellt.', $this->plugin_name),
                'id'      => 'mlp_aktion_selected_page'
            ],
            'checkbox_option_text' => [
                'name' => __('Checkbox Text', $this->plugin_name),
                'type' => 'textarea',
                'desc' => __('Der Text, dem der Kunde beim Checkout zustimmt, um an der Aktion teilzunehmen. Nutzen Sie {participation_terms} und {/participation_terms}, um den Link zu Ihrer gewählten Seite mit den Teilnahmebedingungen zu markieren.', $this->plugin_name),
                'id'   => 'mlp_aktion_checkbox_text'
            ],
            'phone_number_required' => [
                'name' => __('Telefonnummer als Plichtfeld', $this->plugin_name),
                'type' => 'checkbox',
                'desc' => __('Aktivieren Sie diese Option, um die Telefonnummer nachträglich als Pflichtfeld anzuzeigen.', $this->plugin_name),
                'id'   => 'mlp_aktion_phone_number_required'
            ],
            'active_aktion_categories' => [
                'name' => __('Produkt Kategorien für Aktion', $this->plugin_name),
                'type' => 'multiselect',
                'desc' => __('Wählen Sie die Produkt Kategorien, für die der Nutzer die Option zur Teilnahme an der Aktion auswählen kann.', $this->plugin_name),
                'id' => 'mlp_aktion_active_aktion_categories',
                'class'    => 'wc-enhanced-select',
                'options' => $options,
                'default' => [],
            ],
            'section_end' => [
                'type' => 'sectionend',
                'id'   => 'mlp_aktion_settings_section_end'
            ],
            'advanced_section_title' => [
                'name'     => __('Advanced', $this->plugin_name),
                'type'     => 'title',
                'desc'     => 'Erweiterte Einstellungen für die MLP Aktion/Inputs im Checkout.',
                'id'       => 'mlp_aktion_advanced_settings_section_title'
            ],
            'advanced_wrapper_css' => [
                'name' => __('Custom CSS-Klassen (Wrapper)', $this->plugin_name),
                'type' => 'text',
                'desc' => __('Custom CSS-Klassen für den Wrapper', $this->plugin_name),
                'id'   => 'mlp_aktion_custom_wrapper_css'
            ],
            'advanced_input_css' => [
                'name' => __('Custom CSS-Klassen (Input)', $this->plugin_name),
                'type' => 'text',
                'desc' => __('Custom CSS-Klassen für die Inputs', $this->plugin_name),
                'id'   => 'mlp_aktion_custom_inputs_css'
            ],
            'advanced_section_end' => [
                'type' => 'sectionend',
                'id'   => 'mlp_aktion_advanced_settings_section_end'
            ],
        ];
    }

}
