<?php

namespace Blumewas\MlpAktion\Admin\Settings;

use Blumewas\MlpAktion\Plugin\Hooks;

class AdminSettings
{
    public function __construct(
        private Hooks $hooks,
    ) {
    }

    public function init()
    {
        // Woo Commerce Admin Hooks
        $this->hooks->add_filter('woocommerce_settings_tabs_array', $this, 'add_mlp_aktion_settings_tab', 50);
        $this->hooks->add_action('woocommerce_settings_tabs_mlp_aktion', $this, 'settings_tab_content');
        $this->hooks->add_action('woocommerce_update_options_mlp_aktion', $this, 'save_settings');
    }

    /**
     * Add settings tab to woocommerce
     *
     * @param array $tabs
     * @return void
     */
    public function add_mlp_aktion_settings_tab($tabs)
    {
        $plugin_name = MlpAktion()->plugin_name;

        $tabs['mlp_aktion'] = __('MLP Aktion', $plugin_name);
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
        $plugin_name = MlpAktion()->plugin_name;

        $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);

        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return [
            'section_title' => [
                'name'     => __('MLP Aktion', $plugin_name),
                'type'     => 'title',
                'desc'     => 'Einstellungen für die aktuelle MLP Aktion.',
                'id'       => 'mlp_aktion_settings_section_title'
            ],
            'mlp_aktion_selected_page' => [
                'name'    => __('Teilnahme Bedingungen Seite', $plugin_name),
                'type'    => 'single_select_page',
                'desc'    => __('Wählen Sie eine Seite aus, die die Teilnahmebedingungen der Aktion darstellt.', $plugin_name),
                'id'      => 'mlp_aktion_selected_page'
            ],
            'checkbox_option_text' => [
                'name' => __('Checkbox Text', $plugin_name),
                'type' => 'textarea',
                'desc' => __('Der Text, dem der Kunde beim Checkout zustimmt, um an der Aktion teilzunehmen. Nutzen Sie {participation_terms} und {/participation_terms}, um den Link zu Ihrer gewählten Seite mit den Teilnahmebedingungen zu markieren.', $plugin_name),
                'id'   => 'mlp_aktion_checkbox_text'
            ],
            'phone_number_required' => [
                'name' => __('Telefonnummer als Plichtfeld', $plugin_name),
                'type' => 'checkbox',
                'desc' => __('Aktivieren Sie diese Option, um die Telefonnummer nachträglich als Pflichtfeld anzuzeigen.', $plugin_name),
                'id'   => 'mlp_aktion_phone_number_required'
            ],
            'active_aktion_categories' => [
                'name' => __('Produkt Kategorien für Aktion', $plugin_name),
                'type' => 'multiselect',
                'desc' => __('Wählen Sie die Produkt Kategorien, für die der Nutzer die Option zur Teilnahme an der Aktion auswählen kann.', $plugin_name),
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
                'name'     => __('Advanced', $plugin_name),
                'type'     => 'title',
                'desc'     => 'Erweiterte Einstellungen für die MLP Aktion/Inputs im Checkout.',
                'id'       => 'mlp_aktion_advanced_settings_section_title'
            ],
            'advanced_wrapper_css' => [
                'name' => __('Custom CSS-Klassen (Wrapper)', $plugin_name),
                'type' => 'text',
                'desc' => __('Custom CSS-Klassen für den Wrapper', $plugin_name),
                'id'   => 'mlp_aktion_custom_wrapper_css'
            ],
            'advanced_input_css' => [
                'name' => __('Custom CSS-Klassen (Input)', $plugin_name),
                'type' => 'text',
                'desc' => __('Custom CSS-Klassen für die Inputs', $plugin_name),
                'id'   => 'mlp_aktion_custom_inputs_css'
            ],
            'advanced_section_end' => [
                'type' => 'sectionend',
                'id'   => 'mlp_aktion_advanced_settings_section_end'
            ],
        ];
    }

}
