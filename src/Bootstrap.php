<?php

namespace Blumewas\MlpAktion;

use Blumewas\MlpAktion\Admin\Dashboard;
use Blumewas\MlpAktion\Admin\Settings\AdminWooSettings;
use Blumewas\MlpAktion\Helper\Logger;
use Blumewas\MlpAktion\Plugin\Assets;
use Blumewas\MlpAktion\Plugin\Hooks;
use Blumewas\MlpAktion\Registry\Container;

class Bootstrap
{

    /**
     * Dependency Injection Container
     *
     * @var Container
     */
    private $container;

    private bool $initialized = false;

    /**
     * Constructor
     *
     * @param Container $container  The Dependency Injection Container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->init();
    }

    public function load()
    {
        if (! $this->initialized) {
            throw new \Exception('Trying to load uninitialized Bootstraper', 1);
        }

        // Load Hooks and Assets
        $this->load_assets();
        $this->load_hooks();
    }

    /**
     * Init the Plugin
     *
     * @return void
     */
    protected function init()
    {
        if ($this->initialized) {
            return;
        }

        // Register dependencies
        $this->register_dependencies();

        $this->initialized = true;
        Logger::log('Bootstrapped');
    }

    private function load_assets()
    {
        $assets = $this->container->get(Assets::class);
        $assets->load();
    }

    private function load_hooks()
    {
        $hooks = $this->container->get(Hooks::class);
        $hooks->init();
    }

    /**
     * Register our dependencies
     *
     * @return void
     */
    protected function register_dependencies()
    {
        $this->container->register(
            Hooks::class,
            function ($container) {
                return new Hooks();
            }
        );

        $this->container->register(
            Assets::class,
            function ($container) {
                $hooks = $container->get(Hooks::class);
                return new Assets($hooks);
            }
        );

        $this->container->register(
            Dashboard::class,
            function ($container) {
                $hooks = $container->get(Hooks::class);

                return new Dashboard($hooks);
            }
        );

        $this->container->register(
            AdminWooSettings::class,
            function ($container) {
                $hooks = $container->get(Hooks::class);

                return new AdminWooSettings($hooks);
            }
        );
    }

}
