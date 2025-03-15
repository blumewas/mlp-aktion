<?php

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;

class MLP_Aktion_Extend_Store_Endpoint
{

    /**
     * Stores Rest Extending instance.
     *
     * @var ExtendRestApi
     */
    private static $extend;

    /**
     * Plugin Identifier, unique to each plugin.
     *
     * @var string
     */
    const IDENTIFIER = 'mlp-aktion';

    /**
     * Bootstraps the class and hooks required data.
     */
    public static function init()
    {
        self::$extend = Automattic\WooCommerce\StoreApi\StoreApi::container()->get(Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::class);
        self::extend_store();
    }

    /**
     * Registers the actual data into each endpoint.
     */
    public static function extend_store()
    {
        if (is_callable([ self::$extend, 'register_endpoint_data' ])) {
            self::$extend->register_endpoint_data(
                [
                    'endpoint'        => CheckoutSchema::IDENTIFIER,
                    'namespace'       => self::IDENTIFIER,
                    'schema_callback' => [ 'MLP_Aktion_Extend_Store_Endpoint', 'extend_checkout_schema' ],
                    'schema_type'     => ARRAY_A,
                ]
            );
        }
    }

    /**
     * Register shipping workshop schema into the Checkout endpoint.
     *
     * @return array Registered schema.
     */
    public static function extend_checkout_schema()
    {
        return [
            'optin'           => [
                'description' => 'Bestätigung der Teilnahme an der MLP Aktion',
                'type'        => 'boolean',
                'context'     => [ 'view', 'edit' ],
                'readonly'    => true,
                'optional'    => true,
                'arg_options' => [
                    'validate_callback' => function ($value) {
                        return is_bool($value);
                    },
                ],
            ],
            'contact_phone' => [
                'description' => 'Kontakt Telefonnummer die für die Aktionsteilnahme verpflichtend ist',
                'type'        => ['null', 'string'],
                'context'     => [ 'view', 'edit' ],
                'readonly'    => true,
                'optional' => true,
                'arg_options' => [
                    'validate_callback' => function ($value) {
                        if (! is_string($value)) {
                            return false;
                        }

                        return preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $value);
                    },
                ],
            ],
        ];
    }

}
