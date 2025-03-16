/**
 * External dependencies
 */
import { registerPlugin } from '@wordpress/plugins';

const render = () => {};

registerPlugin( 'mlp-aktion', {
	render,
	scope: 'woocommerce-checkout',
} );
