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
 * Version:           1.0.2
 * Author:            Andreas Schneider
 * Author URI:        https://bonnermedis.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mlp-aktion
 * Domain Path:       /languages
 */

use Blumewas\MlpAktion\Autoloader;
use Blumewas\MlpAktion\MlpAktion;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Load core packages and the autoloader.
 *
 * The new packages and autoloader require PHP 5.6+.
 */
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    require __DIR__ . '/src/Autoloader.php';

    if (! Autoloader::init()) {
        return;
    }
} else {
    function mlp_aktion_admin_php_notice()
    {
        ?>
		<div id="message" class="error">
			<p>
			<?php
            printf(
                /* translators: %s is the word upgrade with a link to a support page about upgrading */
                esc_html__('MLP Aktion benötigt mind. PHP Version 8.0 umzu funktionieren. Bitte aktualisieren Sie Ihre aktuelle PHP Version %s.', 'mlp-aktion'),
                '<a href="https://wordpress.org/support/update-php/">' . esc_html__('upgrade', 'mlp-aktion') . '</a>'
            );
        ?>
			</p>
		</div>
		<?php
    }

    add_action('admin_notices', 'mlp_aktion_admin_php_notice', 20);

    return;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MLP_AKTION_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mlp-aktion-activator.php
 */
function activate_mlp_aktion()
{
    require_once plugin_dir_path(__FILE__) . 'includes/activator.php';
    Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mlp-aktion-deactivator.php
 */
function deactivate_mlp_aktion()
{
    require_once plugin_dir_path(__FILE__) . 'includes/deactivator.php';
    Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_mlp_aktion');
register_deactivation_hook(__FILE__, 'deactivate_mlp_aktion');

/**
 * @return MlpAktion $plugin instance
 */
function MlpAktion() // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
{
    $basename = plugin_basename(__FILE__);

    return MlpAktion::instance($basename);
}

$GLOBALS['mlp_aktion'] = MlpAktion();
