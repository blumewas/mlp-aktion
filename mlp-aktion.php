<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bonnermedis.de
 * @since             1.0.0
 * @package           Mlp_Aktion
 *
 * @wordpress-plugin
 * Plugin Name:       MLP Aktion
 * Plugin URI:        https://bonnermedis.de
 * Description:       Plugin um Aktionen und optionale Teilnahme Boxen für eine Bestellung hinzuzufügen.
 * Version:           1.0.0
 * Author:            Andreas Schneider
 * Author URI:        https://bonnermedis.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mlp-aktion
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('MLP_AKTION_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mlp-aktion-activator.php
 */
function activate_mlp_aktion()
{

    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Dieses Plugin benötigt WooCommerce. Bitte installiere und aktiviere WooCommerce.', 'Plugin-Fehler', array('back_link' => true));
    }


    require_once plugin_dir_path(__FILE__) . 'includes/class-mlp-aktion-activator.php';
    Mlp_Aktion_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mlp-aktion-deactivator.php
 */
function deactivate_mlp_aktion()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-mlp-aktion-deactivator.php';
    Mlp_Aktion_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_mlp_aktion');
register_deactivation_hook(__FILE__, 'deactivate_mlp_aktion');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/mlp-aktion-helper.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-mlp-aktion.php';

$basename = plugin_basename(__FILE__);
add_filter("plugin_action_links_{$basename}", function ($links) {
    $url = esc_url( get_admin_url(null, 'admin.php?page=wc-settings&tab=mlp_aktion'));
    $settings_link = "<a href=\"$url\">" . __('Einstellungen', 'mlp-aktion') . '</a>';

    array_unshift($links, $settings_link);
    return $links;
}, 20);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mlp_aktion()
{

    $plugin = new Mlp_Aktion();
    $plugin->run();
}
run_mlp_aktion();
