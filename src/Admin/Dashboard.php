<?php

namespace Blumewas\MlpAktion\Admin;

use Blumewas\MlpAktion\Admin\Actions\ExportOrders;
use Blumewas\MlpAktion\Plugin\Hooks;

class Dashboard
{

    public function __construct(
        private Hooks $hooks,
    ) {
    }

    public function init()
    {
        $this->hooks->add_action(
            'admin_menu',
            $this,
            'add_menu',
        );

        $this->hooks->add_action(
            'admin_post_export_orders_xlsx',
            $this,
            'export_orders'
        );
    }

    public function add_menu()
    {
        add_submenu_page(
            'woocommerce',
            'Export Orders (Excel)',
            'Export Orders (Excel)',
            'manage_woocommerce',
            'export-orders-xlsx',
            [
                $this,
                'render_export_page_xlsx',
            ]
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
}
