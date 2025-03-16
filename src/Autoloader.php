<?php

namespace Blumewas\MlpAktion;

defined('ABSPATH') || exit;

/**
 * Autoloader class.
 *
 * @since 3.7.0
 */
class Autoloader
{

    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * Require the autoloader and return the result.
     *
     * If the autoloader is not present, let's log the failure and display a nice admin notice.
     *
     * @return boolean
     */
    public static function init()
    {
        $autoloader = dirname(__DIR__) . '/vendor/autoload.php';

        if (! is_readable($autoloader)) {
            self::missing_autoloader();

            return false;
        }

        return require $autoloader;
    }

    /**
     * If the autoloader is missing, add an admin notice.
     */
    protected static function missing_autoloader()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(  // phpcs:ignore
                esc_html__('Die Installation des Plugins MLP Aktion konnte nicht abgeschlossen werden.', 'mlp-aktion')
            );
        }

        add_action(
            'admin_notices',
            function () {
                ?>
				<div class="notice notice-error">
					<p>
						<?php
                        printf(
                            __('Die Installation des Plugins MLP Aktion konnte nicht abgeschlossen werden.', 'mlp-aktion')
                        );
                ?>
					</p>
				</div>
				<?php
            }
        );
    }

}
