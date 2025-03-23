<?php

namespace Blumewas\MlpAktion\Public;

use Blumewas\MlpAktion\Plugin\Assets;
use Blumewas\MlpAktion\Plugin\Hooks;

class Section
{
    public function __construct(
        private Hooks $hooks,
        private Assets $assets,
    ) {
    }

    /**
     * Init our public section
     *
     * @return void
     */
    public function init()
    {
        $this->register_assets();
    }

    protected function register_assets(): void
    {
        $this->assets->add_public_asset(
            asset('public/js/mlp-aktion-public.js'),
            ['jquery'],
        );

        $this->assets->add_public_asset(
            asset('public/css/mlp-aktion-public.css'),
        );
    }
}
