<?php

namespace Blumewas\MlpAktion\Admin;

use Blumewas\MlpAktion\Admin\Actions\ExportOrders;
use Blumewas\MlpAktion\Admin\Menu\MlpAktionMenu;
use Blumewas\MlpAktion\Admin\Util\Menu;
use Blumewas\MlpAktion\Helper\Logger;
use Blumewas\MlpAktion\Plugin\Assets;
use Blumewas\MlpAktion\Plugin\Hooks;

class Admin
{
    /**
     * The main menu class for our plugin.
     * Remove or set to null to skip registration.
     *
     * @var class-string<Menu>
     */
    public $mainMenu = MlpAktionMenu::class;

    public function __construct(
        private Hooks $hooks,
        private Assets $assets,
    ) {
    }

    /**
     * Init our admin section
     *
     * @return void
     */
    public function init()
    {
        // Register assets
        $this->register_assets();

        // Register our menu
        $this->register_menu();

        // TODO remove
        $this->hooks->add_action(
            'admin_post_export_orders_xlsx',
            $this,
            'export_orders'
        );
    }

    // TODO remove
    public function export_orders()
    {
        // Verify nonce
        if (!isset($_GET['export_nonce']) || !wp_verify_nonce($_GET['export_nonce'], 'export_orders_action')) {
            wp_die('Security check failed.');
        }

        // Check user capability
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized access.');
        }

        $action = new ExportOrders();

        $action('_mlp_aktion_optin', 1);
    }

    protected function register_assets(): void
    {
        $this->assets->add_admin_asset(
            asset('admin/js/mlp-aktion-admin.js'),
            ['jquery'],
        );

        $this->assets->add_public_asset(
            asset('admin/css/admin.css'),
        );

    }

    protected function register_menu(): void
    {
        // If we have a mainMenu class
        if (isset($this->mainMenu) && is_string($this->mainMenu)) {
            // TODO - resolve dependencies container?
            $menuInstance = (new $this->mainMenu);

            // Check if instance is type menu
            if ($menuInstance instanceof Menu) {
                $this->hooks->add_action('admin_menu', $menuInstance, 'add_menu');
            }
        }
    }
}
