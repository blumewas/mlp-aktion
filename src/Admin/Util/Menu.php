<?php

namespace Blumewas\MlpAktion\Admin\Util;

abstract class Menu
{

    /**
     * The function to register our menu inside wp-admin
     *
     * @return void
     */
    public function add_menu(): void
    {
        add_menu_page(
            $this->pageTitle(),
            $this->title(),
            $this->capability(),
            $this->slug(),
            [
                $this,
                'render'
            ],
            $this->icon(),
            $this->position(),
        );

        // TODO submenu page
        // add_submenu_page(
        //     'woocommerce',
        //     'Export Orders (Excel)',
        //     'Export Orders (Excel)',
        //     'manage_woocommerce',
        //     'export-orders-xlsx',
        //     [
        //         $this,
        //         'render_export_page_xlsx',
        //     ]
        //     // position
        // );
    }

    /**
     * Menu render function.
     *
     * @return void
     */
    abstract public function render();

    /**
     * Get the admin menu title
     *
     * @return string
     */
    public function title(): string
    {
        if ($this->string_exists($this, 'title')) {
            return $this->title;
        }

        return 'Menu';
    }

    /**
     * Get the admin page title
     *
     * @return string
     */
    public function pageTitle(): string
    {
        if ($this->string_exists($this, 'pageTitle')) {
            return $this->pageTitle;
        }

        return $this->title();
    }

    /**
     * The capability required to view the menu.
     *
     * @return string - capability {default: 'manage_options'}
     */
    public function capability(): string
    {
        if ($this->string_exists($this, 'capability')) {
            return $this->capability;
        }

        return 'manage_options';
    }

    /**
     * The icon for the menu
     *
     * @return string - icon {default: 'dashicons-admin-generic'}
     */
    public function icon(): string
    {
        if ($this->string_exists($this, 'icon')) {
            return $this->icon;
        }

        return 'dashicons-admin-generic';
    }

    /**
     * The position for the menu
     *
     * @return int - position {default: 20}
     */
    public function position(): int
    {
        if (property_exists($this, 'position')) {
            return $this->position;
        }

        return 20;
    }

    /**
     * Get the menu slug
     *
     * @return string - the menu slug. If nothing is set we sulgify the classname
     */
    public function slug(): string
    {
        if ($this->string_exists($this, 'slug')) {
            return $this->slug;
        }

        return slugify(
            class_basename($this)
        );
    }

    /**
     * Check if a string property exists
     * on passed object with given name.
     *
     * @param object $classStringOrObject
     * @return string
     */
    private function string_exists($obj, string $name): bool
    {
        return property_exists($obj, $name) && is_string($obj->$name);
    }
}
