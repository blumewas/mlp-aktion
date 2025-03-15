<?php

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

class MLP_Aktion_Extend_Woo_Core
{

    public function __construct(
        private string $name,
    ) {
    }

    public function init()
    {
        $this->save_mlp_aktion_participation_instructions();
        $this->show_mlp_aktion_participation_in_order();
        $this->show_mlp_aktion_participation_in_order_confirmation();
        $this->show_mlp_aktion_participation_in_order_email();
    }

    /**
     * Register mlp_aktion schema into the Checkout endpoint.
     *
     * @return array Registered schema.
     */
    public function extend_checkout_schema()
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
                'arg_options' => [
                    'validate_callback' => function ($value) {
                        return $this->is_phone_number($value);
                    },
                ],
            ],
        ];
    }

    /**
     * Saves the MLP Aktion participation to the order's metadata.
     *
     * @return void
     */
    private function save_mlp_aktion_participation_instructions()
    {
        add_action(
            'woocommerce_store_api_checkout_update_order_from_request',
            function (\WC_Order $order, \WP_REST_Request $request) {
                // If the order does not qualify for the aktion return
                if (! mlp_aktion_order_qualifies_for_aktion($order)) {
                    return;
                }

                // Validate the checkout
                $this->validate($order, $request);

                $mlp_aktion_request_data = $request['extensions'][ $this->name ];

                /**
                 * Get the mlp aktion fields
                 */
                $mlp_aktion_optin = $mlp_aktion_request_data['optin'];
                $mlp_atkion_contact_phone = $mlp_aktion_request_data['contact_phone'];

                // Update the order meta
                $value = $mlp_aktion_optin ? 'Ja' : 'Nein';
                $order->update_meta_data('mlp_aktion_optin', $value);
                $order->update_meta_data('mlp_aktion_contact_phone', $mlp_atkion_contact_phone);

                $order->save();
            },
            10,
            2
        );
    }

    /**
     * Adds the participation in the order page in WordPress admin.
     */
    private function show_mlp_aktion_participation_in_order()
    {
        add_action(
            'woocommerce_admin_order_data_after_shipping_address',
            function (\WC_Order $order) {
                // If the order does not qualify for the aktion return
                if (! mlp_aktion_order_qualifies_for_aktion($order)) {
                    return;
                }

                $mlp_aktion_optin            = $order->get_meta('mlp_aktion_optin');

                echo '<div>';
                echo '<strong>' . esc_html__('Teilnahme an MLP Aktion', $this->name) . '</strong>';
                printf('<p>Zustimmung zur Teilnahme: %s</p>', esc_html($mlp_aktion_optin));

                if ($mlp_aktion_optin == 'Ja') {
                    $mlp_aktion_contact_phone = $order->get_meta('mlp_aktion_contact_phone');
                    printf('<p>Telefonnummer: %s</p>', esc_html($mlp_aktion_contact_phone));
                }

            },
        );
	}

    /**
	 * Adds the address on the order confirmation page.
	 */
	private function show_mlp_aktion_participation_in_order_confirmation() {
		add_action(
			'woocommerce_thankyou',
			function( int $order_id ) {
                $order = wc_get_order( $order_id );

                // If the order does not qualify for the aktion return
                if (! mlp_aktion_order_qualifies_for_aktion($order)) {
                    return;
                }

                $mlp_aktion_optin            = $order->get_meta('mlp_aktion_optin');

                echo '<div>';
                echo '<strong>' . esc_html__('Teilnahme an MLP Aktion', $this->name) . '</strong>';
                printf('<p>Zustimmung zur Teilnahme: %s</p>', esc_html($mlp_aktion_optin));

				if ( '' !== $mlp_aktion_optin ) {
					echo '<h2>' . esc_html__( 'Teilnahme an MLP Aktion', $this->name ) . '</h2>';
                    printf('<p>Zustimmung zur Teilnahme: %s</p>', esc_html($mlp_aktion_optin));

					if ($mlp_aktion_optin == 'Ja') {
                        $mlp_aktion_contact_phone = $order->get_meta('mlp_aktion_contact_phone');
                        printf('<p>Telefonnummer: %s</p>', esc_html($mlp_aktion_contact_phone));
                    }
				}
			}
		);
	}

	/**
	 * Adds the address on the order confirmation email.
	 */
	private function show_mlp_aktion_participation_in_order_email() {
		add_action(
			'woocommerce_email_after_order_table',
			function( $order, $sent_to_admin, $plain_text, $email ) {
				// If the order does not qualify for the aktion return
                if (! mlp_aktion_order_qualifies_for_aktion($order)) {
                    return;
                }

                $mlp_aktion_optin            = $order->get_meta('mlp_aktion_optin');

                echo '<div>';
                echo '<strong>' . esc_html__('Teilnahme an MLP Aktion', $this->name) . '</strong>';
                printf('<p>Zustimmung zur Teilnahme: %s</p>', esc_html($mlp_aktion_optin));

				if ( '' !== $mlp_aktion_optin ) {
					echo '<h2>' . esc_html__( 'Teilnahme an MLP Aktion', $this->name ) . '</h2>';
                    printf('<p>Zustimmung zur Teilnahme: %s</p>', esc_html($mlp_aktion_optin));

					if ($mlp_aktion_optin == 'Ja') {
                        $mlp_aktion_contact_phone = $order->get_meta('mlp_aktion_contact_phone');
                        printf('<p>Telefonnummer: %s</p>', esc_html($mlp_aktion_contact_phone));
                    }
				}
			},
			10,
			4
		);
	}

    /**
	 * @param \WC_Order $order
	 * @param \WP_REST_Request $request
	 *
	 * @return void
	 */
    private function validate( $order, $request )
    {
        // If the order does not qualify for the aktion skip
        if (! mlp_aktion_cart_qualifies_for_aktion()) {
            return;
        }

        if ( $this->has_checkout_data( 'optin', $request ) ) {
            $mlp_aktion_request_data = $request['extensions'][ $this->name ];
            $optin = $mlp_aktion_request_data['optin'];

            $phoneNumberRequired = get_option('mlp_aktion_phone_number_required') == 'yes';
            // If optin is false skip or phone is not required skip
            if (! $optin || ! $phoneNumberRequired) {
                return;
            }

            $contact_phone = $mlp_aktion_request_data['contact_phone'];

            if (! $this->is_phone_number($contact_phone)) {
                throw new RouteException( "mlp_aktion_contact_phone", __('Du musst eine gültige Telefonnummer eingeben, um an der Aktion teilzunehmen.', $this->name), 400 );
            }
		}
	}

	private function has_checkout_data( $param, $request ) {
		$request_data = isset( $request['extensions']['mlp-aktion'] ) ? (array) $request['extensions']['mlp-aktion'] : array();

		return isset( $request_data[ $param ] ) && null !== $request_data[ $param ];
	}

    /**
     *
     *
     * @param mixed $value
     * @return boolean
     */
    private function is_phone_number($value)
    {
        return is_string($value) && preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $value);
    }

}
