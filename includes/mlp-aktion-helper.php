<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! function_exists('mlp_aktion_order_qualifies_for_aktion'))
{

    /**
     * Check if an order qualifies for the aktion
     *
     * @param @param \WC_Order $order
     * @return boolean
     */
    function mlp_aktion_order_qualifies_for_aktion($order)
    {
        // Get the category settings
        $product_category_settings = get_option('mlp_aktion_active_aktion_categories');

        // If no category ids are set the Aktion is active for all products/categories
        if (! $product_category_settings) {
            return true;
        }

        if (is_int($product_category_settings)) {
            $product_category_settings = [$product_category_settings];
        }

        // Get the order items
        $items = $order->get_items();
        if (empty($items)) {
            return false;
        }

        // Get product ids
        $product_ids = array_filter(array_map(function ($item) {
            $data = $item->get_data();

            return ($product_id = $data['product_id']) != false ? $product_id : null;
        }, $items));

        return mlp_aktion_are_products_in_categories($product_ids, $product_category_settings);
    }

}

if (! function_exists('mlp_aktion_cart_qualifies_for_aktion'))
{
    /**
     * Check if cart qualifies for aktion
     *
     * @return boolean
     */
    function mlp_aktion_cart_qualifies_for_aktion()
    {
        $product_category_settings = get_option('mlp_aktion_active_aktion_categories');

        // If no category ids are set the Aktion is active for all products/categories
        if (! $product_category_settings) {
            return true;
        }

        if (is_int($product_category_settings)) {
            $product_category_settings = [$product_category_settings];
        }

        // If settings is no array return false
        if (!is_array($product_category_settings)) {
            return false;
        }

        return mlp_aktion_cart_contains_category($product_category_settings);
    }
}

if (! function_exists('mlp_aktion_cart_contains_category'))
{
    /**
     * Check if the current cart contains the given Categories
     *
     * @return boolean
     */
    function mlp_aktion_cart_contains_category(array $categories)
    {
        // Check if WooCommerce cart exists
        if (!WC()->cart || WC()->cart->is_empty()) {
            return false;
        }

        $product_ids = array_map(function ($cart_item) {
            $product_id = $cart_item['product_id'];

            return $product_id;
        }, WC()->cart->get_cart());

        return mlp_aktion_are_products_in_categories($product_ids, $categories);
    }
}

if (! function_exists('mlp_aktion_are_products_in_categories'))
{

    /**
     * Check if atleast one of the products have the category id
     *
     * @param array $product_ids
     * @param array $categories
     * @return boolean
     */
    function mlp_aktion_are_products_in_categories(array $product_ids, array $categories)
    {
        foreach ($product_ids as $product_id) {
            $product_categories = wc_get_product_terms($product_id, 'product_cat', ['fields' => 'ids']);

            // Check if any category matches
            if (array_intersect($product_categories, $categories)) {
                return true;
            }
        }

        return false;
    }
}
