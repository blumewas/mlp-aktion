<?php
/**
 * Our admin menu template
 *
 * @link              https://bonnermedis.de
 * @since             {VERSION}
 * @package           Mlp_Aktion/templates
 */

$action_url = admin_url('admin-post.php?action=export_orders_xlsx');
?>

<div class="wrap">
    <h1>Export Orders to Excel</h1>
    <a href="<?php esc_attr_e($action_url) ?>" class="button button-primary">
        Download Excel (.xlsx)
    </a>
</div>
