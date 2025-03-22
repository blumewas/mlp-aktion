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
        Logger::log('Initializing admin');
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
        $action = new ExportOrders();

        $action('_mlp_aktion_optin', 1);
    }

    protected function register_assets(): void
    {
        $this->assets->add_admin_asset(
            asset('admin/js/mlp-aktion-admin.js'),
            ['jquery'],
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
