<?php
/**
 * Our admin menu template
 *
 * @link              https://bonnermedis.de
 * @since             1.0.2
 * @package           Mlp_Aktion/templates
 */

$action_url = admin_url('admin-post.php?action=export_orders_xlsx&export_nonce=' . wp_create_nonce('export_orders_action'));
?>

<div class="wrap">
    <h1>Export Orders to Excel</h1>
    <a href="<?php esc_attr_e($action_url) ?>" class="button button-primary">
        Download Excel (.xlsx)
    </a>
</div>
