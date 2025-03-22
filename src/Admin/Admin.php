<?php

namespace Blumewas\MlpAktion\Admin;

use Blumewas\MlpAktion\Admin\Actions\ExportOrders;
use Blumewas\MlpAktion\Admin\Menu\MlpAktionMenu;
use Blumewas\MlpAktion\Admin\Util\Menu;
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
    ) {
    }

    /**
     * Init our admin section
     *
     * @return void
     */
    public function init()
    {
        // Register our menu
        $this->registerMenu();

        $this->hooks->add_action(
            'admin_post_export_orders_xlsx',
            $this,
            'export_orders'
        );
    }

    public function render_export_page_xlsx() {
        echo '
        <div class="wrap">
            <h1>Export Orders to Excel</h1>
            <a href="'. admin_url('admin-post.php?action=export_orders_xlsx') . '" class="button button-primary">
                Download Excel (.xlsx)
            </a>
        </div>';
    }

    public function export_orders()
    {
        $action = new ExportOrders();

        $action('_mlp_aktion_optin', 1);
    }

    protected function registerMenu(): void
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
